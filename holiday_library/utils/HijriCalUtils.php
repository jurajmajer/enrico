<?php

// http://faizshukri.github.io/blog/2013/12/05/convert-gregorian-date-to-hijri/
class HijriCalUtils
{
    function gregorianToHijri($d, $m, $y)
    {
        return $this->JDToHijri(
            cal_to_jd(CAL_GREGORIAN, $m, $d, $y));
    }

    function hijriToGregorian($d, $m, $y)
    {
		$retVal = cal_from_jd($this->HijriToJD($d, $m, $y), CAL_GREGORIAN);
		return array($retVal["day"], $retVal["month"], $retVal["year"]);	
    }

    # Julian Day Count To Hijri
    function jdToHijri($jd)
    {
        $jd = $jd - 1948440 + 10632;
        $n  = (int)(($jd - 1) / 10631);
        $jd = $jd - 10631 * $n + 354;
        $j  = ((int)((10985 - $jd) / 5316)) *
            ((int)(50 * $jd / 17719)) +
            ((int)($jd / 5670)) *
            ((int)(43 * $jd / 15238));
        $jd = $jd - ((int)((30 - $j) / 15)) *
            ((int)((17719 * $j) / 50)) -
            ((int)($j / 16)) *
            ((int)((15238 * $j) / 43)) + 29;
        $m  = (int)(24 * $jd / 709);
        $d  = $jd - (int)(709 * $m / 24);
        $y  = 30*$n + $j - 30;

        return array($d, $m, $y);
    }

    # Hijri To Julian Day Count
    function hijriToJD($d, $m, $y)
    {
        return (int)((11 * $y + 3) / 30) +
            354 * $y + 30 * $m -
            (int)(($m - 1) / 2) + $d + 1948440 - 385;
    }
	
	function getFirstDayOfHijriMonth($hijriMonth, $gregorianYear)
	{
		$retVal = array();
		$hijriYear = $this->gregorianToHijri(30,6,$gregorianYear)[2]-1;
		for($i=0; $i<3; $i++) {
			$r = $this->hijriToGregorian(1, $hijriMonth, $hijriYear+$i);
			if($r[2] == $gregorianYear) {
				array_push($retVal, $r);
			}
		}
		return $retVal;
	}
};

?>