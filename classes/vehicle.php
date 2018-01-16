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

		public function downTime($id, $created) {
			try {
				 $data = $this->get(array('vehicle_id', '=', $id, 'status', '=', Config::get('status/good')),'date_fault_occured,date_fixed');
				 $diff = 0;
				 if($data) {
					 foreach ($data as $key => $value) {
					 	$fault = strtotime($value->date_fault_occured);
						$fixed = strtotime($value->date_fixed);
						$diff += $fixed - $fault;
						}
					 	$life = time() - strtotime($created);
					 	return ($diff != 0) ? round(($diff/$life)*100,2) : 0;
				 	}
					return $diff;
			 } catch(Exception $e){
				 die($e->getMessage());
			 }

		}

	}
