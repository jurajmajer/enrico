<?php
//Jewish calendar calculations from Judaism 101, http://www.jewfaq.org
//Designed to illustrate the principles of calendar calculation discussed at http://www.jewfaq.org/calendr2.htm

include_once("/data/k/a/kayaposoft.com/web/enrico/libs/HolidayLibrary/Date.php");
include_once("DateUtils.php");

class HebrewMonth {
    
	public $startDate;
	public $name;
	public $isIntercalary;

    public function __construct($date, $name, $isIntercalary) {
        
		$this->startDate = $date;
		$this->name = $name;
        $this->isIntercalary = $isIntercalary;
    }
}

class Molad {

	public $part;
	public $hour;
	public $day;
	public $year;
	public $gregorianEquivalent;
	
	public function __construct() {
		$this->part = 0;
		$this->hour = 0;
		$this->day = 0;
		$this->year = 0;
		$this->gregorianEquivalent = "";
	}
}

class HebrewCalUtils {

	public function calculateHebrewCalendar($year) {
		
		$rh0 = $this->calculateRoshHashanah($year - 1);
		$rh1 = $this->calculateRoshHashanah($year);
		$rh2 = $this->calculateRoshHashanah($year + 1);
		$kislevOffset0 = $this->kislevOffset($rh0, $rh1);
		$kislevOffset1 = $this->kislevOffset($rh1, $rh2);
		$hebYear = $this->getHebrewYear($year);
		$isLeapYear = $this->isLeapYear($hebYear);
		
		$dateUtils = new DateUtils();
		$retVal = array();
		array_push($retVal, new HebrewMonth($dateUtils->addDays(Date::createNew($rh0), $kislevOffset0 + 1), "Kislev", false));
		array_push($retVal, $this->calculateMonth(10, $hebYear - 1, $rh1, $retVal));
		array_push($retVal, $this->calculateMonth(11, $hebYear - 1, $rh1, $retVal));
		array_push($retVal, $this->calculateMonth(12, $hebYear - 1, $rh1, $retVal));
		if($this->isLeapYear($hebYear - 1))
			array_push($retVal, $this->calculateMonth(13, $hebYear - 1, $rh1, $retVal));
		array_push($retVal, $this->calculateMonth(1, $hebYear - 1, $rh1, $retVal));
		array_push($retVal, $this->calculateMonth(2, $hebYear - 1, $rh1, $retVal));
		array_push($retVal, $this->calculateMonth(3, $hebYear - 1, $rh1, $retVal));
		array_push($retVal, $this->calculateMonth(4, $hebYear - 1, $rh1, $retVal));
		array_push($retVal, $this->calculateMonth(5, $hebYear - 1, $rh1, $retVal));
		array_push($retVal, $this->calculateMonth(6, $hebYear - 1, $rh1, $retVal));
		array_push($retVal, new HebrewMonth(Date::createNew($rh1), "Tishri", false));
		array_push($retVal, new HebrewMonth($dateUtils->addDays(Date::createNew($rh1), 30), "Cheshvan", false));
		array_push($retVal, new HebrewMonth($dateUtils->addDays(Date::createNew($rh1), $kislevOffset1 + 1), "Kislev", false));
		array_push($retVal, $this->calculateMonth(10, $hebYear, $rh2, $retVal));
		array_push($retVal, $this->calculateMonth(11, $hebYear, $rh2, $retVal));
		
		return $retVal;
	}
	
