<?php
/*
 * Order Class
 * @author lidongxu @ 2014.5.26
 * 
 * 完成订单的生成、修改、取消、支付操作
 */

include_once __DIR__."/attendee.class.php";
include_once __DIR__."/ticket_type.class.php";
include_once __DIR__.'/email.class.php';

class Order {
	protected $order_data;
	protected $attendees;
	protected $tickets;
	protected $quantities;

	/*
	 * 构造函数
	 */
	public function __construct() {
		$this->order_data = array();
		$this->attendees = array();
		$this->tickets = array();
		$this->quantities = array();
	}

	/*
	 * 析构函数
	 */
	public function __destruct() {}

	/*
	 * create函数
	 *
	 * 创建参会者信息
	 */
	public function create($data) {
		$ret = array();

		##拷贝参会者信息
		$attendees = $data['attendees'];
		unset($data['attendees']);

		##去除提交数据中数组类型的值的无用空格
		foreach ($data as $d_key => $d_value) {
			if (is_array($d_value)) {
				foreach ($d_value as $key => $value) {
					$data[$d_key][$key] = trim($value);
				}
				$data[$d_key] = json_encode($data[$d_key]);
			}
		}

		##处理不需要的空格
		$data = filter_array($data, "trim:company_field_id,
			trim:activity_id!,
			tickets!,
			trim:contact_name,
			trim:contact_email!,
			trim:contact_phone,
			trim:company_name,
			trim:company_address,
			trim:company_zipcode,
			trim:company_phone,
			trim:company_fax,
			trim:company_website,
			trim:need_invoice,
			trim:invoice_title,
			trim:extra_info,
			language
			"
		);

		##检查数据
		if (!$data) {
			$ret['r'] = 'error';
			$ret['msg'] = '10001:Required fields can not be empty.';

			return $ret;
		}

		##检查Email格式
		if (!filter_var($data['contact_email'], FILTER_VALIDATE_EMAIL)) {
			$ret['r'] = 'error';
			$ret['msg'] = '20001:Email format error.';

			return $ret;
		}

		##检查company_field_id、activity_id
		if ( !model('wt_activity')->exists($data['activity_id']) 
			|| ( isset($data['company_field_id']) && !model('wt_option')->exists($data['company_field_id']))
			) {
			$ret['r'] = 'error';
			$ret['msg'] = '30001:Illegal data.';

			return $ret;
		}

		##检查tickets
		$tickets = json_decode($data['tickets'],true);
		foreach ($tickets as $ticket) {
			if (!model('wt_ticket_type')->exists($ticket['ticket_id'])) {
				$ret['r'] = 'error';
				$ret['msg'] = '30001:Illegal ticket id.';

				return $ret;
			}
		}

		##检查门票与人员数量是否匹配
		$quantity = array();
		//初始化订单show_attendee_info & charge_status标识
		$data['show_attendee_info'] = -1;
		$data['charge_status'] = 2;
		foreach ($tickets as $ticket) {
			$t = model('wt_ticket_type')->get_one($ticket['ticket_id']);
			if ($t['need_attendee_info'] == '1') {
				$quantity[$ticket['ticket_id']] = $ticket['quantity'];

				//存在门票需要参会者信息，修改show_attendee_info标识
				$data['show_attendee_info'] = 1;
			}
			if ($t['price'] != '0') {
				//存在门票需要缴费，修改charge_status标识
				$data['charge_status'] = -2;
			}
		}
		foreach ($attendees as $a) {
			$quantity[$a['ticket_id']]--;
		}
		if ( array_sum($quantity) != 0 ) {
			$ret['r'] = 'error';
			$ret['msg'] = '10002:The sum of attendees not match with the sum of tickets quantity.';

			return $ret;
		}

		##生成order serial
		$time_str = strval(strtotime('now'));
		$serial = $time_str.strval(rand(1000, 9999));
		while (model('wt_order')->exists(array('order_serial'=>$serial))) {
			$serial = $time_str.strval(rand(1000, 9999));
		}
		$data['order_serial'] = $serial;

		//初始化订单status
		$data['status'] = 1;
		##写入订单
		$insert_id = model('wt_order')->add($data);
		if (!$insert_id) {
			$ret['r'] = 'error';
			$ret['msg'] = '50001:数据插入失败';

			return $ret;
		}

		##订单写入成功
		$this->set_order_data($insert_id);

		##写入参会者信息
		//添加actitity id & order_id
		for ($i = 0; $i < count($attendees); $i++) {
			$attendees[$i]['activity_id'] = $data['activity_id'];
			$attendees[$i]['order_id'] = $this->order_data['order_id'];
		}
		//写入数据
		for ($i = 0; $i < count($attendees); $i++) {
			
			$this->attendees[$i] = new Attendee();
			
			$create_ret = $this->attendees[$i]->create($attendees[$i]);

			if ($create_ret['r'] == 'error') {
				##回滚参会者写入数据
				for ($j = 0; $j < $i; $j++) {
					$this->attendees[$j]->delete_attendee_data();
				}
				##回滚订单写入数据
				$this->delete_order_data();

				return $create_ret;
			}
		}

		##执行成功
		$this->refresh();

		$this->generate_certificate();

		//审核通过
		if ($this->is_approved()) {
			$this->send_mail('order_create_notify');
		}
		//待审核
		else {
			$this->send_mail('order_wait_approve_notify');
			$this->send_mail('host_approve_notify');
		}

		$ret['r'] = 'ok';
		$ret['msg'] = $this;

		return $ret;
	}

