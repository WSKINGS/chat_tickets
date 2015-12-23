<?php
/**
 * user model config
 *
 * @author lidongxu
 */

$user_status_options = array(
	-1 =>'<span style="color:grey">无效</span>',
	1 =>'<span style="color:green">有效</span>'
);

$user_role_options = array(
	"admin"=>'<span style="color:red">管理员</span>',
	'host'=>'<span style="color:green">组办方</span>',
	'operator'=>'<span style="color:grey">业务员</span>'
);

$end_models['wt_user'] = array(
	'type' => 'list', //表示这是一个列表型的模型，对应一个数据库的表
	'name' => '用户列表',	//某型的名字，可以把一个栏目配置成某个模型
	'list_items'=>20, //后台每页显示
	'no_category'=>true,
	'category_fields'=> array(
		'name'=>array(
			'name'=>lang('Name'),
			'type'=>'text',
			'null'=>false
		)
	),
	'fields' => array(
		'username'=>array(
			'name'=>'用户名',
			'type'=>'text',
			'null'=>false
		),
		'password'=>array(
			'name'=>'密码',
			'type'=>'text',
			'null'=>true,
			'width'=>200,
			'prefilter'=>'show_user_empty_password',
			'description'=>'如不需要修改密码，请留空'
		),
		'name'=>array(
			'name'=>'姓名',
			'type'=>'text',
			'null'=>true,
			'search'=> true
		),
		'company_name'=>array(
			'name'=>'公司名',
			'type'=>'text',
			'null'=>true
		),
		'email'=>array(
			'name'=>'Email',
			'type'=>'text',
			'null'=>true
		),
		'phone'=>array(
			'name'=>'手机',
			'type'=>'text',
			'null'=>true
		),
		'secret_key'=>array(
			'name'=>'用户密钥',
			'type'=>'text',
			'null'=>true
		),
		'role'=>array(
			'name'=>'用户角色',
			'type'=>'select',
			'options'=>$user_role_options
		),
		'status' => array(
			'name' => '有效',
			'type' => 'select',
			'options'=>$user_status_options,
			'null' => false
		)
	),
	'list_fields' => array(
		'user_id'=>array(
			'name'=>'ID',
			'width'=>'30',
			'sort'=>true,
			'align'=>'center',
		),
		'username'=>array(
			'name'=>'用户名',
			'width'=>'auto',
			'sort'=>true,
			'type'=>'text',
			'search'=>true
		),
		'name'=>array(
			'name'=>'姓名',
			'width'=>'auto',
			'sort'=>true,
			'type'=>'text',
			'search'=>true
		),
		'company_name'=>array(
			'name'=>'公司',
			'width'=>'auto',
			'type'=>'text',
			'search'=>true
		),
		'phone'=>array(
			'name'=>'phone',
			'width'=>'auto',
			'type'=>'text',
			'search'=>true
		),
		'role'=>array(
			'name'=>'用户角色',
			'width'=>'auto',
			'type'=>'select',
			'options'=>$user_role_options
		),
		'status'=>array(
			'name'=>'有效',
			'edit'=>true,
			'type'=>'select',
			'options'=>$user_status_options
		),
		'create_time'=>array(
			'name'=>'创建时间',
			'filter'=>'end_show_int2date'
		),
		'_options'=>array(
			'name'=>'操作',
			'width'=>100,
			'filter'=>'show_wt_user_options'
		)
	)
);

function show_user_empty_password()
{
	return '';
}

$end_rights[] = array(
	'name'=>'wt_user',
	'description'=>'用户数据',
	'rights'=>array('view','delete','update','add')
);

function show_wt_user_options($item)
{
	$id = 'user_id';
	end_show_view_button($item[$id]);
	end_show_edit_button($item[$id]);
	end_show_delete_button($item[$id]);
}

