<?php
require_once('../nusoap.php');
require_once('../../holiday_library/HolidayCalendar.php');
require_once('../../holiday_library/EnricoDate.php');

$server = new soap_server();
$server->soap_defencoding = 'UTF-8'; 
$server->configureWSDL('enrico', 'http://www.kayaposoft.com/enrico/ws/v2.0/');
$server->wsdl->soap_defencoding = 'UTF-8'; 

// Register the data structures used by the service
$server->wsdl->addComplexType(
    'DateType',
    'complexType',
    'struct',
    'sequence',
    '',
    array(
        'day' => array('name' => 'day', 'type' => 'xsd:int'),
		'month' => array('name' => 'month', 'type' => 'xsd:int'),
		'year' => array('name' => 'year', 'type' => 'xsd:int'),
		'dayOfWeek' => array('name' => 'dayOfWeek', 'type' => 'xsd:int', 'minOccurs' => '0', 'maxOccurs' => '1'),
    )
);
$server->wsdl->addComplexType(
    'LocalizedStringType',
    'complexType',
    'struct',
    'sequence',
    '',
    array(
        'lang' => array('name' => 'lang', 'type' => 'xsd:string'),
		'text' => array('name' => 'text', 'type' => 'xsd:string'),
    )
);
$server->wsdl->addComplexType(
    'HolidayType',
    'complexType',
    'struct',
    'sequence',
    '',
    array(
        'date' => array('name' => 'date', 'type' => 'tns:DateType'),
        'dateTo' => array('name' => 'dateTo', 'type' => 'tns:DateType', 'minOccurs' => '0', 'maxOccurs' => '1'),
		'observedOn' => array('name' => 'observedOn', 'type' => 'tns:DateType', 'minOccurs' => '0', 'maxOccurs' => '1'),
        'name' => array('name' => 'name', 'type' => 'tns:LocalizedStringType', 'minOccurs' => '1', 'maxOccurs' => 'unbounded'),
        'flags' => array('name' => 'flags', 'type' => 'xsd:string', 'minOccurs' => '0', 'maxOccurs' => 'unbounded'),
        'note' => array('name' => 'note', 'type' => 'tns:LocalizedStringType', 'minOccurs' => '0', 'maxOccurs' => 'unbounded'),
		'holidayType' => array('name' => 'holidayType', 'type' => 'xsd:string'),
    )
);
$server->wsdl->addComplexType(
    'HolidayCollectionType',
    'complexType',
    'struct',
    'sequence',
    '',
    array(
    'error' => array('name' => 'error', 'type' => 'xsd:string', 'minOccurs' => '0', 'maxOccurs' => '1'),
    'holiday' => array('name' => 'holiday', 'type' => 'tns:HolidayType', 'minOccurs' => '0', 'maxOccurs' => 'unbounded'),
     )
);
$server->wsdl->addComplexType(
    'IsHolidayType',
    'complexType',
    'struct',
    'sequence',
    '',
    array(
    'error' => array('name' => 'error', 'type' => 'xsd:string', 'minOccurs' => '0', 'maxOccurs' => '1'),
    'isHoliday' => array('name' => 'isHoliday', 'type' => 'xsd:boolean', 'minOccurs' => '0', 'maxOccurs' => '1'),
     )
);
$server->wsdl->addComplexType(
    'IsWorkDayType',
    'complexType',
    'struct',
    'sequence',
    '',
    array(
    'error' => array('name' => 'error', 'type' => 'xsd:string', 'minOccurs' => '0', 'maxOccurs' => '1'),
    'isWorkDay' => array('name' => 'isWorkDay', 'type' => 'xsd:boolean', 'minOccurs' => '0', 'maxOccurs' => '1'),
     )
);
$server->wsdl->addComplexType(
    'SupportedCountryType',
    'complexType',
    'struct',
    'sequence',
    '',
    array(
	'fullName' => array('name' => 'fullName', 'type' => 'xsd:string'),
	'countryCode' => array('name' => 'countryCode', 'type' => 'xsd:string'),
    'region' => array('name' => 'region', 'type' => 'xsd:string', 'minOccurs' => '0', 'maxOccurs' => 'unbounded'),
	'fromDate' => array('name' => 'fromDate', 'type' => 'tns:DateType'),
	'toDate' => array('name' => 'toDate', 'type' => 'tns:DateType'),
    'holidayType' => array('name' => 'holidayType', 'type' => 'xsd:string', 'minOccurs' => '0', 'maxOccurs' => 'unbounded'),
     )
);
$server->wsdl->addComplexType(
    'SupportedCountriesType',
    'complexType',
    'struct',
    'sequence',
    '',
    array(
    'error' => array('name' => 'error', 'type' => 'xsd:string', 'minOccurs' => '0', 'maxOccurs' => '1'),
    'supportedCountry' => array('name' => 'supportedCountry', 'type' => 'tns:SupportedCountryType', 'minOccurs' => '0', 'maxOccurs' => 'unbounded'),
     )
);
$server->wsdl->addComplexType(
    'WhereIsPublicHolidayType',
    'complexType',
    'struct',
    'sequence',
    '',
    array(
	'countryCode' => array('name' => 'countryCode', 'type' => 'xsd:string'),
	'countryFullName' => array('name' => 'countryFullName', 'type' => 'xsd:string'),
    'holidayName' => array('name' => 'holidayName', 'type' => 'tns:LocalizedStringType', 'minOccurs' => '1', 'maxOccurs' => 'unbounded'),
     )
);
$server->wsdl->addComplexType(
    'WhereIsPublicHolidayCountryListType',
    'complexType',
    'struct',
    'sequence',
    '',
    array(
    'error' => array('name' => 'error', 'type' => 'xsd:string', 'minOccurs' => '0', 'maxOccurs' => '1'),
    'country' => array('name' => 'country', 'type' => 'tns:WhereIsPublicHolidayType', 'minOccurs' => '0', 'maxOccurs' => 'unbounded'),
     )
);

