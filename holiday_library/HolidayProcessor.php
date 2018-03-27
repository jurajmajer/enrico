<?php

include_once("Holiday.php");
include_once("EnricoDate.php");
include_once("utils/DateUtils.php");
include_once("utils/OrthodoxCalUtils.php");
include_once("utils/ChineseCalUtils.php");
include_once("utils/EquinoxUtils.php");
include_once("utils/HebrewCalUtils.php");
include_once("utils/HijriCalUtils.php");

class HolidayProcessor {
	
	private $dateUtils;
	private $orthodoxCalUtils;
	private $chineseCalUtils;
	private $hebrewCalUtils;
	private $hijriCalUtils;
	private $equinoxUtils;
	private $countryCode;
	private $region;
	public static $ENRICO_NAMESPACE = "https://kayaposoft.com/enrico/xsd/1.0";
	public static $HOLIDAY_DEFS_DIR = __DIR__."/holiday_defs/";
	public static $HOLIDAY_TYPES = array("public_holiday", "observance", "school_holiday", "other_day", "extra_working_day");
	
	public function __construct($countryCode, $region) {
		$this->countryCode = $countryCode;
		$this->region = $region;
		$this->dateUtils = new DateUtils();
		$this->orthodoxCalUtils = new OrthodoxCalUtils();
		$this->chineseCalUtils = new ChineseCalUtils();
		$this->hebrewCalUtils = new HebrewCalUtils();
		$this->hijriCalUtils = new HijriCalendar();
		$this->equinoxUtils = new EquinoxUtils();
	}
	
	public function getHolidays($year, $holidayType) {
		
		$retVal = array();
		$holidayDefsPathes = $this->constructHolidayDefsPaths($year, $holidayType);
		for($i=0; $i<sizeof($holidayDefsPathes); $i++) {
			$retVal = array_merge($retVal, $this->transformHolidayDefs($this->loadHolidayDefs($holidayDefsPathes[$i]), $year));
		}
		return $retVal;
	}
	
	private function constructHolidayDefsPaths($year, $holidayType) {
		$retVal = array();
		if(in_array($holidayType, HolidayProcessor::$HOLIDAY_TYPES)) {
			$retVal = array_merge($retVal, $this->constructHolidayDefsFileNames($year, HolidayProcessor::$HOLIDAY_DEFS_DIR.$holidayType."/"));
		} else if($holidayType == "all") {
			for($i=0; $i<sizeof(HolidayProcessor::$HOLIDAY_TYPES); $i++) {
				$retVal = array_merge($retVal, $this->constructHolidayDefsFileNames($year, HolidayProcessor::$HOLIDAY_DEFS_DIR.strtolower(HolidayProcessor::$HOLIDAY_TYPES[$i])."/"));
			}
		} else {
			throw new Exception('Unknown holiday type \'' . $holidayType . '\'');
		}
		return $retVal;
	}
	
	private function constructHolidayDefsFileNames($year, $rootPath) {
		$retVal = array();
		$countryCodePath = $rootPath . $this->countryCode . "/";
		array_push($retVal, "$countryCodePath$this->countryCode.xml");
		array_push($retVal, "$countryCodePath$year.xml");
		if(isset($this->region) && strlen($this->region) > 0) {
			array_push($retVal, "$countryCodePath$this->region/$this->region.xml");
			array_push($retVal, "$countryCodePath$this->region/$year.xml");
		}
		return $retVal;
	}
	
	private function transformHolidayDefs($holidayDefs, $year) {
		
		$retVal = array();
		$length = count($holidayDefs);
		for($pos=0; $pos<$length; $pos++) {
			$holidayDef = $holidayDefs[$pos];
			$validFrom = EnricoDate::fromXmlDate($holidayDef->getAttribute("validFrom"));
			if($validFrom->year > $year) {
				continue;
			}
			$validTo = $holidayDef->getAttribute("validTo");
			if($validTo != NULL) {
				$validTo = EnricoDate::fromXmlDate($validTo);
				if($validTo->year < $year) {
					continue;
				}
			}
			if(!$this->isPeriodValid($holidayDef, $year)) {
				continue;
			}
			$holidays = $this->calculateHoliday($holidayDef, $year);
			foreach($holidays as $holiday) {
				if($holiday != NULL && $holiday->date->compare($validFrom) >= 0 && 
					($validTo == NULL || $holiday->date->compare($validTo) <= 0) &&
					$this->isConditionSatisfied($holidayDef, $holiday)) {
					array_push($retVal, $holiday);
				}
			}
		}
		return $retVal;
	}
	
