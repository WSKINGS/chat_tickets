<?php

function end_on_begin_api()
{

}

function end_on_after_db()
{

}

function end_on_end_api()
{
	
}


function end_on_ready()
{
	if ($_GET['p'] != 'login')
	{
		if (!$_SESSION['login_user'])
		{
			model('log')->clear_old(0,END_ADMIN_LOG_NUM);
			header("location:admin.php?p=login&module=admin&backurl="
				.urlencode(basename($_SERVER['SCRIPT_NAME']).'?'.$_SERVER['QUERY_STRING']));
			die;
		}
		$rights = model('rights');
		$r = $rights->get_one($_SESSION['login_user']['rights_id']);
		unset($_SESSION['login_user']['rights']);
		unset($_SESSION['login_user']['allowed_controllers']);
		unset($_SESSION['login_user']['allowed_categories']);
		if ($r && $r['rights'])
		{
			$arr = explode(',',$r['rights']);
			$allowed_categories = array();
			foreach($arr as $val)
			{
				$_SESSION['login_user']['rights'][$val] = true;
				if (preg_match('/^category_\d/i',$val))
				{
					$val = preg_replace('/^category_/i','',$val);
 					$allowed_categories[] = $val;
				}
				$val = preg_replace('/_[^\_]+$/i','',$val);
				$_SESSION['login_user']['allowed_controllers'][$val] = true;
			}
			$_SESSION['login_user']['allowed_categories'] = join(',',$allowed_categories);
		}
	}
	$log = array('admin_id'=>get_admin_id(),'controller'=>$_GET['p'],'time'=>time(),'url'=>$_SERVER['REQUEST_URI']);
	define('END_LOG_ID',model('log')->add($log));
	model('log')->clear_old($log['admin_id'],END_ADMIN_LOG_NUM);
}


function end_on_template_begin()
{
	global $view_data;
	if (intval(END_LOG_ID) > 0 && !count($_POST) && defined('END_LOG_INFO'))
	{
		$log = array('menu' => '1','info' => END_LOG_INFO);
		if (defined('END_LOG_URL')) $log['url'] = END_LOG_URL;
		model('log')->update(END_LOG_ID,$log);
	}
	$view_data['login_user'] = $_SESSION['login_user'];
}
