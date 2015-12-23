<?php
/*
 * Activity Class
 * @author lidongxu @ 2014.5.28
 * 
 * 完成活动的创建、修改、审核操作
 */

include_once __DIR__."/ticket_type.class.php";

class Activity {
	protected $activity_data;
	protected $tickets;
	protected $payment;

	/*
	 * 构造函数
	 */
	public function __construct() {
		$this->activity_data = array();
		$this->tickets = array();

		$this->load_payment();
	}

	/*
	 * 析构函数
	 */
	public function __destruct() {}

	/*
	 * set_activity_data
	 *
	 * 根据activity id加载activity_data
	 */
	public function set_activity_data($id) {
		$ret = array();

		$activity = model('wt_activity')->get_one($id);

		if ($activity) {
			$activity['name'] = json_decode($activity['name'], true);
			$activity['description'] = json_decode($activity['description'],true);
			$this->activity_data = $activity;

			$ret['r'] = 'ok';
		}
		else {
			$ret['r'] = 'error';
			$ret['r'] = '30001:Illegal order id.';
		}

		return $ret;
	}

	/*
	 * get_tickets
	 */
	public function get_tickets() {
		return $this->tickets;
	}

	/*
	 * get_ticket_by_id
	 */
	public function get_ticket_by_id($ticket_id) {
		$ret = array();

		for ($i=0; $i < count($this->tickets); $i++) {
			if ($this->tickets[$i]->get_id() == $ticket_id) {
				$ret['r'] = 'ok';
				$ret['msg'] = $this->tickets[$i];

				return $ret;
			}
		}
		$ret['r'] = 'error';
		$ret['msg'] = '30001:Illegal ticket id.';

		return $ret;
	}

	/*
	 * get_activity_data
	 */
	public function get_activity_data($key = '') {
		if ($key == '') {
			return $this->activity_data;
		}
		else {
			return $this->activity_data[$key];
		}
	}

	/*
	 * set_payment
	 */
	public function set_payment() {
		$ret = array();

		$get_ret = model('wt_payment_channel')->get_one($this->activity_data['payment_channel_id']);

		if (!$get_ret) {
			$ret['r'] = 'error';
			$ret['msg'] = '30001:Illegal payment channel id.';

			return $ret;
		}

		$this->payment = Payment::get_payment($get_ret['key_name']);

		$ret['r'] = 'ok';

		return $ret;
	}

	/*
	 * get_payment
	 */
	public function get_payment() {
		return $this->payment;
	}

	/*
	 * load_payment
	 *
	 * 加载并注册所有的支付方式
	 */
	public function load_payment() {
		//支付方式目录
		$dir = __DIR__."/payments";

		if(is_dir($dir)) {
	     	if ($handle = opendir($dir)) {
	        	while (($file = readdir($handle)) !== false) {
	     			if((is_dir($dir."/".$file)) && $file!="." && $file!="..") {
	     				// echo "<b><font color='red'>文件名：</font></b>",$file,"<br><hr>";
	     				// listDir($dir."/".$file."/");
	     			}
					else {
	         			if($file!="." && $file!="..") {
	         				//echo $file."<br>";
	         				include_once $dir.'/'.$file;
	         				$payname = substr($file, 0, strpos($file, '.', 0));
	         				$payment = 'Payment_'.$payname;

	         				$obj_payment = new $payment;

	         				$obj_payment->register();
	      				}
	     			}
	        	}
	        	closedir($handle);
	     	}
	   	}
	}

	/*
	 * set_activity
	 *
	 * 根据order id假造order_data信息以及attendees信息
	 */
	public function set_activity($id) {
		$ret = array();

		$this->set_activity_data($id);

		$ticket = new Ticket_Type();
		$tickets_ret = $ticket->get_tickets_by_activity($this->activity_data['activity_id']);

		if ($tickets_ret['r'] == 'error') {
			return $tickets_ret;
		}

		$this->tickets = $tickets_ret['msg'];

		$set_ret = $this->set_payment();
		if ($set_ret['r'] == 'error') {
			return $set_ret;
		}

		$ret['r'] = 'ok';

		return $ret;
	}
}