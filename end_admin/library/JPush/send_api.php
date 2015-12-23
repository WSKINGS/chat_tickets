<?php
/**
 *
 * 推送消息API
 * 
 * @author liudanking
 */

include_once('jpush.php');
include_once('config.inc.php'); 
include_once('db.class.php');  


function send_broadcast($data)
{
	if (!isset($data['title']) || !isset($data['content']))	return false;

	$title = $data['title'];
	$content = $data['content'];
	$receiver_value = '';
	dataConnect();
	$sql = "SELECT max(id) from ".DB_TAB."";
	$result = mysql_query($sql);
	$result= mysql_fetch_array($result);
	$sendno = $result[0]+1;
	$platform = platform;
	$msg_content = json_encode(array('n_builder_id'=>0, 'n_title'=>$title, 'n_content'=>$content));        
	$obj = new jpush(masterSecret,appkeys);			 
	$res = $obj->send($sendno, 4, $receiver_value, 1, $msg_content, $platform);	

	return true;
}

function send_to_alias($data, $alias=array('liudanking'))
{
	$receiver_value = '';
	foreach ($alias as $key => $value) {
		$receiver_value = $receiver_value . $value . ',';
	}

	$title = $data['title'];
	$content = $data['content'];
	dataConnect();
	$sql = "SELECT max(id) from ".DB_TAB."";
	$result = mysql_query($sql);
	$result= mysql_fetch_array($result);
	$sendno = $result[0]+1;
	$platform = platform ;
	$msg_content = json_encode(array('n_builder_id'=>0, 'n_title'=>$title, 'n_content'=>$content));
	$obj = new jpush(masterSecret,appkeys);		 
	$res = $obj->send($sendno, 3, $receiver_value, 1, $msg_content, $platform);
}


?>