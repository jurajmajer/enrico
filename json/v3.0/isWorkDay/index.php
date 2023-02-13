<?php 

	require_once('../utils.php');
	require_once('../../../holiday_library/HolidayCalendar.php');
	
	try
	{
		header('Access-Control-Allow-Origin: *');
		header('Content-Type: application/json');
		
		$holidayCalendar = createHolidayCalendar();
		
		validateMandatoryQueryParam('date');
		$date = createDate($_REQUEST["date"]);
		$result = $holidayCalendar->isWorkDay($date);
		
		echo json_encode(array('isWorkDay' => $result));
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