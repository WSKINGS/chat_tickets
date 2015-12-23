<?php
/**********************************
*     		EndCMS
*       www.endcms.com
*         ©2008-now
* under Creative Commons License
**********************************/

/*
./config.php:
  system configuration
*/


/**
* 默认语言
*/
define('END_LANGUAGE','cn');

/**
* 默认时区
*/
define('END_DEFAULT_TIMEZONE','Etc/GMT-8');

/*
* 是否使用php压缩页面成gzip，有些IIS会出问题
* 取值只能是 true/false
*/
define('END_GZIP_OUTPUT',false);

//模板文件所用的字符集，推荐utf-8
define('END_CHARSET','UTF-8');

//默认缓存模板文件
define('END_CACHE_VIEW',true);

//允许上传的文件类型 (废弃) 
//define('END_UPLOAD_FILE_TYPES',',jpg,jpeg,gif,bmp,png,doc,rar,pdf,zip,ppt,docx,xls,');

//上传文件保存到的目录路径，如果不存在，会自动建立
define('END_UPLOAD_DIR','public/'.date('Y/m/'));

//默认的模板文件扩展名
define('END_VIEWER_EXT','html');
//默认的语言文件扩展名
define('END_LANGUAGE_EXT','lang');

//debug模式， 开启之后后台一些危险的操作会打开，模板文件会每次访问都检查是否更新
define('END_DEBUG',true);

//默认数据表前缀
define('END_MYSQL_PREFIX','end_');

//MYSQL数据库信息
$mysql = array(
	'username' => "--endcms-username--", //数据库用户名
	'password' => "--endcms-password--", //密码
	'server' => "--endcms-server--", //数据库服务器地址，绝大部分是 localhost
	'database' => "--endcms-database--", //网站数据表所在的数据库名称 (一个数据库服务器有多个数据库)
);


//定义一些系统路径
define('END_LANGUAGE_DIR',END_MODULE_DIR.'language/');
define('END_CONTROLLER_DIR',END_MODULE_DIR.'controller/');
define('END_API_DIR',END_MODULE_DIR.'api/'); ## api directory
define('END_MODEL_DIR',END_ROOT.'end_'.END_MODULE.'/model/');
define('END_PLUGIN_DIR',END_ROOT.'end_'.END_MODULE.'/plugin/');
define('END_THEME_DIR',END_ROOT.'end_'.END_MODULE.'/view/');

$end_extension = array();
$end_module = array();