// Register methods to expose
$server->register('getHolidaysForMonth',                				// method name
    array('month' => 'xsd:integer', 'year' => 'xsd:integer', 'country' => 'xsd:string', 
        'region' => 'xsd:string', 'holidayType' => 'xsd:string'),           // input parameters
    array('holidays' => 'tns:HolidayCollectionType'),      					// output parameters
    'http://www.kayaposoft.com/enrico/ws/v2.0/',                      					// namespace
    'http://www.kayaposoft.com/enrico/ws/v2.0/#getPublicHolidaysForMonth',               			// soapaction
    'rpc',                                						// style
    'literal',                            						// use
    'Gets holidays for month'            						// documentation
);

$server->register('getHolidaysForYear',                				// method name
    array('year' => 'xsd:integer', 'country' => 'xsd:string', 
        'region' => 'xsd:string', 'holidayType' => 'xsd:string'),           // input parameters
    array('holidays' => 'tns:HolidayCollectionType'),      					// output parameters
    'http://www.kayaposoft.com/enrico/ws/v2.0/',                      					// namespace
    'http://www.kayaposoft.com/enrico/ws/v2.0/#getPublicHolidaysForYear',               			// soapaction
    'rpc',                                						// style
    'literal',                            						// use
    'Gets holidays for year'            						// documentation
);

$server->register('getHolidaysForDateRange',                				// method name
    array('fromDate' => 'tns:DateType', 'toDate' => 'tns:DateType', 'country' => 'xsd:string', 
        'region' => 'xsd:string', 'holidayType' => 'xsd:string'),                                                      // input parameters
    array('holidays' => 'tns:HolidayCollectionType'),      					// output parameters
    'http://www.kayaposoft.com/enrico/ws/v2.0/',                      					// namespace
    'http://www.kayaposoft.com/enrico/ws/v2.0/#getPublicHolidaysForDateRange',               			// soapaction
    'rpc',                                						// style
    'literal',                            						// use
    'Gets holidays for date range'            						// documentation
);

$server->register('isPublicHoliday',                				// method name
    array('date' => 'tns:DateType', 'country' => 'xsd:string', 'region' => 'xsd:string'),     // input parameters
    array('isPublicHoliday' => 'tns:IsHolidayType'),      					// output parameters
    'http://www.kayaposoft.com/enrico/ws/v2.0/',                      					// namespace
    'http://www.kayaposoft.com/enrico/ws/v2.0/#isPublicHoliday',               			// soapaction
    'rpc',                                						// style
    'literal',                            						// use
    'Checks if the specified date is public holiday'        // documentation
);

// $server->register('isSchoolHoliday',                				// method name
    // array('date' => 'tns:DateType', 'country' => 'xsd:string', 'region' => 'xsd:string'),     // input parameters
    // array('return' => 'tns:IsHolidayType'),      					// output parameters
    // 'http://www.kayaposoft.com/enrico/ws/v2.0/',                      					// namespace
    // 'http://www.kayaposoft.com/enrico/ws/v2.0/#isSchoolHoliday',               			// soapaction
    // 'rpc',                                						// style
    // 'literal',                            						// use
    // 'Checks if the specified date is school holiday'        // documentation
// );

$server->register('isWorkDay',                				// method name
    array('date' => 'tns:DateType', 'country' => 'xsd:string', 'region' => 'xsd:string'),     // input parameters
    array('isWorkDay' => 'tns:IsWorkDayType'),      					// output parameters
    'http://www.kayaposoft.com/enrico/ws/v2.0/',                      					// namespace
    'http://www.kayaposoft.com/enrico/ws/v2.0/#isWorkDay',               			// soapaction
    'rpc',                                						// style
    'literal',                            						// use
    'Checks if the specified date is a work holiday'        // documentation
);

