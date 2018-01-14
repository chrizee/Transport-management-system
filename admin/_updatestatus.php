<?php 
	require_once '../core/init.php';
	//echo Input::get('old');
	//echo Input::get('new');
	if(!empty(Input::get('driver'))) {
		$driverObj = new Driver('drivers');
		try {
			if(!empty(Input::get('new'))) {
				$driverObj->update(Input::get('new'), array('status' => Config::get('status/selected')));	
				echo 'success';
			}
			if(!empty(Input::get('old'))) {	
				$driverObj->update(Input::get('old'), array('status' => Config::get('status/active')));
				echo 'success';	
			}
		} catch (Exception $e) {
			echo "X";
		}
	}

	if(!empty(Input::get('vehicle'))) {
		$vehicleObj = new Vehicle('vehicles');
		try {
			if(!empty(Input::get('new'))) {
				$vehicleObj->update(Input::get('new'), array('status' => Config::get('status/selected')));
			}
			if(!empty(Input::get('old'))) {
				$vehicleObj->update(Input::get('old'), array('status' => Config::get('status/active')));
			}	
			echo 'success';
		} catch (Exception $e) {
			echo "X";
		}	
	}
	if(Input::get('key') == 'waybill' && !empty(Input::get('id'))) {
		$waybillObj = new Waybill('waybill');
		try {
			foreach (Input::get('id') as $key => $value) {
				$waybillObj->update($value, array('status' => Config::get('waybill/in_park')));
			}
			echo '1';
		} catch (Exception $e) {
			echo 'X';
		}
	}