	public function calculateRoshHashanah($year) {
		if($year < 2010) 
			throw new Exception('HebrewCalendar: Years before 2013 are not supported');
			
		//Step 1: Start with a Known Molad 
		//  Define the variable baseMolad, which is 
		//  Molad Tishri in the Jewish year 5732
		//  used as a basis for other calculations
		$baseMolad = new Molad();
		$baseMolad->day = 2;
		$baseMolad->hour = 7;
		$baseMolad->part = 743;
		$baseMolad->year = 5732;
		$baseMolad->gregorianEquivalent = new Date(20, 9, 1971);
		$hebYear = $this->getHebrewYear($year);
		
		//Step 2: Determine the Number of Months to Tishri of Your Year
		$months = $this->monthsBetween($baseMolad->year, $hebYear);
		
		//Step 3: Multiply the Number of Months by the Length of the Molad
		//The elapsed time of one month is 29d 12h 793p
		$elapsed = new Molad();
		$elapsed->part = (793 * $months);     
		$elapsed->hour = (12 * $months);     
		$elapsed->day = (29 * $months);    
		$elapsed = $this->roundMolad($elapsed);
		
		//Step 4: Add the Result to the Starting Molad
		$finalMolad = new Molad();
		$finalMolad->part = $elapsed->part + $baseMolad->part;
		$finalMolad->hour = $elapsed->hour + $baseMolad->hour;
		$finalMolad->day = $elapsed->day;
		$finalMolad = $this->roundMolad($finalMolad);
		$elapsedDays = $finalMolad->day;
		$finalMolad->day += $baseMolad->day;
		$finalMolad->day = $finalMolad->day % 7;
		if($finalMolad->day == 0) $finalMolad->day = 7;
		
		//Step 5: Dechiyah
		$extraDays = $this->dechiyah1($finalMolad->hour);
		$extraDays += $this->dechiyah2(($finalMolad->day + $extraDays) % 7);
		$extraDays += $this->dechiyah3($finalMolad, $hebYear);
		$extraDays += $this->dechiyah4($finalMolad, $hebYear);
		
		$retVal = Date::createNew($baseMolad->gregorianEquivalent);
		$dateUtils = new DateUtils();
		$retVal = $dateUtils->addDays($retVal, $extraDays + $elapsedDays);
		return $retVal;
	}
	
	// A cycle is 19 years. Years 3, 6, 8, 11, 14, 17 and 19 are leap years
	public function isLeapYear($hebYear) {
		switch($hebYear % 19) {                       
			case 0 : case 3 : case 6 : case 8 :      
			case 11 : case 14 : case 17 :             
				return true;                  
			default :                                
				return false;
		}
	}

	public function monthsBetween($startYear, $endYear) {
		$cycles = floor(($endYear - $startYear) / 19);   
		$mb = $cycles * 235;                    // Each complete cycle of 19 years has 235 months      
		for ($hebYear = $startYear + (19 * $cycles); $hebYear < $endYear; $hebYear++) {
			if ($this->isLeapYear($hebYear)) {                
				$mb += 13;                          
			} else {                                  
				$mb += 12;                           
			}
		}
		
		return $mb;
	}
	
	public function getHebrewYear($year) {
		return $year + 3761;
	}
	
	public function roundMolad($molad) {
		$molad->hour += floor($molad->part / 1080);  //round parts into hours...
		$molad->part = $molad->part % 1080;   
		$molad->day += floor($molad->hour / 24);    //round hours into days...
		$molad->hour = $molad->hour % 24;
		return $molad;
	}
	
	//Dechiyah 1: Molad Zakein
	public function dechiyah1($hour) {
		if ($hour >= 18)     //if the molad is after noon (18h)...
			return 1;           
	
		return 0;
	}
	
	//Dechiyah 2: Lo A"DU Rosh
	public function dechiyah2($weekday) {
		switch ($weekday) {
			case 1 :        //if Rosh Hashanah is on a Sunday...
			case 4 :        //...or a Wednesday...
			case 6 :        //...or a Friday...
				return 1;
		}
		
		return 0;
	}
	
	//Dechiyah 3: Gatarad
	// if the current year is not a leap year and the Molad occurs on Tuesday and the Molad occurs in the 9th hour at or after 204 parts then add 2 days -- one for Gatarad and one for Lo A"DU Rosh
	public function dechiyah3($finalMolad, $hebYear) {
		if (!$this->isLeapYear($hebYear)) {
			if ($finalMolad->day == 3) {                        
				if ($finalMolad->hour == 9 && $finalMolad->part >= 204) {
					return 2;	//add 2 days -- one for Gatarad and one for Lo A"DU Rosh
				}
				if ($finalMolad->hour > 9 && $finalMolad->hour < 18) {	// after the 18th hour Molad Zakein is applied
					return 2;                                 
				}
			}
		}
		
		return 0;
	}
	
