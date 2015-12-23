<?php

$ret = array();

if ( !isset($_GET['order']) 
	|| !isset($_GET['price']) ) {
	$ret['r'] = 'error';
	$ret['msg'] = 'Page argument error.';
	die(json_encode($ret));
}

$order = new Order();
$order_id=$_GET['order'];
$price = $_GET['price'];
$ret = $order->set_order($order_id);
if ($ret['r'] == 'error') {
	die(json_encode($ret));
}

$charge_data['charge_time'] = time();
$charge_data['charge_amount'] = $price;//$data['item_price'];
$ret = $order->success($charge_data);
if ($ret['r'] == 'error') {
	die(json_encode($ret));
}
die(json_encode($ret));