<?php 

	require_once(__DIR__.'/../../holiday_library/EnricoDate.php');

	function getJson($result)
	{
		$retVal = array();
		for($i=0; $i<count($result); $i++)
			$retVal[] = $result[$i]->getArray();
		return json_encode($retVal);
	}

	function createDate($string)
	{
		$parts = explode("-", $string);
		if(count($parts) != 3)
			throw new InvalidArgumentException("Invalid date '" . $string . "'! Date string must be in format YYYY-mm-dd, e.g. 2035-01-15");
		return new EnricoDate($parts[2], $parts[1], $parts[0]);
	}

	function validateMandatoryQueryParam($queryParam)
	{
		if(!isset($_REQUEST[$queryParam]) || strlen($_REQUEST[$queryParam]) == 0)
			throw new InvalidArgumentException("Query parameter '" . $queryParam . "' is not set!");
	}
	
	function createHolidayCalendar()
	{
		validateMandatoryQueryParam('country');
		$region = "";
		if(isset($_REQUEST["region"])) {
			$region = $_REQUEST["region"];
		}
		return new HolidayCalendar($_REQUEST["country"], $region);
	}
	
	function getHolidayType()
	{
		$holidayType = "all";
		if(isset($_REQUEST["holidayType"])) {
			$holidayType = $_REQUEST["holidayType"];
		}
		return $holidayType;
	}
	
	function handleException($statusCode, $msg)
	{
		http_response_code($statusCode);
		echo json_encode(array('error' => $msg));
	}

?>