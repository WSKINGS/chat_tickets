<?php
/**********************************
*     		EndCMS
*       www.endcms.com
*         ©2008-now
* under Creative Commons License
**********************************/

/*
./common.php:
  common things of every request
*/

//not support php version less than 4.1.0
PHP_VERSION < "4.1.0" && die('Sorry! Your PHP version is too old!');

@ini_set('memory_limit', '128M');

@set_magic_quotes_runtime(0);
@ini_set('magic_quotes_sybase', 0);

//default time zone
function_exists('date_default_timezone_set') && @date_default_timezone_set(END_DEFAULT_TIMEZONE);

if (!defined('SESSION_STARTED') && END_OPEN_SESSION != false)
{
	isset($_REQUEST['PHPSID']) && session_id($_REQUEST['PHPSID']);
	session_start();
	define('SESSION_STARTED','true');
}

//check convert encoding function
if(!function_exists('iconv'))
{
	if (function_exists('mb_convert_encoding'))
	{
		function iconv($f,$t,$s) { return mb_convert_encoding ($s,$t,$f);}
	}
	else
	{
		function iconv() { die("<div style='color:red;font-size:20px;'>no convert encoding function found!</div>"); }
	}
}

//cancle global variables
if (ini_get('register_globals'))
{
	if (isset($_REQUEST['GLOBALS'])) die('what do you want to do?');
	$_notunset = array('GLOBALS', '_GET', '_POST', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES');
	$_input = array_merge($_GET, $_POST, $_COOKIE, $_SERVER, $_ENV, $_FILES, isset($_SESSION) && is_array($_SESSION) ? $_SESSION : array());
	foreach($_input as $_k => $_v)
	{
		if (!in_array($_k, $_notunset) && isset($GLOBALS[$_k])) 
		{
			$GLOBALS[$_k] = NULL;
			unset($GLOBALS[$_k]);
		}
	}
	unset($_input);
	unset($_notunset);
}


if (!function_exists('strip_magic_quotes'))
{
	function strip_magic_quotes($string)
	{
		if(!is_array($string)) return stripslashes($string);
		foreach($string as $key => $val)
		{
			$string[$key] = strip_magic_quotes($val);
		}
		return $string;
	}
}

if (!function_exists('add_magic_quotes'))
{
	function add_magic_quotes($string)
	{
		if(!is_array($string)) return addslashes($string);
		foreach($string as $key => $val)
		{
			$string[$key] = add_magic_quotes($val);
		}
		return $string;
	}
}

//magic gpc
if(get_magic_quotes_gpc())
{
	$_GET = strip_magic_quotes($_GET);
	$_POST = strip_magic_quotes($_POST);
	$_COOKIE = strip_magic_quotes($_COOKIE);
}
$_REQUEST = array_merge($_GET,$_POST);

if (!function_exists("file_get_contents"))
{
	function file_get_contents($path)
	{
		if (!file_exists($path)) return false;
		$fp=@fopen($path,"r");
		$all=fread($fp,filesize($path));
		fclose($fp);
		return $all;
	}
}

if (!function_exists("file_put_contents"))
{
	function file_put_contents($path,$val)
	{
		$fp=@fopen($path,"w");
		fputs($fp,$val);
		fclose($fp);
		return true;
	}
}


if (!function_exists('json_encode'))
{
	function json_encode($arr)
	{
		$keys = array_keys($arr);
		$isarr = true;
		$json = "";
		for ( $i=0; $i<count($keys); $i++)
		{
			if ($keys[$i] !== $i)
			{
				$isarr = false;
				break;
			}
		}
		$json = $space;
		$json.= ($isarr)?"[":"{";
		for( $i=0; $i<count($keys); $i++)
		{
			if ($i!=0) $json.= ",";
			$item = $arr[$keys[$i]];
			$json.= ($isarr)?"":$keys[$i].':';
			if (is_array($item))
				$json.= json_encode($item);
			else if (is_string($item))
				$json.= '"'.str_replace(array("\r","\n"),array('\r','\n'),$item).'"';
			else 
				$json.= $item;
		}
		$json.= ($isarr)?"]":"}";
		return $json;
	}
}



