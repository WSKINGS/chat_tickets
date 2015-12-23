<?php

/**
 * 极光推送-V2. PHP服务器端
 * @author 夜阑小雨
 * @Email 37217911@qq.com
 * @Website http://www.yelanxiaoyu.com
 * @version 20130118 
 */
	define('DB_HOST', 'localhost');
	define('DB_USER', 'root'); //数据库用户名
	define('DB_PWD', ''); //数据库密码
	define('DB_NAME', 'chat'); //数据库
	define('DB_TAB', 'end_chat_push');//数据表
	define('DB_CODE','utf8');
	define('appkeys','873410d9e348fea0515d4d89');	//appkey值 极光portal上面提供 
	define('masterSecret', 'ab7c5a88b4be0bb1cc7dff36');    //API MasterSecert值 极光portal上面提供 
	define('platform', 'android,ios');    //推送平台
?>