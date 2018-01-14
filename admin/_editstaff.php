<?php
	require_once '../core/init.php';
	if(Input::get('identity') == 'driver') {
		$driverObj = new Driver('drivers');
		try {
			if(Input::get('sender') == 'staff') {
				$driverObj->update(Input::get('id'), array(
				'current_location' => Input::get('location'),
				));
				echo "Location updated";	
			}
			if(empty(Input::get('sender'))) {
				$driverObj->update(Input::get('id'), array(
					'name' => Input::get('name'),
					'email' => Input::get('email'),
					'phone' => Input::get('phone'),
					'status' => Input::get('status'),
					'current_location' => Input::get('location'),
					));
				echo ucfirst(Input::get('name'))."'s record updated";
			}
		} catch (Exception $e) {
			echo 'There was a problem updating that record. please ensure allcfields are correctly filled';
		}
	} elseif (Input::get('identity') == 'staff') {
		$user = new User();
		try {
			$user->update(array(
				'name' => Input::get('name'),
				'phone' => Input::get('phone'),
				'email' => Input::get('email'),
				'address' => Input::get('address'),
				'location' => Input::get('location'),
				'status' => Input::get('status'),
				'groups' => Input::get('level'),
				), Input::get('id'));
			echo ucfirst(Input::get('name'))."'s record updated";
		} catch (Exception $e) {
			echo 'There was a problem updating that record. please ensure allcfields are correctly filled';
		}
		
	}
	//echo Input::get('id');
?>