<?php
	Class Passenger extends Action {
		protected $_table;

		public function __construct($table) {
			$this->_table = $table;
		}

		public function moveToPerm($passengers, $travelId) {
			foreach ($passengers as $key => $value) {
				$this->create(array(
					'name' => $value->name,
					'travel_id' => $travelId,
					'address' => $value->address,
					'phone' => $value->phone,
					'blood_group' => $value->blood_group,
					'next_of_kin' => $value->next_of_kin,
					'email' => $value->email,
					'ticket' => $value->ticket
					));
			}
		}
	}