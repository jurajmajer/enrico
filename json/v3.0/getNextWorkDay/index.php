<?php 

	require_once('../utils.php');
	require_once('../../../holiday_library/HolidayCalendar.php');
	require_once('../../../holiday_library/utils/DateUtils.php');
	
	try
	{
		header('Access-Control-Allow-Origin: *');
		header('Content-Type: application/json');
		
		$holidayCalendar = createHolidayCalendar();
		
		validateMandatoryQueryParam('date');
		$date = createDate($_REQUEST["date"]);
		$deltaDays = 1;
		if(isset($_REQUEST["deltaDays"])) {
			$deltaDays = $_REQUEST["deltaDays"];
		}
		if (abs($deltaDays) > 100) {
			throw new InvalidArgumentException('deltaDays out of bounds');
		}
		
		$result = $date;
		if ($deltaDays != 0) {
			$delta = $deltaDays / abs($deltaDays);
			$deltaDays = abs($deltaDays);
			$newDate = $date;
			$dateUtils = new DateUtils();
			$top = $deltaDays * 10;
			for($i=0; $i<$top; $i++) {
				$newDate = $dateUtils->addDays($newDate, $delta);
				if($holidayCalendar->isWorkDay($newDate)) {
					$deltaDays--;
					if ($deltaDays == 0) {
						$result = $newDate;
						break;
					}
				}
			}
		}
		$result->calculateDayOfWeek();
		echo json_encode(array('day' => $result->day, 'month' => $result->month, 'year' => $result->year, 'dayOfWeek' => $result->dayOfWeek));
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