<?php
//入口验证
END_MODULE != 'admin' && die('Access Denied');
//过滤数据，并写入全局变量
filter_array($_GET,'m,action,intval:item_id,intval:category_id,item_type,status',true);

load_models();

$category = model('category');
$err_msg = '';
$success_msg = '';

if ($action == 'get_tree')
{
	
}

/* 必须传入 category_id*/
if ($category_id || $item_type)
{
	if (!$item_type)
	{
		$this_category = $category->get_one($category_id);
		$item_type = preg_replace('/_list$/i','',$this_category['status']);
		define('END_ADMIN_CATEGORY_ID',$category_id);
	}
	else
	{
		$this_category = $category->get_one(array('status'=>$item_type.'_list'));
		$category_id = $this_category['category_id'];
		define('END_ADMIN_CATEGORY_ID',$category_id);
	}
	if ($item_type)
	{
		define('END_ADMIN_ITEM_TYPE',$item_type);
	}
	else
		die('please provide valid category_id or item_type!');
}

//如果不是列表页面
if ($this_category && !preg_match('/_list$/',$this_category['status']))
{
	$item_type = false;
	$view_data['category_fields'] = $end_models[$this_category['status']]['category_fields'];
	$view_data['category_type'] = $end_models[$this_category['status']]['name'];
	$view_data['category'] = $this_category;
}

