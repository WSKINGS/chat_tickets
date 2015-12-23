<?php
/**
 * certificate model config
 *
 * @author lidongxu @ 2014-5-28
 */

$certificate_status_options = array(
	-1 => '<span style="color:grey">无效</span>',
	1 => '<span style="color:green">有效</span>'
);

$end_models['wt_certificate'] = array(
	'type' => 'list', //表示这是一个列表型的模型，对应一个数据库的表
	'name' => '身份验证',	//某型的名字，可以把一个栏目配置成某个模型
	'list_items'=>30, //后台每页显示
	'no_category'=>true,
	'category_fields'=> array(
		'name'=>array(
			'name'=>lang('Name'),
			'type'=>'text',
			'null'=>false
		)
	),
	'fields' => array(
		'activity_id'=>array(
			'name'=>'活动ID',
			'type'=>'text',
			'null'=>false,
			'description'=>'标识验证信息所属活动'
		),
		'email'=>array(
			'name'=>'Email(小写)',
			'type'=>'text',
			'null'=>false,
			'description'=>'全小写的email地址，用于身份认证标识'
		),
		'certificate_number'=>array(
			'name'=>'认证码',
			'type'=>'text',
			'null'=>false,
			'description'=>'系统生成的验证码'
		),
		'status'=>array(
			'name' => '有效',
			'type' => 'select',
			'options' => $certificate_status_options,
			'null'=>false
		)
	),
	'list_fields' => array(
		'certificate_id'=>array(
			'name'=>'ID',
			'width'=>'30',
			'sort'=>true,
			'align'=>'center',
		),
		'activity_id'=>array(
			'name'=>'活动ID',
			'width'=>'auto',
			'sort'=>true,
		),
		'email'=>array(
			'name'=>'Email',
			'width'=>'auto',
			'sort'=>true,
		),
		'certificate_number'=>array(
			'name'=>'验证码',
			'width'=>'auto',
			'search'=>true
		),
		'status'=>array(
			'name'=>'状态',
			'width'=>'auto',
			'type'=>'select',
			'options'=>$certificate_status_options,
			'edit'=>true
		),
		'create_time'=>array(
			'name'=>'创建时间',
			'width'=>'auto',
			'filter'=>'end_show_int2date'
		),
		'_options'=>array(
			'name'=>'操作',
			'width'=>100,
			'filter'=>'show_wt_certificate_options'
		)
	)
);

$end_rights[] = array(
	'name'=>'wt_certificate',
	'description'=>'选项',
	'rights'=>array('view','delete','update','add')
);

function show_wt_certificate_options($item)
{
	$id = 'certificate_id';
	end_show_view_button($item[$id]);
	end_show_edit_button($item[$id]);
	end_show_delete_button($item[$id]);
}
