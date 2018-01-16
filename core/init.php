<?php
ob_start();
session_start();
ini_set("smtp_port", "25");
ini_set("sendmail_from", "okoroefe16@gmail.com");
ini_set("display_errors", 'on');
ini_set("SMTP", "gmail");

$GLOBALS['config'] = array(
	'mysql' => array(
		'host' => '127.0.0.1',
		'username' => 'root',
		'password' => 'christo16',
		'db' => 'transport'
	),
	'session' => array(
		'session_admin' => 'admin',
		'session_staff' => 'staff',
		'load' => 'passengers',
		'token_name' => 'token'
	),
	'cookie' => array(
		'cookie_name' => 'cook',
		'remember' => 'remember',
		'load' => 'passengers',
		'expiry_one_day' => 86400,
		'expiry_one_week' => 604800
	),
	'status' => array(
		'sacked' => 0,	//staffs/drivers
		'active' => 1,
		'leave' => 2,
		'sick' => 3,
		'good' => 1,	//vehicle
		'faulty' => 2,
		'out' => 'X',
		'selected' => 4,
		'travelling' => 5,
		'travels' => array(
			'travelling' => 0,
			'arrived' => 1
			),
	),
	'waybill' => array(
		'placed' => 0,
		'travelling' => 1,
		'arrived' => 2,
		'in_park' => 3,
		'picked' => 4,
		'total_load_weight' => 10,
	),
	'ticket' => array(
		'start' => 100,
	),
	'permissions' => array(
		'loading_officer' => 1,
		'manager' => 3,
		'waybill' => 4,
		'ceo' => 2,
		'all' => '*',
	),
	'message' => array(
		'not_read' => 0,
		'read' => 1,
	),
	'notification' => array(
		'status' => array(
			'not_responded' => 0,
			'seen' => 1,
			'responded' => 2,
			),
		'request_vehicle' => 1,
		'vehicle_bad' => 2,
		'vehicle_back' => 3,

	),
);

spl_autoload_register(function($class) {
	require_once '../classes/' . $class . '.php';	//requires a class only when needed
	}
);
require_once '../functions/sanitize.php'; //includes the function file

//checks if cookie exists and that session is not set for remember me functionality
	if (Cookie::exists(Config::get('cookie/remember')) && !Session::exists(Config::get('session/session_staff'))) {
		//get the value of the cookie that is set  when remember me button is checked
		$hash = Cookie::get(Config::get('cookie/remember'));
		//check if that hash exists in the database and grabs it from there
		$hashCheck = DB::getInstance()->get('users_session', array('hash', '=', $hash));

		if($hashCheck->count()) {
			$user = new User($hashCheck->first()->user_id);
			$user->login();
		}
	}

?>
