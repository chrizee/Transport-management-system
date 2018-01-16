<?php
	class Notification extends Action{
		protected $_table = 'notification';
		public $headers = array('',
										'Vehicle Request',
										'Vehicle Maintenance',
										'Vehicle active',
										'New Park',
										'New staff',
										'New driver',
										'New vehicle',
										'Price update',
									);		//heading to show in notification panel,first value 0 is discarded.index used is that of notification is in config file
		public $message = array('',
											'You have a new vehicle request.',
											 '', '','','',''

										 );				//message to display for some notification
		public $links = array('', "vehiclerequest", 'viewvehicles', 'viewvehicles', 'viewlocation','viewstaff','viewstaff','viewvehicles','viewlocation');

		public function getN($affected, $location) {
			$sql = "SELECT * FROM notification WHERE affected IN('*', $affected) AND location_affected IN('*', $location)";
			if(!$data = DB::getInstance()->query($sql)) {
				throw new Exception("There was a problem getting data");
			}
			return $data->results();
		}
	}
