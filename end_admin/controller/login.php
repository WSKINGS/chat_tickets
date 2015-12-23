<?php
!defined('END_MODULE') && die('Access Denied');
$module = $_GET['module'];
$m = $_GET['m'];
$back_url = $_REQUEST['backurl']?$_REQUEST['backurl']:'admin.php';

if ($m == 'login')
{
	$admin = model('admin');
	$data = filter_array($_POST,'name!,end_encode:password!');
	if ($data)
	{
		$u = $admin->check_password($data['name'],$data['password']);
		if ($u['admin_id'])
		{
			$_SESSION['login_user'] = $u;
			header('Location:'.$back_url);
			//end_exit(lang('LOGIN_SUCCESS'),$back_url,1);
		}
		else
		{
			$err_msg = lang('LOGIG_ERROR');
		}
	}
}
else if ($m == 'logout')
{
	unset($_SESSION['login_user']);
	end_exit(lang('LOGOUT_SUCCESS'),$back_url,1);
}	

$view_data['backurl'] = $back_url;
$view_data['err_msg'] = $err_msg;

