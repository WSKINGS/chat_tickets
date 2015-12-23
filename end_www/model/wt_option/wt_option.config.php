<?php
/**
 * hotel model config
 *
 * @author liudanking @ 2013.11.16
 */

$option_status_options = array(
	-1 => '<span style="color:green">无效</span>',
	1 => '<span style="color:grey">有效</span>'
);

$end_models['wt_option'] = array(
	'type' => 'list', //表示这是一个列表型的模型，对应一个数据库的表
	'name' => '辅助选项列表',	//某型的名字，可以把一个栏目配置成某个模型
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
			'name'=>'选项名称',
			'type'=>'text',
			'null'=>false
		),
		'key_name'=>array(
			'name'=>'选项组',
			'type'=>'text',
			'null'=>false,
			'description'=>'选项分组关键字'
		),
		'order_id'=>array(
			'name'=>'排序',
			'type'=>'text',
			'null'=>true,
			'description'=>'输入浮点数，数字越小越靠前'
		),
		'status'=>array(
			'name' => '有效',
			'type' => 'select',
			'null' => false,
			'options' => $option_status_options 
		)
	),
	'list_fields' => array(
		'option_id'=>array(
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
			'name'=>'选项名称',
			'width'=>'auto',
			'sort'=>false,
			'type'=>'text',
			'search'=>true,
			'edit'=>true
		),
		'key_name'=>array(
			'name'=>'选项组',
			'width'=>'auto',
			'sort'=>true,
			'type'=>'text',
			'search'=>true
		),
		'status'=>array(
			'name'=>'状态',
			'width'=>'auto',
			'type'=>'select',
			'options'=>$option_status_options,
			'edit'=>true
		),
		'_options'=>array(
			'name'=>'操作',
			'width'=>100,
			'filter'=>'show_wt_option_options'
		)
	)
);



$end_rights[] = array(
	'name'=>'wt_option',
	'description'=>'选项',
	'rights'=>array('view','delete','update','add')
);

function show_wt_option_options($item)
{
	$id = 'option_id';
	end_show_view_button($item[$id]);
	end_show_edit_button($item[$id]);
	end_show_delete_button($item[$id]);
}
