<?php
/**********************************
*     		EndCMS
*       www.endcms.com
*         ©2008-now
* under Creative Commons License
**********************************/

END_MODULE != 'admin' && die('Access Denied');

$admin_id = intval($_GET['admin_id']);
$m = $_GET['m'];
$action = $_GET['action'];
$admin = model('admin');
$rights = model('rights');
$rights_id = isset($_GET['rights_id'])?intval($_GET['rights_id']):false;

if ($m == 'new_admin')
{
	check_allowed('admin','add');
	$data = filter_array($_POST,'name!,end_encode:password!,email');
	
	if ($admin->exists(array('name'=>$data['name'])))
	{
		end_exit(lang("ADMIN_EXISTS"),'admin.php?p=admin',1);
	}
	else if ( $admin->add($data) )
	{
		end_exit(lang('ADMIN_NEW_SUCCESS'),'admin.php?p=admin',1);
	}
	else
	{
		$err_msg = lang('ADMIN_NEW_ERROR');
		$action = 'new_admin';
	}
}
else
{
	define('END_LOG_INFO',LANG_TITLE);
	define('END_LOG_URL','admin.php?p=admin');
}
$view_data['page_description'] = lang('ADMIN_INDEX');
$view_data['err_msg'] = $err_msg;
$view_data['admin_id'] = $admin_id;
$view_data['rights'] = $rights->get_list();

$cond = array();
if ($rights_id !== false)
{
	$cond['rights_id'] = $rights_id;
}

//order added by longbill
if ($_GET['order'] && $_GET['asc'])
	$cond['order'] = $_GET['order'].' asc';
else if ($_GET['order'])
	$cond['order'] = $_GET['order'].' desc';
else
	$cond['order']=$admin->id.' DESC';
if (is_array($_GET['search']))
{
	foreach($_GET['search'] as $key=>$val)
	{
		$val = str_replace('*','%',$val);
		$cond['where'] = " `$key` LIKE '%".mysql_escape_string($val)."%' ";
	}
}

$admins = end_page($admin,$cond,(intval($config['admin_admin_page_size']))?intval($config['admin_admin_page_size']):20);
$_rights = array();
foreach($view_data['rights'] as $val)
{
	$_rights[$val['rights_id']] = $val['name'];
}
for($i=0;$i<count($admins);$i++) 
{
	$admins[$i]['rights_group_name'] = $_rights[$admins[$i]['rights_id']]?$_rights[$admins[$i]['rights_id']]:lang('DEFAULT_RIGHTS_GROUP');
}

$view_data['admins'] = $admins;
$view_data['rights_id'] = ($rights_id===false)?-1:$rights_id;
?>