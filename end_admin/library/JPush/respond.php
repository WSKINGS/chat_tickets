<?php

/**
 * 极光推送-V2. PHP服务器端
 * @author 夜阑小雨
 * @Email 37217911@qq.com
 * @Website http://www.yelanxiaoyu.com
 * @version 20130118 
 */
 
 
 // 此文件是用来接受服务器端在发送完推送消息后反馈回来的信息。 这个文件地址需要填写在极光推送portal的回调地址栏里面。


// 消息发送回调接口
//如果在极光推送portal 上有注册过合法的回调URL接口，极光Push Server消息发送结束时，调用此回调接口，以向调用者反馈发送结果。
//
//请开发者提供此接口支持 HTTP POST 请求。
//
//请求参数名称是："push_result"。
//
//返回内容（JSON 格式）里包含里如下的信息：
//
//Key名称	Value内容说明
//sendno	发送序号。接口调用时指定的。
//errcode	错误码。参考：错误码定义
//errmsg	错误说明
//total_user	本次推送满足条件的用户数
//send_cnt	本次推送当前在线用户数

 
include('config.inc.php'); 
include('db.class.php'); 

$ret = $_REQUEST['push_result'];

//print_r($_REQUEST);
$ret = stripslashes($ret); //去掉转义符

$res = json_decode($ret,true);


$result = array();

$res['app_keys'] = "1dad03ee5308c4eed680e920";// 目前官方没有提供push_result[app_keys],所以先自己写一个，可以先把数据写到一个表里去，
//后面等官方提供了这个参数，就注释掉，则根据这个app_keys来判断是那个app的返回信息。

 if ($res['app_keys'] == appkeys ){
	dataConnect();
	$sql = "UPDATE `".DB_TAB."` SET `total_user` = '".$res['total_user']."', `send_cnt` = '".$res['send_cnt']."' WHERE `sendno` = ".$res['sendno']."";
	echo "$sql";
	$query = mysql_query($sql);		
	//print_r($query); 
	 
 } 

record($ret);

function record($con)
{
	$con .= '  '.date('Y-m-d H:i:s',time())."\r\n";
	$handler = fopen('log.txt','a');
	fwrite($handler,$con);
	fclose($handler);
 
}
 
?>