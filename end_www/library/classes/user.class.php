<?php 
/*
 * User Class
 * @author lidongxu @ 2014.5.26
 * 
 * 单个线程内仅能存在一个User对象
 */
class User {
	protected $user_data;

	protected static $_instance = null;

	/*
	 * 构造函数
	 * 
	 * 禁止通过构造函数生成对象
	 */
	protected function __construct() {
		$this->user_data = array();
	}

	/*
	 * 对象生成函数，确保单个线程内存在且仅能存在一个User对象
	 */
	public static function instance() {
		if (self::$_instance == null) {
			self::$_instance = new User();
		}
		return self::$_instance;
	}

	/*
	 * 析构函数
	 */
	public function __destruct() {
	}

	/*
	 * login函数
	 * 
	 * 完成用户登陆的行为
	 */
	public function login($data = array()) {
		##返回结果参数
		$ret = array();
		
		##去除无用空格
		$data = filter_array($data, "trim:username,
			trim:password"
			);

		##检查并加工对应内容
		$data = filter_array($data,"strtolower:username!,
				end_encode:password!
				");

		

		if ($data) {
			$user = model('wt_user')->get_list($data);
			if ($user) {
				$this->user_data = $user[0];

				$ret['r'] = 'ok';
				$ret['msg'] = $this;
				return $ret;
			}
			else {
				$ret['r'] = 'error';
				$ret['msg'] = '40001:The username or password error.';
				return $ret;
			}
		}
		else {
			$ret['r'] = 'error';
			$ret['msg'] = '10001:The username or password can not be empty.';
			return $ret;
		}
	}

	/*
	 * register函数
	 * 
	 * 完成用户注册的数据行为
	 */
	public function register($data = array()) {
		##返回结果参数
		$ret = array();

		##删除内容前后的空格
		$data = filter_array($data, "trim:username,
			trim:password,
			trim:name,
			trim:company_name,
			trim:email,
			trim:phone,
			role"
		);

		##处理用户名并检查数据不可缺数据
		$data = filter_array($data, "strtolower:username!,
			password!,
			name,
			company_name,
			email!,
			phone,
			role!"
		);

		if ($data) {
			##检查用户名是否重复
			if (model('wt_user')->exists(array('username' => $data['username']))) {
				return '40002:The username has been used.';
			}
			else {
				##添加用户状态
				$data['status'] = 1;

				##写入数据库
				$id = model('wt_user')->add($data);

				if ($id) {
					$user = model('wt_user')->get_one($id);

					$this->user_data = $user;

					$ret['r'] = 'ok';
					$ret['msg'] = $this;

					return $this;
				}
				else {
					$ret['r'] = 'error';
					$ret['msg'] = '50001:Data insert error.';

					return $ret;
				}
			}
		}
		else {
			$ret['r'] = 'error';
			$ret['msg'] = '10001:Required fields can not be emmty.';
			return $ret;
		}
	}

	/*
	 * set_user_data
	 */
	public function set_user_data($user_id) {
		$ret = array();

		$get_ret = model('wt_user')->get_one($user_id);

		if ($get_ret) {
			$this->user_data = $get_ret;
		}
		else {
			$ret['r'] = 'error';
			$ret['msg'] = '30001:User id error.';
		}

		$ret['r'] = 'ok';
		return $ret;
	}

	/*
	 * get_user_data函数
	 *
	 * 获取protected user_data
	 */
	public function get_user_data($key = '') {
		if ($key == '') {
			return $this->user_data;
		}
		else {
			return $this->user_data[$key];
		}
	}
}