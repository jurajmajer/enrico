<?php 

	require_once('../utils.php');
	require_once('../../../holiday_library/HolidayCalendar.php');
	
	try
	{
		header('Access-Control-Allow-Origin: *');
		header('Content-Type: application/json');
		
		$supportedCountries = HolidayCalendar::getSupportedCountries();
		$retVal = array_values($supportedCountries);
		usort($retVal, array('SupportedCountry','compare'));
		
		echo getJson($retVal);
	}
	catch(InvalidArgumentException $e) 
	{
		handleException(400, $e->getMessage());
    }
	catch(Exception $e) 
	{
		handleException(500, $e->getMessage());
    }
?>