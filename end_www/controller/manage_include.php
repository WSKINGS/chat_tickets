<?php

session_start();

$activity = new Activity();
$activity->set_activity(1);

$_SESSION['activity'] = $activity;

$user = User::instance();
$login_ret = $user->login( array('username'=>'horwathhtl', 'password'=>'horwathhtl') );

if ($login_ret['r'] == 'ok') {
	$user = $login_ret['msg'];
	$_SESSION['manage_user'] = $user;
}

if ( !isset($_SESSION['activity']) || !isset($_SESSION['manage_user']) ) {
	die("page error.");
}

$activity = $_SESSION['activity'];
$activity_id = $activity->get_activity_data('activity_id');
$user = $_SESSION['user'];