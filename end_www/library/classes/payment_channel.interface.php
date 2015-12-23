<?php
/*
 * Payment Channel Interface
 * @author lidongxu @ 2014.5.28
 * 
 * 定义支付渠道接口
 */

include_once __DIR__."/order.class.php";
include_once __DIR__."/activity.class.php";

interface Payment_Channel {
	public function pay(Order $order, Activity $activity, $lang);
	public function receive_result($data);
	public function register();
}