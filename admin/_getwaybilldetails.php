<?php
	require_once '../core/init.php';
	$waybillObj = new Waybill('waybill');
	if(!empty(Input::get('id')) && empty(Input::get('key'))) {
		try {
			$info = $waybillObj->get(array('id', '=', Input::get('id')), 'item,sender_name,sender_phone,sender_address,receiver_name,receiver_phone,receiver_address,weight,date_placed,status,date_picked_up');
			if(!empty($info)) {
				echo json_encode($info[0]);
			}
		} catch (Exception $e) {
			echo 'X';
		}
	}

	if(!empty(Input::get('key')) && !empty(Input::get('id'))) {
		if($waybillObj->confirmKey(Input::get('id'), Input::get('key'))) {
			echo '1';
		}else{
			echo "X";
		}

	}
