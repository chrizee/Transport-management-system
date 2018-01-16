<?php
	require_once '../core/init.php';
	if(!empty(Input::get('tripid'))) {
		$travelObj = new Travels('travels');
		try {
			$travel = $travelObj->get(array('id', '=', Input::get('tripid')));
			$user = new User();
			if(!empty($travel)) {
				$driver = new Driver('drivers');
				$driver->update($travel[0]->driver_id, array(
					'current_location' => $user->data()->location,
					'status' => Config::get('status/active'),
					));
				$vehicle = new Vehicle('vehicles');
				$vehicle->update($travel[0]->vehicle_id, array(
					'current_location' => $user->data()->location,
					'status' => Config::get('status/active'),
					));
				$travelObj->update($travel[0]->id, array(
					'status' => Config::get('status/travels/arrived'),
					));
				$sql  = "UPDATE waybill SET status = ? WHERE travel_id = {$travel[0]->id}";
				DB::getInstance()->query($sql, array(Config::get('waybill/arrived')));
				echo '1';
			}
		} catch(Exception $e) {
			echo "X";
		}
	}
