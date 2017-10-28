<?php

class OrthodoxCalUtils {

	public function getOrthodoxEasterSunday($year) {
		// http://www.felgall.com/jstip82.htm
		/*$a = $year % 19;
		$d = $year % 4;
		$e = $year % 7;
		$h = (19 * $a + 15) % 30;
		$i = 2 * $d + 4 * $e + 6 * $h;
		$j = $i % 7;
		$k = 3 + $h + $j;
		$month = ($k < 31) ? 4 : 5;
		$day = ($k > 30) ? $k-30 : $k;
		return new Date($day, $month, $year);*/
		// thanks to http://smart.net/~mmontes/ortheast.html#ALG
		$G = $year % 19;

		//For the Julian calendar:
		$I = (19*$G + 15) % 30;
		$J = ($year + floor($year/4) + $I) % 7;

		//Thereafter, for both calendars:
		$L = $I - $J;
		$month = 3 + floor(($L + 40)/44);
		$day = $L + 28 - 31*floor($month/4);

		return $this->convertJulianDateToGregorianDate(new EnricoDate($day, $month, $year));
	}

	private function convertJulianDateToGregorianDate($julianDate) {
		return $this->getGregorianDateFromJD($this->getJDFromJulianDate($julianDate->day, $julianDate->month, $julianDate->year));
	}

	// http://stason.org/TULARC/society/calendars/2-15-1-Is-there-a-formula-for-calculating-the-Julian-day-nu.html
	private function getJDFromJulianDate($day, $month, $year) {
		$a =floor((14-$month)/12);
		$y = $year+4800-$a;
		$m = $month + 12*$a - 3;

		//For a date in the Gregorian calendar:
		//JD = day + (153*m+2)/5 + y*365 + y/4 - y/100 + y/400 - 32045

		//For a date in the Julian calendar:
		//JD = day + (153*m+2)/5 + y*365 + y/4 - 32083
		return $day + floor((153*$m+2)/5) + $y*365 + floor($y/4) - 32083;
	}

	private function getGregorianDateFromJD($JD) {
		//For the Gregorian calendar:
		$a = $JD + 32044;
		$b = floor((4*$a+3)/146097);
		$c = $a - floor(($b*146097)/4);

		//For the Julian calendar:
		//$b = 0;
		//$c = JD + 32082;

		//Then, for both calendars:
		$d = floor((4*$c+3)/1461);
		$e = $c -floor((1461*$d)/4);
		$m = floor((5*$e+2)/153);

		$day = $e - floor((153*$m+2)/5) + 1;
		$month = $m + 3 - 12*floor($m/10);
		$year  = $b*100 + $d - 4800 + floor($m/10);

		return new EnricoDate($day, $month, $year);
	}
}

?>