<?php 
	
	require_once('../../holiday_library/HolidayCalendar.php');
	require_once('../../holiday_library/EnricoDate.php');
	
	function getJson($result)
	{
		$retVal = array();
		for($i=0; $i<count($result); $i++)
			$retVal[] = $result[$i]->getArray();
		return resolveJsonp(json_encode($retVal));
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
	
	function resolveJsonp($json)
	{
		if(isset($_REQUEST["jsonp"]) && strlen($_REQUEST["jsonp"]) > 0)
		{
			header('Content-Type: application/javascript');
			return $_REQUEST["jsonp"] . "(" . $json . ");";
		}
		else
		{
			header('Content-Type: application/json');
		}
		return $json;
	}
	
	function getHolidaysForMonth($holidayCalendar, $holidayType)
	{
		validateMandatoryQueryParam('month');
		validateMandatoryQueryParam('year');
		$result = $holidayCalendar->getHolidaysForMonth($_REQUEST["month"], $_REQUEST["year"], $holidayType);
		return getJson($result);
	}
	
	function getHolidaysForYear($holidayCalendar, $holidayType) 
	{
		validateMandatoryQueryParam('year');
		$result = $holidayCalendar->getHolidaysForYear($_REQUEST["year"], $holidayType);
		return getJson($result);
	}
	
	function getHolidaysForDateRange($holidayCalendar, $holidayType) 
	{
		validateMandatoryQueryParam('fromDate');
		validateMandatoryQueryParam('toDate');
		$fromDate = createDate($_REQUEST["fromDate"]);
		$toDate = createDate($_REQUEST["toDate"]);
		$result = $holidayCalendar->getHolidaysForDateRange($fromDate, $toDate, $holidayType);
		return getJson($result);
	}
	
	function isPublicHoliday($holidayCalendar)
	{
		validateMandatoryQueryParam('date');
		$date = createDate($_REQUEST["date"]);
		$result = $holidayCalendar->isPublicHoliday($date);
		return resolveJsonp(json_encode(array('isPublicHoliday' => $result)));
	}
	
	function isSchoolHoliday($holidayCalendar)
	{
		validateMandatoryQueryParam('date');
		$date = createDate($_REQUEST["date"]);
		$result = $holidayCalendar->isSchoolHoliday($date);
		return resolveJsonp(json_encode(array('isSchoolHoliday' => $result)));
	}
	
	function isWorkDay($holidayCalendar)
	{
		validateMandatoryQueryParam('date');
		$date = createDate($_REQUEST["date"]);
		$result = $holidayCalendar->isWorkDay($date);
		return resolveJsonp(json_encode(array('isWorkDay' => $result)));
	}
	
	function getSupportedCountries()
	{
		$supportedCountries = HolidayCalendar::getSupportedCountries();
		return getJson(array_values($supportedCountries));
	}
	
	// script starts here
	try
	{
		header('Access-Control-Allow-Origin: *');
		validateMandatoryQueryParam('action');
		if(strcmp($_REQUEST['action'], "getSupportedCountries") == 0)
		{
			echo getSupportedCountries();
			exit(0);
		}
		
		validateMandatoryQueryParam('country');
		$region = "";
		if(isset($_REQUEST["region"])) {
			$region = $_REQUEST["region"];
		}
		$holidayCalendar = new HolidayCalendar($_REQUEST["country"], $region);
		
		$holidayType = "all";
		if(isset($_REQUEST["holidayType"])) {
			$holidayType = $_REQUEST["holidayType"];
		}
		
		if(strcmp($_REQUEST['action'], "getHolidaysForMonth") == 0)
			echo getHolidaysForMonth($holidayCalendar, $holidayType);
		else if(strcmp($_REQUEST['action'], "getHolidaysForYear") == 0)
			echo getHolidaysForYear($holidayCalendar, $holidayType);
		else if(strcmp($_REQUEST['action'], "getHolidaysForDateRange") == 0)
			echo getHolidaysForDateRange($holidayCalendar, $holidayType);
		else if(strcmp($_REQUEST['action'], "isPublicHoliday") == 0)
			echo isPublicHoliday($holidayCalendar);
		else if(strcmp($_REQUEST['action'], "isSchoolHoliday") == 0)
			echo isSchoolHoliday($holidayCalendar);
		else if(strcmp($_REQUEST['action'], "isWorkDay") == 0)
			echo isWorkDay($holidayCalendar);
		else
			throw new Exception("Unknown action!");
	}
	catch(Exception $e) 
	{
		echo resolveJsonp(json_encode(array('error' => $e->getMessage())));
    }
?>