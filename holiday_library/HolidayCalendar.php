<?php 

include_once("EnricoDate.php");
include_once("utils/Utils.php");
include_once("HolidayProcessor.php");
include_once("SupportedCountry.php");
include_once("utils/DateUtils.php");

class HolidayCalendar {
	
	protected $countryCode;
	protected $region;
	protected $contryConfig;
	
	public function __construct($countryCode, $region) {
		$this->countryCode = Utils::canonicalizeCountryCode($countryCode);
		$this->region = Utils::canonicalizeRegion($this->countryCode, $region);
		
		$supportedCountries = HolidayCalendar::getSupportedCountries();
		if(!isset($supportedCountries[$this->countryCode]))
			throw new Exception('Country \'' . $this->countryCode . '\' is not supported');
		$this->contryConfig = $supportedCountries[$this->countryCode];
		if(isset($this->region) && strlen($this->region) > 0 && count($this->contryConfig->regions) > 0 && $this->contryConfig->isRegionSupported($this->region) == FALSE) {
			throw new Exception('Region \'' . $this->region . '\' in country \'' . $this->countryCode . '\' is not supported');
		}
	}
	
	public function getHolidaysForMonth($month, $year, $holidayType="all") {
		$fromDate = new EnricoDate(1, $month, $year);
		$lastDay = 31;
		while(checkdate($month, $lastDay, $year) == FALSE)
			$lastDay--;
		$toDate = new EnricoDate($lastDay, $month, $year);
		return $this->getHolidaysForDateRange($fromDate, $toDate, $holidayType);
	}

	public function getHolidaysForYear($year, $holidayType="all") {
		$fromDate = new EnricoDate(1, 1, $year);
		$toDate = new EnricoDate(31, 12, $year);
		return $this->getHolidaysForDateRange($fromDate, $toDate, $holidayType);
	}
	
	public function getHolidaysForDateRange($fromDate, $toDate, $holidayType="all") {
		
		if(checkdate($fromDate->month, $fromDate->day, $fromDate->year) == FALSE)
			throw new Exception($fromDate->toString() . " is not a valid date");
		if(checkdate($toDate->month, $toDate->day, $toDate->year) == FALSE)
			throw new Exception($toDate->toString() . " is not a valid date");
		if($fromDate->compare($toDate) > 0)
			throw new Exception($fromDate->toString() . " is later than " . $toDate->toString());
		if($fromDate->compare($this->contryConfig->fromDate) < 0)
			throw new Exception("Dates before " . $this->contryConfig->fromDate->toString() . " are not supported");
		if($toDate->compare($this->contryConfig->toDate) > 0)
			throw new Exception("Dates after " . $this->contryConfig->toDate->toString() . " are not supported");
		$retVal = array();
		$holidayProcessor = new HolidayProcessor($this->countryCode, $this->region);
		$year = $fromDate->year;
		while($year <= $toDate->year) {
			$holidays = $holidayProcessor->getHolidays($year, $holidayType);
			$retVal = array_merge($retVal, $this->pickHolidaysBetweeenDates($fromDate, $toDate, $holidays));
			$year += 1;
		}
		return $retVal;
	}
	
	public function isPublicHoliday($date) {
		return $this->isHoliday($this->getHolidaysForDateRange($date, $date, "public_holiday"));
	}
	
	public function isSchoolHoliday($date) {
		return $this->isHoliday($this->getHolidaysForDateRange($date, $date, "school_holiday"));
	}
	
	public function isWorkDay($date) {
		// is it extra working day?
		if($this->isHoliday($this->getHolidaysForDateRange($date, $date, "extra_working_day"))) {
			return TRUE;
		}
		// is it Saturday or Sunday?
		$dateUtils = new DateUtils();
		$dayOfWeek = $dateUtils->getDayOfWeek($date);
		if($dayOfWeek == 6 || $dayOfWeek == 7) {
			return FALSE;
		}
		// is it public holiday?
		if($this->isPublicHoliday($date)) {
			return FALSE;
		}
		// is some public holiday observed on this day?
		$holidays = $this->getHolidaysForYear($date->year-1, "public_holiday");
		$holidays = array_merge($holidays, $this->getHolidaysForYear($date->year, "public_holiday"));
		$holidays = array_merge($holidays, $this->getHolidaysForYear($date->year+1, "public_holiday"));
		for($i=0; $i<sizeof($holidays); $i++) {
			if(!in_array("REGIONAL_HOLIDAY", $holidays[$i]->flags) && isset($holidays[$i]->observedOn) &&
				$holidays[$i]->observedOn != NULL && $holidays[$i]->observedOn->compare($date) == 0) {
				return FALSE;
			}
		}
		return TRUE;
	}
	
