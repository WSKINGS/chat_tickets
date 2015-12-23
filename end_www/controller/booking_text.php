<?php

session_start();

$lang = (isset($_GET['lang']) && $_GET['lang'] == 'en')?$_GET['lang']:'zh';
$channel = (isset($_GET['channel']) && $_GET['channel'] != '')?$_GET['channel']:'usual';
$view_data['lang'] = $lang;
$view_data['channel'] = $channel;

if (!isset($_GET['activity'])) {
	die('Page error.');
}

if (!isset($booking_activity)) {
	$activity_id = $_GET['activity'];
	$view_data['activity_id'] = $activity_id;

	global $booking_activity;
	$booking_activity = new Activity();
	$booking_activity->set_activity($activity_id);
}

include_once "order_form_text.php";