<?php
/*
 * Attendee Class
 * @author lidongxu @ 2014.5.26
 * 
 * 完成参会者的创建、修改、审核操作
 */

class Attendee {
	protected $attendee_data;

	/*
	 * 构造函数
	 */
	public function __construct() {
		$this->attendee_data = array();
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
		##返回结果参数
		$ret = array();

		##去除提交数据中数组类型的值的无用空格
		foreach ($data as $d_key => $d_value) {
			if (is_array($d_value)) {
				foreach ($d_value as $key => $value) {
					$data[$d_key][$key] = trim($value);
				}
				$data[$d_key] = json_encode($data[$d_key]);
			}
		}

		##去除内容两端的无用空格并检查必须项
		$data = filter_array($data, "activity_id!,
			order_id!,
			ticket_id!,
			trim:name,
			trim:company,
			trim:title,
			trim:phone,
			trim:telephone,
			trim:email!,
			trim:extra_info
			"
		);

		##检查数据
		if (!$data) {
			$ret['r'] = 'error';
			$ret['msg'] = '10001:Required fields can not be emmty.';

			return $ret;
		}
		
		##检查Email格式
		if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
			$ret['r'] = 'error';
			$ret['msg'] = '20001:Email format error.';

			return $ret;
		}

		##检查活动、订单、门票id
		if ( !model('wt_activity')->exists($data['activity_id']) 
			|| !model('wt_order')->exists($data['order_id'])
			|| !model('wt_ticket_type')->exists($data['ticket_id'])
			) {
			$ret['r'] = 'error';
			$ret['msg'] = '30001:Illegal data.';

			return $ret;
		}

		##获取attendee_type_id，与ticket_type设置保持一致
		$ticket = model('wt_ticket_type')->get_one($data['ticket_id']);
		$data['attendee_type_id'] = $ticket['attendee_type_id'];

		##设置审核状态
		$data['host_check'] = ($ticket['need_approve'] == 1)?-2:2;

		##设置active状态
		$data['active'] = ($ticket['need_approve'] == -1 && $ticket['price'] == 0)?1:-1;

		##设置数据初始status值
		$data['status'] = 1;

		$id = model('wt_attendee')->add($data);

