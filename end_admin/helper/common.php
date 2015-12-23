<?php

function check_show($action)
{
	$controller = defined('ITEM_TYPE')?ITEM_TYPE:END_CONTROLLER;
	return $_SESSION['login_user']['rights'][$controller.'_'.$action];
}





/*
check if the action is allowed
*/
function check_allowed($controller,$action,$text = false)
{
	if (END_MODULE != 'admin') return true;

	if (!$action) 
		$action = $controller;
	else
		$action = $controller.'_'.$action;
	if (!$_SESSION['login_user']['rights'][$action])
	{
		if ($text)
		{
			echo LANG_NOT_ALLOWED;
			die;
		}
		else
			end_exit(LANG_NOT_ALLOWED);
	}
	else return true;
}



function check_allowed_category($category_id,$text = false)
{
	if (END_MODULE != 'admin') return true;
	if ($_SESSION['login_user']['limit_category_id'] && !$_SESSION['login_user']['rights']['categroy_'.$category_id])
	{
		if ($text)
		{
			echo LANG_NOT_ALLOWED;
			die;
		}
		else
			end_exit(LANG_NOT_ALLOWED);
	}
	else return true;
}

/*
output a msg box for $t seconds then redirect to $url
*/
function end_exit($content,$url='javascript:history.go(-1);',$t = 2)
{
	$temp = template('exit.html');
	$temp->assign( array
	(
		'content' => $content,
		'url' => $url,
		'time' => $t,
	));
	$temp->display();
	die;
}




/*
get a simple formated date string
*/
function format_date($t)
{
	if (strpos($t,'-') !== false) return $t;
	return date('Y-m-d H:i',$t);
}

function show_day($t)
{
	if (strpos($t,'-') !== false) return $t;
	return date('m-d',$t);
}

/*
get a short type date string 
e.g.	today 21:23 (when it s the same day)
		yesterday 21:23 (when it's yesterday)
*/
function format_date_short($t)
{
	if (date('Y',$t) != date('Y'))
	{
		$re = date('Y'.LANG_YEAR.'m'.LANG_MONTH.'d'.LANG_DAY,$t);
	}
	else if (date('m',$t) != date('m'))
	{
		$re = date('m'.LANG_MONTH.'d'.LANG_DAY,$t);
	}
	else if (date('d',$t) != date('d'))
	{
		switch( date('d',$t) - date('d') )
		{
			case 2:
				$re = LANG_TDAT;
				break;
			case 1:
				$re = LANG_TOMORROW;
				break;
			case -1:
				$re = LANG_YESTERDAY;
				break;
			default:
				$re = date('d'.LANG_DAY,$t);
		}
	}
	else
	{
		$re = LANG_TODAY;
	}
	$re .= date('H:i',$t);
	return $re;
}




/*
check if certain action is allowed or not
*/
function if_allowed($action)
{
	if ($_SESSION['login_user']['name'] == 'Administrator' || $_SESSION['login_user']['rights'] == 'all')
	{
		return true;
	}
	else
	{
		return (strpos(','.$_SESSION['login_user']['rights'].',',','.$action.',') !== false);
	}
}
/*
get user name with user_id
*/
function get_user_name($id)
{
	global $end_users;
	if (!is_array($end_users))
	{
		include_once('model/user.php');
		$user = new END_User;
		$end_users = $user->get_array();
	}
	return $end_users[$id];
}


/*
separate description and whole content
*/
function end_separate($s,$url)
{
	global $config;
	if (strpos($s,$config['end_separator']) === false) return $s;
	$arr = explode($config['end_separator'],$s);
	return $arr[0].'...<a href="'.$url.'" class="readmore">'.$config['end_readmore'].'</a>';
}

function getext($filename)
{
	$filename = trim(strtolower(basename($filename)));
	$arr = explode('.',$filename);
	$type = $arr[count($arr)-1];
	return $type;
}

function myurlencode($url)
{
	return str_replace( array('+','%2F'),array('%20','/'),urlencode($url));
}

function thumb_png($orig_path,$mw=100,$mh=100,$thumb=false,$method='fill')
{
	return thumb($orig_path,$mw,$mh,$thumb,$method,true);
}

