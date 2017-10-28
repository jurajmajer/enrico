<?php

include_once("Holiday.php");
include_once("EnricoDate.php");
include_once("utils/DateUtils.php");
include_once("utils/OrthodoxCalUtils.php");
include_once("utils/ChineseCalUtils.php");
include_once("utils/EquinoxUtils.php");

class HolidayProcessor {
	
	private $commonHolidayDefs;
	private $dateUtils;
	private $orthodoxCalUtils;
	private $chineseCalUtils;
	private $equinoxUtils;
	private $holidayDefsDir;
	private $holidayDefsRegionDir;
	public static $ENRICO_NAMESPACE = "https://kayaposoft.com/enrico/xsd/1.0";
	
	public function __construct($countryCode, $region) {
		$this->holidayDefsDir = __DIR__."/holiday_defs/$countryCode/";
		$this->commonHolidayDefs = $this->loadHolidayDefs("$this->holidayDefsDir$countryCode.xml");
		if(isset($region) && strlen($region) > 0) {
			$this->commonHolidayDefs = array_merge($this->commonHolidayDefs, $this->loadHolidayDefs("$this->holidayDefsDir$region/$region.xml"));
			$this->holidayDefsRegionDir = "$this->holidayDefsDir$region/";
		}
		$this->dateUtils = new DateUtils();
		$this->orthodoxCalUtils = new OrthodoxCalUtils();
		$this->chineseCalUtils = new ChineseCalUtils();
		$this->equinoxUtils = new EquinoxUtils();
	}
	
