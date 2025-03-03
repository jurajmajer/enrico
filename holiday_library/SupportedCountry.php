<?phpinclude_once("EnricoDate.php");class SupportedCountry {		public $fullName;    public $countryCode;	public $regions;	public $holidayTypes;	public $fromDate;    public $toDate;		public static $fullNames = array('afg'=>'Afghanistan', 'ala'=>'Aland Islands', 'alb'=>'Albania', 'dza'=>'Algeria', 'asm'=>'American Samoa', 'and'=>'Andorra', 'ago'=>'Angola', 'aia'=>'Anguilla', 'ata'=>'Antarctica', 'atg'=>'Antigua and Barbuda', 'arg'=>'Argentina', 'arm'=>'Armenia', 'abw'=>'Aruba', 'aus'=>'Australia', 'aut'=>'Austria', 'aze'=>'Azerbaijan', 'bhs'=>'Bahamas', 'bhr'=>'Bahrain', 'bgd'=>'Bangladesh', 'brb'=>'Barbados', 'blr'=>'Belarus', 'bel'=>'Belgium', 'blz'=>'Belize', 'ben'=>'Benin', 'bmu'=>'Bermuda', 'btn'=>'Bhutan', 'bol'=>'Bolivia', 'bih'=>'Bosnia and Herzegovina', 'bwa'=>'Botswana', 'bvt'=>'Bouvet Island', 'bra'=>'Brazil', 'vgb'=>'British Virgin Islands', 'iot'=>'British Indian Ocean Territory', 'brn'=>'Brunei Darussalam', 'bgr'=>'Bulgaria', 'bfa'=>'Burkina Faso', 'bdi'=>'Burundi', 'khm'=>'Cambodia', 'cmr'=>'Cameroon', 'can'=>'Canada', 'cpv'=>'Cape Verde', 'cym'=>'Cayman Islands', 'caf'=>'Central African Republic', 'tcd'=>'Chad', 'chl'=>'Chile', 'chn'=>'China', 'hkg'=>'Hong Kong', 'mac'=>'Macao', 'cxr'=>'Christmas Island', 'cck'=>'Cocos (Keeling) Islands', 'col'=>'Colombia', 'com'=>'Comoros', 'cog'=>'Congo (Brazzaville)', 'cod'=>'Congo (Kinshasa)', 'cok'=>'Cook Islands', 'cri'=>'Costa Rica', 'civ'=>'Côte d\'Ivoire', 'hrv'=>'Croatia', 'cub'=>'Cuba', 'cyp'=>'Cyprus', 'cze'=>'Czech Republic', 'dnk'=>'Denmark', 'dji'=>'Djibouti', 'dma'=>'Dominica', 'dom'=>'Dominican Republic', 'ecu'=>'Ecuador', 'egy'=>'Egypt', 'slv'=>'El Salvador', 'gnq'=>'Equatorial Guinea', 'eri'=>'Eritrea', 'est'=>'Estonia', 'eth'=>'Ethiopia', 'flk'=>'Falkland Islands (Malvinas)', 'fro'=>'Faroe Islands', 'fji'=>'Fiji', 'fin'=>'Finland', 'fra'=>'France', 'guf'=>'French Guiana', 'pyf'=>'French Polynesia', 'atf'=>'French Southern Territories', 'gab'=>'Gabon', 'gmb'=>'Gambia', 'geo'=>'Georgia', 'deu'=>'Germany', 'gha'=>'Ghana', 'gib'=>'Gibraltar', 'grc'=>'Greece', 'grl'=>'Greenland', 'grd'=>'Grenada', 'glp'=>'Guadeloupe', 'gum'=>'Guam', 'gtm'=>'Guatemala', 'ggy'=>'Guernsey', 'gin'=>'Guinea', 'gnb'=>'Guinea-Bissau', 'guy'=>'Guyana', 'hti'=>'Haiti', 'hmd'=>'Heard and Mcdonald Islands', 'vat'=>'Holy See (Vatican City State)', 'hnd'=>'Honduras', 'hun'=>'Hungary', 'isl'=>'Iceland', 'ind'=>'India', 'idn'=>'Indonesia', 'irn'=>'Iran', 'irq'=>'Iraq', 'irl'=>'Ireland', 'imn'=>'Isle of Man', 'isr'=>'Israel', 'ita'=>'Italy', 'jam'=>'Jamaica', 'jpn'=>'Japan', 'jey'=>'Jersey', 'jor'=>'Jordan', 'kaz'=>'Kazakhstan', 'ken'=>'Kenya', 'kir'=>'Kiribati', 'prk'=>'Korea (North)', 'kor'=>'Korea (South)', 'kwt'=>'Kuwait', 'kgz'=>'Kyrgyzstan', 'lao'=>'Lao PDR', 'lva'=>'Latvia', 'lbn'=>'Lebanon', 'lso'=>'Lesotho', 'lbr'=>'Liberia', 'lby'=>'Libya', 'lie'=>'Liechtenstein', 'ltu'=>'Lithuania', 'lux'=>'Luxembourg', 'mkd'=>'Macedonia', 'mdg'=>'Madagascar', 'mwi'=>'Malawi', 'mys'=>'Malaysia', 'mdv'=>'Maldives', 'mli'=>'Mali', 'mlt'=>'Malta', 'mhl'=>'Marshall Islands', 'mtq'=>'Martinique', 'mrt'=>'Mauritania', 'mus'=>'Mauritius', 'myt'=>'Mayotte', 'mex'=>'Mexico', 'fsm'=>'Micronesia', 'mda'=>'Moldova', 'mco'=>'Monaco', 'mng'=>'Mongolia', 'mne'=>'Montenegro', 'msr'=>'Montserrat', 'mar'=>'Morocco', 'moz'=>'Mozambique', 'mmr'=>'Myanmar', 'nam'=>'Namibia', 'nru'=>'Nauru', 'npl'=>'Nepal', 'nld'=>'Netherlands', 'ant'=>'Netherlands Antilles', 'ncl'=>'New Caledonia', 'nzl'=>'New Zealand', 'nic'=>'Nicaragua', 'ner'=>'Niger', 'nga'=>'Nigeria', 'niu'=>'Niue', 'nfk'=>'Norfolk Island', 'mnp'=>'Northern Mariana Islands', 'nor'=>'Norway', 'omn'=>'Oman', 'pak'=>'Pakistan', 'plw'=>'Palau', 'pse'=>'Palestinian Territory', 'pan'=>'Panama', 'png'=>'Papua New Guinea', 'pry'=>'Paraguay', 'per'=>'Peru', 'phl'=>'Philippines', 'pcn'=>'Pitcairn', 'pol'=>'Poland', 'prt'=>'Portugal', 'pri'=>'Puerto Rico', 'qat'=>'Qatar', 'reu'=>'Réunion', 'rou'=>'Romania', 'rus'=>'Russian Federation', 'rwa'=>'Rwanda', 'blm'=>'Saint-Barthélemy', 'shn'=>'Saint Helena', 'kna'=>'Saint Kitts and Nevis', 'lca'=>'Saint Lucia', 'maf'=>'Saint-Martin (French part)', 'spm'=>'Saint Pierre and Miquelon', 'vct'=>'Saint Vincent and Grenadines', 'wsm'=>'Samoa', 'smr'=>'San Marino', 'stp'=>'Sao Tome and Principe', 'sau'=>'Saudi Arabia', 'sen'=>'Senegal', 'srb'=>'Serbia', 'syc'=>'Seychelles', 'sle'=>'Sierra Leone', 'sgp'=>'Singapore', 'svk'=>'Slovakia', 'svn'=>'Slovenia', 'slb'=>'Solomon Islands', 'som'=>'Somalia', 'zaf'=>'South Africa', 'sgs'=>'South Georgia and the South Sandwich Islands', 'ssd'=>'South Sudan', 'esp'=>'Spain', 'lka'=>'Sri Lanka', 'sdn'=>'Sudan', 'sur'=>'Suriname', 'sjm'=>'Svalbard and Jan Mayen Islands', 'swz'=>'Swaziland', 'swe'=>'Sweden', 'che'=>'Switzerland', 'syr'=>'Syrian Arab Republic (Syria)', 'twn'=>'Taiwan', 'tjk'=>'Tajikistan', 'tza'=>'Tanzania', 'tha'=>'Thailand', 'tls'=>'Timor-Leste', 'tgo'=>'Togo', 'tkl'=>'Tokelau', 'ton'=>'Tonga', 'tto'=>'Trinidad and Tobago', 'tun'=>'Tunisia', 'tur'=>'Turkey', 'tkm'=>'Turkmenistan', 'tca'=>'Turks and Caicos Islands', 'tuv'=>'Tuvalu', 'uga'=>'Uganda', 'ukr'=>'Ukraine', 'are'=>'United Arab Emirates', 'gbr'=>'United Kingdom', 'usa'=>'United States of America', 'umi'=>'US Minor Outlying Islands', 'ury'=>'Uruguay', 'uzb'=>'Uzbekistan', 'vut'=>'Vanuatu', 'ven'=>'Venezuela (Bolivarian Republic)', 'vnm'=>'Viet Nam', 'vir'=>'Virgin Islands', 'wlf'=>'Wallis and Futuna Islands', 'esh'=>'Western Sahara', 'xkx'=>'Kosovo', 'yem'=>'Yemen', 'zmb'=>'Zambia', 'zwe'=>'Zimbabwe');	public static $toDates = array();    public function __construct($countryCode) {        $this->countryCode = $countryCode;		$this->regions = array();		$this->holidayTypes = array();		$this->fullName = SupportedCountry::$fullNames[$countryCode];		$fromDates = $this->getFromDates(); 		if(array_key_exists($countryCode, $fromDates)) {			$this->fromDate = EnricoDate::createNew($fromDates[$countryCode]);		} else {			$this->fromDate = EnricoDate::createNew(new EnricoDate(1, 1, 2011));		}		$toDates = $this->getToDates(); 		if(array_key_exists($countryCode, $toDates)) {			$this->toDate = EnricoDate::createNew($toDates[$countryCode]);		} else {			$this->toDate = EnricoDate::createNew(new EnricoDate(31, 12, 32767));		}    }	public function isRegionSupported($region)	{		for($i = 0; $i < count($this->regions); $i++)		{			if(strcmp($region, $this->regions[$i]) == 0)				return true;		}		return false;	}		public function getArray() {		return array('countryCode' => $this->countryCode, 'regions' => $this->regions, 'holidayTypes' => $this->holidayTypes, 'fullName' => $this->fullName,					'fromDate' => array('day' => $this->fromDate->day, 'month' => $this->fromDate->month, 'year' => $this->fromDate->year),					'toDate' => array('day' => $this->toDate->day, 'month' => $this->toDate->month, 'year' => $this->toDate->year));	}		public function getFromDates() {		$retVal = array('ago'=>new EnricoDate(1, 1, 2014), 'aut'=>new EnricoDate(1, 1, 1946), 'bih'=>new EnricoDate(1, 1, 2017), 'bra'=>new EnricoDate(1, 1, 2016), 'chn'=>new EnricoDate(1, 1, 2013), 'col'=>new EnricoDate(1, 1, 2016), 'cze'=>new EnricoDate(1, 1, 1952), 'grc'=>new EnricoDate(1, 1, 2017), 'hkg'=>new EnricoDate(1, 1, 2013), 'imn'=>new EnricoDate(1, 1, 2015), 'isr'=>new EnricoDate(1, 1, 2014), 'mkd'=>new EnricoDate(1, 1, 2017), 'mex'=>new EnricoDate(1, 1, 2014), 'pol'=>new EnricoDate(1, 1, 1952), 'rou'=>new EnricoDate(1, 1, 2015), 'svk'=>new EnricoDate(1, 1, 1952), 'zaf'=>new EnricoDate(1, 1, 2013), 'kor'=>new EnricoDate(1, 1, 2013), 'sct'=>new EnricoDate(1, 1, 2016), 'ukr'=>new EnricoDate(1, 1, 2015), 'sgp'=>new EnricoDate(1, 1, 2018), 'phl'=>new EnricoDate(1, 1, 2018), 'che'=>new EnricoDate(1, 1, 2019), 'per'=>new EnricoDate(1, 1, 2019), 'chl'=>new EnricoDate(1, 1, 2019), 'blr'=>new EnricoDate(1, 1, 2021), 'esp'=>new EnricoDate(1, 1, 2021), 'xkx'=>new EnricoDate(1, 1, 2023), 'cyp'=>new EnricoDate(1, 1, 2023), 'mne'=>new EnricoDate(1, 1, 2024), 'slv'=>new EnricoDate(1, 1, 2024), 'bgr'=>new EnricoDate(1, 1, 2025), 'arg'=>new EnricoDate(1, 1, 2025));				return $retVal;	}		public function getToDates() {		$retVal = array('bih'=>new EnricoDate(31, 12, 2100), 'chn'=>new EnricoDate(31, 12, 2100), 'mkd'=>new EnricoDate(31, 12, 2100), 'rus'=>new EnricoDate(31, 12, 2100), 'srb'=>new EnricoDate(31, 12, 2100), 'blr'=>new EnricoDate(31, 12, 2100), 'xkx'=>new EnricoDate(31, 12, 2100), 'mne'=>new EnricoDate(31, 12, 2100));				return $retVal;	}		public static function compare($a, $b)  {		return strcmp($a->fullName, $b->fullName);	}}?>