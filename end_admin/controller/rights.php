<?php
/**********************************
*     		EndCMS
*       www.endcms.com
*         ©2008-now
* under Creative Commons License
**********************************/

END_MODULE != 'admin' && die('Access Denied');
$rights = model('rights');
$m = $_GET['m'];
$category = model('category');
load_models();


$rights_id = $_GET['rights_id'];
if ($m == 'new_group')
{
	check_allowed('rights','add');
	$data = filter_array($_POST,'name!');
	if ($data && $rights->add($data))
	{
		end_exit(lang('rights_add_success'),'admin.php?p=rights');
	}
	else
		end_exit(lang('rights_add_failed'),'admin.php?p=rights');
}
elseif ($m == 'config' && $rights_id)
{
	check_allowed('rights','update');
	$r = array();
	foreach($_POST as $key=>$val)
	{
		if (strtolower($val) == 'on') $r[] = $key;
	}
	$data['rights'] = join(',',$r);
	if ($rights->update($rights_id,$data)) end_exit(lang('rights_updated'),'admin.php?p=rights');
}
else
{
	define('END_LOG_INFO',LANG_TITLE);
	define('END_LOG_URL','admin.php?p=rights');
}
if ($rights_id)
{
	$_SESSION['login_user']['rights']['limit_category_id'] = false;
	$view_data['rights'] = $end_rights;
	$arr = $rights->get_one($rights_id);
	$view_data['this_group'] = $arr;
	$category->flat_tree($category->tree_category(0),$view_data['categories']);
	$r = explode(',',$arr['rights']);
	foreach($r as $val) $view_data['this_rights'][$val] = true;
}
$view_data['groups'] = $rights->get_list();
$view_data['page_description'] = lang('rights');