	public function getHolidays($year) {
		
		$retVal = $this->transformHolidayDefs($this->commonHolidayDefs, $year);
		$yearFile = "$this->holidayDefsDir$year.xml";
		if(file_exists($yearFile)) {
			$retVal = array_merge($retVal, $this->transformHolidayDefs($this->loadHolidayDefs($yearFile), $year));
		}
		if(isset($this->holidayDefsRegionDir)) {
			$yearFile = "$this->holidayDefsRegionDir$year.xml";
			if(file_exists($yearFile)) {
				$retVal = array_merge($retVal, $this->transformHolidayDefs($this->loadHolidayDefs($yearFile), $year));
			}
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
				if($holiday != NULL && $holiday->date->compare($validFrom) >= 0) {
					if($validTo == NULL || $holiday->date->compare($validTo) <= 0) {
						array_push($retVal, $holiday);
					}
				}
			}
		}
		return $retVal;
	}
	
	private function calculateHoliday($holidayDef, $year) {
		$retVal = new Holiday($this->calculateDate($holidayDef->getElementsByTagNameNS(HolidayProcessor::$ENRICO_NAMESPACE, "date")[0], $year));
		$names = $holidayDef->getElementsByTagNameNS(HolidayProcessor::$ENRICO_NAMESPACE, "name");
		for($i=0; $i<$names->length; $i++) {
			array_push($retVal->name, new LocalizedString($names[$i]->getAttribute("lang"), $names[$i]->nodeValue));
		}
		$notes = $holidayDef->getElementsByTagNameNS(HolidayProcessor::$ENRICO_NAMESPACE, "note");
		for($i=0; $i<$notes->length; $i++) {
			array_push($retVal->note, new LocalizedString($notes[$i]->getAttribute("lang"), $notes[$i]->nodeValue));
		}
		$flags = $holidayDef->getElementsByTagNameNS(HolidayProcessor::$ENRICO_NAMESPACE, "flag");
		for($i=0; $i<$flags->length; $i++) {
			array_push($retVal->flags, $flags[$i]->nodeValue);
		}
		$additionalHolidays = $this->resolveObservance($holidayDef, $retVal);
		$retVal = array($retVal);
		foreach($additionalHolidays as $additionalHoliday) {
			array_push($retVal, $additionalHoliday);
		}
		return $retVal;
	}
	
	private function calculateDate($dateElement, $year) {
		$firstChild = $this->getFirstNonCommentChild($dateElement);
		if(strcmp($firstChild->nodeName, "fixedDate") == 0) {
			return new EnricoDate($firstChild->getAttribute("day"), $firstChild->getAttribute("month"), $year);
		}
		if(strcmp($firstChild->nodeName, "specialDate") == 0) {
			return $this->resolveSpecialDateValue($firstChild->nodeValue, $year);
		}
		if(strcmp($firstChild->nodeName, "nthWeekdayRuleDate") == 0) {
			return $this->dateUtils->getNthWeekday($firstChild->getAttribute("dayOfWeek"), $firstChild->getAttribute("month"), $firstChild->getAttribute("nth"), $year);
		}
		if(strcmp($firstChild->nodeName, "dateTransformation") == 0) {
			$baseDate = $this->calculateDate($this->getFirstNonCommentChild($firstChild), $year);
			return $this->resolveDateTransformation($baseDate, $this->getLastNonCommentChild($firstChild));
		}
		
		throw new Exception('Unknown date calculation method \'' . $firstChild->nodeName . '\'');
	}
	
	private function getFirstNonCommentChild($element) {
		$child = $element->firstChild;
		while($child != NULL && strcmp($child->nodeName, "#comment") == 0) {
			$child = $child->nextSibling;
		}
		if($child != NULL && strcmp($child->nodeName, "#comment") != 0) {
			return $child;
		}
		return NULL;
	}
	
	private function getLastNonCommentChild($element) {
		$child = $element->lastChild;
		while($child != NULL && strcmp($child->nodeName, "#comment") == 0) {
			$child = $child->previousSibling;
		}
		if($child != NULL && strcmp($child->nodeName, "#comment") != 0) {
			return $child;
		}
		return NULL;
	}
	
	private function resolveSpecialDateValue($specialDateValue, $year) {
		if(strcmp($specialDateValue, "EASTER_SUNDAY") == 0) {
			return $this->dateUtils->addDays(new EnricoDate(21 , 3, $year), easter_days($year));
		}
		if(strcmp($specialDateValue, "ORTHODOX_EASTER_SUNDAY") == 0) {
			return $this->orthodoxCalUtils->getOrthodoxEasterSunday($year);
		}
		if(strcmp($specialDateValue, "CHINESE_MONTH_1ST_START") == 0) {
			return $this->chineseCalUtils->calculateChineseCalendar($year)[0];
		}
		if(strcmp($specialDateValue, "CHINESE_MONTH_5TH_START") == 0) {
			return $this->chineseCalUtils->calculateChineseCalendar($year)[4];
		}
		if(strcmp($specialDateValue, "CHINESE_MONTH_8TH_START") == 0) {
			return $this->chineseCalUtils->calculateChineseCalendar($year)[7];
		}
		if(strcmp($specialDateValue, "MARCH_EQUINOX") == 0) {
			return $this->equinoxUtils->getMarchEquinox($year);
		}
		if(strcmp($specialDateValue, "SEPTEMBER_EQUINOX") == 0) {
			return $this->equinoxUtils->getSeptemberEquinox($year);
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
		$retVal = array();
		for($i=0; $i<$observanceRules->length; $i++) {
			$retVal = array_merge($retVal, $this->resolveObservanceRule($observanceRules[$i]->nodeValue, $observedOn, $holiday));
			if (!empty($retVal)) {
				break;
			}
			if($observedOn->compare($holiday->date) != 0) {
				$holiday->observedOn = $observedOn;
				break;
			}
		}
		return $retVal;
	}
	
	private function resolveObservanceRule($observanceRule, $date, $holiday) {
		$additionalHolidayDaysPlus = NULL;
		if(strcmp($observanceRule, "SUB_1DAY_IF_FRIDAY") == 0) {
			if($this->dateUtils->getDayOfWeek($date) == 5) {
				$this->dateUtils->addDays($date, -1);
			}
		} else if(strcmp($observanceRule, "SUB_1DAY_IF_SATURDAY") == 0) {
			if($this->dateUtils->getDayOfWeek($date) == 6) {
				$this->dateUtils->addDays($date, -1);
			}
		} else if(strcmp($observanceRule, "SUB_2DAYS_IF_SUNDAY") == 0) {
			if($this->dateUtils->getDayOfWeek($date) == 7) {
				$this->dateUtils->addDays($date, -2);
			}
		} else if(strcmp($observanceRule, "ADD_2DAYS_IF_SATURDAY") == 0) {
			if($this->dateUtils->getDayOfWeek($date) == 6) {
				$this->dateUtils->addDays($date, 2);
			}
		} else if(strcmp($observanceRule, "ADD_1DAY_IF_SUNDAY") == 0) {
			if($this->dateUtils->getDayOfWeek($date) == 7) {
				$this->dateUtils->addDays($date, 1);
			}
		} else if(strcmp($observanceRule, "ADD_2DAYS_IF_SUNDAY") == 0) {
			if($this->dateUtils->getDayOfWeek($date) == 7) {
				$this->dateUtils->addDays($date, 2);
			}
		} else if(strcmp($observanceRule, "ADD_1DAY_IF_MONDAY") == 0) {
			if($this->dateUtils->getDayOfWeek($date) == 1) {
				$this->dateUtils->addDays($date, 1);
			}
		} else if(strcmp($observanceRule, "ADD_1DAY_IF_TUESDAY") == 0) {
			if($this->dateUtils->getDayOfWeek($date) == 2) {
				$this->dateUtils->addDays($date, 1);
			}
		} else if(strcmp($observanceRule, "ADDITIONAL_HOLIDAY_2DAYS_AFTER_IF_SATURDAY") == 0) {
			if($this->dateUtils->getDayOfWeek($date) == 6) {
				$additionalHolidayDaysPlus = 2;
			}
		} else if(strcmp($observanceRule, "ADDITIONAL_HOLIDAY_1DAY_AFTER_IF_SUNDAY") == 0) {
			if($this->dateUtils->getDayOfWeek($date) == 7) {
				$additionalHolidayDaysPlus = 1;
			}
		} else if(strcmp($observanceRule, "ADDITIONAL_HOLIDAY_2DAYS_AFTER_IF_SUNDAY") == 0) {
			if($this->dateUtils->getDayOfWeek($date) == 7) {
				$additionalHolidayDaysPlus = 2;
			}
		} else if(strcmp($observanceRule, "ADDITIONAL_HOLIDAY_1DAY_AFTER_IF_MONDAY") == 0) {
			if($this->dateUtils->getDayOfWeek($date) == 1) {
				$additionalHolidayDaysPlus = 1;
			}
		}
		if($additionalHolidayDaysPlus != NULL) {
			$additionalDate = EnricoDate::createNew($date);
			$additionalDate = $this->dateUtils->addDays($additionalDate, $additionalHolidayDaysPlus);
			$additionalHoliday = new Holiday($additionalDate);
			$additionalHoliday->name = $holiday->name;
			array_push($additionalHoliday->flags, "ADDITIONAL_HOLIDAY");
			return array($additionalHoliday);
		}
		return array();
	}
	
	private function loadHolidayDefs($xmlFileName) {
		if(!file_exists($xmlFileName)) {
			return array();
		}
		$xml= new DOMDocument();
		$xml->load($xmlFileName, LIBXML_NOBLANKS);
		return iterator_to_array($xml->getElementsByTagNameNS(HolidayProcessor::$ENRICO_NAMESPACE, "holiday"));
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
}

?>