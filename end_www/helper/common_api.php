<?php
/*
 * @author liudanking
 * 2013.07
 * */

function post_receive() {
	/*$raw_data = file_get_contents("php://input");
	$strs = explode( "&",$raw_data);
	$data = array();
	foreach ($strs as $str) {
		$vals = explode("=", $str);
		if ($vals[0]) {
			$key = $vals[0];
			$value = $vals[1];
		}
		else {
			die_json_msg("post data illegle", 10000);
		}
		$data[$key] = $value;
	}*/
	if ($_POST) {
		return $_POST;
	}
	else {
		die_json_msg("post data error", 11000);
	}
}

// 接收提交到服务器端的json数据 
function json_receive()
{
	$raw_data = file_get_contents("php://input");
	$data = json_decode($raw_data, true);
	if ($data)
	{
		return $data;
	}
	else
	{
		die_json_msg("post json data error", 10000);
	}
}

// 服务器发送json数据给客户端
function json_send($data=array())
{
	$data = array("ret"=>0, // 0代表成功，其他数字代表失败
		  		  "msg"=>$data);
	// $php_ver = (int)substr(PHP_VERSION, 2,1);
	// if ($php_ver >= 4)
		// $json_data = json_encode($data, JSON_PRETTY_PRINT);
	// else
		$json_data = json_encode($data);
	if ($json_data !== FALSE)
	{
		echo $json_data;
	}
	else
	{
		echo json_encode(array("ret"=>10000,
						  	   "msg"=>"json encode error"));
	}
	die();
}

// 获取json 字符串
function json_api_str($data=array())
{
	$data = array("ret"=>0, // 0代表成功，其他数字代表失败
		  		  "msg"=>$data);
	// $php_ver = (int)substr(PHP_VERSION, 2,1);
	// if ($php_ver >= 4)
		// $json_data = json_encode($data, JSON_PRETTY_PRINT);
	// else
		// $json_data = json_encode($data);
	$json_data = json_encode($data);
	if ($json_data !== FALSE)
	{
		return $json_data;
	}
	else
	{
		echo json_encode(array("ret"=>10000,
						  	   "msg"=>"json encode error"));
		die();
	}
}

// 服务器发送json数据给客户端
function die_json_msg($message, $error_code=10000)
{
	$json_data =  json_encode(array("ret"=>$error_code,
									"msg"=>$message));
	if ($json_data)
	{
		echo $json_data;
	}
	else
	{
		echo json_encode(array("ret"=>10000,
							   "msg"=>"json encode error"));
	}
	die();


}

// 常规加盐哈希函数
function hash_normal($factor)
{
	$salt = 'fenhe@dreamgram1.0';
	$raw_str = $factor.$salt;
	return sha1(sha1($raw_str));
}

// 随机哈希函数
function hash_random($factor)
{
	$salt = '+_)(*&^%$#@!~';
	$raw_str = $factor.time().$salt.rand(2013,65535);
	return hash('sha256',$raw_str);
}

// php post 提交数据，返回结果
function do_post_request($url, $data, $method, $optional_headers = null)
{
	$params = array('http' => array(
	             'method' => $method,//'POST',
	          	 'content' => $data
	       ));
	if ($optional_headers !== null) {
	$params['http']['header'] = $optional_headers;
	}
	$ctx = stream_context_create($params);
	$fp = @fopen($url, 'rb', false, $ctx);
	if (!$fp) {
	throw new Exception("Problem with $url, $php_errormsg");
	}
	$response = @stream_get_contents($fp);
	if ($response === false) {
	throw new Exception("Problem reading data from $url, $php_errormsg");
	}
	return $response;
}

// url相对地址转绝对地址
function abs_url($url)
{
	if (!$url)	return $url;
	if (strlen($url) == 0)	return $url;
	
	if (strpos($url, 'http://') === 0)
		return $url;
	else
	{
		if (strpos($url, '/') === 0)
			return 'http://'.$_SERVER['SERVER_NAME'].$url;
		else
			return 'http://'.$_SERVER['SERVER_NAME'].'/'.$url;
	}
}


// html特殊字符转换
function htmlnormalchars($str)
{
	return str_replace(array("&nbsp;", "<br>", "&lt;", "&gt;", '&amp;', '&#039;', '&quot;','&lt;', '&gt;'), 
					   array(" ",      "\r\n", "<",    ">",    '&',     '\'',     '"',     '<',    '>'),
					   $str);
}