	/*
	 * update函数
	 * 
	 * 更新订单信息
	 */
	public function update($data) {
		$ret = array();

		##处理不需要的空格，并处理json格式数据
		foreach ($data as $d_key => $d_value) {
			if (is_array($d_value)) {
				foreach ($d_value as $key => $value) {
					$data[$d_key][$key] = trim($value);
				}
				$data[$d_key] = json_encode($data[$d_key]);
			}
			else {
				$data[$d_key] = trim($data[$d_key]);
			}
		}

		##检查数据
		if (!$data) {
			$ret['r'] = 'error';
			$ret['msg'] = '10001:Required fields can not be empty.';

			return $ret;
		}

		##检查Email格式
		if ( isset($data['contact_email'])
			&& !filter_var($data['contact_email'], FILTER_VALIDATE_EMAIL)) {
			$ret['r'] = 'error';
			$ret['msg'] = '20001:Email format error.';

			return $ret;
		}

		##检查company_field_id
		if ( isset($data['company_field_id']) 
			&& !model('wt_option')->exists($data['company_field_id']) ) {
			$ret['r'] = 'error';
			$ret['msg'] = '30001:Illegal company_field id.';

			return $ret;
		}

		##更新订单
		$update_ret = model('wt_order')->update( $this->order_data['order_id'] , $data);
		if (!$update_ret) {
			$ret['r'] = 'error';
			$ret['msg'] = '50002:数据更新失败';

			return $ret;
		}

		##订单写入成功
		$this->refresh();

		##执行成功
		$ret['r'] = 'ok';
		$ret['msg'] = $this;

		return $ret;
	}

	/*
	 * cancel函数
	 *
	 * 取消订单
	 */
	public function cancel() {
		$ret = array();

		if ($this->is_charged() && $this->is_approved()) {
			$ret['r'] = 'error';
			$ret['msg'] = '60002:The paid & approved order can not be canceled.';

			return $ret;
		}

		$update_ret = model('wt_order')->update($this->order_data['order_id'], array('status' => -1));
		if (!$update_ret) {
			$ret['r'] = 'error';
			$ret['msg'] = '50002:Data update failed.';

			return $ret;
		}

		##重新加载订单数据
		$this->set_order_data($this->order_data['order_id']);

		##修改用户状态
		for ($i = 0; $i < count($this->attendees); $i++) {
			$deactive_ret = $this->attendees[$i]->deactive();
		}

		$ret['r'] = 'ok';
		return $ret;
	}

	/*
	 * success
	 */
	public function success($data) {
		$ret = array();

		$data['charge_status'] = 1;
		$update_ret = model('wt_order')->update($this->order_data['order_id'], $data);
		if (!$update_ret) {
			$ret['r'] = 'error';
			$ret['msg'] = '50002:Data update failed.';

			return $ret;
		}

		for ($i=0; $i < count($this->attendees); $i++) {
			$active_ret = $this->attendees[$i]->active();

			if ($active_ret['r'] == 'error') {
				return $active_ret;
			}
		}

		$this->refresh();

		//发送邮件
		$this->send_mail('order_success_notify_contact');
		$this->send_mail('order_success_notify_attendee');
		$this->send_mail('order_success_notify_host');

		$ret['r'] = 'ok';
		return $ret;
	}

