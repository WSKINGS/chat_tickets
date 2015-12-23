<?php
/*
 * Payment_Creator Class
 *
 * @author lidongxu @ 2014.5.28
 * 
 * 定义支付对象实例化工厂
 */

class Payment {
	public static function get_payment($key) {
		if (include_once 'payments/' . $key . '.class.php') {
            $payment = 'Payment_' . $key;
            return new $payment;
        } 
        else {
            throw new Exception ('Payment not found');
        }
	}
}