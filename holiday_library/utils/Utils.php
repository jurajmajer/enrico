<?php

class Utils {
	
	public static $countryCodeAliases = array('wal'=>'wls', 'rok'=>'kor', 'ger'=>'deu', 'gbr'=>'eng');
	public static $nzlRegions = array('auckland'=>'auk', 'bayofplenty'=>'bop', 'canterbury'=>'can', 'southcanterbury'=>'can', 'gisborne'=>'gis', 'hawke\'sbay'=>'hkb', 'hawkesbay'=>'hkb', 'marlborough'=>'mbh', 'manawatu-wanganui'=>'mwt', 'nelson'=>'nsn', 'northland'=>'ntl', 'otago'=>'ota', 'southland'=>'stl', 'tasman'=>'tas', 'taranaki'=>'tki', 'waikato'=>'wko', 'wellington'=>'wgn', 'westcoast'=>'wtc', 'westland'=>'wtc', 'chathamislandsterritory'=>'cit', 'chathamislands'=>'cit');
	public static $ausRegions = array('newsouthwales'=>'nsw', 'queensland'=>'qld', 'southaustralia'=>'sa', 'tasmania'=>'tas', 'victoria'=>'vic', 'westernaustralia'=>'wa', 'australiancapitalterritory'=>'act', 'northernterritory'=>'nt');
	public static $canRegions = array('alberta'=>'ab', 'britishcolumbia'=>'bc', 'manitoba'=>'mb', 'newbrunswick'=>'nb', 'newfoundlandandlabrador'=>'nl', 'novascotia'=>'ns', 'ontario'=>'on', 'princeedwardisland'=>'pe',
	'quebec'=>'qc', 'saskatchewan'=>'sk', 'northwestterritories'=>'nt', 'nunavut'=>'nu', 'yukon'=>'yt');
	public static $usaRegions = array('alabama'=>'al', 'alaska'=>'ak', 'arizona'=>'az', 'arkansas'=>'ar', 'california'=>'ca', 'colorado'=>'co', 'connecticut'=>'ct', 'delaware'=>'de', 'districtofcolumbia'=>'dc', 'florida'=>'fl', 'georgia'=>'ga', 
	'alabama'=>'al', 'hawaii'=>'hi', 'idaho'=>'id', 'Illinois'=>'il', 'indiana'=>'in', 'iowa'=>'ia', 'kansas'=>'ks', 'kentucky'=>'ky', 'louisiana'=>'la', 'maine'=>'me', 'maryland'=>'md', 'massachusetts'=>'ma', 'michigan'=>'mi', 'minnesota'=>'mn', 'mississippi'=>'ms', 'missouri'=>'mo', 'montana'=>'mt', 'nebraska'=>'ne', 'nevada'=>'nv', 'newhampshire'=>'nh', 'newjersey'=>'nj', 'newmexico'=>'nm', 'newyork'=>'ny', 'northcarolina'=>'nc', 'northdakota'=>'nd', 'ohio'=>'oh', 'oklahoma'=>'ok', 'oregon'=>'or', 'pennsylvania'=>'pa', 'rhodeisland'=>'ri', 'southcarolina'=>'sc', 'southdakota'=>'sd', 'tennessee'=>'tn', 'texas'=>'tx', 'utah'=>'ut', 'vermont'=>'vt', 'virginia'=>'va', 'washington'=>'wa', 'westvirginia'=>'wv', 'wisconsin'=>'wi', 'wyoming'=>'wy'
	);
	
	public static function canonicalizeCountryCode($countryCode) {
		$countryCode = strtolower($countryCode);
		if(array_key_exists($countryCode, Utils::$countryCodeAliases)) {
			$countryCode = Utils::$countryCodeAliases[$countryCode];
		}
		return $countryCode;
	}
	
	public static function canonicalizeRegion($countryCode, $region) {
		$region = strtolower($region);
		$region = preg_replace('/\s+/', '', $region);
		$regionAliases = Utils::getRegionAliases($countryCode);
		if(array_key_exists($region, $regionAliases)) {
			$region = $regionAliases[$region];
		}
		return $region;
	}
	
	private static function getRegionAliases($countryCode) {
		if(strcmp("nzl", $countryCode) == 0) {
			return Utils::$nzlRegions;
		}
		if(strcmp("aus", $countryCode) == 0) {
			return Utils::$ausRegions;
		}
		if(strcmp("can", $countryCode) == 0) {
			return Utils::$canRegions;
		}
		if(strcmp("usa", $countryCode) == 0) {
			return Utils::$usaRegions;
		}
		
		return array();
	}
	
}

?>