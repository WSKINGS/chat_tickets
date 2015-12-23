<?php

include_once __DIR__."/ticket_include.php";

if (!isset($_GET['ticket']) || !isset($_GET['quantity']) || !isset($_GET['need_invoice'])) {
	die('Page argument error.');
}

$view_data['title'] = 'Submit Information';

$ticket_id = $_GET['ticket'];
$quantity = $_GET['quantity'];
$need_invoice = $_GET['need_invoice'];
$view_data['ticket_id'] = $ticket_id;
$view_data['quantity'] = $quantity;
$view_data['need_invoice'] = $need_invoice;

if ($quantity == '0') {
	$ret['r'] = 'error';
	$ret['msg'] = '30001:Ticket quantity can not be zero.';

	die(json_encode($ret));
}

$ticket_ret = $booking_activity->get_ticket_by_id($ticket_id);

if ($ticket_ret['r'] == 'error') {
	die(json_encode($ticket_ret));
}

$ticket = $ticket_ret['msg'];

$check_ret = $ticket->check_ticket($quantity);
if ($check_ret['r'] == 'error') {
	die(json_encode($check_ret));
}

if ($ticket->is_need_attendee_info()) {
	$attendee_form_number = $quantity;
}
$view_data['attendee_form_number'] = $attendee_form_number;

$company_fields = model('wt_option')->get_list(array('key_name'=>'company_field', 'status'=>'1', 'order' => 'order_id asc'));
for ($i=0; $i < count($company_fields); $i++) {
	$company_fields[$i]['name'] = json_decode($company_fields[$i]['name'], true);
}
$view_data['company_fields'] = $company_fields;