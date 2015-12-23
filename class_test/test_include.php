<?php

define('END_MODULE','www');

//获得并设置系统执行的路径
$_system_folder = dirname(__FILE__);
$_system_folder = str_replace("\\", "/", $_system_folder);
$_system_folder = str_replace("/class_test", "", $_system_folder);

//END_SYSTEM_DIR 是指end_system的路径
define('END_SYSTEM_DIR',$_system_folder.'/end_system/');
//END_ROOT 表示 end_system所在文件夹的路径，也就是系统的根目录
define('END_ROOT',preg_replace('/\/end_system\/?$/','/',END_SYSTEM_DIR));
//当前模块的路径
define('END_MODULE_DIR',END_ROOT.'end_'.END_MODULE.'/');

//载入 end_system 下面的核心辅助函数库
include_once(END_SYSTEM_DIR.'helper/core.php');

//载入系统所需的输出相关辅助函数库，包含 ip() ,cn_substr(), en_substr() ,end_page() 等最常用的辅助函数
helper('html');

//载入外部模块的钩子函数文件，在 END_MODULE_DIR/helper/hooks.php，可以不存在
helper('hooks',1);

//默认载入外部公共辅助函数库, 可以不存在
helper('common',1);
helper('common_api',1);

//调用钩子
function_exists('end_on_begin') && end_on_begin();

//定义默认不使用多语言模式
//由于define一个变量之后，再次define是无效的，所以在入口文件定义的END_ENABLE_LANGUAGE不会被覆盖
define('END_ENABLE_LANGUAGE',false);

//载入system下的config
if (!file_exists(END_SYSTEM_DIR.'config.php')) die("config.php not found in end_system!");
include_once(END_SYSTEM_DIR.'config.php');
//载入mysql类， 包含了贯穿全局的DB类
include_once(END_SYSTEM_DIR.'library/mysql.php');
//载入model基础类，其他所有的model都是继承自MODEL类
include_once(END_SYSTEM_DIR.'library/model.php');

//载入当前模块下的config.php
if (file_exists(END_MODULE_DIR.'config.php')) include_once(END_MODULE_DIR.'config.php');

//连接数据库
//$db变量会贯穿程序的整个执行过程，以统计整个页面运行了多少次 SQL 查询
$db = new DB;
$db->connect($mysql['server'],$mysql['username'],$mysql['password'],$mysql['database']);

//发送header声明页面编码
header("CONTENT-TYPE:text/html; CHARSET=".END_CHARSET);