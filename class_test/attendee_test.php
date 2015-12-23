<?php

include "test_func.php";

$attendee = new Attendee();

var_d($attendee);

##create
// $data = array(
// 	'activity_id' => 1,
// 	'order_id' => 1,
// 	'ticket_id' => 1,
// 	'name' => array('zh'=>' test ','en'=>'test '),
// 	'company' => array('zh'=>' test ','en'=>'test'),
// 	'title' => array('zh'=>' test ','en'=>'test'),
// 	'phone' => ' test ',
// 	'telephone' => ' test',
// 	'email' => ' Test@test.com ',
// 	'extra_info' => ' test  '
// );

// var_d($attendee->create($data));

##update
// $attendee->set_attendee_data(18);

// var_d($attendee);

// $data = array(
// 	'name' => array('zh'=>' test1 ','en'=>'test3 '),
// 	'company' => array('zh'=>' test2 ','en'=>'test2 '),
// 	'title' => array('zh'=>' test ','en'=>'test'),
// 	'phone' => ' test2 ',
// 	'telephone' => ' test2',
// 	'email' => ' Test@test2.com ',
// 	'extra_info' => ' test2  '
// );

// $attendee->update($data);

##set & get
// var_d($attendee);

// var_d($attendee);

// $attendee->set_attendee_data(5);

// var_d($attendee->get_attendee_data());


## approve
// $attendee->set_attendee_data(5);

// var_d($attendee->approve());