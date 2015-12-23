<?php
/*
 * 响应订票页面的各类jquery请求
 */

include_once __DIR__."/ticket_include.php";

if ($_GET['do'] == 'get_price') {
	$ret = array();
	
	if (!isset($_GET['ticket']) || !isset($_GET['quantity'])) {
		$ret['r'] = 'error';
		$ret['msg'] = '10001:Argument error.';

		die(json_encode($ret));
	}

	$ticket_ret = $booking_activity->get_ticket_by_id($_GET['ticket']);

	if ($ticket_ret['r'] == 'error') {
		die(json_encode($ticket_ret));
	}

	$ticket = $ticket_ret['msg'];

	$ret['r'] = 'ok';
	$ret['msg'] = $ticket->get_amount($_GET['quantity']);

	die(json_encode($ret));
}

if ($_GET['do'] == 'check_ticket') {
	$ret = array();

	$need_invoice = isset($_POST['need_invoice'])?1:-1;

	$tickets = $_POST['ticket'];

	$check_ret = check_tickets($tickets);

	if ($check_ret['r'] == 'error') {
		return $check_ret;
	}

	$ticket = $check_ret['msg'];

	$ret['r'] = 'ok';
	$ret['msg'] = '?p=submit_info&activity='.$_GET['activity'].'&ticket='.$ticket['ticket_id'].'&quantity='.$ticket['quantity'].'&need_invoice='.$need_invoice.'&lang='.$lang.'&channel='.$channel;

	die(json_encode($ret));
}

if ($_GET['do'] == 'certificate') {
	$ret = array();

	if ( !isset($_POST) ) {
		$ret['r'] = 'error';
		$ret['msg'] = '10001:Argument error.';

		die(json_encode($ret));
	}

	$certificate_ret = Order::check_certificate($activity_id, $_POST);

	if ($certificate_ret['r'] == 'error') {
		die(json_encode($certificate_ret));
	}

	$ret['r'] = 'ok';
	$_SESSION['booking_user'] = trim($_POST['email']);
	$ret['msg'] = '?p=ticket_manage&activity='.$activity_id.'&lang='.$lang.'&channel='.$channel;

	die(json_encode($ret));
}

if ($_GET['do'] == 'exit_manage') {
	$ret = array();
	unset($_SESSION['booking_user']);

	$ret['r'] = 'ok';
	$ret['msg'] = "?p=ticket_choose&activity=".$activity_id."&lang=".$lang."&channel=".$channel;

	die(json_encode($ret));
}

