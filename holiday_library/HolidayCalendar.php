<?php 

include_once("EnricoDate.php");
include_once("utils/Utils.php");
include_once("HolidayProcessor.php");
include_once("SupportedCountry.php");

class HolidayCalendar {
	
	protected $countryCode;
	protected $region;
	
	public function __construct($countryCode, $region) {
		$this->countryCode = Utils::canonicalizeCountryCode($countryCode);
		$this->region = Utils::canonicalizeRegion($countryCode, $region);
		
		$supportedCountries = HolidayCalendar::getSupportedCountries();
		if(!isset($supportedCountries[$this->countryCode]))
			throw new Exception('Country \'' . $this->countryCode . '\' is not supported');
		$c = $supportedCountries[$this->countryCode];
		if(isset($this->region) && strlen($this->region) > 0 && count($c->regions) > 0 && $c->isRegionSupported($this->region) == FALSE) {
			throw new Exception('Region \'' . $this->region . '\' in country \'' . $this->countryCode . '\' is not supported');
		}
	}
	
	public function getHolidaysForMonth($month, $year) {
		$fromDate = new EnricoDate(1, $month, $year);
		$lastDay = 31;
		while(checkdate($month, $lastDay, $year) == FALSE)
			$lastDay--;
		$toDate = new EnricoDate($lastDay, $month, $year);
		return $this->getHolidaysForDateRange($fromDate, $toDate);
	}

	public function getHolidaysForYear($year) {
		$fromDate = new EnricoDate(1, 1, $year);
		$toDate = new EnricoDate(31, 12, $year);
		return $this->getHolidaysForDateRange($fromDate, $toDate);
	}
	
	public function getHolidaysForDateRange($fromDate, $toDate) {
		
		if(checkdate($fromDate->month, $fromDate->day, $fromDate->year) == FALSE)
			throw new Exception($fromDate->toString() . " is not a valid date");
		if(checkdate($toDate->month, $toDate->day, $toDate->year) == FALSE)
			throw new Exception($toDate->toString() . " is not a valid date");
		if($fromDate->compare($toDate) > 0)
			throw new Exception($fromDate->toString() . " is later than " . $toDate->toString());
		$supportedCountry = HolidayCalendar::getSupportedCountries()[$this->countryCode];
		if($fromDate->compare($supportedCountry->fromDate) < 0)
			throw new Exception("Dates before " . $supportedCountry->fromDate->toString() . " are not supported");
		if($toDate->compare($supportedCountry->toDate) > 0)
			throw new Exception("Dates after " . $supportedCountry->toDate->toString() . " are not supported");
		
		$retVal = array();
		$holidayProcessor = new HolidayProcessor($this->countryCode, $this->region);
		$year = $fromDate->year;
		while($year <= $toDate->year) {
			$holidays = $holidayProcessor->getHolidays($year);
			$retVal = array_merge($retVal, $this->pickHolidaysBetweeenDates($fromDate, $toDate, $holidays));
			$year += 1;
		}
		return $retVal;
	}
	
	public function isHoliday($date) {
		$retVal = $this->getHolidaysForDateRange($date, $date);
		if(count($retVal) > 0)
			return TRUE;

		return FALSE;
	}
	
	private function pickHolidaysBetweeenDates($fromDate, $toDate, $holidays) {
		$retVal = array();

		for($i=0; $i<count($holidays); $i++) {
			$date = $holidays[$i]->date;
			
			if($date->year < $fromDate->year || $date->year > $toDate->year) {
				continue;
			}
			
			if($date->year == $fromDate->year) {
				if($fromDate->month > $date->month)
					continue;
				if($fromDate->month == $date->month &&
				   $fromDate->day > $date->day)
					continue;
			}
			
			if($date->year == $toDate->year) {
				if($date->month > $toDate->month)
					continue;
				if($date->month == $toDate->month &&
				   $date->day > $toDate->day)
					continue;
			}
			array_push($retVal, $holidays[$i]);
		}

		usort($retVal, array('HolidayCalendar', 'sortByDate'));
		return $retVal;
	}
	
	private static function sortByDate($holiday1, $holiday2) {
		return $holiday1->date->compare($holiday2->date);
	}
	
