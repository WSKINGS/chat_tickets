<?php
END_MODULE != 'admin' && die('Access Denied');

filter_array($_GET,'action,m,intval:category_id',true);
$category = model('category');

load_models();

if ($m == 'new_category')
{
	check_allowed('category','add');
	$data = filter_array($_POST,'name!,intval:parent_id,status!');
	if ($data && !$data['parent_id']) $data['parent_id'] = 0;
	if ($data)
	{
		if ($new_id = $category->add( $data ) )
		{
			header('Location: admin.php?p=category&action=edit_category&category_id='.$new_id);
			die;
		}
		else
		{
			$action = 'new_category';
			$err_msg = lang('CATEGOTY_NEW_ERROR');
		}
	}
	else
	{
		$action = 'new_category';
		$err_msg = lang('CATEGOTY_FILL_ALL');
		$view_data['category'] = $_POST;
	}
}
else if ($m == 'edit_category')
{
	check_allowed('category','update');
	$_category = $category->get_one($category_id);
	$data = array('category_id'=>$category_id);
	$errors = array();
	$_fields = $end_models[$_category['status']]['category_fields'];
	
	if ( intval($_POST['parent_id']) < 0 )
	{
		$errors[] = lang('Please choose a parent category');
	}
	else
	{
		$data['parent_id'] = intval($_POST['parent_id']);
	}
	if ($_fields)
	{
		//处理提交的数据
		include('edit_field.php');
	}
	//提交数居后的处理
	if ($_fields['__after_edit']) $_fields['__after_edit']($data);

	if (count($data)>0 && count($errors) == 0)
	{
		$re = $category->update( $category_id, $data);
		if ( $re )
		{ 
			$return_to = $_SESSION['backurl']?$_SESSION['backurl']:'admin.php?p=category';
			end_exit(lang('CATEGORY_EDIT_SUCCESS'),$return_to,1);
		}
		else
		{
			$action = 'edit_category';
			$err_msg = lang('UPDATE_FAILED');
		}
	}
	else
	{
		$action = 'edit_category';
		//生成错误提示信息
		$err_msg = array();
		foreach($errors as $key=>$err)
		{
			$err_msg[] = $_fields[$key]['name'].' '.$err;
		}
		$err_msg = join('<br />',$err_msg);
	}
}

if($action == 'ajax_get')
{
	if (!$category_id) $category_id=0;
	$data['tree'] = model('category')->get_list(array('parent_id'=>$category_id));
	$data['depth'] = $_GET['depth']*1;
	$tmp = template('category_list_item.html');
	$tmp->assign($data);
	$tmp->display();
	die;
}
elseif ($action == "edit_category")
{
	$_SESSION['backurl'] = ($_GET['backurl'])?$_GET['backurl']:$_SERVER['HTTP_REFERER'];
	if ($action == "edit_category")
	{
		if (!$category_id) end_exit("need category_id!",'javascript:history.go(-1)',5);
		$_category = $category->get_one($category_id);
	}
	
	$edit_view = 'category_edit.html';
	$temp = template($edit_view);
	if (count($_POST)>0)
	{
		$__category = $_POST;
	}
	else
	{
		$__category = $_category;
	}

	$temp->assign( array(
		'content' => $__category,
		'err_msg' => $err_msg,
		'fields'=>$end_models[$_category['status']]['category_fields'],
		'category_id' => $category_id,
		'login_user' => $_SESSION['login_user'],
		'category_tree' => print_category_tree($category->tree_category(0),$_category['parent_id'],$category_id),
	));
	$view_data['page_description'] = lang('EDIT_CATEGORY');
	$view_data['page_content'] = $temp->result();
}

$view_data['this_category'] = $category->get_one($category_id);

if (!$action && !$m)
{
	define('END_LOG_INFO',($category_id)?LANG_NAVI_CATEGORY.'&gt;'.$view_data['this_category']['name']:LANG_NAVI_CATEGORY);
	define('END_LOG_URL',($category_id)?'admin.php?p=category&category_id='.$view_data['this_category']['category_id']:'admin.php?p=category');
}
//显示分类和项目列表

//$categories = $category->get_list( array('parent_id'=>$category_id) );
$_tree = $category->tree_category(0);

//$view_data['tree'] = $category->get_list(array('parent_id'=>0));
//$category->flat_tree($_tree,$view_data['tree']);

//$view_data['all_category'] = print_category_tree_link('admin.php?p=category&category_id=',$_tree,$category_id);
$view_data['category_tree'] = print_category_tree($_tree);
//$view_data['categories'] = $categories;
$view_data['page_description'] = lang('CATEGORIES_LIST');
$view_data['err_msg'] = $err_msg;
$view_data['success_msg'] = $success_msg;
$view_data['category_id'] = $category_id;
//$view_data['position'] = $category->position_category($category_id); 
$view_data['end_models'] = $end_models;
//unset($view_data['position'][count($view_data['position'])-1]);


$view_data['statuses'] = $end_models;


function show_status($s)
{
	global $end_models;
	return $end_models[$s]['name']?$end_models[$s]['name']:lang('unknown');
}
?>