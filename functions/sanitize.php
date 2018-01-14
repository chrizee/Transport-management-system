<?php
function escape($string) {
	return htmlentities($string, ENT_QUOTES, 'UTF-8');
}
//produces yyyy-mm-dd HH:MM from mm/dd/yyyy HH:MM AM/PM 
function cleanDate($datetime) {
	$arr = explode(' ', $datetime);
	$date = explode('/', $arr[0]);
	$time = explode(':', $arr[1]);
	$timestr = $arr[2];
	$ndate = $date[2].'-'.$date[0].'-'.$date[1];
	
	$hh = $time[0];
	$mm = $time[1];

	
	if($timestr == 'am' || $timestr == 'AM') {
		$pad = 0;
	} else {
		$pad = 12;
	}
	$hh += $pad;
	$ntime = $hh.':'.$mm; 
	$newDatetime = $ndate.' '.$ntime;
	return $newDatetime;
}
function timeToStamp($time) {
	$arr = explode(':', $time);
	if(count($arr) == 3) {
		return ($arr[0] * 60 * 60) + ($arr[1] * 60) + $arr[2];
	}
	if(count($arr) == 2) {
		return ($arr[0] * 60 * 60) + ($arr[1] * 60);
	}
	return false;
}
function encode($data) {
	return base64_encode($data);
}
function decode($data) {
	return base64_decode($data);
}