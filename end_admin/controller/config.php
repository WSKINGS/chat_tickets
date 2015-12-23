<?php
/**********************************
*     		EndCMS
*       www.endcms.com
*         ©2008-now
* under Creative Commons License
**********************************/

END_MODULE != 'admin' && die('Access Denied');
$m = $_GET['m'];

$_config = model('config');
$config_id = intval($_GET['config_id']);


if ($m == "new_config")
{
	check_allowed('config','add');
	$data = filter_array($_POST,'name!,description!,type!');
	if ($data)
	{
		if ($_config->add( $data ) )
		{
			end_exit(lang('CONFIG_NEW_SUCCESS'),'admin.php?p=config',1);
		}
		else
		{
			$action = 'new_category';
			$err_msg = lang('CONFIG_NEW_ERROR');
		}
	}
	else
	{
		$action = 'new_config';
		$err_msg = lang('CONFIG_FILL_ALL');
		$view_data['thisconfig'] = $_POST;
	}
}

$view_data['err_msg'] = $err_msg;
$view_data['items'] = $_config->get_list();
$view_data['page_description'] = lang('TITLE');

?>