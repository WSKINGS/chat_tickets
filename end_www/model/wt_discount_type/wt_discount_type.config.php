<?php
/**
 * discount type model config
 *
 * @author lidongxu @ 2013.11.16
 */

$discount_type_status_options = array(
	-1 => '<span style="color:grey">无效</span>',
	1 => '<span style="color:green">有效</span>'
);

$discount_type_use_code_options = array(
	-1 => '<span style="color:grey">不使用</span>',
	1 => '<span style="color:green">使用</span>'
);

$end_models['wt_discount_type'] = array(
	'type' => 'list', //表示这是一个列表型的模型，对应一个数据库的表
	'name' => '折扣方式',	//某型的名字，可以把一个栏目配置成某个模型
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
		'name'=>array(
			'name'=>'折扣方式名',
			'type'=>'text',
			'null'=>false
		),
		'key_name'=>array(
			'name'=>'标识key',
			'type'=>'text',
			'null'=>false,
			'description'=>'标识key，唯一识别支付渠道'
		),
		'order_id'=>array(
			'name'=>'排序',
			'type'=>'text',
			'null'=>true,
			'description'=>'输入浮点数，数字越小越靠前'
		),
		'use_code'=>array(
			'name'=>'使用折扣码',
			'type'=>'select',
			'options'=>$discount_type_use_code_options
		),
		'status'=>array(
			'name' => '有效',
			'type' => 'select',
			'null' => false,
			'options' => $discount_type_status_options 
		)
	),
	'list_fields' => array(
		'type_id'=>array(
			'name'=>'ID',
			'width'=>'30',
			'sort'=>true,
			'align'=>'center',
		),
		'order_id'=>array(
			'name'=>'排序',
			'sort'=>true,
			'edit'=>true,
			'type'=>'text'
		),
		'name'=>array(
			'name'=>'折扣方式名',
			'width'=>'auto',
			'search'=>true
		),
		'key_name'=>array(
			'name'=>'标识key',
			'width'=>'auto',
			'search'=>true
		),
		'use_code'=>array(
			'name'=>'使用折扣码',
			'width'=>'auto',
			'type'=>'select',
			'options'=>$discount_type_use_code_options
		),
		'status'=>array(
			'name'=>'状态',
			'width'=>'auto',
			'type'=>'select',
			'options'=>$discount_type_status_options,
			'edit'=>true
		),
		'_options'=>array(
			'name'=>'操作',
			'width'=>100,
			'filter'=>'show_wt_discount_type_options'
		)
	)
);

$end_rights[] = array(
	'name'=>'wt_discount_type',
	'description'=>'选项',
	'rights'=>array('view','delete','update','add')
);

function show_wt_discount_type_options($item)
{
	$id = 'type_id';
	end_show_view_button($item[$id]);
	end_show_edit_button($item[$id]);
	end_show_delete_button($item[$id]);
}