	//Dechiyah 4: Betutkafot
	// if the preceeding year was a leap year and the Molad occurs on Monday and the Molad occurs in the 15th hour at or after 589 parts then add one day
	public function dechiyah4($finalMolad, $hebYear) {
		if ($this->isLeapYear($hebYear - 1)) {
			if ($finalMolad->day == 2) {
				if ($finalMolad->hour == 15 && $finalMolad->part >= 589) {
					return 1;                                
				}
				if ($finalMolad->hour > 15 && $finalMolad->hour < 18) {     // after the 18th hour Molad Zakein is applied
					return 1;                                 
				}
			}
		}
	
		return 0;
	}
	
	// returns the number of days from Rosh Hashanah to 1 Kislev in year of $rh1
	// this varies because the length of Cheshvan (the preceding month) varies
	// you can't calculate backwards from the end of the year, because Kislev also varies
	public function kislevOffset($rh1, $rh2) {
		$dateUtils = new DateUtils();
		$temp = $dateUtils->diffDates($rh1, $rh2);
		if($temp == 355 || $temp == 385) return 59;
		
		return 58;
	}	
	
	public function calculateMonth($hebMonth, $hebYear, $rh, $arr) {

		$dateUtils = new DateUtils();
		switch($hebMonth) {
			
			case 10:	// Tevet
				$offset = -266;
				if($this->isLeapYear($hebYear)) $offset -= 30;
				$date = $dateUtils->addDays(Date::createNew($rh), $offset + 1);
				return new HebrewMonth($date, "Tevet", false);
				
			case 11:	// Shevat
				$offset = -237;
				if($this->isLeapYear($hebYear)) $offset -= 30;
				$date = $dateUtils->addDays(Date::createNew($rh), $offset + 1);
				return new HebrewMonth($date, "Shevat", false);
				
			case 12:	// Adar or Adar I
				$offset = -207;
				if($this->isLeapYear($hebYear)) $offset -= 30;
				$date = $dateUtils->addDays(Date::createNew($rh), $offset + 1);
				return new HebrewMonth($date, "Adar", false);
				
			case 13:	// Adar II
				$offset = -207;
				$date = $dateUtils->addDays(Date::createNew($rh), $offset + 1);
				return new HebrewMonth($date, "Adar II", true);
				
			case 1:	// Nissan
				$offset = -178;
				$date = $dateUtils->addDays(Date::createNew($rh), $offset + 1);
				return new HebrewMonth($date, "Nissan", false);
				
			case 2:	// Iyar
				$offset = -148;
				$date = $dateUtils->addDays(Date::createNew($rh), $offset + 1);
				return new HebrewMonth($date, "Iyar", false);
				
			case 3:	// Sivan
				$offset = -119;
				$date = $dateUtils->addDays(Date::createNew($rh), $offset + 1);
				return new HebrewMonth($date, "Sivan", false);
				
			case 4:	// Tammuz
				$offset = -89;
				$date = $dateUtils->addDays(Date::createNew($rh), $offset + 1);
				return new HebrewMonth($date, "Tammuz", false);
				
			case 5:	// Av
				$offset = -60;
				$date = $dateUtils->addDays(Date::createNew($rh), $offset + 1);
				return new HebrewMonth($date, "Av", false);
				
			case 6:	// Elul
				$offset = -30;
				$date = $dateUtils->addDays(Date::createNew($rh), $offset + 1);
				return new HebrewMonth($date, "Elul", false);
		}
	}
}

//$test = new HebrewCalUtils();
/*for($i=0; $i<50; $i++) {
	print_r($test->calculateHebrewCalendar(2013+$i));
	echo "<br/>";
}*/
//print_r($test->calculateHebrewCalendar(2014));
//$test->calculateHebrewCalendar(2014);

?>