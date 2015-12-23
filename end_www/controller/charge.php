<?php

include_once __DIR__."/ticket_include.php";

$view_data['title'] = 'Check & Pay';

if ( !isset($_GET['order']) 
	|| !isset($_GET['certificate']) ) {
	die('Page argument error.');
}

##验证链接地址
$order = new Order();

$set_ret = $order->set_order($_GET['order']);

if ($set_ret['r'] == 'error') {
	die('Argument order error.');
}

$url_code = $order->generate_url_code();

if ($_GET['certificate'] !== $url_code) {
	var_dump($url_code);
	die('Permission denied, you can not locate this page.');
}

$order_status = $order->is_ok();

//检查是否已支付
if ($order_status && $order->is_charged() && $order->is_approved()) {
	header('Location: ?p=success&activity='.$activity_id.'&order='.$_GET['order'].'&lang='.$lang.'&certificate='.$_GET['certificate'].'&channel='.$_GET['channel']);
}

$view_data['success_url'] = "?p=success&activity=".$activity_id."&order=".$_GET['order']."&lang=".$lang."&certificate=".$_GET['certificate']."&channel=".$channel;

$host_check_status = $order->is_approved();

$manage_notify = $order_status;

$payment_notify = $order_status;

$check_ret = $order->check_pay();
$pay = ($check_ret['r'] == 'error')?false:true;
$pay = ($host_check_status)?$pay:$host_check_status;
$button_status = ($order_status)?$pay:$order_status;

if ($button_status) {
	$button_html = $booking_activity->get_payment()->pay($order, $booking_activity, $lang);
}
else {
	$button_html = '<a href="?p=ticket_choose&activity='.$activity_id.'&lang='.$lang.'&channel='.$channel.'" type="button" class="btn btn-info">'.$texts['button_finish'].'</a>'.	'&nbsp;&nbsp;&nbsp;&nbsp;<button id="return_home" class="btn btn-info" onclick="return_home(\''.$texts['home_url'].'\')">'.$texts['return_home'].'</button>';
}

$view_data['order_status'] = $order_status;
$view_data['host_check_status'] = $host_check_status;
$view_data['payment_notify'] = $payment_notify;
$view_data['manage_notify'] = $manage_notify;
$view_data['button_status'] = $button_status;
$view_data['button_html'] = $button_html;
$view_data['order_data'] = $order->get_order_data();

$view_data['end_time'] = inttodate($order->get_end_time());