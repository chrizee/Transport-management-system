<?php
	class Notification extends Action{
		protected $_table = 'notification';
		public $headers = array('', 'Vehicle Request', 'Vehicle Maintenance', 'Vehicle active');		//heading to show in notification panel,first value 0 is discarded.index used is that of notification is in config file
		public $message = array('', 'You have a new vehicle request.', '', '');				//message to display for some notification
		public $links = array('', "vehiclerequest.php", 'viewvehicles.php', 'viewvehicles.php');

		public function getN($affected, $location) {
			$sql = "SELECT * FROM notification WHERE affected IN('*', $affected) AND location_affected IN('*', $location)";
			if(!$data = DB::getInstance()->query($sql)) {
				throw new Exception("There was a problem getting data");
			}
			return $data->results();
		}
	}
