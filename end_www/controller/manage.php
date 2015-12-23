<?php 

include_once "manage_include.php";

function decode_phone($s) {
	$p = json_decode($s, true);
	if ($p['country_code'] == '' && $p['city_code'] == '' && $p['number'] == '') {
		return "";
	}
	else {
		return "+".$p['country_code']."-".$p['city_code']."-".$p['number'];
	}
}

function decode_telephone($s) {
	$p = json_decode($s, true);
	return "+".$p['country_code']."-".$p['number'];
}

$title = "Register Manage";
$view_data['title'] = $title;

if (isset($_GET['s']) && $_GET['s'] != '') {
	$data = array();
	$data['activity_id'] = $activity_id;
	$data['status'] = '1';
	$data['order'] = 'order_id DESC';

	$s = $_GET['s'];

	$json_s = json_encode($s);
	$json_s = str_replace('"', '%', $json_s);
	$json_s = str_replace('\u', '%', $json_s);
	$data['where'] = '(order_serial LIKE "%'.$s.'%" OR contact_name LIKE "%'.$s.'%" OR contact_email LIKE "%'.$s.'%" OR company_name LIKE "%'.$s.'%" OR company_name LIKE "'.$json_s.'" OR order_id in (SELECT order_id FROM end_wt_attendee_data where name LIKE "'.$json_s.'"))';

	$order_list = model('wt_order')->get_list($data);
}
else {
	$page = (isset($_GET['page']))?$_GET['page']:1;
	$offset = 20;
	$view_data['page'] = $page;
	$view_data['offset'] = $offset;

	$item_count = model('wt_order')->get_list(array('activity_id'=>$activity_id, 'status'=>'1', 'select'=>'count(1)'));
	$page_num = ceil(floatval($item_count[0]['count(1)'])/floatval($offset));
	$view_data['page_num'] = $page_num;

	$from = ($page-1)*$offset;
	$total = $offset;
	$order_list = model('wt_order')->get_list(array('activity_id' => $activity_id, 'status' => '1', 'from' => $from, 'total' => $total, 'order'=>'order_id DESC'));
}

$orders = array();
foreach ( $order_list  as $value) {
	$orders[$value['order_id']] = new Order();
	$orders[$value['order_id']]->set_order($value['order_id']); 
}
$view_data['orders'] = $orders;

$orders_contact_phone = array();
foreach ($orders as $key => $o) {
	$phone = json_decode($o->get_order_data('contact_phone'), true);
	$orders_contact_phone[$key] = $phone['country_code']."-".$phone['number'];
}
$view_data['orders_contact_phone'] = $orders_contact_phone;

$orders_company_name = array();
foreach ($orders as $key => $value) {
	$names = json_decode($value->get_order_data('company_name'), true);
	$orders_company_name[$key] = $names;
}
$view_data['orders_company_name'] = $orders_company_name;

$orders_tickets = array();
foreach ($orders as $key => $value) {
	$tickets = $value->get_tickets();
	$t = $tickets['tickets'][0]->get_ticket_data('name');
	$orders_tickets[$key]['name'] = $t['name']['zh'];
	$orders_tickets[$key]['quantity'] = $tickets['quantities'][0];
}
$view_data['orders_tickets'] = $orders_tickets;

$orders_amount = array();
foreach ($orders as $key => $value) {
	$price_ret = $value->get_price();
	$orders_amount[$key] = $price_ret['msg'];
}
$view_data['orders_amount'] = $orders_amount;

$orders_approve = array();
foreach ( $orders as $key => $o ) {
	switch ($o->get_check_status()) {
		case '-2':
			$orders_approve[$key] = '<span class="text-danger">待审核</span>';
			break;
		case '-1':
			$orders_approve[$key] = '<span class="text-muted">未通过</span>';
			break;
		case '1':
			$orders_approve[$key] = '已通过';
			break;
		case '2':
			$orders_approve[$key] = '不需审核';
			break;
		default:
			# code...
			break;
	}
}
$view_data['orders_approve'] = $orders_approve;

$orders_charge_status = array();
foreach ( $orders as $key => $o ) {
	$charge = $o->is_charged();
	$orders_charge_status[$key] = ($charge)?"已付款":"待付款";
}
$view_data['orders_charge_status'] = $orders_charge_status;

$orders_charge_time = array();
foreach ( $orders as $key => $o ) {
	$t = intval($o->get_order_data('charge_time'));
	if ($t != 0) {
		$orders_charge_time[$key] = date( "Y-m-d H:i:s" , $t);
	}
	else {
		$orders_charge_time[$key] = "";
	}
}
$view_data['orders_charge_time'] = $orders_charge_time;

$company_fields = model('wt_option')->get_list(array('key_name'=>'company_field'));
foreach ($company_fields as $key => $v) {
	$company_fields[$v['option_id']]['name'] = json_decode($v['name'], true);
}
$view_data['company_fields'] = $company_fields;