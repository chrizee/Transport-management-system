<?php
	class Notification extends Action{
		protected $_table = 'notification';
		public $headers = array('', 'Vehicle Request');		//heading to show in notification panel,first value 0 is discarded
		public $message = array('', 'You have a new vehicle request.');
		public $links = array('', "vehiclerequest.php");

		public function getN($affected, $location) {
			$sql = "SELECT * FROM notification WHERE affected IN('al', $affected) AND location_affected IN('al', $location)";
			if(!$data = DB::getInstance()->query($sql)) {
				throw new Exception("There was a problem getting data");
			}
			return $data->results();
		}

		//returns the time part of the notification in a precise format
		public function date($date) {
			$init = new dateTime($date);
			$now = new dateTime();
			$diff = $now->diff($init);
			if($diff->y != 0 ) {
				return $diff->format("%y Years");
			} elseif($diff->m != 0) {
				return $diff->format("%m months");
			} elseif($diff->d != 0) {
				return $diff->format("%d days");
			} elseif($diff->h != 0) {
				return $diff->format("%h hours");
			} elseif($diff->i != 0) {
				return $diff->format("%i mins");
			} elseif($diff->s != 0) {
				return $diff->format("%s secs");
			} elseif($diff->s == 0) {
				return "now";
			}
		}
	}
?>