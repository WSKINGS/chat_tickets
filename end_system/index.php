<?php
/**********************************
*     		EndCMS
*       www.endcms.com
*         ©2008-now
* under Creative Commons License
**********************************/

/**
 * MVC核心处理过程
 * @author Longbill
 * 2010-03-13
 */

//页面执行时间计时开始
error_reporting(E_ALL ^ E_NOTICE);
list($_usec, $_sec) = explode(' ', microtime()); 
$_time_start = (float)$_usec + (float)$_sec;


//获得并设置系统执行的路径
$_system_folder = dirname(__FILE__);
$_system_folder = str_replace("\\", "/", $_system_folder);

//END_SYSTEM_DIR 是指end_system的路径
define('END_SYSTEM_DIR',$_system_folder.'/');
//END_ROOT 表示 end_system所在文件夹的路径，也就是系统的根目录
define('END_ROOT',preg_replace('/\/end_system\/?$/','/',$_system_folder));
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

//从数据库中读取config信息，覆盖掉$config变量
//这样做的好处就是，在config.php中可以写默认值，可以在后台添加一个同名的设置变量，就可以覆盖该值
if (!is_array($config)) $config = array();
$__arr = $db->get_all("SELECT * FROM ".END_MYSQL_PREFIX."config");
if ($__arr) foreach($__arr as $__c) $config[$__c['name']] = $__c['value'];

//钩子
function_exists('end_on_after_db') && end_on_after_db();

//发送header声明页面编码
header("CONTENT-TYPE:text/html; CHARSET=".END_CHARSET);

//根据url中的p变量来确定对应的controller
$_page = ($_GET['p'])?$_GET['p']:'index';
$_page = preg_replace('/[^a-zA-Z0-9_]/','',$_page);
define('END_CONTROLLER',$_page);
$_controller = $_page.'.php';
$_viewer = $_page.'.'.END_VIEWER_EXT;


//如果开启多语言模式，那么载入当前模块下的 common.lang 和 对应页面的 .lang 文件
if (END_ENABLE_LANGUAGE) { language('common'); language($_page); }

//common scripts
include_once(END_SYSTEM_DIR.'common.php');
//hook
function_exists('end_on_ready') && end_on_ready();

//the ultimate view data array
$view_data = array();
$view_html = '';
define('END_VIEWER_DIR',END_MODULE_DIR.'view/');

//load the main controller
$_c_filename = END_CONTROLLER_DIR.$_controller;
file_exists($_c_filename) && include($_c_filename);
if (!$view_html)
{
	$_viewer_dir = END_VIEWER_DIR;
	function_exists('end_on_template_begin') && end_on_template_begin();
	if (!file_exists($_viewer_dir.$_viewer))
	{
		if (function_exists('end_on_notfound'))
		{
			end_on_notfound();
		}
		else
		{
			die('URL request for '.$_viewer.' not found!');
		}
		die;
	}
	
	$_template = template($_viewer);
	
	$r_path = dirname($_SERVER['SCRIPT_NAME'].'index.php');
	$r_path = str_replace('\\','/',$r_path);
	if (!$r_path || $r_path == '/') $r_path = '.';
	$_url_base = str_replace('/./','/',str_replace('//','/',$_SERVER['HTTP_HOST'].'/'.$r_path.'/'));
	$view_data['url_base'] = 'http://'.$_url_base;
	
	$view_data['config'] = $config;
	$view_data['debug'] = END_DEBUG;
	$view_data['view_root'] = str_replace(END_ROOT,'',END_VIEWER_DIR);
	define('LANG_VIEW_ROOT',$view_data['view_root']);
	//total output array by controllers
	$_template->assign($view_data);
	
	//timer stop
	list($_usec, $_sec) = explode(' ', microtime()); 
	$_time_end = (float)$_usec + (float)$_sec;
	
	$end_time_used = intval(($_time_end-$_time_start)*1000)/1000;
	$_template->assign('time_used',$end_time_used);
	$_template->assign('total_query',$db->query_num);
	$_template->assign('db',$db);
	$view_html = $_template->result();
	unset($_template);
}
if (function_exists('end_on_end'))
	end_on_end();
else
{
	if (END_GZIP_OUTPUT === true) $view_html = end_gzip($view_html);
	echo $view_html;
	die;
}
?>