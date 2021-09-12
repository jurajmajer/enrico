<?php

class Utils {
	
	public static $countryCodeAliases = array('af'=>'afg', 'ax'=>'ala', 'al'=>'alb', 'dz'=>'dza', 'as'=>'asm', 'ad'=>'and', 'ao'=>'ago', 'ai'=>'aia', 'aq'=>'ata', 'ag'=>'atg', 'ar'=>'arg', 'am'=>'arm', 'aw'=>'abw', 'au'=>'aus', 'at'=>'aut', 'az'=>'aze', 'bs'=>'bhs', 'bh'=>'bhr', 'bd'=>'bgd', 'bb'=>'brb', 'by'=>'blr', 'be'=>'bel', 'bz'=>'blz', 'bj'=>'ben', 'bm'=>'bmu', 'bt'=>'btn', 'bo'=>'bol', 'ba'=>'bih', 'bw'=>'bwa', 'bv'=>'bvt', 'br'=>'bra', 'vg'=>'vgb', 'io'=>'iot', 'bn'=>'brn', 'bg'=>'bgr', 'bf'=>'bfa', 'bi'=>'bdi', 'kh'=>'khm', 'cm'=>'cmr', 'ca'=>'can', 'cv'=>'cpv', 'ky'=>'cym', 'cf'=>'caf', 'td'=>'tcd', 'cl'=>'chl', 'cn'=>'chn', 'hk'=>'hkg', 'mo'=>'mac', 'cx'=>'cxr', 'cc'=>'cck', 'co'=>'col', 'km'=>'com', 'cg'=>'cog', 'cd'=>'cod', 'ck'=>'cok', 'cr'=>'cri', 'ci'=>'civ', 'hr'=>'hrv', 'cu'=>'cub', 'cy'=>'cyp', 'cz'=>'cze', 'dk'=>'dnk', 'dj'=>'dji', 'dm'=>'dma', 'do'=>'dom', 'ec'=>'ecu', 'eg'=>'egy', 'sv'=>'slv', 'gq'=>'gnq', 'er'=>'eri', 'ee'=>'est', 'et'=>'eth', 'fk'=>'flk', 'fo'=>'fro', 'fj'=>'fji', 'fi'=>'fin', 'fr'=>'fra', 'gf'=>'guf', 'pf'=>'pyf', 'tf'=>'atf', 'ga'=>'gab', 'gm'=>'gmb', 'ge'=>'geo', 'de'=>'deu', 'gh'=>'gha', 'gi'=>'gib', 'gr'=>'grc', 'gl'=>'grl', 'gd'=>'grd', 'gp'=>'glp', 'gu'=>'gum', 'gt'=>'gtm', 'gg'=>'ggy', 'gn'=>'gin', 'gw'=>'gnb', 'gy'=>'guy', 'ht'=>'hti', 'hm'=>'hmd', 'va'=>'vat', 'hn'=>'hnd', 'hu'=>'hun', 'is'=>'isl', 'in'=>'ind', 'id'=>'idn', 'ir'=>'irn', 'iq'=>'irq', 'ie'=>'irl', 'im'=>'imn', 'il'=>'isr', 'it'=>'ita', 'jm'=>'jam', 'jp'=>'jpn', 'je'=>'jey', 'jo'=>'jor', 'kz'=>'kaz', 'ke'=>'ken', 'ki'=>'kir', 'kp'=>'prk', 'kr'=>'kor', 'kw'=>'kwt', 'kg'=>'kgz', 'la'=>'lao', 'lv'=>'lva', 'lb'=>'lbn', 'ls'=>'lso', 'lr'=>'lbr', 'ly'=>'lby', 'li'=>'lie', 'lt'=>'ltu', 'lu'=>'lux', 'mk'=>'mkd', 'mg'=>'mdg', 'mw'=>'mwi', 'my'=>'mys', 'mv'=>'mdv', 'ml'=>'mli', 'mt'=>'mlt', 'mh'=>'mhl', 'mq'=>'mtq', 'mr'=>'mrt', 'mu'=>'mus', 'yt'=>'myt', 'mx'=>'mex', 'fm'=>'fsm', 'md'=>'mda', 'mc'=>'mco', 'mn'=>'mng', 'me'=>'mne', 'ms'=>'msr', 'ma'=>'mar', 'mz'=>'moz', 'mm'=>'mmr', 'na'=>'nam', 'nr'=>'nru', 'np'=>'npl', 'nl'=>'nld', 'an'=>'ant', 'nc'=>'ncl', 'nz'=>'nzl', 'ni'=>'nic', 'ne'=>'ner', 'ng'=>'nga', 'nu'=>'niu', 'nf'=>'nfk', 'mp'=>'mnp', 'no'=>'nor', 'om'=>'omn', 'pk'=>'pak', 'pw'=>'plw', 'ps'=>'pse', 'pa'=>'pan', 'pg'=>'png', 'py'=>'pry', 'pe'=>'per', 'ph'=>'phl', 'pn'=>'pcn', 'pl'=>'pol', 'pt'=>'prt', 'pr'=>'pri', 'qa'=>'qat', 're'=>'reu', 'ro'=>'rou', 'ru'=>'rus', 'rw'=>'rwa', 'bl'=>'blm', 'sh'=>'shn', 'kn'=>'kna', 'lc'=>'lca', 'mf'=>'maf', 'pm'=>'spm', 'vc'=>'vct', 'ws'=>'wsm', 'sm'=>'smr', 'st'=>'stp', 'sa'=>'sau', 'sn'=>'sen', 'rs'=>'srb', 'sc'=>'syc', 'sl'=>'sle', 'sg'=>'sgp', 'sk'=>'svk', 'si'=>'svn', 'sb'=>'slb', 'so'=>'som', 'za'=>'zaf', 'gs'=>'sgs', 'ss'=>'ssd', 'es'=>'esp', 'lk'=>'lka', 'sd'=>'sdn', 'sr'=>'sur', 'sj'=>'sjm', 'sz'=>'swz', 'se'=>'swe', 'ch'=>'che', 'sy'=>'syr', 'tw'=>'twn', 'tj'=>'tjk', 'tz'=>'tza', 'th'=>'tha', 'tl'=>'tls', 'tg'=>'tgo', 'tk'=>'tkl', 'to'=>'ton', 'tt'=>'tto', 'tn'=>'tun', 'tr'=>'tur', 'tm'=>'tkm', 'tc'=>'tca', 'tv'=>'tuv', 'ug'=>'uga', 'ua'=>'ukr', 'ae'=>'are', 'gb'=>'gbr', 'us'=>'usa', 'um'=>'umi', 'uy'=>'ury', 'uz'=>'uzb', 'vu'=>'vut', 've'=>'ven', 'vn'=>'vnm', 'vi'=>'vir', 'wf'=>'wlf', 'eh'=>'esh', 'ye'=>'yem', 'zm'=>'zmb', 'zw'=>'zwe', 'wal'=>'wls', 'rok'=>'kor', 'ger'=>'deu');
	public static $nzlRegions = array('auckland'=>'auk', 'bayofplenty'=>'bop', 'canterbury'=>'can', 'southcanterbury'=>'can', 'gisborne'=>'gis', 'hawke\\\'sbay'=>'hkb', 'hawkes\\\'bay'=>'hkb', 'hawkesbay'=>'hkb', 'marlborough'=>'mbh', 'manawatu-wanganui'=>'mwt', 'nelson'=>'nsn', 'northland'=>'ntl', 'otago'=>'ota', 'southland'=>'stl', 'tasman'=>'tas', 'taranaki'=>'tki', 'waikato'=>'wko', 'wellington'=>'wgn', 'westcoast'=>'wtc', 'westland'=>'wtc', 'chathamislandsterritory'=>'cit', 'chathamislands'=>'cit');
	public static $ausRegions = array('newsouthwales'=>'nsw', 'queensland'=>'qld', 'southaustralia'=>'sa', 'tasmania'=>'tas', 'victoria'=>'vic', 'westernaustralia'=>'wa', 'australiancapitalterritory'=>'act', 'northernterritory'=>'nt');
	public static $canRegions = array('alberta'=>'ab', 'britishcolumbia'=>'bc', 'manitoba'=>'mb', 'newbrunswick'=>'nb', 'newfoundlandandlabrador'=>'nl', 'novascotia'=>'ns', 'ontario'=>'on', 'princeedwardisland'=>'pe',
	'quebec'=>'qc', 'saskatchewan'=>'sk', 'northwestterritories'=>'nt', 'nunavut'=>'nu', 'yukon'=>'yt');
	public static $usaRegions = array('alabama'=>'al', 'alaska'=>'ak', 'arizona'=>'az', 'arkansas'=>'ar', 'california'=>'ca', 'colorado'=>'co', 'connecticut'=>'ct', 'delaware'=>'de', 'districtofcolumbia'=>'dc', 'florida'=>'fl', 'georgia'=>'ga', 
	'alabama'=>'al', 'hawaii'=>'hi', 'idaho'=>'id', 'illinois'=>'il', 'indiana'=>'in', 'iowa'=>'ia', 'kansas'=>'ks', 'kentucky'=>'ky', 'louisiana'=>'la', 'maine'=>'me', 'maryland'=>'md', 'massachusetts'=>'ma', 'michigan'=>'mi', 'minnesota'=>'mn', 'mississippi'=>'ms', 'missouri'=>'mo', 'montana'=>'mt', 'nebraska'=>'ne', 'nevada'=>'nv', 'newhampshire'=>'nh', 'newjersey'=>'nj', 'newmexico'=>'nm', 'newyork'=>'ny', 'northcarolina'=>'nc', 'northdakota'=>'nd', 'ohio'=>'oh', 'oklahoma'=>'ok', 'oregon'=>'or', 'pennsylvania'=>'pa', 'rhodeisland'=>'ri', 'southcarolina'=>'sc', 'southdakota'=>'sd', 'tennessee'=>'tn', 'texas'=>'tx', 'utah'=>'ut', 'vermont'=>'vt', 'virginia'=>'va', 'washington'=>'wa', 'westvirginia'=>'wv', 'wisconsin'=>'wi', 'wyoming'=>'wy'
	);
	public static $deuRegions = array('baden-wuerttemberg'=>'bw','baden-württemberg'=>'bw', 'bavaria'=>'by', 'berlin'=>'be', 'brandenburg'=>'bb', 'bremen'=>'hb', 'hamburg'=>'hh', 'hesse'=>'he', 'mecklenburg-vorpommern'=>'mv', 'lowersaxony'=>'ni', 'northrhine-westphalia'=>'nw', 'rhineland-palatinate'=>'rp', 'saarland'=>'sl', 'saxony'=>'sn', 'saxony-anhalt'=>'st', 'schleswig-holstein'=>'sh', 'thuringia'=>'th');
	public static $espRegions = array('andalusia'=>'an', 'aragon'=>'ar', 'asturias'=>'as', 'canaryislands'=>'cn', 'cantabria'=>'cb', 'castileandleon'=>'cl', 'castile-lamancha'=>'cm', 'catalonia'=>'ct', 'ceuta'=>'ce', 'extremadura'=>'ex', 'galicia'=>'ga', 'balearicislands'=>'ib', 'larioja'=>'ri', 'madrid'=>'md', 'melilla'=>'ml', 'murcia'=>'mc', 'navarre'=>'nc', 'basquecountry'=>'pv', 'valencia'=>'vc');

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
		if(strcmp("deu", $countryCode) == 0) {
			return Utils::$deuRegions;
		}
		if(strcmp("esp", $countryCode) == 0) {
			return Utils::$espRegions;
		}
		
		return array();
	}
	
}

?>