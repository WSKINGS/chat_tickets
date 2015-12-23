<?php
/*
 * Email Class
 * @author lidongxu @ 2014.6.3
 * 
 * 完成邮件的发送操作
 */

Class Email {
	protected $email_config;

	/*
	 * 构造函数
	 */
	public function __construct($fromName) {
		$this->email_config = array(
			'host'=>'smtpcloud.sohu.com',
			'port'=>25,
			'username'=>'postmaster@chatchina.sendcloud.org',
			//'password'=>'rgue5hoJ',
			'password'=>'2banTvVk9cvsoPz1',
			'fromName'=>$fromName,
			'fromAddress'=>'admin@chatchina.com.cn',
			'replyTo'=>'admin@chatchina.com.cn'
		);
	}

	protected function send_cloud_mail($recipient, $subject, $body) {
		$ret = array();

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);	# 关闭证书检查
	    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
	    curl_setopt($ch, CURLOPT_URL, 'https://sendcloud.sohu.com/webapi/mail.send.json');
	    //不同于登录SendCloud站点的帐号，您需要登录后台创建发信子帐号，使用子帐号和密码才可以进行邮件的发送。
	    curl_setopt($ch, CURLOPT_POSTFIELDS,
	            array('api_user' => $this->email_config['username'],
	            'api_key' => $this->email_config['password'],
	            'from' => $this->email_config['fromAddress'],
	            'fromname' => $this->email_config['fromName'],
	            'to' => $recipient,
	            'subject' => $subject,
	            'html' => $body)
	            );        
	            
        $result = curl_exec($ch);

        //请求失败
        if($result === false) {
        	$ret['r'] = 'error';
        	$ret['msg'] = 'curl last error : '.curl_error($ch);

        	return $ret;
        }

        curl_close($ch);

		$result_array = json_decode($result, true);
		if ($result_array['message'] == 'success') {
			$ret['r'] = 'ok';
			return $ret;
		}
		else {
			$ret['r'] = 'error';
			$ret['msg'] = $result;

			return $ret;
		}
	}

	/* email模板解析函数：将数据库保存的email模板实例化 */
	protected function load_email_template($data, $key_name) {
		$ret = array();

		$template = model('wt_email_template')->get_one(array('key_name'=>$key_name));
		if (!$template) {
			$ret['r'] = 'error';
			$ret['msg'] = '50004:Template not found.';

			return $ret;
		}
		if (!empty($data)) {
			foreach ($data as $key => $value) {
				$search[] = "%".$key."%";
				$replace[] = $value;
			}
			
			$template['body'] = str_replace($search, $replace, $template['body']);
		}
		return $template;
	}

	/* 发送模板邮件 */
	public function send_template_mail($template_name, $data, $to)
	{
		$email_template = $this->load_email_template($data,$template_name);
		$subject = $email_template['subject'];
		$content = $email_template['body'];
		$ret = $this->send_cloud_mail($to, $subject, $content);

		if ($ret['r'] == 'error') {
			//write_log
			$msg = date("Y-m-d H:i:s", strtotime('now'))." : ".$template_name." ".$ret['msg']."\r\n";
			error_log($msg, 3, __DIR__."/../../../public/email_error.log");
		}
		else {
			$ret['r'] = 'ok';
		}
		
		return $ret;
	}
}