if ($_GET['do'] == 'submit_info') {
	$ret = array();

	//检查post数据是否为空
	if (!isset($_POST)) {
		$ret['r'] = 'error';
		$ret['msg'] = '10001:Post data can not be empty';

		die(json_encode($ret));
	}

	##数据检查参数单
	$order_config = array(
		'contact_name' => array(
			'operate' => 'trim',
			'required' => 'is_require'
			),
		'invoice_title' => array(
			'operate' => 'trim',
			'required' => 'besides',
			'required_argument' => true
			),
		'contact_eamil' => array(
			'operate' => 'trim',
			'required' => 'is_require'
			),
		'contact_phone' => array(
			'operate' => 'trim',
			'required' => 'is_require'
			),
		'company_field_id' => array(
			'operate' => 'trim',
			'required' => 'is_require'
			),
		'company_name' => array(
			'operate' => 'trim',
			'required' => 'is_require'
			),
		'company_address' => array(
			'operate' => 'trim',
			'required' => 'is_require'
			),
		'company_zipcode' => array(
			'operate' => 'trim',
			'required' => 'not_require'
			),
		'company_phone' => array(
			'operate' => 'trim',
			'required' => 'is_require'
			),
		'company_fax' => array(
			'operate' => 'trim',
			'required' => 'not_require'
			),
		'company_website' => array(
			'operate' => 'trim',
			'required' => 'not_require'
			),
		'company_phone' => array(
			'operate' => 'trim',
			'required' => 'is_require'
			),
		'tickets' => array(
			'operate' => 'trim',
			'required' => 'is_require'
			),
		'need_invoice' => array(
			'operate' => 'trim',
			'required' => 'is_require'
			),
		);

	##参会者数据检查参数单
	$attendee_config = array(
		'name' => array(
			'operate' => 'trim',
			'required' => 'is_require'
			),
		'title' => array(
			'operate' => 'trim',
			'required' => 'is_require'
			),
		'eamil' => array(
			'operate' => 'trim',
			'required' => 'is_require'
			),
		'telephone' => array(
			'operate' => 'trim',
			'required' => 'is_require'
			),
		'phone' => array(
			'operate' => 'trim',
			'required' => 'is_require'
			),
		'extra_info' => array(
			'operate' => 'trim',
			'required' => 'not_require'
			)
		);

	$order_data = $_POST;
	//写入数据库数据
	$write_order = array();

	//处理参会者数据
	if (isset($order_data['attendee'])) {
		$attendees_data = $order_data['attendee'];
		unset($order_data['attendee']);
	}
	else {
		$attendees_data = array();
	}

	//检查门票
	$check_ret = check_tickets(array($order_data['tickets'][0]['ticket_id'] => $order_data['tickets'][0]['quantity']));
	if ($check_ret['r'] == 'error') {
		die(json_encode($check_ret));
	}

	$ticket_id = $order_data['tickets'][0]['ticket_id'];
	$order_data['tickets'] = json_encode($order_data['tickets']);

	##检查订单数据
	//检查不为空数据
	$check_ret = check_data($order_data, $order_config);
	if ($check_ret['r'] == 'error') {
		die(json_encode($check_ret));
	}
	$write_order = $order_data;
	$write_order['activity_id'] = $activity_id;
	$write_order['language'] = $lang;

	##检查参会者数据
	foreach ($attendees_data as $key => $attendee_data) {
		//处理firstname & lastname
		$attendee_data['name']['en']['firstname'] = trim($attendee_data['name']['en']['firstname']);
		$attendee_data['name']['en']['lastname'] = trim($attendee_data['name']['en']['lastname']);
		//检查firstname & lastname
		if ($attendee_data['name']['en']['firstname'] == ''
			|| $attendee_data['name']['en']['lastname'] == '') {
			$ret['r'] = 'error';
			$ret['msg'] = '10001:'.$texts['required_field_empty'];

			die(json_encode($ret));
		}
		else {
			$attendees_data[$key]['name']['en'] = json_encode($attendees_data[$key]['name']['en']);
		}
		//检查attendee数据
		$check_ret = check_data($attendees_data[$key], $attendee_config);

		if ($check_ret['r'] == 'error') {
			die(json_encode($check_ret));
		}
		else {
			$attendees_data[$key] = $check_ret['msg'];
		}

		##检查email
		foreach ($attendees_data as $k => $v) {
			if ( ($attendee_data['email'] == $v['email']) 
			&& ($key != $k) ) {
				$ret['r'] = 'error';
				$ret['msg'] = '40002:'.$texts['email_repeat_error_1'];

				die(json_encode($ret));
			}
		}
		$check_ret = Attendee::check_email_exists($activity_id, $attendee_data['email']);
		if ($check_ret['r'] == 'error') {
			$ret['r'] = 'error';
			$ret['msg'] = '40002:'.$attendee_data['email'].' '.$texts['email_repeat_error_2'];

			die(json_encode($ret));
		}

		$attendees_data[$key]['ticket_id'] = $ticket_id;
		$attendees_data[$key]['company'] = $order_data['company_name'];
	}

	$write_order['attendees'] = $attendees_data;

	$order = new Order();
	$create_ret = $order->create($write_order);

	if ($create_ret['r'] == 'error') {
		die(json_encode($create_ret));
	}

	$order->generate_certificate();

	$url_code = $order->generate_url_code();

	$ret['r'] = 'ok';
	$order_data = $order->get_order_data();
	$ret['msg'] = "?p=charge&activity=".$activity_id."&order=".$order_data['order_id']."&lang=".$order_data['language']."&certificate=".$url_code."&channel=".$channel;

	die(json_encode($ret));
}