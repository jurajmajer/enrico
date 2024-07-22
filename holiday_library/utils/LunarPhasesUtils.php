<?php 

include_once("DateUtils.php");
include_once("MoonPhase.php");

class LunarPhasesUtils {
	
	private $dateUtils;
	
	public function __construct() {
		$this->dateUtils = new DateUtils();
	}

	public function getNextFullMoon($date, $countryCode) {
		
		return $this->getNextMoonPhase($date, $countryCode, 6);
	}
	
	public function getNextNewMoon($date, $countryCode) {
		return $this->getNextMoonPhase($date, $countryCode, 4);
	}
	
	private function getNextMoonPhase($date, $countryCode, $moonPhase) {
		if(!array_key_exists($countryCode, DateUtils::$diffToUTC)) {
			throw new Exception('CountryCode not supported: ' . $countryCode);
		}

		$system_timezone = date_default_timezone_get();

		date_default_timezone_set("UTC");
		$startTimestamp = gmmktime($date->hour, $date->minute, $date->second, $date->month, $date->day, $date->year);
		$moonPhaseCalculator = new MoonPhase($startTimestamp);
		$nextMoonPhase = "";
		if($moonPhase == 4) {
			$nextMoonPhase = $moonPhaseCalculator->next_new_moon();
		} else if($moonPhase == 6) {
			$nextMoonPhase = $moonPhaseCalculator->full_moon();
			if($startTimestamp > $nextMoonPhase) {
				$nextMoonPhase = $moonPhaseCalculator->next_full_moon();
			}
		}
		$retVal = new EnricoDate(date("j", intval($nextMoonPhase)), date("n", intval($nextMoonPhase)), date("Y", intval($nextMoonPhase)));
		$retVal->hour = date("G", intval($nextMoonPhase));
		$retVal->minute = date("i", intval($nextMoonPhase));
		$retVal->second = date("s", intval($nextMoonPhase));
		$retVal = $this->dateUtils->addSeconds($retVal, DateUtils::$diffToUTC[$countryCode] * 60 * 60);

        date_default_timezone_set($system_timezone);

		return $retVal;
	}
	
	// http://www.math.nus.edu.sg/aslaksen/calendar/deepavali.html
	// http://www.deepavali.net/calendar.php
	public function calculateDeepavali($year, $countryCode) {
		$newMoon1 = $this->getNextNewMoon(new EnricoDate(14,10,$year), $countryCode);
		$newMoon2 = $this->getNextNewMoon($newMoon1, $countryCode);
		$temp = array($newMoon1, $newMoon2);
		for($i=0; $i<count($temp); $i++) {
			if($temp[$i]->hour <= 10) {
				$temp[$i] = $this->dateUtils->addDays($temp[$i], -2);
			} else {
				$temp[$i] = $this->dateUtils->addDays($temp[$i], -1);
			}
		}
		if($temp[1]->day <= 15 && $temp[1]->month == 11) {
			return $temp[1];
		}
		return $temp[0];
	}
}

?>