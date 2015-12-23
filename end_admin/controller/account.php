<?php
END_MODULE != 'admin' && die('Access Denied');
define('END_RESPONSE','text');
$m = $_GET['m'];
$admin = model('admin');

if ($m == 'update_password')
{
	check_allowed('account','update',1);
	$data = filter_array($_POST,'end_encode:old_password,end_encode:password');
	
	if ($data && $admin->exists(array('admin_id'=>get_admin_id(),'password'=>$data['old_password'])))
	{
		if ($admin->update(get_admin_id(),array('password'=>$data['password'])))
		{
			$_SESSION['login_user'] = $admin->get_one(get_admin_id());
			echo lang('admin_UPDATE_SUCCESS');
			die;
		}
		else
		{
			echo  lang('admin_UPDATE_ERR');
			die;
		}
	}
	else
	{
		echo  lang('ADMIN_OLD_PASSWORD_ERROR');
		die;
	}
}
else if ($m == 'get_admin')
{
	$arr = $admin->get_one(get_admin_id());
	echo json_encode($arr);
	die;
}

$view_data['admin'] = $admin->get_one(get_admin_id());
$view_data['page_description'] = lang('MY_ACCOUNT');