	// TODO: solve this problem
	public static function getSupportedCountries() {
		$retVal = array();
		$retVal['ago'] = new SupportedCountry('Angola', 'ago', new EnricoDate(1, 1, 2014), new EnricoDate(31, 12, 32767));
		$retVal['aus'] = new SupportedCountry('Australia', 'aus', new EnricoDate(1, 1, 2011), new EnricoDate(31, 12, 32767));
		$retVal['aus']->addRegion("act"); $retVal['aus']->addRegion("qld"); $retVal['aus']->addRegion("nsw"); $retVal['aus']->addRegion("nt"); $retVal['aus']->addRegion("sa"); 
		$retVal['aus']->addRegion("tas"); $retVal['aus']->addRegion("vic");  $retVal['aus']->addRegion("wa");
		$retVal['aut'] = new SupportedCountry('Austria', 'aut', new EnricoDate(1, 1, 1946), new EnricoDate(31, 12, 32767));
		$retVal['bel'] = new SupportedCountry('Belgium', 'bel', new EnricoDate(1, 1, 2011), new EnricoDate(31, 12, 32767));
		$retVal['bra'] = new SupportedCountry('Brazil', 'bra', new EnricoDate(1, 1, 2016), new EnricoDate(31, 12, 32767));
		$retVal['can'] = new SupportedCountry('Canada', 'can', new EnricoDate(1, 1, 2011), new EnricoDate(31, 12, 32767));
		$retVal['can']->addRegion("ab"); $retVal['can']->addRegion("bc"); $retVal['can']->addRegion("mb");
		$retVal['can']->addRegion("nb"); $retVal['can']->addRegion("nl"); $retVal['can']->addRegion("nt"); $retVal['can']->addRegion("ns"); $retVal['can']->addRegion("nu");
		$retVal['can']->addRegion("on"); $retVal['can']->addRegion("pe"); $retVal['can']->addRegion("qc"); $retVal['can']->addRegion("sk"); $retVal['can']->addRegion("yt");
		$retVal['chn'] = new SupportedCountry('China', 'chn', new EnricoDate(1, 1, 2013), new EnricoDate(31, 12, 2100));
		$retVal['col'] = new SupportedCountry('Colombia', 'col', new EnricoDate(1, 1, 2016), new EnricoDate(31, 12, 32767));
		$retVal['hrv'] = new SupportedCountry('Croatia', 'hrv', new EnricoDate(1, 1, 2011), new EnricoDate(31, 12, 32767));
		$retVal['cze'] = new SupportedCountry('Czech Republic', 'cze', new EnricoDate(1, 1, 1952), new EnricoDate(31, 12, 32767));
		$retVal['dnk'] = new SupportedCountry('Denmark', 'dnk', new EnricoDate(1, 1, 2011), new EnricoDate(31, 12, 32767));
		$retVal['eng'] = new SupportedCountry('England', 'eng', new EnricoDate(1, 1, 2011), new EnricoDate(31, 12, 32767));
		$retVal['est'] = new SupportedCountry('Estonia', 'est', new EnricoDate(1, 1, 2011), new EnricoDate(31, 12, 32767));
		$retVal['fin'] = new SupportedCountry('Finland', 'fin', new EnricoDate(1, 1, 2011), new EnricoDate(31, 12, 32767));
		$retVal['fra'] = new SupportedCountry('France', 'fra', new EnricoDate(1, 1, 2011), new EnricoDate(31, 12, 32767));
		$retVal['fra']->addRegion("Alsace"); $retVal['fra']->addRegion("Moselle");
		$retVal['deu'] = new SupportedCountry('Germany', 'deu', new EnricoDate(1, 1, 2011), new EnricoDate(31, 12, 32767));
		$retVal['deu']->addRegion("Baden-Württemberg"); $retVal['deu']->addRegion("Bavaria"); $retVal['deu']->addRegion("Berlin"); $retVal['deu']->addRegion("Brandenburg");
		$retVal['deu']->addRegion("Bremen"); $retVal['deu']->addRegion("Hamburg"); $retVal['deu']->addRegion("Hesse"); $retVal['deu']->addRegion("Mecklenburg-Vorpommern");
		$retVal['deu']->addRegion("Lower Saxony"); $retVal['deu']->addRegion("North Rhine-Westphalia"); $retVal['deu']->addRegion("Rhineland-Palatinate"); $retVal['deu']->addRegion("Saarland"); 
		$retVal['deu']->addRegion("Saxony"); $retVal['deu']->addRegion("Saxony-Anhalt"); $retVal['deu']->addRegion("Schleswig-Holstein"); $retVal['deu']->addRegion("Thuringia");
		$retVal['grc'] = new SupportedCountry('Greece', 'grc', new EnricoDate(1, 1, 2017), new EnricoDate(31, 12, 32767));
		$retVal['hkg'] = new SupportedCountry('Hong Kong', 'hkg', new EnricoDate(1, 1, 2013), new EnricoDate(31, 12, 32767));
		$retVal['hun'] = new SupportedCountry('Hungary', 'hun', new EnricoDate(1, 1, 2011), new EnricoDate(31, 12, 32767));
		$retVal['isl'] = new SupportedCountry('Iceland', 'isl', new EnricoDate(1, 1, 2011), new EnricoDate(31, 12, 32767));
		$retVal['irl'] = new SupportedCountry('Ireland', 'irl', new EnricoDate(1, 1, 2011), new EnricoDate(31, 12, 32767));
		$retVal['imn'] = new SupportedCountry('Isle of Man', 'imn', new EnricoDate(1, 1, 2015), new EnricoDate(31, 12, 32767));
		$retVal['isr'] = new SupportedCountry('Israel', 'isr', new EnricoDate(1, 1, 2014), new EnricoDate(31, 12, 32767));
		$retVal['ita'] = new SupportedCountry('Italy', 'ita', new EnricoDate(1, 1, 2011), new EnricoDate(31, 12, 32767));
		$retVal['jpn'] = new SupportedCountry('Japan', 'jpn', new EnricoDate(1, 1, 2011), new EnricoDate(31, 12, 32767));
		$retVal['lva'] = new SupportedCountry('Latvia', 'lva', new EnricoDate(1, 1, 2011), new EnricoDate(31, 12, 32767));
		$retVal['ltu'] = new SupportedCountry('Lithuania', 'ltu', new EnricoDate(1, 1, 2011), new EnricoDate(31, 12, 32767));
		$retVal['lux'] = new SupportedCountry('Luxembourg', 'lux', new EnricoDate(1, 1, 2011), new EnricoDate(31, 12, 32767));
		$retVal['mex'] = new SupportedCountry('Mexico', 'mex', new EnricoDate(1, 1, 2014), new EnricoDate(31, 12, 32767));
		$retVal['nld'] = new SupportedCountry('Netherlands', 'nld', new EnricoDate(1, 1, 2011), new EnricoDate(31, 12, 32767));
		$retVal['nzl'] = new SupportedCountry('New Zealand', 'nzl', new EnricoDate(1, 1, 2011), new EnricoDate(31, 12, 32767));
		$retVal['nzl']->addRegion("auk"); $retVal['nzl']->addRegion("bop"); $retVal['nzl']->addRegion("can"); 
		$retVal['nzl']->addRegion("gis"); $retVal['nzl']->addRegion("hkb");  $retVal['nzl']->addRegion("mbh");  $retVal['nzl']->addRegion("mwt");  $retVal['nzl']->addRegion("nsn");  $retVal['nzl']->addRegion("ntl"); 
		$retVal['nzl']->addRegion("ota");  $retVal['nzl']->addRegion("stl");  $retVal['nzl']->addRegion("tas");  $retVal['nzl']->addRegion("tki");  $retVal['nzl']->addRegion("wko");  $retVal['nzl']->addRegion("wgn");
		$retVal['nzl']->addRegion("wtc"); $retVal['nzl']->addRegion("cit");  
		$retVal['nir'] = new SupportedCountry('Northern Ireland', 'nir', new EnricoDate(1, 1, 2011), new EnricoDate(31, 12, 32767));
		$retVal['nor'] = new SupportedCountry('Norway', 'nor', new EnricoDate(1, 1, 2011), new EnricoDate(31, 12, 32767));
		$retVal['pol'] = new SupportedCountry('Poland', 'pol', new EnricoDate(1, 1, 1952), new EnricoDate(31, 12, 32767));
		$retVal['prt'] = new SupportedCountry('Portugal', 'prt', new EnricoDate(1, 1, 2011), new EnricoDate(31, 12, 32767));
		$retVal['rou'] = new SupportedCountry('Romania', 'rou', new EnricoDate(1, 1, 2015), new EnricoDate(31, 12, 32767));
		$retVal['rus'] = new SupportedCountry('Russia', 'rus', new EnricoDate(1, 1, 2011), new EnricoDate(31, 12, 2100));
		$retVal['srb'] = new SupportedCountry('Serbia', 'srb', new EnricoDate(1, 1, 2011), new EnricoDate(31, 12, 2100));
		$retVal['svk'] = new SupportedCountry('Slovakia', 'svk', new EnricoDate(1, 1, 1952), new EnricoDate(31, 12, 32767));
		$retVal['svn'] = new SupportedCountry('Slovenia', 'svn', new EnricoDate(1, 1, 2011), new EnricoDate(31, 12, 32767));
		$retVal['zaf'] = new SupportedCountry('South Africa', 'zaf', new EnricoDate(1, 1, 2013), new EnricoDate(31, 12, 32767));
		$retVal['kor'] = new SupportedCountry('South Korea', 'kor', new EnricoDate(1, 1, 2013), new EnricoDate(31, 12, 32767));
		$retVal['sct'] = new SupportedCountry('Scotland', 'sct', new EnricoDate(1, 1, 2016), new EnricoDate(31, 12, 32767));
		$retVal['swe'] = new SupportedCountry('Sweden', 'swe', new EnricoDate(1, 1, 2011), new EnricoDate(31, 12, 32767));
		$retVal['ukr'] = new SupportedCountry('Ukraine', 'ukr', new EnricoDate(1, 1, 2015), new EnricoDate(31, 12, 32767));
		$retVal['usa'] = new SupportedCountry('United States of America', 'usa', new EnricoDate(1, 1, 2011), new EnricoDate(31, 12, 32767));
		$retVal['usa']->addRegion("al"); $retVal['usa']->addRegion("ak"); $retVal['usa']->addRegion("az"); $retVal['usa']->addRegion("ar");
		$retVal['usa']->addRegion("ca"); $retVal['usa']->addRegion("co"); $retVal['usa']->addRegion("ct"); $retVal['usa']->addRegion("de");
		$retVal['usa']->addRegion("dc"); $retVal['usa']->addRegion("fl"); $retVal['usa']->addRegion("ga"); $retVal['usa']->addRegion("hi");
		$retVal['usa']->addRegion("id"); $retVal['usa']->addRegion("il"); $retVal['usa']->addRegion("in"); $retVal['usa']->addRegion("ia");
		$retVal['usa']->addRegion("ks"); $retVal['usa']->addRegion("ky"); $retVal['usa']->addRegion("la"); $retVal['usa']->addRegion("me"); $retVal['usa']->addRegion("md");
		$retVal['usa']->addRegion("ma"); $retVal['usa']->addRegion("mi"); $retVal['usa']->addRegion("mn"); $retVal['usa']->addRegion("ms"); $retVal['usa']->addRegion("mo"); $retVal['usa']->addRegion("mt");
		$retVal['usa']->addRegion("ne"); $retVal['usa']->addRegion("nv"); $retVal['usa']->addRegion("nh"); $retVal['usa']->addRegion("nj"); $retVal['usa']->addRegion("nm"); $retVal['usa']->addRegion("ny");
		$retVal['usa']->addRegion("nc"); $retVal['usa']->addRegion("nd"); $retVal['usa']->addRegion("oh"); $retVal['usa']->addRegion("ok"); $retVal['usa']->addRegion("or");
		$retVal['usa']->addRegion("pa"); $retVal['usa']->addRegion("ri"); $retVal['usa']->addRegion("sc"); $retVal['usa']->addRegion("sd"); $retVal['usa']->addRegion("tn");
		$retVal['usa']->addRegion("tx"); $retVal['usa']->addRegion("ut"); $retVal['usa']->addRegion("vt"); $retVal['usa']->addRegion("va"); $retVal['usa']->addRegion("wa");
		$retVal['usa']->addRegion("wv"); $retVal['usa']->addRegion("wi"); $retVal['usa']->addRegion("wy");
		$retVal['wls'] = new SupportedCountry('Wales', 'wls', new EnricoDate(1, 1, 2011), new EnricoDate(31, 12, 32767));

		return $retVal;
	}
}

?>