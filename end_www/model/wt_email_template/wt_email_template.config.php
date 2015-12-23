<?php
/**
 * emial template model config
 *
 * @author lidongxu @ 2014-5-23
 */

$email_template_status_options = array(
	-1 => '<span style="color:grey">无效</span>',
	1 => '<span style="color:green">有效</span>'
);

$end_models['wt_email_template'] = array(
	'type' => 'list', //表示这是一个列表型的模型，对应一个数据库的表
	'name' => '邮件模板',	//某型的名字，可以把一个栏目配置成某个模型
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
		'key_name'=>array(
			'name'=>'模板key',
			'type'=>'text',
			'null'=>false,
			'description'=>'唯一标识邮件模板'
		),
		'subject'=>array(
			'name'=>'邮件主题',
			'type'=>'text',
			'null'=>false
		),
		'body'=>array(
			'name'=>'邮件正文',
			'type'=>'richtext',
			'null'=>false
		),
		'attachment'=>array(
			'name'=>'附件',
			'type'=>'text',
			'null'=>true
		),
		'status'=>array(
			'name' => '有效',
			'type' => 'select',
			'options' => $email_template_status_options,
			'null'=>false
		)
	),
	'list_fields' => array(
		'template_id'=>array(
			'name'=>'ID',
			'width'=>'30',
			'sort'=>true,
			'align'=>'center',
		),
		'key_name'=>array(
			'name'=>'模板key',
			'width'=>'auto',
			'sort'=>true,
			'type'=>'text',
			'search'=>true
		),
		'subject'=>array(
			'name'=>'邮件主题',
			'width'=>'auto',
			'sort'=>true,
			'type'=>'text',
			'search'=>true
		),
		'status'=>array(
			'name'=>'状态',
			'width'=>'auto',
			'type'=>'select',
			'options'=>$email_template_status_options,
			'edit'=>true
		),
		'create_time'=>array(
			'name'=>'创建时间',
			'width'=>'auto',
			'sort'=>true,
			'filter'=>'end_show_int2date'
		),
		'update_time'=>array(
			'name'=>'最近修改时间',
			'width'=>'auto',
			'sort'=>true,
			'filter'=>'end_show_int2date'
		),
		'_options'=>array(
			'name'=>'操作',
			'width'=>100,
			'filter'=>'show_wt_email_template_options'
		)
	)
);

$end_rights[] = array(
	'name'=>'wt_email_template',
	'description'=>'邮件模板数据',
	'rights'=>array('view','delete','update','add')
);

function show_wt_email_template_options($item)
{
	$id = 'template_id';
	end_show_view_button($item[$id]);
	end_show_edit_button($item[$id]);
	end_show_delete_button($item[$id]);
}
