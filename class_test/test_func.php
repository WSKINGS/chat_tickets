<?php

include "test_include.php";

function var_d($var) {
	var_dump($var);
	echo '<br/><br/>';
}

$user = User::instance();

$data = array(
	'username' => 'horwathhtl',
	'password' => 'horwathhtl'
);

$ret = $user->login($data);

$_SESSION['wt_user'] = $ret['msg'];