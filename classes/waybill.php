<?php
	Class Waybill extends Action {
		protected $_table;

		public function __construct($table) {
			$this->_table = $table;
		}

		public function changeStatus($id, $status, $travelId = null) {
			if(is_array($id)) {
				foreach ($id as $key => $value) {
					if($travelId) {
						$this->update($value, array(
							'status' => Config::get("waybill/".$status),
							'travel_id' => $travelId,
							));
					} else {
						$this->update($value, array(
							'status' => Config::get("waybill/".$status),
						));
					}
				}
			} else {
				if($travelId) {
					$this->update($value, array(
						'status' => Config::get("waybill/".$status),
						'travel_id' => $travelId,
						));
				} else {
					$this->update($value, array(
						'status' => Config::get("waybill/".$status),
					));
				}
			}
		}

		public function confirmKey($id, $key) {
			try {
				$info = $this->get(array('id', '=', $id), 'collection_key,salt');	
			} catch (Exception $e) {
				die($e->getMessage());
			}
			$pass = $info[0]->collection_key;
			$salt = $info[0]->salt;
			if($pass === Hash::encrypt($key, $salt)) {
				$date = new dateTime();
				$this->update($id, array('status' => Config::get('waybill/picked'), 'date_picked_up' => $date->format('Y-m-d h:i:s')));
				return true;
			}
			return false;
		}
	}