	/*
	 * set_order_data函数
	 *
	 * 根据order id加载order_data
	 */
	public function set_order_data($id) {
		$ret = array();

		$order = model('wt_order')->get_one($id);

		if ($order) {
			$order['tickets'] = json_decode($order['tickets'], true);
			$this->order_data = $order;

			$ret['r'] = 'ok';
		}
		else {
			$ret['r'] = 'error';
			$ret['msg'] = '30001:Illegal order id.';
		}

		return $ret;
	}

	/*
	 * get_order_data函数
	 *
	 * 返回order_data
	 */
	public function get_order_data($key = '') {
		if ($key == '') {
			return $this->order_data;
		}
		else {
			return $this->order_data[$key];
		}
	}

	/*
	 * delete_order_data函数
	 *
	 * 删除本订单数据
	 */
	protected function delete_order_data() {
		$ret = array();
		if ( !model('wt_order')->delete($this->order_data['order_id']) ) {
			$ret['r'] = 'error';
			$ret['msg'] = '50003:Order delete failed.';

			return $ret;
		}

		$this->order_data = array();

		$ret['r'] = 'ok';
		return $ret;
	}

	/*
	 * set_order函数
	 *
	 * 根据order id假造order_data信息以及attendees信息
	 */
	public function set_order($id) {
		$ret = array();

		$set_ret = $this->set_order_data($id);

		if ($set_ret['r'] == 'error') {
			return $set_ret;
		}

		if ($this->order_data['show_attendee_info'] == '1') {
			$attendee = new Attendee();
			$attendees_ret = $attendee->get_attendees_by_order($this->order_data['order_id']);

			if ($attendees_ret['r'] == 'error') {
				return $attendees_ret;
			}

			$this->attendees = $attendees_ret['msg'];
		}

		$tickets_ret = $this->set_tickets();

		if ($tickets_ret['r'] == 'error') {
			return $tickets_ret;
		}

		$ret['r'] = 'ok';

		return $ret;
	}

	/*
	 * get_attendees
	 * 
	 * 返回attendees
	 */
	public function get_attendees() {
		return $this->attendees;
	}

	/*
	 * set_tickets
	 */
	public function set_tickets() {
		$ret = array();

		$tickets = $this->order_data['tickets'];
		for ($i = 0; $i < count($tickets); $i++) {
			$this->tickets[$i] = new Ticket_Type();
			$set_ret = $this->tickets[$i]->set_ticket_data($tickets[$i]['ticket_id']);
			if ($set_ret['r'] == 'error') {
				return $set_ret;
			}

			$this->quantities[$i] = $tickets[$i]['quantity'];
		}

		$ret['r'] = 'ok';
		return $ret;
	}

	/*
	 * get_tickets
	 */
	public function get_tickets() {
		$ticket_quantity = array();
		$ticket_quantity['tickets'] = $this->tickets;
		$ticket_quantity['quantities'] = $this->quantities;
		return $ticket_quantity;
	}

	/*
	 * generate_certificate
	 *
	 * 生成用于该订单管理的验证信息
	 */
	public function generate_certificate() {
		$ret = array();

		##检查对应活动的对应联系人认证信息是否已存在
		$exists_ret = model('wt_certificate')->exists(array('activity_id'=>$this->order_data['activity_id'], 'email'=>strtolower($this->order_data['contact_email'])));
		if (!$exists_ret) {
			$certificate_number = $this->random_str();

			//写入数据
			$data = array(
				'activity_id' => $this->order_data['activity_id'],
				'email' => strtolower($this->order_data['contact_email']),
				'certificate_number' => $certificate_number,
				'status' => 1
				);
			$insert_ret = model('wt_certificate')->add($data);

			if (!$insert_ret) {
				$ret['r'] = 'error';
				$ret['msg'] = '50001:数据插入失败';

				return $ret;
			}
		}

		$ret['r'] = 'ok';
		return $ret;
	}

	/*
	 * generate_url_certificate()
	 */
	public function generate_url_code() {
		$activity_id = $this->get_order_data('activity_id');
		$contact_email = strtolower($this->get_order_data('contact_email'));
		$certificate = model('wt_certificate')->get_one(array('activity_id'=>$activity_id, 'email'=>$contact_email));
		$md5_str = $certificate['email'].$certificate['certificate_number'];

		return md5($md5_str);
	}

