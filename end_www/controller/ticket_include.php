<?php
include_once __DIR__."/booking_text.php";

$texts = $texts[$lang];
$view_data['texts'] = $texts;

function check_tickets($datas = array()) {
	$ret = array();
	global $booking_activity;

	//检查门票数量
	if (array_sum($datas) == '0') {
		$ret['r'] = 'error';
		$ret['msg'] = '30001:Ticket quantity can not be zero.';

		return $ret;
	}

	foreach ($datas as $key => $value) {
		if ($value != '0') {
			$ticket_id = $key;
			$quantity = $value;
			break;
		}
	}

	$ticket_ret = $booking_activity->get_ticket_by_id($ticket_id);

	if ($ticket_ret['r'] == 'error') {
		return $ticket_ret;
	}

	$ticket = $ticket_ret['msg'];

	$check_ret = $ticket->check_ticket($quantity);
	if ($check_ret['r'] == 'error') {
		return $check_ret;
	}

	$ret['r'] = 'ok';
	$ret['msg'] = array(
		'ticket_id' => $ticket_id,
		'quantity' => $quantity
		);

	return $ret;
}

function is_require() {
	return true;
}

function not_require() {
	return false;
}

function besides($data = array()) {
	if ($data['need_invoice'] == '1') {
		return true;
	}
	else {
		return false;
	}
}

function email_format_check($str) {
	return filter_var( $str, FILTER_VALIDATE_EMAIL );
}

function check_data($data = array(), $config = array()) {
	$ret = array();

	global $texts;

	foreach ($data as $key => $value) {
		if (is_array($value)) {
			foreach ($value as $k => $v) {
				if ($config[$key]['operate'] != '') {
					$funcs = explode(',', $config[$key]['operate']);
					foreach ($funcs as $func) {
						$data[$key][$k] = call_user_func($func, $v);
					}
				}
				if ($config[$key]['required'] != '') {
					if ($config[$key]['required_argument']) {
						$required = call_user_func($config[$key]['required'], $data);
					}
					else {
						$required = call_user_func($config[$key]['required']);
					}

					if ($required) {
						if ($data[$key][$k] == '') {
							$ret['r'] = 'error';
							$ret['msg'] = '10001:'.$texts['required_field_empty'];

							return $ret;;
						}
					}
				}
			}
		}
		else {
			if ($config[$key]['operate'] != '') {
				$funcs = explode(',', $config[$key]['operate']);
				foreach ($funcs as $func) {
					$data[$key] = call_user_func($func, $value);
				}
			}
			if ($config[$key]['required'] != '') {
				if ($config[$key]['required_argument']) {
					$required = call_user_func($config[$key]['required'], $data);
				}
				else {
					$required = call_user_func($config[$key]['required']);
				}

				if ($required) {
					if ($data[$key] == '') {
						$ret['r'] = 'error';
						$ret['msg'] = '10001:'.$texts['required_field_empty'];

						return $ret;
					}
				}
			}
		}
	}

	$ret['r'] = 'ok';
	$ret['msg'] = $data;

	return $ret;
}