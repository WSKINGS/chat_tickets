<?php
 
/**
 * 极光推送-V2. PHP服务器端
 * @author 夜阑小雨
 * @Email 37217911@qq.com
 * @Website http://www.yelanxiaoyu.com
 * @version 20130118 
 */
function dataConnect()
{		   
	$con = mysql_connect(DB_HOST, DB_USER, DB_PWD);
	if (!$con){die('Could not connect: ' . mysql_error());}
	mysql_select_db(DB_NAME, $con);
	mysql_query("SET NAMES 'utf8'");
}

?>