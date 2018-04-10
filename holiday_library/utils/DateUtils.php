<?php 

class DateUtils {
	
	public static $diffToUTC = array('sgp'=>8,);

	// http://mathforum.org/library/drmath/view/55837.html
	/*
	* 1 = monday
	* 2 = tuesday
	* 3 = wednesday
	* 4 = thursday
	* 5 = friday
	* 6 = saturday
	* 7 = sunday
	*/
	public function getDayOfWeek($date) {
		$d = $date->day;
		$m = $date->month;
		$y = $date->year;
		if($m == 1 || $m == 2) {
			$m += 12;
			$y--;
		}
		$N = $d + 2*$m + floor(3*($m+1)/5) + $y + floor($y/4) - floor($y/100) + floor($y/400) + 2;
		$dayOfWeek = $N % 7;
		$retVal = 0;
		if($dayOfWeek == 0)	//saturday
			$retVal = 6;
		if($dayOfWeek == 1) //sunday
			$retVal = 7;
		if($dayOfWeek > 1)
			$retVal = $dayOfWeek - 1;
		return $retVal;
	}

	public function getNthWeekday($dayOfWeek, $month, $nth, $year) {
		$temp = $this->getDayOfWeek(new EnricoDate(1, $month, $year));
		$delta = $dayOfWeek - $temp;
		if($delta < 0)
			$delta = 7 + $delta;
		$retVal = new EnricoDate($delta + 1, $month, $year);
		if($nth != "last") {
			$retVal->day += ($nth - 1) * 7;
			if(checkdate($retVal->month, $retVal->day, $retVal->year) == TRUE)
				return $retVal;
			return NULL;
		}
		while(checkdate($retVal->month, $retVal->day, $retVal->year) == TRUE) {
			$retVal->day += 7;
		}
		$retVal->day -= 7;
		return $retVal;
	}

	public function getLastDayOfMonth($month, $year) {
		if(	$month == 1 || $month == 3 || $month == 5 || $month == 7 || $month == 8 ||
			$month == 10 || $month == 12)
			return 31;
		if($month == 2) {
			if(checkdate($month, 29, $year) == TRUE) return 29;
			return 28;
		}
		return 30;
	}
	
	public function addDays($date, $days) {
		
		$sign = 1;
		if($days < 0) {
			$days = -1 * $days;
			$sign = -1;
		}
		while($days > 0) {
			$date->day += $sign * 1;
			$days--;
			if($date->day > 0 && $date->day < 28) {
				continue;
			}
			if($date->day == 0) {
				$date->month -= 1;
				if($date->month == 0) {
					$date->month = 12;
					$date->year -= 1;
				}
				$date->day = $this->getLastDayOfMonth($date->month, $date->year);
				continue;
			}
			$lastDay = $this->getLastDayOfMonth($date->month, $date->year);
			if($date->day > $lastDay) {
				$date->day = 1;
				$date->month += 1;
				if($date->month == 13) {
					$date->month = 1;
					$date->year += 1;
				}
			}
		}
		
		return $date;
	}
	
	public function addSeconds($date, $seconds) {
		
		$seconds = $date->second + $seconds;
		$date->second = $seconds % 60;
		$minutes = floor($seconds / 60) + $date->minute;
		if($date->second < 0) {
			$date->second += 60;
			$minutes -= 1;
		}
		$date->minute = $minutes % 60;
		$hours = floor($minutes / 60) + $date->hour;
		if($date->minute < 0) {
			$date->minute += 60;
			$hours -= 1;
		}
		$date->hour = $hours % 24;
		$days = floor($hours / 24);
		if($date->hour < 0) {
			$date->hour += 24;
			$days -= 1;
		}
		return $this->addDays($date, $days);
	}
	
	public function getNearestWeekdayAfter($baseDate, $dayOfWeek) {
		$num = $this->getDayOfWeek($baseDate);
		if($num < $dayOfWeek)
			return $this->addDays($baseDate, $dayOfWeek - $num);
		return $this->addDays($baseDate, 7 - $num + $dayOfWeek);
	}
	
	public function getNearestWeekday($baseDate, $dayOfWeek) {
		$num = $this->getDayOfWeek($baseDate);
		if($num == $dayOfWeek) {
			return $baseDate;
		}
		$delta = $num - $dayOfWeek;
		$sig = 1;
		if($delta < 0) {
			$sig = -1;
			$delta = -1 * $delta;
		}
		if($delta < 4) {
			return $this->addDays($baseDate, -1 * $delta * $sig);
		}
		return $this->addDays($baseDate, (7 - $delta) * $sig);
	}
	
	public function dateDistance($date1, $date2) {
		$compare = $date1->compare($date2);
		if($compare == 0) return 0;
		$earlierDate = EnricoDate::createNew($date1);
		$laterDate = EnricoDate::createNew($date2);
		if($compare > 0) {
			$earlierDate = EnricoDate::createNew($date2);
			$laterDate = EnricoDate::createNew($date1);
		}
		$retVal = 0;
		while($earlierDate->compare($laterDate) != 0) {
			$retVal++;
			$earlierDate = $this->addDays($earlierDate, 1);
		}
		
		return $retVal;
	}
}

?>