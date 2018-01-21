<?php
	require_once '../core/init.php';
	if(Input::get('action') == 'delete') {
		$messageObj = new Message();
		foreach (Input::get('id') as $key => $value) {
			try {
				$messageObj->update(decode($value), array('status' => Config::get('message/deleted')));	
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
				$messageObj->update(decode($value), array('status' => Config::get('message/read')));	
			} catch (Exception $e) {
				die($e->getMessage());
			}
		}
		echo 1;
	}