function thumb($orig_path,$mw=100,$mh=100,$thumb=false,$method='fill',$png=false)
{
	if (!$orig_path) return 'about:blank';
	$path = END_ROOT.$orig_path;
	
	$temarr = explode('.',$path);
	$ftype = array_pop($temarr);
	$etag = basename($path).$mw.'x'.$mh;
	$etag.= $png ? '.png':'.jpg';

	if (!file_exists($path)) return '';
	
	if ($thumb === false)
		$thumb = dirname($path).'/'.$etag;

	if (file_exists($thumb)) return dirname($orig_path).'/'.$etag;

	if (!$imgarr=@getimagesize($path)) return ''; 
	$width_orig=$imgarr[0];
	$height_orig=$imgarr[1];

	$mime_orig=$imgarr["mime"];
	$mime=str_replace("image/","",$mime_orig);
	$mime=($mime=="bmp")?"wbmp":$mime;
	if (!function_exists("imagecreatefrom$mime")) return false;

	$p = $mw/$width_orig;
	$_p = $mh/$height_orig;
	if ($_p == 1 && $p == 1) //如果尺寸相同
	{
		return $orig_path;
	}
	if ($_p>$p)
	{
		$p = $_p;
		$width = $p*$width_orig;
		$height = $p*$height_orig;
		$cut_height = 0;
		$cut_width = intval(($width_orig - $mw/$p)/2);
	}
	else
	{
		$width = $p*$width_orig;
		$height = $p*$height_orig;
		$cut_height = intval(($height_orig - $mh/$p)/2);;
		$cut_width = 0;
	}
	$width = $mw;
	$height = $mh;
	
	$image_p = @imagecreatetruecolor($width, $height);
	$_func = 'imagecreatefrom'.$mime;
	$image = @$_func($path);
	if ($png) //保存透明
	{
		imagealphablending($image_p,true);
		$tcolor = imagecolortransparent($image_p, imagecolorallocatealpha($image_p, 0, 0, 0,127));
		imagefill($image_p, 0, 0, $tcolor);
		imagesavealpha($image_p, true);
	}
	@imagecopyresampled($image_p, $image, 0, 0, $cut_width, $cut_height, $width, $height, $mw/$p, $mh/$p);
	$_func = $png?'imagepng':'imagejpeg';
	imagepng($image_p,$thumb);
	return (file_exists($thumb))?dirname($orig_path).'/'.$etag:false;
}



/*
if the client browser is IE
*/
function is_ie()
{
	$useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
	if((strpos($useragent, 'opera') !== false) || (strpos($useragent, 'konqueror') !== false)) return false;
	if(strpos($useragent, 'msie ') !== false) return true;
	return false;
}


function load_models()
{
	global $end_models,$end_rights;
	$_h = opendir(END_ROOT);
	while($v = readdir($_h))
	{
		if (is_dir(END_ROOT.$v) 
		&& preg_match('/^end_/',$v) 
		&& is_dir($mdir = END_ROOT.$v.'/model/') 
		&& $__h = opendir($mdir) )
		{
			while($v = readdir($__h))
			{
				if (is_dir($mdir.$v) && file_exists($mfile = $mdir.$v.'/'.$v.'.config.php'))
				{
					include_once($mfile);
					$end_models[$v]['model_path'] = $mdir.$v.'/';
					//define('LANG_RIGHTS_'.strtoupper($v),$end_models[$v]['name']);
				}
			}
			closedir($__h);
		}
	}
	closedir($_h);
	$_models = array();
	foreach($end_models as $key=>$arr)
	{
		if ($arr['type'] == 'list') $key.= '_list';
		$_models[$key] = $arr;
	}
	$end_models = $_models;
}

function end_show_view_button($id,$s=LANG_VIEW)
{
	echo ' <a href="admin.php?p=item&action=view_item&category_id='.END_ADMIN_CATEGORY_ID.'&item_id='.$id.'">'.$s.'</a> ';
}
function end_show_edit_button($id,$s=LANG_EDIT)
{
	echo ' <a href="admin.php?p=item&action=edit_item&category_id='.END_ADMIN_CATEGORY_ID.'&item_id='.$id.'">'.$s.'</a> ';
}
function end_show_delete_button($id,$s = LANG_DELETE)
{
	echo ' <a href="javascript:;//'.$id.'" onclick="delete_item(\''.$id.'\',this)">'.$s.'</a> ';
}
function end_show_update_status_button($id,$status,$m)
{
	echo ' <a href="javascript:;" onclick="change_status(\''.$id.'\',\''.$status.'\',this)">'.$m.'</a> ';
}

/*
 * Format int to datetime
 * author: lidongxu @ 2014-5-22
 */
function end_show_int2date($t) 
{
	if ($t == '0' || $t == null) {
		return "";
	}
	else {
		return date('Y-m-d H:i:s',$t);
	}
}

function get_admin_id()
{
	return $_SESSION['login_user']['admin_id'];
}



