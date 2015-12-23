<?php
header("content-type:text/html; charset=utf-8");
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="zh-CN" lang="zh-CN"> 
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<title>EndCMS 简易安装</title>
	<script src="end_blog/view/default/js/jquery.js"></script>
</head>
<body>

<?php

if ($_GET['do'] == 'save' && count($_POST)>0)
{
	if (!mysql_connect($_POST['server'],$_POST['username'],$_POST['password']))
		echo "<h2>连接数据库失败</h2>";
	if (!mysql_select_db($_POST['database']))
		echo "<h2>选择数据库失败!</h2>";
	else
	{
		$charset = "utf8"; 
		if(mysql_get_server_info() > '4.1' && $charset)
		{
			mysql_query("SET character_set_connection=$charset, character_set_results=$charset, character_set_client=binary");
		}
		if(mysql_get_server_info() > '5.0')
		{
			mysql_query("SET sql_mode=''");
		}
		
		$sql = file_get_contents("install.sql");
		$lines = explode(";\n",$sql);
		foreach($lines as $line)
		{
			mysql_query($line);
		}
		
		$s = file_get_contents('end_system/config.sample.php');
		$s = str_replace(array("--endcms-username--","--endcms-password--","--endcms-server--","--endcms-database--")
		,array($_POST['username'],$_POST['password'],$_POST['server'],$_POST['database']),$s);
		if (file_put_contents('end_system/config.php',$s))
		{
			//unlink('install.sql');
			//unlink('install.php');
			exec('rm end_system/cache/template/*');
			die("安装成功！");
		}
	}
	
	mysql_error();
}


?>
<h1>数据库信息(MySQL Only):</h1>
<form action="install.php?do=save" method="post">
<table border="0">
	<tr>
		<td>服务器:</td>
		<td><input type="text" name="server" size="20" value="localhost" /></td>
	</tr>
	<tr>
		<td>用户名:</td>
		<td><input type="text" name="username" size="20" value="root" /></td>
	</tr>
	<tr>
		<td>密　码:</td>
		<td><input type="password" name="password" size="20" value="" /></td>
	</tr>
	<tr>
		<td>数据库名:</td>
		<td><input type="text" name="database" size="20" value="" /></td>
	</tr>
	<tr>
		<td></td>
		<td><input type="submit" value="提交" /></td>
	</tr>
</table>
</form>

</body>
</html>