if ($item_type)
{
	$item = model($item_type,$end_models[$item_type.'_list']['model_path']);
	
	$item_model = $end_models[$item_type.'_list'];
	$_fields = $item_model['fields'];

	define('ITEM_TYPE',$item_type);
	if ($m == 'edit_item')
		check_allowed($item_type,'update');
	else if ($m == 'new_item')
		check_allowed($item_type,'add');
	else if (!$m)
		check_allowed($item_type,'view');

	//添加或者修改，提交处理部分
	if ($m == 'edit_item' || $m == 'new_item')
	{
		$data = array();
		if ($item_id) $data[$item->id] = $item_id;
		$errors = array();
		if (!$item_model['no_category'])
		{
			if (!intval($_POST['category_id']))
				$errors[] = "请选择分类";
			else
				$data['category_id'] = intval($_POST['category_id']);
		}
		
		
		//点击保存草稿或者直接发布按钮
		if (isset($_POST['saveas']))
		{
			foreach($_POST['saveas'] as $_k=>$_v)
				$data['status'] = intval($_k);
		}
		
		//处理提交的数据
		include('edit_field.php');

		//提交数居后的处理
		if ($_fields['__after_edit']) $_fields['__after_edit']($data);
		//提交数居后的处理 -- by liudan
		if ($m == 'new_item' && $_fields['__after_edit_ld']) $_fields['__after_edit_ld']($data);

		if (count($data)>0 && count($errors) == 0)
		{
			//数据合法，写入数据库
			if ($item_id)
			{
				$re = $item->update( $item_id, $data);
			}
			else
			{
				$re = $item->add($data);

				if ($re && intval($re)) $item_id = intval($re);
			}
			if ($re)
			{
				//写入数据库后
				if ($_fields['__after_db']) $_fields['__after_db']($item->get_one($item_id));
				$return_to = $_POST['return_to']?$_POST['return_to']:'admin.php?p=item&category_id='.$category_id;
				end_exit(lang('ITEM_SAVE_SUCCESS'),$return_to,1);
			}
			else
			{
				$action = 'edit_item';
				$err_msg = lang('ITEM_UNKNOWN_ERROR');
			}
		}
		else
		{
			$action = 'edit_item';
			//生成错误提示信息
			$err_msg = array();
			foreach($errors as $key=>$err)
			{
				$err_msg[] = $_fields[$key]['name'].' '.$err;
			}
			$err_msg = join('<br />',$err_msg);
		}
	}
	///////////////////////////////以下为显示控制部分////////////////////////////////
	//添加或者修改

	if ($action == 'edit_item' || $action =='new_item' || $action == 'view_item')
	{
		if(isset($_GET['category_id']) && $_GET['category_id']==2)
		{
			// print_r($_POST);die;
		}
		else
		{
			if($action == 'view_item') {
				$temp = template('item_view.html');
			} else {
				$temp = template('item_edit.html');
			}
			
			if (count($_POST)>0) //re-edit
			{
				$_item = $_POST;
			}
			elseif ($item_id) //edit
			{
				$_item = $item->get_one($item_id);
				$_item['return_to'] = $_SERVER['HTTP_REFERER'];
				//显示数居前的处理
				if ($_fields['__before_edit']) $_fields['__before_edit']($_item);
			}
			else //new
			{
				$_item = array();
			}
		
			$temp->assign( array(
				'content' => $_item,
				'item_id' => $item_id,
				'categories' => $category->get_list(),
				'category_id' => $category_id,
				'fields'=>$_fields,
				'statuses'=>$end_models[$item_type.'_list']['status'],
				'this_category' => $this_category,
				'category_name' => $this_category['name'],
				'login_user' => $_SESSION['login_user'],
				'category_tree' => print_category_tree(
					$category->tree_category(array('status'=>$this_category['status'])),
					$category_id),
			));

			$view_data['page_content'] = $temp->result();
		}
	} 
	//显示内容列表
	else
	{
		$_SESSION['end_last_list_page'] = $_SERVER['REQUEST_URI'];
		$categories_arr = $category->get_list();
		$categories = array();
		$all_category = array();
		foreach($categories_arr as $c)
		{
			if ($c['parent_id'] == $category_id)
			{
				$categories[] = $c;
			}
			$all_category[$c['category_id']] = $c['name'];
		}
		
		//分类
		if($item_model['no_category'])
			$cond = array('where'=>'1=1');
		else
			$cond = array('where'=>"(category_id='$category_id' OR category_id=0)");
		//status处理
		if($item_model['status'])
		{
			if (isset($_GET['status']))
				$cond['status'] = intval($_GET['status']);
			else
				$cond['where'].=' AND `status` != -1 ';
		}
		//搜索
		if (is_array($_GET['search']))
		{
			foreach($_GET['search'] as $key=>$val)
			{
				$val = str_replace('*','%',$val);
				$cond['where'].= " AND `$key` LIKE '%".mysql_real_escape_string($val)."%' ";
			}
		}
		//排序
		if ($_GET['order'] && $_GET['asc'])
			$cond['order'] = $_GET['order'].' asc';
		else if ($_GET['order'])
			$cond['order'] = $_GET['order'].' desc';
		
		$pagesize = isset($end_models[$item_type.'_list']['list_items'])?$end_models[$item_type.'_list']['list_items']:20; //默认20条每页
		if (isset($_GET['export'])) $pagesize = 100000;
		
		//分页
		$items = end_page(
				$item,
				$cond,
				$pagesize
			);

		$view_data['categories'] = $categories;
		$view_data['items'] = $items;
		if ($category_id)
		{
			$view_data['this_category'] = $this_category;
			$view_data['this_category']['is_list'] = preg_match('/_list$/',$this_category['status']);
		}
		else
		{
			$view_data['this_category'] = array( 'is_list' => true);
		}
		define('END_LOG_INFO',LANG_NAVI_ITEM.'&gt;'.$this_category['name']);
		define('END_LOG_URL','admin.php?p=item&category_id='.$this_category['category_id']);
		$view_data['page_description'] = lang('ITEM_LIST');
	}

	//nav buttons
	if (isset($end_models[$item_type.'_list']['status']))
	{
		$statuses = array();
		foreach($end_models[$item_type.'_list']['status'] as $_key=>$_val) $statuses[] = array('index'=>$_key,'value'=>$_val);
	
		$view_data['statuses'] = $statuses;
	}
	$view_data['category_tree'] = print_category_tree($category->tree_category(array('status'=>$this_category['status'])));
	$view_data['current_status_all'] = isset($_GET['status'])?false:true;
	$view_data['err_msg'] = $err_msg;
	$view_data['success_msg'] = $success_msg;
	
	$view_data['status'] = $view_data['current_status_all']?'-999':$status;
	$view_data['table'] = $item_type;
	$view_data['category_id'] = $category_id;

	if (file_exists(END_ROOT.'end_www/model/'.$item_type.'/end_admin_item_list.html'))
	{
		$list_tmp = template('end_admin_item_list.html',END_ROOT.'end_www/model/'.$item_type.'/');
	}
	else
	{
		$list_tmp = template('item_list.html');
	}
	$list_tmp->assign('item_model',$item);
	$list_tmp->assign('list_fields',$end_models[$item_type.'_list']['list_fields']);
	$list_tmp->assign($view_data);
	$view_data['list_content'] = $list_tmp->result();
	if (isset($_GET['export']))
	{
		$s = strip_tags($view_data['list_content'],'<table><thead><tbody><tr><td><th><span>');
		$s = preg_replace('/\<table[^\>]+\>/','<table>',$s);
		$s = preg_replace('/\<th\s[^\>]+\>/','<th>',$s);
		$arr = explode('<table>',$s);$s = '<table border="1">'.$arr[1];
		$s = preg_replace('/\<\/table\>(\n*.*\n*)*$/','</table>',$s);

		$s = '<meta http-equiv="Content-Type" content="application/vnd.ms-excel; charset=UTF-8" />'.$s.'';
		
		$filename = $this_category['name'].'_'.date("YmdHi").'.xls';
		if (preg_match('/MSIE/',$_SERVER['HTTP_USER_AGENT']))  $filename = rawurlencode($filename);  
		
		header('Pragma: public');  
		header('Last-Modified: '.gmdate('D, d M Y H:i:s') . ' GMT');  
		header('Cache-Control: no-store, no-cache, must-revalidate, pre-check=0, post-check=0, max-age=0');  
		header('Pragma: no-cache');  
		//header('Content-Transfer-Encoding: binary');  
		header('Content-Encoding: utf-8');  

		header("Content-type: application/vnd.ms-excel; charset=utf-8; format=attachment;");
		header('Content-Disposition: attachment; filename="'.$filename.'"');  
		header("Content-length:".strlen($s));
		echo $s;
		die;
	}
}
$view_data['position'] = $category->position_category($category_id);
$_tree = $category->tree_category(0);
$view_data['all_category'] = print_category_tree_link('admin.php?p=item&category_id=',$_tree,$category_id);
$view_data['search'] = isset($_GET['search'])?$_GET['search']:'';
$view_data['category_id'] = $category_id;
