<?php
/*
 * Payment Yoopay Class
 * @author lidongxu @ 2014.5.28
 * 
 * 定义友付支付渠道
 */

include_once __DIR__."/../payment_channel.interface.php";

class Payment_yoopay implements Payment_Channel {
	private $name = 'Yoopay';
	private $key_name = 'yoopay';

	private $pay_url = "https://yoopay.cn/yapi";
	private $seller_email = 'lluo@horwathhtl.com';
	#测试支付
	private $sandbox = '0';
	#预期结果
	private $sandbox_target_status = '1';
	#app key
	private $app_key = 'db498a47ef059f7ede9beadfbceb7d13';
	
	/*
	 * 构造函数
	 */
	public function __construct() {
	}
	
	public function pay(Order $order, Activity $activity, $lang) {
		$order_data = $order->get_order_data();
		$activity_data = $activity->get_activity_data();

		$data = array();

		$data['seller_email'] = $this->seller_email;
		//$data['language'] = $order_data['language'];
		$data['language'] = $lang;
		$data['type'] = "CHARGE";
		$data['tid'] = $order_data['order_serial'];
		
		$t_lang = array(
			'zh' => '门票',
			'en' => 'tickets'
			);
		//$data['item_name'] = $activity_data['name'][$order_data['language']]." ".$t_lang[$order_data['language']];
		$data['item_name'] = $activity_data['name'][$lang]." ".$t_lang[$lang];

		$item_body_str = "";
		$tqs = $order->get_tickets();
		$tickets = $tqs['tickets'];
		$quantities = $tqs['quantities'];
		for ($i=0; $i<count($tickets); $i++) {
			$ticket_data = $tickets[$i]->get_ticket_data();
			//$item_body_str .= $quantities[$i]." ".$ticket_data['name'][$order_data['language']]."<br/>";
			$item_body_str .= $quantities[$i]." ".$ticket_data['name'][$lang]."<br/>";
		}
		$price_ret = $order->get_price();
		if ($price_ret['r'] == 'error') {
			return $price_ret;
		}
		$price = $price_ret['msg'];
		$item_body_str .= "￥".$price;
		$data['item_body'] = $item_body_str;

		$data['item_price'] = $price;
		$data['item_currency'] = "CNY";
		$data['payment_method'] = "1;2;3;4;5;6";

		$data['customer_name'] = $order_data['contact_name'];
		$data['customer_email'] = $order_data['contact_email'];

		$data['notify_url'] = "http://chattkt.dreamgram.com/?p=payment_receive&activity=".$activity_data['activity_id'];

		$order_url = 'http://'.$_SERVER['SERVER_NAME'].'/?p=charge&activity='.$activity->get_activity_data('activity_id').'&order='.$order->get_order_data('order_id').'&lang='.$order->get_lang().'&certificate='.$order->generate_url_code();
		$res_url = array(
			'zh' => "http://www.chatchina.com.cn/category/registration/?rel=",
			'en' => 'http://en.chatchina.com.cn/category/registration/?rel='
			);
		$data['return_url'] = $res_url[$order->get_order_data('language')].urlencode($order_url);
		
		$data['invoice'] = '0';

		$data['sandbox'] = $this->sandbox;
		$data['sandbox_target_status'] = $this->sandbox_target_status;

		#签名
		$sign_str = $this->app_key.$data['seller_email'].$data['tid'].$data['item_price'].$data['item_currency'].$data['notify_url'].$data['sandbox'].$data['invoice'];

		$sign_str = strtoupper($sign_str);

		$data['sign'] = md5($sign_str);


		return $this->generate_html($data);
	}

	protected function generate_html($data) {
		$ret = array();

		$pay_html = "";

		$pay_html .= '<form id="pay_form" action="'.$this->pay_url.'" method="POST" target="_blank">';
		$pay_html .= '<div style="display:none">';

		foreach ($data as $key => $value) {
			$pay_html .= '<input name="'.$key.'" value="'.$value.'" />';
		}

		$pay_html .= "</div>";

		$btn_text = array(
			'zh' => '立即付款',
			'en' => 'Pay Now'
			);
		$pay_html .= '<button id="yoopay_btn" type="submit" class="btn btn-success" onclick="pay_js()">'.$btn_text[$data['language']].'</button>';
		$pay_html .= "</form>";

		return $pay_html;
	}

	public function receive_result($data) {
		// $data = filter_array($data,"yapi_tid,
		// tid,
		// item_price,
		// item_currency,
		// result_status,
		// result_desc,
		// type,
		// sign,
		// sandbox,
		// invoice,
		// invoice_title,
		// invoice_recipient,
		// invoice_phone,
		// invoice_mailing_address,
		// invoice_city,
		// invoice_postal_code
		// ");

		$msg = date("Y-m-d H:i:s", strtotime('now'))." : ".json_encode($data)."\r\n";
		error_log($msg, 3, __DIR__."/../../../../public/payment_log/yoopay.log");

		$sign_str = $this->app_key.$data['yapi_tid'].$data['tid'].$data['item_price'].$data['item_currency'].$data['result_status'].$data['type'].$data['sandbox'];
		$sign_str = strtoupper($sign_str);

		$sign_check = md5($sign_str);

		if ($data['sign'] == $sign_check)
		{
			#付款成功
			if ($data['result_status'] == '1')
			{
				$for_order = model('wt_order')->get_one(array('order_serial'=>$data['tid']));
				if ($for_order)
				{
					//处理支付成功订单
					$order = new Order();
					$set_ret = $order->set_order($for_order['order_id']);
					if ($set_ret['r'] == 'error') {
						active_error_log($data['tid']);
					}
					$charge_data['charge_time'] = time();
					$charge_data['charge_amount'] = $data['item_price'];
					$ret = $order->success($charge_data);
					if ($ret['r'] == 'error') {
						active_error_log($data['tid']);
					}
				}
			}

			die("SUCCESS");
		}
	}

	protected function active_error_log($tid) {
		$msg = date("Y-m-d H:i:s", strtotime('now'))." : Order ".$tid." active failed.\r\n";
		error_log($msg, 3, __DIR__."/../../../../public/active_error.log");
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