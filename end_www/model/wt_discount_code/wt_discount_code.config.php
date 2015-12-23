<?php
/**
 * discount code model config
 *
 * @author lidongxu
 */

$discount_code_status_options = array(
	-1 => '<span style="color:grey">无效</span>',
	1 => '<span style="color:green">有效</span>'
);

$discount_code_used_options = array(
	-1 => '<span style="color:grey">未使用</span>',
	1 => '<span style="color:green">已使用</span>'
);

$end_models['wt_discount_code'] = array(
	'type' => 'list', //表示这是一个列表型的模型，对应一个数据库的表
	'name' => '折扣码',	//某型的名字，可以把一个栏目配置成某个模型
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
			'description'=>'标识折扣码所属活动'
		),
		'discount_type_id'=>array(
			'name'=>'折扣方式ID',
			'type'=>'text',
			'null'=>false,
			'description'=>'标识折扣码所属折扣方式'
		),
		'code'=>array(
			'name'=>'码',
			'type'=>'text',
			'null'=>false
		),
		'used'=>array(
			'name'=>'使用状态',
			'type'=>'select',
			'options'=>$discount_code_used_options
		),
		'used_time'=>array(
			'name'=>'使用时间',
			'type'=>'datetime',
			'null'=>true
		),
		'status'=>array(
			'name' => '有效',
			'type' => 'select',
			'options' => $discount_code_status_options,
			'null'=>false
		)
	),
	'list_fields' => array(
		'code_id'=>array(
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
		'discount_type_id'=>array(
			'name'=>'折扣方式ID',
			'width'=>'auto',
			'sort'=>true,
		),
		'code'=>array(
			'name'=>'折扣码',
			'width'=>'auto',
			'search'=>true
		),
		'used'=>array(
			'name'=>'使用状态',
			'width'=>'auto',
			'type'=>'select',
			'options'=>$discount_code_used_options
		),
		'used_time'=>array(
			'name'=>'使用时间',
			'width'=>'auto',
			'filter'=>'end_show_int2date'
		),
		'status'=>array(
			'name'=>'状态',
			'width'=>'auto',
			'type'=>'select',
			'options'=>$discount_code_status_options,
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
			'filter'=>'show_wt_discount_code_options'
		)
	)
);

$end_rights[] = array(
	'name'=>'wt_discount_code',
	'description'=>'选项',
	'rights'=>array('view','delete','update','add')
);

function show_wt_discount_code_options($item)
{
	$id = 'code_id';
	end_show_view_button($item[$id]);
	end_show_edit_button($item[$id]);
	end_show_delete_button($item[$id]);
}
