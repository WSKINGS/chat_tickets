<?php
/*
 * Payment HorwathPayment Class
 * @author lidongxu @ 2014.5.28
 * 
 * 定义友付支付渠道
 */

include_once __DIR__."/../payment_channel.interface.php";

class Payment_horwath implements Payment_Channel {
	private $name = 'Horwath Pay';
	private $key_name = 'horwath';

	/*
	 * 构造函数
	 */
	public function __construct() {
	}

	public function pay(Order $order, Activity $activity, $lang) {
		$activity_data = $activity->get_activity_data();
		$info = model('wt_payment_info')->get_one(array('activity_id' => $activity_data['activity_id']));
		if (!$info) {
			$ret['r'] = 'error';
			$ret['msg'] = '50004:Payment Info get failed.';
		}
		
		$data = array();
		$data['payment_info'] = ($lang == 'en') ? $info['info_en']: $info['info_zh'];

		return $this->generate_html($data);
	}

	protected function generate_html($data) {
		return $data['payment_info'];
	}

	public function receive_result($data) {
	}

	public function register() {
		$ret = array();

		$data = array(
			'name' => $this->name,
			'key_name' => $this->key_name	
			);

		$exists_ret = model('wt_payment_channel')->exists($data);

		if (!$exists_ret) {
			##写入数据
			$data['status'] = 1;
			$add_ret = model('wt_payment_channel')->add($data);
			if (!$add_ret) {
				$ret['r'] = 'error';
				$ret['msg'] = '50001:Data insert failed.';

				return $ret;
			}

			$ret['r'] = 'ok';

			return $ret;
		}

		$ret['r'] = 'ok';
		$ret['msg'] = 'This payment has regitered.';
	}
}