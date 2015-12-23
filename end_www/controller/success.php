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
	die('Permission denied, you can not locate this page.');
}

if ($order->is_ok() && $order->is_charged() && $order->is_approved()) {
	$show_success = true;
	$button_html = '<a href="?p=ticket_choose&activity='.$activity_id.'&lang='.$lang.'&channel='.$channel.'" type="button" class="btn btn-info">'.$texts['button_finish'].'</a>'.	'&nbsp;&nbsp;&nbsp;&nbsp;<button id="return_home" class="btn btn-info" onclick="return_home(\''.$texts['home_url'].'\')">'.$texts['return_home'].'</button>';
}
else {
	$button_html = '<a href="?p=charge&activity='.$activity_id.'&lang='.$lang.'&order='.$_GET['order'].'&certificate='.$_GET['certificate'].'&channel='.$channel.'" type="button" class="btn btn-info">'.$texts['return_charge'].'</a>';
}

$view_data['show_success'] = $show_success;
$view_data['button_html'] = $button_html;

$view_data['order_data'] = $order->get_order_data();