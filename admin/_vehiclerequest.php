<?php
	require_once "../core/init.php";
	if(Input::exists()) {
		if(count(Input::get('driver')) == 0 || count(Input::get('vehicle')) == 0) {
			Session::flash('home', "Both vehicle and driver is required");
			Redirect::to($_SERVER['HTTP_REFERER']);
		}
		if(count(Input::get('driver')) < count(Input::get('vehicle'))) {
			Session::flash('home', "vehicles cannot be more than drivers");
			Redirect::to($_SERVER['HTTP_REFERER']);
		}
		$travelObj = new Travels('travels');
		$notification = new Notification();
		$notice = $notification->get(array('id', '=', Input::get('request')))[0];
		$routeObj = new Route('routes');
		$vehicleObj = new Vehicle('vehicles');	
		$driverObj = new Driver('drivers');
		$user = new User();
		foreach (Input::get('vehicle') as $key => $value) {	
			$route = $routeObj->get(array('source', '=', $notice->location_affected, 'destination', '=', $notice->location_initiated));
			try {
				if(!empty($route)) {
					$travelObj->create(array(
						'vehicle_id' => $value,
						'route_id' => $route[0]->id,
						'source' => $notice->location_affected,
						'destination' => $notice->location_initiated,
						'driver_id' => Input::get('driver')[$key],
						'user_id' => $user->data()->id,
						'type' => 1,
						));
					$driverObj->update(Input::get('driver')[$key], array('status' => Config::get('status/travelling')));
					$vehicleObj->update($value, array('status' => Config::get('status/travelling')));
					$notification->update($notice->id, array('status' => Config::get('notification/status/responded')));
					//echo '1';
					Session::flash('home', "response sent");
					Redirect::to($_SERVER['HTTP_REFERER']);
				} else {
					$travelObj->create(array(
						'vehicle_id' => $value,
						'driver_id' => Input::get('driver')[$key],
						'source' => $notice->location_affected,
						'destination' => $notice->location_initiated,
						'user_id' => $user->data()->id,
						'type' => 1,
						));
					$driverObj->update(Input::get('driver')[$key], array('status' => Config::get('status/travelling')));
					$vehicleObj->update($value, array('status' => Config::get('status/travelling')));
					$notification->update($notice->id, array('status' => Config::get('notification/status/responded')));
					//echo '1';
					Session::flash('home', "response sent");
					Redirect::to($_SERVER['HTTP_REFERER']);
				}	
			} catch (Exception $e) {
				//echo "0";
				Session::flash('home', "Error responding to request");
				Redirect::to($_SERVER['HTTP_REFERER']);
			}
			
		}
	}