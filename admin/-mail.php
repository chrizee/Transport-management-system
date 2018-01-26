<?php
	require_once '../core/init.php';
	if(Input::get('action') == 'delete') {
		$messageObj = new Message();
		foreach (Input::get('id') as $key => $value) {
			$status = (Input::get('who') == 'sender') ? 'sender_status' : 'receiver_status';
			try {
				$messageObj->update(decode($value), array($status => Config::get('message/deleted')));	
			} catch (Exception $e) {
				die($e->getMessage());
			}
		}
		echo 1;
	}
	if(Input::get('action') == 'restore') {
		$messageObj = new Message();
		foreach (Input::get('id') as $key => $value) {
			try {
				$mess = $messageObj->get(array('id', '=', decode($value)));
				//print_r($mess);
				$status = ($mess[0]->from == Input::get('user')) ? 'sender_status' : 'receiver_status';
				$messageObj->update(decode($value), array($status => Config::get('message/read')));	
			} catch (Exception $e) {
				die($e->getMessage());
			}
		}
		echo 1;
	}