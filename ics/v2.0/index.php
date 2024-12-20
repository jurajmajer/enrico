<?php 
	
	require_once('../../holiday_library/HolidayCalendar.php');
	require_once('../../holiday_library/EnricoDate.php');
	
	$uids = array();
	
	function getStartSequence($calendarName)
	{
		$retVal = 	"BEGIN:VCALENDAR\r\nVERSION:2.0\r\nPRODID:-//kayaposoft.com/enrico//EN\r\nCALSCALE:GREGORIAN\r\nMETHOD:PUBLISH\r\nX-WR-CALNAME:" . $calendarName . "\r\nX-WR-CALDESC:" . $calendarName . "\r\n";
		return $retVal;
	}
	
	function generateUID($uidBase, $date)
	{
		global $uids;
		$orig = "enrico-";
		$orig .= $uidBase . "-";
		if(isset($_REQUEST["en"]) && strlen($_REQUEST["en"]) > 0) $orig .= "en-";
		$orig .= $date;
		$retVal = $orig;
		while(in_array($retVal, $uids)) {
			$retVal = $orig . "-" . rand(0, 1000000);
		}
		array_push($uids, $retVal);
		return $retVal . "@kayaposoft.com";
	}
	
	function getDateStr($date)
	{
		$retVal = $date->year;
		$temp = $date->month;
		if(strlen($temp) == 1) $temp = "0" . $temp;
		$retVal .= $temp;
		$temp = $date->day;
		if(strlen($temp) == 1) $temp = "0" . $temp;
		$retVal .= $temp;
		return $retVal;
	}
	
	function escape($txt)
	{
		return str_replace(",", "\,", $txt);
	}
	
	function getEventDateFrom($publicHoliday) {
		if(isset($publicHoliday->observedOn)) {
			return $publicHoliday->observedOn;
		}
		return $publicHoliday->date;
	}
	
	function getEventDateTo($publicHoliday) {
		$dateUtils = new DateUtils();
		if(isset($publicHoliday->dateTo)) {
			return $dateUtils->addDays($publicHoliday->dateTo, 1);
		}
		return $dateUtils->addDays(getEventDateFrom($publicHoliday), 1);
	}
	
	function getEventName($publicHoliday) {
		$lang = getLang($publicHoliday->name);
		for($i=0; $i<count($publicHoliday->name); $i++) {
			if(strcmp($lang, $publicHoliday->name[$i]->lang) == 0) {
				$retVal = $publicHoliday->name[$i]->text;
				if($lang == "en" && isset($publicHoliday->observedOn)) {
					$retVal .= " (Observance)";
				}
				return $retVal;
			}
		}
		throw new Exception("Unknown language '".$lang."'");
	}
	
	function getEventDescription($publicHoliday) {
		if(isset($publicHoliday->observedOn)) {
			return "Holiday in lieu of " . $publicHoliday->date->toString();
		}
		$lang = getLang($publicHoliday->note);
		for($i=0; $i<count($publicHoliday->note); $i++) {
			if(strcmp($lang, $publicHoliday->note[$i]->lang) == 0) {
				return $publicHoliday->note[$i]->text;
			}
		}
		return NULL;
	}
	
	function getLang($arrayOfLocStrings=array()) {
		if(isset($_REQUEST["lang"])) {
			return strtolower($_REQUEST["lang"]);
		}
		for($i=0; $i<count($arrayOfLocStrings); $i++) {
			if(strcmp($arrayOfLocStrings[$i]->lang, "en") != 0) {
				return $arrayOfLocStrings[$i]->lang;
			}
		}
		return "en";
	}
	
	function getEvent($publicHoliday, $uidBase)
	{
		$retVal = "BEGIN:VEVENT\r\n";
		$start = getDateStr(getEventDateFrom($publicHoliday));
		$end = getDateStr(getEventDateTo($publicHoliday));
		$timestamp = date("Ymd\THis\Z");
		$retVal .= "UID:" . generateUID($uidBase, $start) . "\r\n";
		$retVal .= "DTSTAMP:" . $timestamp . "\r\n" .
					"CREATED:" . $timestamp . "\r\n" .
					"LAST-MODIFIED:" . $timestamp . "\r\n";
		$retVal .= "DTSTART;VALUE=DATE:" . $start . "\r\n";
		$retVal .= "DTEND;VALUE=DATE:" . $end . "\r\n";
		$summary = getEventName($publicHoliday);
		$retVal .= "SUMMARY:" . escape($summary) . "\r\n";
		$note = getEventDescription($publicHoliday);
		if($note != NULL) {
			$retVal .= "DESCRIPTION:" . escape($note) . "\r\n";
		}
		$retVal .= "TRANSP:TRANSPARENT\r\nCATEGORIES:HOLIDAY\r\nCLASS:PUBLIC\r\nEND:VEVENT\r\n";
		return $retVal;
	}
	
	function getEndSequence()
	{
		return "END:VCALENDAR";
	}
	
	function createICS($result, $calendarName, $uidBase)
	{
		$retVal = getStartSequence($calendarName);
		for($i=0; $i<count($result); $i++)
			$retVal .= getEvent($result[$i], $uidBase);
		$retVal .= getEndSequence();
		return $retVal;
	}
	
	function createDate($string)
	{
		$parts = explode("-", $string);
		if(count($parts) != 3)
			throw new Exception("Invalid date '" . $string . "'! Date string must be in format dd-mm-YYYY, e.g. 15-01-2035");
		return new EnricoDate($parts[0], $parts[1], $parts[2]);
	}
	
	function validateMandatoryQueryParam($queryParam)
	{
		if(!isset($_REQUEST[$queryParam]) || strlen($_REQUEST[$queryParam]) == 0)
			throw new Exception("Query parameter '" . $queryParam . "' is not set!");
	}
	
	function getCalendarName($countryCode, $region, $holidayType) {
		$retVal = $countryCode;
		if(strlen($region) > 0) {
			$retVal .= " (" . $region . ")";
		}
		$retVal = strtoupper($retVal);
		$retVal .= " " . getHolidayTypeString($holidayType);
		return $retVal;
	}
	
	function getHolidayTypeString($holidayType) {
		if(strcmp($holidayType, "public_holiday") == 0) {
			return "Public Holidays";
		}
		if(strcmp($holidayType, "observance") == 0) {
			return "Observances";
		}
		if(strcmp($holidayType, "school_holiday") == 0) {
			return "School Holidays";
		}
		if(strcmp($holidayType, "other_day") == 0) {
			return "Important Dates";
		}
		if(strcmp($holidayType, "extra_working_day") == 0) {
			return "Extra Working Days";
		}
		if(strcmp($holidayType, "postal_holiday") == 0) {
			return "Postal Holidays";
		}
		return "All Dates";
	}
	
	function getUIDBase($countryCode, $region) {
		$retVal = $countryCode;
		if(strlen($region) > 0) {
			$retVal .= "_" . $region;
		}
		return $retVal;
	}
	
	// script starts here
	try
	{
		validateMandatoryQueryParam('country');
		validateMandatoryQueryParam('fromDate');
		validateMandatoryQueryParam('toDate');
		$region = "";
		if(isset($_REQUEST["region"])) {
			$region = $_REQUEST["region"];
		}
		$holidayType = "all";
		if(isset($_REQUEST["holidayType"])) {
			$holidayType = $_REQUEST["holidayType"];
		}
		$holidayCalendar = new HolidayCalendar($_REQUEST["country"], $region);
		$result = $holidayCalendar->getHolidaysForDateRange(createDate($_REQUEST["fromDate"]), createDate($_REQUEST["toDate"]), $holidayType);
		
		$calendarName = getCalendarName($_REQUEST["country"], $region, $holidayType);
		header( 'Content-Type: text/calendar; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="'.$calendarName.'.ics"' );
		echo createICS($result, $calendarName, getUIDBase($_REQUEST["country"], $region));
	}
	catch(Exception $e) 
	{
		echo $e->getMessage();
    }
?>