<?php
/**********************************
*     		EndCMS
*       www.endcms.com
*         ©2008-now
* under Creative Commons License
**********************************/

END_MODULE != 'admin' && die('Access Denied');

$m = $_GET['m'];
$value = $_POST['value'];
$id = intval($_GET['id']);
$table = $_GET['table'];
$column = $_GET['column'];

$_allowed = ',';
foreach($_SESSION['login_user']['allowed_controllers'] as $_c=>$_v) $_allowed.= $_c.',';
define('END_RESPONSE','text');

check_allowed($table,$m,1);


load_models();


if (!$id)
{
	ajax_exit('id needed');
}

if ($table)
{
	if (strpos(",$_allowed,",",$table,") !== false)
	{
		if ($end_models[$table.'_list'])
			$obj = model($table,$end_models[$table.'_list']['model_path']);
		else
			$obj = model($table);
	}
}

if (!$obj)
{
	ajax_exit('table needed');
}

if ($m == 'update')
{
	if ($id && $column )
	{
		if ($obj->update($id,array($column => $value)))
		{
			$arr = $obj->get_one($id);
			ajax_exit($arr[$column]);
		}
		else
		{
			ajax_exit(lang("UPDATE_ERROR"));
		}
	}
}
else if ($m == 'delete')
{
	if ($id)
	{
		if ($obj->delete($id,true))
		{
			ajax_exit('success');
		}
		else
		{
			ajax_exit('error');
		}
	}
}
else if ($m == 'new')
{
	if ($_POST)
	{
		if ( $obj->add( $_POST ) )
		{
			ajax_exit($db->insert_id());
		}
	}
	ajax_exit('error');
}
else if ($m == 'update_password') 
{
	if ($id && $value)
	{
		$password = end_encode($value);
		if ($obj->update($id,array('password' => $password)))
		{
			ajax_exit('success');
		}
		else
		{
			ajax_exit('error');
		}
	}
}


function ajax_exit($s)
{
	echo $s;
	die;
}
?>