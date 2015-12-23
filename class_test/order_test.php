<?php

include "test_func.php";

$order = new Order();

//var_d($order);

##set_order

// $order->set_order(7);

// var_d($order);

// $dd = array(
// 	array(
// 		'ticket_id' => 1,
// 		'quantity' => 1
// 	),
// 	array(
// 		'ticket_id' => 1,
// 		'quantity' => 1
// 	)
// );
// $json_dd = json_encode($dd);
// var_d($json_dd);
// var_d(json_decode($json_dd));

##create
// $attendees = array(
// 	array(
// 		'ticket_id' => 1,
// 		'name' => ' test ',
// 		'company' => ' test ',
// 		'title' => ' test ',
// 		'phone' => ' test ',
// 		'telephone' => ' test',
// 		'email' => ' Test@test.com ',
// 		'extra_info' => ' test  '
// 	),
// 	array(
// 		'ticket_id' => 1,
// 		'name' => ' test ',
// 		'company' => ' test ',
// 		'title' => ' test ',
// 		'phone' => ' test ',
// 		'telephone' => ' test',
// 		'email' => ' Test@test.com ',
// 		'extra_info' => ' test  '
// 	)
// );

// $data = array(
// 	'company_field_id' => 1,
// 	'activity_id' => 1,
// 	'tickets' => '[{"ticket_id":1,"quantity":2},{"ticket_id":2,"quantity":1}]',
// 	'contact_name' => array('zh' => 'test'),
// 	'contact_email' => 'test@test.com',
// 	'contact_phone' => 'test',
// 	'company_name' => array(
// 		'zh'=>'测试名字',
// 		'en'=>'test name'
// 		),
// 	'company_address' => 'test',
// 	'company_zipcode' => 'test',
// 	'company_phone' => 'test',
// 	'company_fax' => 'test',
// 	'company_website' => 'test',
// 	'need_invoice' => 1,
// 	'extra_info' => 'test',
// 	'attendees' => $attendees
// );

// var_d($order->create($data));

##update
// $data = array(
// 	'company_field_id' => 1,
// 	'contact_name' => array('zh'=>'test1  '),
// 	'contact_email' => 'tesT1@test.com',
// 	'contact_phone' => ' test1 ',
// 	'company_name' => array('zh'=>'test1 ','en'=>'test1'),
// 	'company_address' => 'test 1',
// 	'company_zipcode' => 'test1 ',
// 	'company_phone' => 'test1 ',
// 	'company_fax' => 'test1 ',
// 	'company_website' => 'test1 ',
// 	'need_invoice' => -1,
// 	'extra_info' => 'test1 '
// );

// var_d($order->update($data));

##cancel
// var_d($order->cancel());

// var_d($order);

##get
// $order_data = $order->get_order_data();
// var_d($order_data);

// $order_data['order_id'] = 4;

// var_d($order->get_order_data());

// $attendees = $order->get_attendees();
// var_d($attendees);

// var_d($attendees[0]->update(array('title' => 'ttt')));

// var_d($order->get_attendees());

##generate_certificate
//var_d($order->generate_certificate());

##check_certificate
// $data = array(
// 	'email' => 'test1@test.com',
// 	'certificate_number' => 'e78f'
// 	);
// var_d($order->check_certificate($data));

##set tickets
// var_d($order->set_tickets());
// var_d($order);

##check pay
// var_d($order->check_pay());

##get_price
//var_d($order->get_price());

##success
// $order->set_order(7);

//var_d($order->success());

// var_d($order);

##send mail
// $order->set_order(12);

// $order->send_mail('order_create_notify');
// $order->send_mail('order_wait_approve_notify');
// $order->send_mail('order_approved_notify');
// $order->send_mail('order_success_notify_contact');
// $order->send_mail('order_success_notify_attendee');

// echo "order 12 is ok.<br/>";

// if ($_GET['s'] == 'sendemail') {

// 	$nonpay_orders = array(104,105,106,107,108,110,112);
// 	foreach ($nonpay_orders as $o) {
// 		$order->set_order($o);

// 		$order->send_mail('order_wait_approve_notify');
// 		$order->send_mail('order_success_notify_contact');
// 		$order->send_mail('order_success_notify_attendee');

// 		echo "order $o is ok : order_wait_approve_notify & order_success_notify_contact & order_success_notify_attendee.<br/>";

// 	}

// 	$approved_wait_payment_orders = array(96);
// 	foreach ($approved_wait_payment_orders as $o) {
// 		$order->set_order($o);

// 		$order->send_mail('order_wait_approve_notify');
// 		$order->send_mail('order_approved_notify');

// 		echo "order $o is ok : order_wait_approve_notify & order_approved_notify.<br/>";
// 	}

// 	$approved__paid_orders = array(98);
// 	foreach ($approved__paid_orders as $o) {
// 		$order->set_order($o);

// 		$order->send_mail('order_wait_approve_notify');
// 		$order->send_mail('order_approved_notify');
// 		$order->send_mail('order_success_notify_contact');
// 		$order->send_mail('order_success_notify_attendee');

// 		echo "order $o is ok : order_wait_approve_notify & order_approved_notify & order_success_notify_contact & order_success_notify_attendee.<br/>";
// 	}

// 	$noneedapprove_wait_for_payment_orders = array(109,111);
// 	foreach ($noneedapprove_wait_for_payment_orders as $o) {
// 		$order->set_order($o);

// 		$order->send_mail('order_create_notify');

// 		echo "order $o is ok : order_create_notify.<br/>";
// 	}
// }