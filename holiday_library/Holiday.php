<?php

class Holiday {
	
    public $date;
	public $dateTo;
    public $name = array();
    public $flags = array();
    public $note = array();
	public $holidayType;
	public $observedOn;

    public function __construct($date) {
        $this->date = $date;
    }

    public function compare($other) {
        return $this->date->compare($other->date);
    }
	
	public function getArray() {
		$this->date->calculateDayOfWeek();
		$retVal = array('date' => array('day' => $this->date->day, 'month' => $this->date->month, 'year' => $this->date->year, 'dayOfWeek' => $this->date->dayOfWeek));
		if($this->observedOn != NULL) {
			$this->observedOn->calculateDayOfWeek();
			$retVal['observedOn'] = array('day' => $this->observedOn->day, 'month' => $this->observedOn->month, 'year' => $this->observedOn->year, 'dayOfWeek' => $this->observedOn->dayOfWeek);
		}
		$name = $this->getLocalizedStringArray($this->name);
		if($name != NULL) {
			$retVal['name'] = $name;
		}
		$note = $this->getLocalizedStringArray($this->note);
		if($note != NULL) {
			$retVal['note'] = $note;
		}
		if($this->flags != NULL) {
			$retVal['flags'] = $this->flags;
		}
		if($this->holidayType != NULL) {
			$retVal['holidayType'] = $this->holidayType;
		}
		return $retVal;
	}
	
	private function getLocalizedStringArray($ar) {
		$count = count($ar);
		if($count > 0) {
			$retVal = array();
			for($i=0; $i<$count; $i++) {
				array_push($retVal, array('lang' => $ar[$i]->lang, 'text' => $ar[$i]->text));
			}
			return $retVal;
		}
		return NULL;
	}
}

class LocalizedString {
	
	public $lang;
	public $text;
	
	public function __construct($lang, $text) {
        $this->lang = $lang;
		$this->text = $text;
    }
}
?>
