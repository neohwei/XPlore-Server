<?php

	$value = ini_get('upload_max_filesize');
	$val = trim(ini_get('upload_max_filesize'));
    $unit = strtolower(substr($val, -1));
    $times = $val * 1024;
    $toInt = intval($times);
	
	$message = "Trim : $val, unit : $unit, times : $times, toInt : $toInt";
	echo $message;

?>