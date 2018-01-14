<?php
	require_once '../core/init.php';
	if(!empty(Input::get('source')) && !empty(Input::get('destination'))) {
		$route = new Route('routes');
		try {
			$price = $route->get(array('source', '=', Input::get('source'), 'destination', '=', Input::get('destination')), 'price');
			$sql = "SELECT COUNT(*) as count FROM `travels` WHERE DATE(date) = CURRENT_DATE AND source =". Input::get('source')." AND destination =". Input::get('destination')."";
			$tripNO = ((int) DB::getInstance()->query($sql)->first()->count) + 1;		//next value of ticket to use
			$ticket = $tripNO + Config::get('ticket/start');
			//update ticket no in db to that of currently selected trip
			DB::getInstance()->update('temp_passengers', "'".Input::get('hash')."'", array('ticket' => $ticket), 'hash');
			if(!empty($price) && is_array($price)) {
				$info = ['price' => $price[0]->price,'trip' => $tripNO];
				echo json_encode($info);
				//echo $price[0]->price;
			} else {
				$info = ['price' => "X",'trip' => $tripNO];
				echo json_encode($info);
				//echo "X";
			}
		} catch(Exception $e) {
			echo '0';
		}
	}
	if(!empty(Input::get('vehicleid'))) {
		try {
			$vehicle = new Vehicle('vehicles');
			$info = $vehicle->get(array('id', '=', Input::get('vehicleid')), 'no_of_seats, ac');
			if(!empty($info)) {
				echo json_encode($info[0]);
			} else {
				echo "X";
			}
		} catch (Exception $e) {
			echo '0';
		}
	}