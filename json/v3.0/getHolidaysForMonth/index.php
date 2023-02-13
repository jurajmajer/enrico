<?php 

	require_once('../utils.php');
	require_once('../../../holiday_library/HolidayCalendar.php');
	
	try
	{
		header('Access-Control-Allow-Origin: *');
		header('Content-Type: application/json');
		
		$holidayCalendar = createHolidayCalendar();
		$holidayType = getHolidayType();
		validateMandatoryQueryParam('month');
		validateMandatoryQueryParam('year');
		$result = $holidayCalendar->getHolidaysForMonth($_REQUEST["month"], $_REQUEST["year"], $holidayType);
		
		echo getJson($result);
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