	private function calculateHoliday($holidayDef, $year) {
		$retVal = array();
		$dateElements = $holidayDef->getElementsByTagNameNS(HolidayProcessor::$ENRICO_NAMESPACE, "date");
		$dates = $this->calculateDate($dateElements->item(0), $year);
		foreach($dates as $date) {
			$holiday = new Holiday($date);
			$dateTo = $holidayDef->getElementsByTagNameNS(HolidayProcessor::$ENRICO_NAMESPACE, "dateTo");
			if($dateTo->length > 0) {
				$holiday->dateTo = $this->calculateDate($dateTo->item(0), $year);
				$holiday->dateTo = $holiday->dateTo[0];
			}
			$names = $holidayDef->getElementsByTagNameNS(HolidayProcessor::$ENRICO_NAMESPACE, "name");
			for($i=0; $i<$names->length; $i++) {
				array_push($holiday->name, new LocalizedString($names->item($i)->getAttribute("lang"), $names->item($i)->nodeValue));
			}
			$notes = $holidayDef->getElementsByTagNameNS(HolidayProcessor::$ENRICO_NAMESPACE, "note");
			for($i=0; $i<$notes->length; $i++) {
				array_push($holiday->note, new LocalizedString($notes->item($i)->getAttribute("lang"), $notes->item($i)->nodeValue));
			}
			$flags = $holidayDef->getElementsByTagNameNS(HolidayProcessor::$ENRICO_NAMESPACE, "flag");
			for($i=0; $i<$flags->length; $i++) {
				array_push($holiday->flags, $flags->item($i)->nodeValue);
			}
			$holidayType = $holidayDef->getAttribute("holidayType");
			if($holidayType != NULL) {
				$holiday->holidayType = $holidayType;
			} else {
				$holiday->holidayType = "public_holiday";
			}
			$additionalHolidays = $this->resolveObservance($holidayDef, $holiday);
			array_push($retVal, $holiday);
			foreach($additionalHolidays as $additionalHoliday) {
				array_push($retVal, $additionalHoliday);
			}
		}
		return $retVal;
	}
	
	private function calculateDate($dateElement, $year) {
		$firstChild = $this->getFirstNonCommentChild($dateElement);
		if(strcmp($firstChild->nodeName, "fixedDate") == 0) {
			return array(new EnricoDate($firstChild->getAttribute("day"), $firstChild->getAttribute("month"), $year));
		}
		if(strcmp($firstChild->nodeName, "specialDate") == 0) {
			return $this->resolveSpecialDateValue($firstChild->nodeValue, $year);
		}
		if(strcmp($firstChild->nodeName, "nthWeekdayRuleDate") == 0) {
			return array($this->dateUtils->getNthWeekday($firstChild->getAttribute("dayOfWeek"), $firstChild->getAttribute("month"), $firstChild->getAttribute("nth"), $year));
		}
		if(strcmp($firstChild->nodeName, "dateTransformation") == 0) {
			$baseDates = $this->calculateDate($this->getFirstNonCommentChild($firstChild), $year);
			$retVal = array();
			foreach($baseDates as $baseDate) {
				array_push($retVal, $this->resolveDateTransformation($baseDate, $this->getLastNonCommentChild($firstChild)));
			}
			return $retVal;
		}
		
		throw new Exception('Unknown date calculation method \'' . $firstChild->nodeName . '\'');
	}
	
	private function getFirstNonCommentChild($element) {
		$child = $element->firstChild;
		while($child != NULL && (strcmp($child->nodeName, "#comment") == 0 || strcmp($child->nodeName, "#text") == 0)) {
			$child = $child->nextSibling;
		}
		if($child != NULL && strcmp($child->nodeName, "#comment") != 0 && strcmp($child->nodeName, "#text") != 0) {
			return $child;
		}
		return NULL;
	}
	
