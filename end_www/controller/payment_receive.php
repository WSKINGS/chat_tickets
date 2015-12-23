<?php
/*
 * payment_receive.php
 *
 * 接收支付消息
 *
 * @author lidongxu @2014-5-29
 */

if (isset($_GET['activity']) && isset($_POST)) {

	$activity = new Activity();

	$activity->set_activity($_GET['activity']);

	$activity->get_payment()->receive_result($_POST);
}
