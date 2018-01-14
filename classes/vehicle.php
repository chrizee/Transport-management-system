<?php
	class Vehicle extends Action {
		protected $_table;

		public function __construct($table) {
			$this->_table = $table;
		}

		//methos to check if requested is available in the requested park 
		public function requestCheck($location, $requestNo) {
			$vehiclecheck = $this->get(array('current_location', '=', $location, 'status', '=', Config::get('status/good')));
			return (count($vehiclecheck) < $requestNo) ? false : true;
		}

		public function requestGet(User $user) {
			//gets notification for the current year 
			$sql = "SELECT * FROM notification WHERE affected IN({$user->data()->groups}) AND location_affected IN({$user->data()->location}) AND YEAR(date) = YEAR(CURRENT_DATE) AND category =". Config::get('notification/request_vehicle');
			if(!$data = DB::getInstance()->query($sql)) {
				throw new Exception("There was a problem getting data");
			}
			return $data->results();
		}

	}