	private function getLastNonCommentChild($element) {
		$child = $element->lastChild;
		while($child != NULL && (strcmp($child->nodeName, "#comment") == 0 || strcmp($child->nodeName, "#text") == 0)) {
			$child = $child->previousSibling;
		}
		if($child != NULL && strcmp($child->nodeName, "#comment") != 0 && strcmp($child->nodeName, "#text") != 0) {
			return $child;
		}
		return NULL;
	}
	
	private function resolveSpecialDateValue($specialDateValue, $year) {
		if(strcmp($specialDateValue, "EASTER_SUNDAY") == 0) {
			return array($this->dateUtils->addDays(new EnricoDate(21 , 3, $year), easter_days($year)));
		}
		if(strcmp($specialDateValue, "ORTHODOX_EASTER_SUNDAY") == 0) {
			return array($this->orthodoxCalUtils->getOrthodoxEasterSunday($year));
		}
		if(strcmp($specialDateValue, "CHINESE_MONTH_1ST_START") == 0) {
			$temp = $this->chineseCalUtils->calculateChineseCalendar($year);
			return array($temp[0]);
		}
		if(strcmp($specialDateValue, "CHINESE_MONTH_4TH_START") == 0) {
			$temp = $this->chineseCalUtils->calculateChineseCalendar($year);
			return array($temp[3]);
		}
		if(strcmp($specialDateValue, "CHINESE_MONTH_5TH_START") == 0) {
			$temp = $this->chineseCalUtils->calculateChineseCalendar($year);
			return array($temp[4]);
		}
		if(strcmp($specialDateValue, "CHINESE_MONTH_8TH_START") == 0) {
			$temp = $this->chineseCalUtils->calculateChineseCalendar($year);
			return array($temp[7]);
		}
		if(strcmp($specialDateValue, "CHINESE_MONTH_9TH_START") == 0) {
			$temp = $this->chineseCalUtils->calculateChineseCalendar($year);
			return array($temp[8]);
		}
		if(strcmp($specialDateValue, "HEBREW_MONTH_1ST_START") == 0) {
			$hebrewCalendar = $this->hebrewCalUtils->calculateHebrewCalendar($year);
			$nissanIndex = 4;
			if(count($hebrewCalendar) == 16) {
				$nissanIndex = 5;
			}
			return array($hebrewCalendar[$nissanIndex]->startDate);
		}
		if(strcmp($specialDateValue, "HIJRI_MONTH_10TH_START") == 0) {
			return $this->hijriCalUtils->getFirstDayOfHijriMonth(10, $year);
		}
		if(strcmp($specialDateValue, "HIJRI_MONTH_12TH_START") == 0) {
			return $this->hijriCalUtils->getFirstDayOfHijriMonth(12, $year);
		}
		if(strcmp($specialDateValue, "MARCH_EQUINOX") == 0) {
			return array($this->equinoxUtils->getMarchEquinox($year));
		}
		if(strcmp($specialDateValue, "SEPTEMBER_EQUINOX") == 0) {
			return array($this->equinoxUtils->getSeptemberEquinox($year));
		}
		
		throw new Exception('Unknown special date value \'' . $specialDateValue . '\'');
	}
	
	private function resolveDateTransformation($baseDate, $transformation) {
		if(strcmp($transformation->nodeName, "addDays") == 0) {
			return $this->dateUtils->addDays($baseDate, $transformation->nodeValue);
		}
		if(strcmp($transformation->nodeName, "addSeconds") == 0) {
			return $this->dateUtils->addSeconds($baseDate, $transformation->nodeValue);
		}
		if(strcmp($transformation->nodeName, "nearestWeekday") == 0) {
			return $this->dateUtils->getNearestWeekday($baseDate, $transformation->getAttribute("dayOfWeek"));
		}
		if(strcmp($transformation->nodeName, "nearestWeekdayAfter") == 0) {
			return $this->dateUtils->getNearestWeekdayAfter($baseDate, $transformation->getAttribute("dayOfWeek"));
		}
		
		throw new Exception('Unknown date transformation mode \'' . $transformation->nodeName . '\'');
	}
	
