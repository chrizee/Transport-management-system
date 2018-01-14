<?php
class Travels extends Action{
	protected $_table;
	public $duration = '', $percent = 40, $msg = 'Duration not set.Progress bar will be static', $expectedArrival = '';
	private $_str = array();

	public function __construct($table) {
		$this->_table = $table;
	}

	public function calcpercent($routeid, Route $routeObj, $starttime, $id ) {
		$date = new DateTime($starttime);		//time journey started
		$initstamp = strtotime($date->format('H:i:s'));	
		if($routeid != 0) {
			$this->duration = $routeObj->get(array('id', '=', $routeid), 'duration')[0]->duration;
			if($this->duration != "00:00:00") {
				$arr = explode(':', $this->duration);
				$_con = "PT".$arr[0]."H".$arr[1]."M".$arr[2]."S";
				$interval = new DateInterval($_con);
				$date->add($interval);
				$this->expectedArrival = $date->format('d-M-Y h:i a');
				$diff = strtotime($date->format('Y-M-d H:i:s')) - time();			//time remaining to complete journey
				if($diff < 0) {
					$this->percent = 100;
					$this->expectedArrival .= ". Duration elapsed";
				} else{
					$div = strtotime($this->expectedArrival) - $initstamp;
					$this->percent = 100 - round(($diff/$div) * 100);		//percent of journey completed
					$arr = ["id" => $id, "left" => strtotime($date->format('Y-M-d H:i:s')), "right" => time(), "div" => $div];
					array_push($this->_str, $arr);
					if($this->percent < 0 || $this->percent>100) {
						$this->percent = 40;
					}
				}
			}

		}
	}

	public function json() {
		return json_encode($this->_str);
	}
}