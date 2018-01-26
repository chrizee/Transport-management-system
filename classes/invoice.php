<?php
class Invoice extends Action{
	protected $_table = 'invoice';
	public $total,$message;
	private $_msg;

	public function generate($price, $noOfItems, $travelId, $userId) {
		$this->total =  $price * count($noOfItems);
		$this->create(array(
			'travel_id' => $travelId,
			'unit_price' => $price,
			'trip_amount' => $this->total,
			'user_id' => $userId,
			));
	}

	private function period($period) {
		$now = new dateTime();
		switch ($period) {
			case Config::get('periods/today'):
				$this->_msg = "today";
				$period = "DATE(i.date_generated) = {$now->format('Y-m-d')}"; 	
				break;
			case Config::get('periods/yesterday'):
				$this->_msg = "yesterday";
				$period = "DATE(i.date_generated) = {$now->sub(new DateInterval('P1D'))->format('Y-m-d')}"; 	
				break;
			case Config::get('periods/this_week'):
				$this->_msg = "this week";
				$period = "WEEKOFYEAR(i.date_generated) = {$now->format('W')}"; 	
				break;
			case Config::get('periods/last_week'):
				$this->_msg = "last week";
				$period = "WEEKOFYEAR(i.date_generated) = {$now->sub(new DateInterval('P1W'))->format('W')}"; 	
				break;
			case Config::get('periods/this_month'):
				$this->_msg = "this month";
				$period = "MONTH(i.date_generated) = {$now->format('m')} AND YEAR(i.date_generated) = {$now->format('Y')}";
				break;
			case Config::get('periods/last_month'):
				$this->_msg = "last month";
				$period = "MONTH(i.date_generated) = {$now->sub(new DateInterval('P1M'))->format('m')} AND YEAR(i.date_generated) = {$now->sub(new DateInterval('P1M'))->format('Y')}"; 	
				break;
			default:
				$this->_msg = "today";
				$period = "DATE(i.date_generated) = {$now->format('Y-m-d')}";	//defaults to the current day
				break;
		}
		return $period;
	}

	public function getN($period,$user,$start,$end,$location,$staff, Park $park) {
		$sql = "SELECT i.trip_amount as amount, i.user_id as user, i.date_generated as `date`, t.source as source, t.destination as destination FROM invoice i inner JOIN travels t ON i.travel_id = t.id WHERE ";
		if($staff) {
			$this->message = "Invoice by {$user->getStaffs(array('id', '=', $staff))[0]->name} ";
			$sql .= "i.user_id = {$staff} AND ";
			if($start && $end) {
				$this->message .= "between {$start} and {$end} ";
				$sql .= "(i.date_generated) BETWEEN {$start} AND {$end}";
			} elseif($period) {
				$sql .= $this->period($period);
				$this->message .= "for ".$this->_msg;
			}
		} elseif($location) {
			if($location != '*') {
				$this->message = "Invoice of {$park->get($location, 'park')->park} ";
				$sql .= "t.source = {$location} AND ";
			}else{
				$this->message = "Invoice of all locations ";
			}
			if($start && $end) {
				$this->message .= "between {$start} and {$end} ";
				$sql .= "(i.date_generated) BETWEEN {$start} AND {$end}";
			} elseif($period) {
				$sql .= $this->period($period);
				$this->message .= "for ".$this->_msg;
			}
		} elseif($user->data()->id) {
			$this->message = "Invoice by {$user->getStaffs(array('id', '=', $user->data()->id))[0]->name} ";
			$sql .= "i.user_id = {$user->data()->id} AND ";
			if($start && $end) {
				$this->message .= "between {$start} and {$end} ";
				$sql .= "(i.date_generated) BETWEEN {$start} AND {$end}";
			} elseif($period) {
				$sql .= $this->period($period);
				$this->message .= "for ".$this->_msg;
			}
			
		}

		if($sql) {
			if(!$data = DB::getInstance()->query($sql)) {
				throw new Exception("There was a problem getting data");
			}
			return $data->results();
		}
	}
}