<?php
 
include_once("utils/DateUtils.php");
 
class EnricoDate {
    public $day;
    public $month;
    public $year;
	public $hour;
	public $minute;
	public $second;
	public $dayOfWeek;

    public function __construct($day, $month, $year) {
        $this->day = intval($day);
        $this->month = intval($month);
        $this->year = intval($year);
		$this->hour = 0;
		$this->minute = 0;
		$this->second = 0;
    }
	
	public static function createNew($other) {
		$retVal = new EnricoDate($other->day, $other->month, $other->year);
		$retVal->hour = $other->hour;
		$retVal->minute = $other->minute;
		$retVal->second = $other->second;
		return $retVal;
    }
	
	public static function fromXmlDate($xmlDate) {
		if(strlen($xmlDate) != 10) {
			throw new Exception("Date from xml is not in format YYYY-MM-DD");
		}
		return new EnricoDate(substr($xmlDate, 8, 2), substr($xmlDate, 5, 2), substr($xmlDate, 0, 4));
	}
	
	public function calculateDayOfWeek() {
		$dateUtils = new DateUtils();
		$this->dayOfWeek = $dateUtils->getDayOfWeek($this); 
	}

    public function compare($other) {
        if($this->year > $other->year)
            return 1;
        if($this->year < $other->year)
            return -1;
        if($this->month > $other->month)
            return 1;
        if($this->month < $other->month)
            return -1;
        if($this->day > $other->day)
            return 1;
        if($this->day < $other->day)
            return -1;
		if($this->hour > $other->hour)
            return 1;
        if($this->hour < $other->hour)
            return -1;
		if($this->minute > $other->minute)
            return 1;
        if($this->minute < $other->minute)
            return -1;
		if($this->second > $other->second)
            return 1;
        if($this->second < $other->second)
            return -1;
        return 0;
    }

    public function toString() {
		
        return $this->day . " " . EnricoDate::getMonthName($this->month) . " " . $this->year;
    }
	
	public static function getMonthName($month)
	{
		switch($month)
		{
			case 1: return "Jan";
			case 2: return "Feb";
			case 3: return "Mar";
			case 4: return "Apr";
			case 5: return "May";
			case 6: return "Jun";
			case 7: return "Jul";
			case 8: return "Aug";
			case 9: return "Sep";
			case 10: return "Oct";
			case 11: return "Nov";
			case 12: return "Dec";
		}
	}
}
?>