	public static function whereIsPublicHoliday($date) {
		
		$retVal = array();
		$supportedCountries = HolidayCalendar::getSupportedCountries();
		foreach ($supportedCountries as $key => $value) {
			$country = $key;
			$region = "";
			if(count($value->regions) > 0) {
				$region = $value->regions[0];
			}
			$holidayCalendar = new HolidayCalendar($country, $region);
			$hol = $holidayCalendar->getHolidaysForDateRange($date, $date, "public_holiday");
			if($holidayCalendar->isHoliday($hol)) {
				array_push($retVal, array("countryCode" => $value->countryCode,
						"countryFullName" => $value->fullName,
						"holidayName" => $hol[0]->getLocalizedStringArray($hol[0]->name)));
			}
		}
		return $retVal;
	}
	
	private function isHoliday($holidays) {
		for($i=0; $i<sizeof($holidays); $i++) {
			if(!in_array("REGIONAL_HOLIDAY", $holidays[$i]->flags)) {
				return TRUE;
			}
		}
		return FALSE;
	}
	
	private function pickHolidaysBetweeenDates($fromDate, $toDate, $holidays) {
		$retVal = array();

		for($i=0; $i<count($holidays); $i++) {
			$date = $holidays[$i]->date;
			
			if($date->year < $fromDate->year || $date->year > $toDate->year) {
				continue;
			}
			
			if($date->year == $fromDate->year) {
				if($fromDate->month > $date->month)
					continue;
				if($fromDate->month == $date->month &&
				   $fromDate->day > $date->day)
					continue;
			}
			
			if($date->year == $toDate->year) {
				if($date->month > $toDate->month)
					continue;
				if($date->month == $toDate->month &&
				   $date->day > $toDate->day)
					continue;
			}
			array_push($retVal, $holidays[$i]);
		}

		usort($retVal, array('HolidayCalendar', 'sortByDate'));
		return $retVal;
	}
	
	private static function sortByDate($holiday1, $holiday2) {
		return $holiday1->date->compare($holiday2->date);
	}
	
	public static function getSupportedCountries() {
		$retVal = array();
		
		for($i=0; $i<sizeof(HolidayProcessor::$HOLIDAY_TYPES); $i++) {
			$holidayType = HolidayProcessor::$HOLIDAY_TYPES[$i];
			$directories = glob(HolidayProcessor::$HOLIDAY_DEFS_DIR . strtolower($holidayType) . '/*' , GLOB_ONLYDIR);
			for($k=0; $k<sizeof($directories); $k++) {
				$countryCode = basename($directories[$k]);
				if(!isset($retVal[$countryCode])) {
					$retVal[$countryCode] = new SupportedCountry($countryCode);
				}
				$retVal[$countryCode]->holidayTypes[] = $holidayType;
			}
		}
		
		$retVal["aus"]->regions = array("act", "qld", "nsw", "nt", "sa", "tas", "vic", "wa");
		$retVal["can"]->regions = array("ab", "bc", "mb", "nb", "nl", "nt", "ns", "nu", "on", "pe", "qc", "sk", "yt");
		$retVal["nzl"]->regions = array("auk", "bop", "can", "gis", "hkb", "mbh", "mwt", "nsn", "ntl", "ota", "stl", "tas", "tki", "wko", "wgn", "wtc", "cit");
		$retVal["usa"]->regions = array("al", "ak", "az", "ar","ca", "co", "ct", "de","dc", "fl", "ga", "hi","id", "il", "in", "ia","ks", "ky", "la", "me", "md","ma", "mi", "mn", "ms", "mo", "mt","ne", "nv", "nh", "nj", "nm", "ny","nc", "nd", "oh", "ok", "or","pa", "ri", "sc", "sd", "tn","tx", "ut", "vt", "va", "wa","wv", "wi", "wy");
		$retVal["deu"]->regions = array("bw", "by", "be", "bb", "hb", "hh", "he", "ni", "mv", "nw", "rp", "sl", "sn", "st", "sh", "th");
		$retVal["gbr"]->regions = array("eng", "nir", "sct", "wls");
		$retVal["svk"]->regions = array("bc", "bl", "ki", "ni", "pv", "ta", "tc", "zi");
		$retVal["che"]->regions = array("ag", "ai", "ar", "bl", "bs", "be", "fr", "ge", "gl", "gr", "ju", "lu", "ne", "nw", "ow", "sg", "sh", "sz", "so", "tg", "ti", "ur", "vs", "vd", "zg", "zh");
		
		return $retVal;
	}
}

?>