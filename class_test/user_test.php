<?php

include "test_func.php";

$user = User::instance();

var_d($user);

var_d($user->get_user_data());


##登陆
// $data = array(
// 	'username' => 'hOrwathhtl ',
// 	'password' => ' horwathhtl '
// );
// var_d($user->login($data));

// $data = array(
// 	'username' => 'horwathhtl',
// 	'password' => '  '
// );
// var_d($user->login($data));

##注册
// $data = array(
// 	'username' => ' Test@TESt.com',
// 	'password' => 'test ',
// 	'name' => 'test ',
// 	'company_name' => ' test',
// 	'email' => 'test@TEst.com ',
// 	'phone' => 'test ',
// 	'role' => 'host'
// );

// var_d($user->register($data));