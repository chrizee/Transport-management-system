<?php
	require_once '../core/init.php';
	$passenger = new Passenger('temp_passengers');
	$user = new User();
	if(empty(Input::get('flag'))) {
		$validate = new Validate();
		$validation = $validate->check($_POST, array(
			'name' => array(
				'required' => true,
				'max' => 100,
				),
			'email' => array(
				'max' => 50,
				'function' => 'checkEmail'
				),
			'address' => array(
				'max' => 100,
				),
			'phone' => array(
				'function'=> 'checkPhone'
				),
			'next_of_kin' => array(
				'max' => 100
				),
			));
		if($validation->passed()) {
			try {
				$passenger->create(array(
					'name' => Input::get('name'),
					'address' => Input::get('address'),
					'phone' => Input::get('phone'),
					'email' => Input::get('email'),
					'next_of_kin' => Input::get('next_of_kin'),
					'blood_group' => Input::get('blood_group'),
					'user_id' => $user->data()->id,
					'ticket' => Input::get('ticket'),
					'hash' => Input::get('hash')
					));
				echo "1";
			} catch (Exception $e) {
				echo "X";
			}	
		} else {
			echo json_encode($validation->errors());
		}
	}

	if(!empty(Input::get('flag'))) {
		$validate = new Validate();
		$validation = $validate->check($_POST, array(
			'name' => array(
				'required' => true,
				'max' => 100,
				),
			));
		if($validation->passed()) {
			try {
				$passenger->delete(array('name', '=', Input::get('name'), 'hash', '=', Input::get('hash')));
				echo "1";	
			} catch (Exception $e) {
				echo 'X';
			}	
		} else {
			echo json_encode($validation->errors());
		}
	}