	private function resolveObservance($holidayDef, $holiday) {
		$observanceRules = $holidayDef->getElementsByTagNameNS(HolidayProcessor::$ENRICO_NAMESPACE, "observanceRule");
		$observedOn = EnricoDate::createNew($holiday->date);
		for($i=0; $i<$observanceRules->length; $i++) {
			$retVal = $this->resolveObservanceRule($observanceRules->item($i), $observedOn, $holiday);
			if (!empty($retVal)) {
				return $retVal;
			}
			if($observedOn->compare($holiday->date) != 0) {
				$holiday->observedOn = $observedOn;
				return array();
			}
		}
		return array();
	}
	
	private function resolveObservanceRule($observanceRule, $date, $holiday) {
		$dayOfWeek = intval($observanceRule->getAttribute("dayOfWeek"));
		if($this->dateUtils->getDayOfWeek($date) != $dayOfWeek) {
			return array();
		}
		$additionalHoliday = $observanceRule->getAttribute("additionalHoliday") === 'true';
		$addDays = intval($observanceRule->getAttribute("addDays"));
		if(!$additionalHoliday) {
			$this->dateUtils->addDays($date, $addDays);
			return array();
		}
		$additionalDate = EnricoDate::createNew($date);
		$additionalDate = $this->dateUtils->addDays($additionalDate, $addDays);
		$additionalHoliday = new Holiday($additionalDate);
		$additionalHoliday->name = $holiday->name;
		$additionalHoliday->note = $holiday->note;
		$additionalHoliday->holidayType = $holiday->holidayType;
		$additionalHoliday->flags = $holiday->flags;
		array_push($additionalHoliday->flags, "ADDITIONAL_HOLIDAY");
		return array($additionalHoliday);
	}
	
	private function loadHolidayDefs($xmlFileName) {
		if(!file_exists($xmlFileName)) {
			return array();
		}
		$xml = new DOMDocument();
		$xml->load($xmlFileName, LIBXML_NOBLANKS);
		if (!$xml->schemaValidate(HolidayProcessor::$HOLIDAY_DEFS_DIR . "enrico.xsd"))
		{
		   throw new Exception("Xml file " . $xmlFileName . " is not valid against xsd!");
		}
		$holidayDefs = $xml->getElementsByTagNameNS(HolidayProcessor::$ENRICO_NAMESPACE, "holiday");
		$retVal = array();
		foreach ($holidayDefs as $holidayDef) {
			array_push($retVal, $holidayDef);
		}
		return $retVal;
	}
	
	private function isPeriodValid($holidayDef, $year) {
		$frequency = $holidayDef->getAttribute("frequency");
		if($frequency == NULL || strpos($frequency, '%') == false) {
			return true;
		}
		$parts = explode('%', $frequency);
		$divisor = intval($parts[0]);
		$remainder = intval($parts[1]);
		if($year % $divisor == $remainder) {
			return true;
		}
		return false;
	}
	
	private function isConditionSatisfied($holidayDef, $holiday) {
		
		$if = $holidayDef->getElementsByTagNameNS(HolidayProcessor::$ENRICO_NAMESPACE, "if");
		$ifNot = $holidayDef->getElementsByTagNameNS(HolidayProcessor::$ENRICO_NAMESPACE, "ifNot");
		
		if($if->length != 0) {
			$match = false;
			for($i=0; $i<$if->length; $i++) {
				$dayOfWeek = intval($if->item($i)->getAttribute("dayOfWeek"));
				if($this->dateUtils->getDayOfWeek($holiday->date) == $dayOfWeek) {
					$match = true;
					break;
				}
			}
			if($match == false) {
				return false;
			}
		}
		
		if($ifNot->length != 0) {
			for($i=0; $i<$ifNot->length; $i++) {
				$dayOfWeek = intval($ifNot->item($i)->getAttribute("dayOfWeek"));
				if($this->dateUtils->getDayOfWeek($holiday->date) == $dayOfWeek) {
					return false;
				}
			}
		}
		
		return true;
	}
}

?>