		if ($id) {
			//$this->attendee_data = model('wt_attendee')->get_one($id);
			$this->set_attendee_data($id);

			$ret['r'] = 'ok';
			$ret['msg'] = $this;

			return $ret;
		}
		else {
			$ret['r'] = 'error';
			$ret['msg'] = '50001:Data insert error.';

			return $ret;
		}
	}

	/*
	 * update函数
	 *
	 * 修改参会者信息
	 */
	public function update($data) {
		##返回结果参数
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
		if ( isset($data['email'])
			&& !filter_var($data['email'], FILTER_VALIDATE_EMAIL) ) {
			$ret['r'] = 'error';
			$ret['msg'] = '20001:Email format error.';

			return $ret;
		}

		$update_ret = model('wt_attendee')->update($this->attendee_data['attendee_id'], $data);

		if ($update_ret) {
			$this->set_attendee_data($this->attendee_data['attendee_id']);

			$ret['r'] = 'ok';
			$ret['msg'] = $this;

			return $ret;
		}
		else {
			$ret['r'] = 'error';
			$ret['msg'] = '50001:Data update error.';

			return $ret;
		}
	}

	/*
	 * set_attendee_data函数
	 * 
	 * 根据attendee id从数据库获取attendee data
	 */
	public function set_attendee_data($id) {
		$ret = array();

		$attendee = model('wt_attendee')->get_one($id);

		if ($attendee) {
			$this->attendee_data = $attendee;

			$ret['r'] = 'ok';
		}
		else {
			$ret['r'] = 'error';
			$ret['r'] = '30001:Illegal attendee id.';
		}

		return $ret;
	}

	/*
	 * get_attendee_data函数
	 */
	public function get_attendee_data($key = '') {
		if ($key == '') {
			return $this->attendee_data;
		}
		else {
			return $this->attendee_data[$key];
		}
	}

	protected function delete_attendee_data() {
		$ret = array();
		if ( !model('wt_attendee')->delete($this->attendee_data['attendee_id']) ) {
			$ret['r'] = 'error';
			$ret['msg'] = '50003:Attendee delete failed.';

			return $ret;
		}

		$this->attendee_data = array();

		$ret['r'] = 'ok';
		return $ret;
	}

	/*
	 * approve
	 * 
	 * 审核参会信息
	 */
	public function approve($pass = true) {
		$ret = array();

		if ($pass) {
			if ($this->attendee_data['host_check'] == '2' || $this->attendee_data['host_check'] == '1') {
				$ret['r'] = 'ok';
				$ret['msg'] = 'No need to check.';

				return $ret;
			}

			$update_ret = model('wt_attendee')->update($this->attendee_data['attendee_id'], array('host_check'=>1));
			if ($update_ret) {
				$this->set_attendee_data($this->attendee_data['attendee_id']);

				$ret['r'] = 'ok';
			}
			else {
				$ret['r'] = 'error';
				$ret['msg'] = '50002:Data update error.';
			}
		}
		else {
			if ($this->attendee_data['host_check'] == '2' || $this->attendee_data['host_check'] == '-1') {
				$ret['r'] = 'ok';
				$ret['msg'] = 'No need to check.';

				return $ret;
			}

			$update_ret = model('wt_attendee')->update($this->attendee_data['attendee_id'], array('host_check'=> -1));
			if ($update_ret) {
				$this->set_attendee_data($this->attendee_data['attendee_id']);

				$ret['r'] = 'ok';
			}
			else {
				$ret['r'] = 'error';
				$ret['msg'] = '50002:Data update error.';
			}
		}

		return $ret;
	}

	/*
	 * get_attendees_by_order函数
	 * 
	 * 根据提供的order id生成参会者对象数组
	 */
	public function get_attendees_by_order($order_id) {
		$ret = array();

		$attendees = array();

		$attendee_list = model('wt_attendee')->get_list(array('order_id'=>$order_id));
		if (!$attendee_list) {
			$ret['r'] = 'error';
			$ret['msg'] = '50004:Attendee list get failed.';

			return $ret;
		}

		for ($i = 0; $i < count($attendee_list); $i++) {
			$attendees[$i] = new self();
			$attendees[$i]->set_attendee_data($attendee_list[$i]['attendee_id']);
		}

		$ret['r'] = 'ok';
		$ret['msg'] = $attendees;

		return $ret;
	}

	/*
	 * active函数
	 *
	 * 激活参会用户
	 */
	public function active() {
		$ret = array();

		$update_ret = model('wt_attendee')->update($this->attendee_data['attendee_id'], array('active' => 1));
		if (!$update_ret) {
			$ret['r'] = 'error';
			$ret['msg'] = '50002:Data update failed.';

			return $ret;
		}

		$this->set_attendee_data($this->attendee_data['attendee_id']);
		
		$ret['r'] = 'ok';
		return $ret;
	}

	/*
	 * deactive函数
	 *
	 * 取消激活参会用户
	 */
	public function deactive() {
		$ret = array();

		$update_ret = model('wt_attendee')->update($this->attendee_data['attendee_id'], array('active' => -1));
		if (!$update_ret) {
			$ret['r'] = 'error';
			$ret['msg'] = '50002:Data update failed.';

			return $ret;
		}

		$this->set_attendee_data($this->attendee_data['attendee_id']);
		
		$ret['r'] = 'ok';
		return $ret;
	}

	/*
	 * check_email_exists
	 */
	public static function check_email_exists($activity_id, $email) {
		$ret = array();

		$exists_ret = model('wt_attendee')->exists(array('activity_id'=>$activity_id, 'email'=>$email, 'active'=>'1'));
		if ($exists_ret) {
			$ret['r'] = 'error';
			$ret['msg'] = '40002:'.$email.'has been exists.';

			return $ret;
		}
		else {
			$ret['r'] = 'ok';

			return $ret;
		}
	}

	/*
	 * is_approved
	 */
	public function is_approved() {
		if ($this->attendee_data['host_check'] == '2' || $this->attendee_data['host_check'] == '1') {
			return true;
		}
		else {
			return false;
		}
	}

	/*
	 * is_need_approve
	 */
	public function is_need_approve() {
		if ($this->attendee_data['host_check'] != '2') {
			return true;
		}
		else {
			return false;
		}
	}

	/*
	 * is_not_pass
	 */
	public function is_not_pass() {
		if ($this->attendee_data['host_check'] == '-1') {
			return true;
		}
		else {
			return false;
		}
	}
}