<?php 
	require_once '../core/init.php';
	//echo Input::get('vehicle_id');
	$vehicleObj = new Vehicle('vehicles');
	if(!empty(Input::get('vehicle')) && !empty(Input::get('location'))) {
		try {
			$vehicle = $vehicleObj->get(array('status', '=', Config::get('status/active'), 'current_location', '=', Input::get('location')), 'id,plate_no');
			echo json_encode($vehicle);
		} catch (Exception $e) {
			die("script is dead");
		}
	}
