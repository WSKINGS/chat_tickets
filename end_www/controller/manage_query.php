<?php

include_once "manage_include.php";

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

if ($_GET['do'] == 'approve') {
	$ret = array();
	
	$order_id = $_GET['order'];
	$order = new Order();
	$order->set_order($order_id);

	if ($_GET['pass'] == 'true') {
		$approve_ret = $order->approve(true);

		die(json_encode($approve_ret));
	}
	else {
		$approve_ret = $order->approve(false);

		die(json_encode($approve_ret));
	}
}

if ($_GET['do'] == 'delete') {
    $ret = array();
    
    $order_id = $_GET['order'];
    $order = new Order();
    $order->set_order($order_id);

    $ret = $order->cancel();

    die(json_encode($ret));
}

if ($_GET['do'] == 'update_order') {
    $ret = array();
    if (!isset($_GET['order'])) {
        $ret['r'] = 'error';
        $ret['msg'] = '10001:Argument error.';

        die(json_encode($ret));
    }
    $order_id = $_GET['order'];

    $order = new Order();
    $order->set_order($order_id);

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
        'contact_email' => array(
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
            )
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
    //禁止修改联系人email
    // if (isset($order_data['contact_email'])) {
    //     unset($order_data['contact_email']);
    // }
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

    ##检查订单数据
    //检查不为空数据
    $check_ret = check_data($order_data, $order_config);
    if ($check_ret['r'] == 'error') {
        die(json_encode($check_ret));
    }
    $write_order = $order_data;

    ##检查参会者数据
    //取出参会者
    $attendees = $order->get_attendees();
    if (count($attendees_data) != count($attendees)) {
        $ret['r'] = 'error';
        $ret['msg'] = '30001:Illegal data.';

        die(json_encode($ret));
    }
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
        // foreach ($attendees_data as $k => $v) {
        //     if ( ($attendee_data['email'] == $v['email']) 
        //     && ($key != $k) ) {
        //         $ret['r'] = 'error';
        //         $ret['msg'] = '40002:'.$texts['email_repeat_error_1'];

        //         die(json_encode($ret));
        //     }
        // }
        // $check_ret = Attendee::check_email_exists($activity_id, $attendee_data['email']);
        // if ($check_ret['r'] == 'error') {
        //     $ret['r'] = 'error';
        //     $ret['msg'] = '40002:'.$attendee_data['email'].' '.$texts['email_repeat_error_2'];

        //     die(json_encode($ret));
        // }

        ##禁止修改email
        // if (isset($attendees_data[$key]['email'])) {
        //     unset($attendees_data[$key]['email']);
        // }

        $attendees_data[$key]['company'] = $order_data['company_name'];
    }

    //更新订单
    $update_ret = $order->update($write_order);
    if ($update_ret['r'] == 'error') {
        die(json_encode($update_ret));
    }

    //更新参会者
    for ($i=0; $i < count($attendees_data); $i++) { 
        $update_ret = $attendees[$i]->update($attendees_data[$i]);
        if ($update_ret['r'] == 'error') {
            die(json_encode($update_ret));
        }
    }

    $ret['r'] = 'ok';

    die(json_encode($ret));
}