$server->register('getSupportedCountries',                				// method name
    array(),                                                      // input parameters
    array('supportedCountries' => 'tns:SupportedCountriesType'),      					// output parameters
    'http://www.kayaposoft.com/enrico/ws/v2.0/',                      					// namespace
    'http://www.kayaposoft.com/enrico/ws/v2.0/#getSupportedCountries',               			// soapaction
    'rpc',                                						// style
    'literal',                            						// use
    'Gets the list of supported countries'            						// documentation
);

$server->register('whereIsPublicHoliday',                				// method name
    array('date' => 'tns:DateType'),                                                      // input parameters
    array('countryList' => 'tns:WhereIsPublicHolidayCountryListType'),      					// output parameters
    'http://www.kayaposoft.com/enrico/ws/v2.0/',                      					// namespace
    'http://www.kayaposoft.com/enrico/ws/v2.0/#whereIsPublicHoliday',               			// soapaction
    'rpc',                                						// style
    'literal',                            						// use
    'Gets the list of countries where given date is public holiday'            						// documentation
);

// Define exposed methods as a PHP function
function getHolidaysForMonth($month, $year, $country, $region, $holidayType) {
	$retVal = array();
	try
	{
		$holidayCalendar = new HolidayCalendar($country, $region);
		$result = $holidayCalendar->getHolidaysForMonth($month, $year, $holidayType);
		for($i=0; $i<count($result); $i++) {
				$retVal[] = $result[$i]->getArray();
		}
		return array('holiday' => $retVal);
	}
	catch(Exception $e) 
	{
		return array('error' => $e->getMessage());
    }
}

function getHolidaysForYear($year, $country, $region, $holidayType) {
	$retVal = array();
	try
	{
		$holidayCalendar = new HolidayCalendar($country, $region);
		$result = $holidayCalendar->getHolidaysForYear($year, $holidayType);
		for($i=0; $i<count($result); $i++) {
				$retVal[] = $result[$i]->getArray();
		}
		return array('holiday' => $retVal);
	}
	catch(Exception $e) 
	{
		return array('error' => $e->getMessage());
    }
}

function getHolidaysForDateRange($fromDate, $toDate, $country, $region, $holidayType) {
	$retVal = array();
	try 
	{
		$holidayCalendar = new HolidayCalendar($country, $region);
		$from = new EnricoDate($fromDate["day"], $fromDate["month"], $fromDate["year"]);
		$to = new EnricoDate($toDate["day"], $toDate["month"], $toDate["year"]);
		$result = $holidayCalendar->getHolidaysForDateRange($from, $to, $holidayType);
		for($i=0; $i<count($result); $i++) {
				$retVal[] = $result[$i]->getArray();
		}
		return array('holiday' => $retVal);
	}
	catch(Exception $e) 
	{
		return array('error' => $e->getMessage());
    }
}

function isPublicHoliday($date, $country, $region) {
	try
	{
		$holidayCalendar = new HolidayCalendar($country, $region);
		$d = new EnricoDate($date["day"], $date["month"], $date["year"]);
		$retVal = $holidayCalendar->isPublicHoliday($d);
		return array('isHoliday' => $retVal);
	}
	catch(Exception $e) 
	{
		return array('error' => $e->getMessage());
    }
}

function isSchoolHoliday($date, $country, $region) {
	try
	{
		$holidayCalendar = new HolidayCalendar($country, $region);
		$d = new EnricoDate($date["day"], $date["month"], $date["year"]);
		$retVal = $holidayCalendar->isSchoolHoliday($d);
		return array('isHoliday' => $retVal);
	}
	catch(Exception $e) 
	{
		return array('error' => $e->getMessage());
    }
}

function isWorkDay($date, $country, $region) {
	try
	{
		$holidayCalendar = new HolidayCalendar($country, $region);
		$d = new EnricoDate($date["day"], $date["month"], $date["year"]);
		$retVal = $holidayCalendar->isWorkDay($d);
		return array('isWorkDay' => $retVal);
	}
	catch(Exception $e) 
	{
		return array('error' => $e->getMessage());
    }
}

function getSupportedCountries() {
	$retVal = array();
	try
	{
		$supportedCountries = array_values(HolidayCalendar::getSupportedCountries());
		usort($supportedCountries, array('SupportedCountry','compare'));
		for($i=0; $i<count($supportedCountries); $i++) {
			$item = $supportedCountries[$i]->getArray();
			$item['region'] = $item['regions'];
			$item['holidayType'] = $item['holidayTypes'];
			$retVal[] = $item;
		}
		return array('supportedCountry' => $retVal);
	}
	catch(Exception $e) 
	{
		return array('error' => $e->getMessage());
    }
}

function whereIsPublicHoliday($date) {
	try
	{
		$d = new EnricoDate($date["day"], $date["month"], $date["year"]);
		$retVal = HolidayCalendar::whereIsPublicHoliday($d);
		return array('country' => $retVal);
	}
	catch(Exception $e) 
	{
		return array('error' => $e->getMessage());
    }
}

// Use the request to (try to) invoke the service
$server->service(utf8_encode(file_get_contents("php://input")));
?>
