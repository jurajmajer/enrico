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
		$moonPhaseCalculator = new \Solaris\MoonPhase(mktime($date->hour, $date->minute, $date->second, $date->month, $date->day, $date->year));
		$nextMoonPhase = "";
		if($moonPhase == 4) {
			$nextMoonPhase = $moonPhaseCalculator->next_new_moon();
		} else if($moonPhase == 6) {
			$nextMoonPhase = $moonPhaseCalculator->next_full_moon();
		}
		$retVal = new EnricoDate(date("j", $nextMoonPhase), date("n", $nextMoonPhase), date("Y", $nextMoonPhase));
		$retVal->hour = date("G", $nextMoonPhase);
		$retVal->minute = date("i", $nextMoonPhase);
		$retVal->second = date("s", $nextMoonPhase);
		$retVal = $this->dateUtils->addSeconds($retVal, DateUtils::$diffToUTC[$countryCode] * 60 * 60);
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