	/*
	 * get_certificate_number
	 */
	public function get_certificate_number() {
		$certificate = model('wt_certificate')->get_one(array('activity_id'=>$this->order_data['activity_id'], 'email'=>strtolower($this->order_data['contact_email'])));

		return $certificate['certificate_number'];
	}

	/*
	 * check_certificate
	 *
	 * 认证登陆用户
	 */
	public static function check_certificate($activity_id, $data = array()) {
		$ret = array();

		##去除不必要空格
		$data = filter_array($data, "trim:email!,
			trim:certificate_number!
			");

		##检查数据
		if (!$data) {
			$ret['r'] = 'error';
			$ret['msg'] = '10001:Required fields can not be empty.';

			return $ret;
		}

		$data['activity_id'] = $activity_id;
		$data['status'] = 1;
		$exists_ret = model('wt_certificate')->exists($data);

		if (!$exists_ret) {
			$ret['r'] = 'error';
			$ret['msg'] = '40001:Certificate failed.';

			return $ret;
		}

		$ret['r'] = 'ok';

		return $ret;
	}

	/*
	 * get_price
	 *
	 * 获取订单的价格
	 */
	public function get_price() {
		$ret = array();

		$amount = floatval(0);
		for ($i=0; $i < count($this->tickets); $i++) { 
			$amount += $this->tickets[$i]->get_amount($this->quantities[$i]);
		}

		$ret['r'] = 'ok';
		$ret['msg'] = $amount;

		return $ret;
	}

	/*
	 * check_pay
	 *
	 * 检查能否支付
	 */
	public function check_pay() {
		$ret = array();

		##检查是否已支付
		if ($this->order_data['charge_status'] == '1') {
			$ret['r'] = 'error';
			$ret['msg'] = 'This order has been paid.';

			return $ret;
		}

		##检查票量与门票时间
		for ($i=0; $i < count($this->tickets); $i++) {
			$check_ret = $this->tickets[$i]->check_ticket();
			if ($check_ret['r'] == 'error') {
				return $check_ret;
			}
		}

		$ret['r'] = 'ok';
		return $ret;
	}

	/*
	 * refresh
	 */
	public function refresh() {
		return $this->set_order($this->order_data['order_id']);
	}

	/*
	 * random_str
	 *
	 * 生成制定长度由制定字符组成的随机字符串
	 */
	protected function random_str( $length = 4 ) {  
		// 密码字符集，可任意添加你需要的字符  
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';  
		$random_str = '';  
		for ( $i = 0; $i < $length; $i++ ) {  
		// 这里提供两种字符获取方式  
		// 第一种是使用 substr 截取$chars中的任意一位字符；  
		// 第二种是取字符数组 $chars 的任意元素  
		// $password .= substr($chars, mt_rand(0, strlen($chars) – 1), 1);  
		$random_str .= $chars[ mt_rand(0, strlen($chars) - 1) ];  
		}  
		return $random_str; 
	} 

	/*
	 * get_order_id
	 */
	public function get_order_id() {
		return $this->order_data['order_id'];
	}

	/*
	 * get_lang
	 */
	public function get_lang() {
		return $this->order_data['language'];
	}

	/*
	 * is_ok
	 */
	public function is_ok() {
		if ($this->order_data['status'] == '1') {
			return true;
		}
		else {
			return false;
		}
	}

	/*
	 * approve
	 */
	public function approve($pass = true) {
		$ret = array();

		foreach ($this->attendees as $atd) {
			$approve_ret = $atd->approve($pass);
			if ($approve_ret['r'] == 'error') {
				return $approve_ret;
			}
		}
		$ret['r'] = 'ok';

		if ($pass) {
			//发送邮件
			if (!$this->is_charged()) {
				$this->send_mail('order_approved_notify');
			}
			else {
				$data = array(
					'charge_time' => time(),
					'charge_amount' => 0
					);
				$s_ret = $this->success($data);
				if ($s_ret['r'] == 'error') {
					return $s_ret;
				}
			}
		}

		return $ret;
	}

	/*
	 * is_approved
	 */
	public function is_approved() {
		$attendees = $this->attendees;
		foreach ($attendees as $attendee) {
			if (!$attendee->is_approved()) {
				return false;
			}
		}
		return true;
	}

	/* 
	 * get_check_status
	 */
	public function get_check_status() {
		$attendees = $this->attendees;

		foreach ($attendees as $attendee) {
			if ($attendee->is_not_pass()) {
				return -1;
			}
		}

		foreach ($attendees as $attendee) {
			if (!$attendee->is_approved()) {
				return -2;
			}
		}

		foreach ($attendees as $attendee) {
			if ($attendee->is_need_approve()) {
				return 1;
			}
		}

		return 2;
	}

	/*
	 * get_end_time
	 */
	public function get_end_time() {
		$tickets_with_quantity = $this->get_tickets();

		$tickets = $tickets_with_quantity['tickets'];
		foreach ($tickets as $t) {
			if (!isset($end_time) || $end_time > $t->get_end_time()) {
				$end_time = $t->get_end_time();
			}
		}

		return $end_time;
	}

	/*
	 * is_charged
	 */
	public function is_charged() {
		if ($this->order_data['charge_status'] == '1' || $this->order_data['charge_status'] == '2') {
			return true;
		}
		else {
			return false;
		}
	}

	/*
	 * get_activity
	 */
	public function get_activity() {
		$activity_ret = model('wt_activity')->get_one($this->get_order_id());

		$activity = new Activity();
		$activity->set_activity($activity_ret['activity_id']);

		return $activity;
	}

	/*
	 * send_mail
	 */
	public function send_mail($key) {
		##发送邮件
		$activity = new Activity();
		$activity->set_activity($this->get_order_data('activity_id'));

		$user = User::instance();
		$user->set_user_data($activity->get_activity_data('user_id'));

		$activity_name = $activity->get_activity_data('name');
		$email = new Email($activity_name[$this->get_lang()]);

		$mail_data = array();
		$mail_data['contact_name'] = $this->get_order_data('contact_name');
		$mail_data['order_serial'] = $this->get_order_data('order_serial');
		$mail_data['certificate_number'] = $this->get_certificate_number();
		$company_name = json_decode($this->get_order_data('company_name'), true);
		$mail_data['company_name'] = $company_name[$this->get_order_data('language')];
		$order_url = 'http://'.$_SERVER['SERVER_NAME'].'/?p=charge&activity='.$activity->get_activity_data('activity_id').'&order='.$this->get_order_id().'&lang='.$this->get_lang().'&certificate='.$this->generate_url_code();
		//返回网站网址
		$res_url = array(
			'zh' => "http://www.chatchina.com.cn/category/registration/?rel=",
			'en' => 'http://en.chatchina.com.cn/category/registration/?rel='
			);
		$mail_data['order_url'] = $res_url[$this->get_order_data('language')].urlencode($order_url);

		switch ($key) {
			//发送联系人
			case 'order_create_notify':
				$email->send_template_mail($activity->get_activity_data($key), $mail_data, $this->get_order_data('contact_email'));
				break;
			//发送联系人
			case 'order_wait_approve_notify':
				$email->send_template_mail($activity->get_activity_data($key), $mail_data, $this->get_order_data('contact_email'));
				break;
			//发送主办方
			case 'host_approve_notify':
				$email->send_template_mail($activity->get_activity_data($key), $mail_data, $user->get_user_data('email'));
				break;
			//发送联系人
			case 'order_approved_notify':
				$email->send_template_mail($activity->get_activity_data($key), $mail_data, $this->get_order_data('contact_email'));
				break;
			//发送联系人
			case 'order_success_notify_contact':
				$email->send_template_mail($activity->get_activity_data($key), $mail_data, $this->get_order_data('contact_email'));
				break;
			//发送参会者
			case 'order_success_notify_attendee':
				foreach ($this->attendees as $atd) {
					$name = $atd->get_attendee_data('name');
					$name = json_decode($name, true);
					$name['en'] = json_decode($name['en'], true);
					$mail_data['name_zh'] = $name['zh'];
					$mail_data['name_en_firstname'] = $name['en']['firstname'];
					$mail_data['name_en_lastname'] = $name['en']['lastname'];

					$email->send_template_mail($activity->get_activity_data($key), $mail_data, $atd->get_attendee_data('email'));
				}
				break;
			//发送主办方
			case 'order_success_notify_host':
				$email->send_template_mail($activity->get_activity_data($key), $mail_data, $user->get_user_data('email'));
				break;
			default:
				break;
		}
	}
}