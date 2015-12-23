<?php
/**
 * clause model config
 *
 * @author lidongxu @ 2014.6.2
 */

$end_models['wt_clause'] = array(
	'type' => 'list', //表示这是一个列表型的模型，对应一个数据库的表
	'name' => '条款列表',	//某型的名字，可以把一个栏目配置成某个模型
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
			'description'=>'标识所属活动'
		),
		'clause_en'=>array(
			'name'=>'英文版',
			'type'=>'richtext',
			'null'=>true
		),
		'clause_zh'=>array(
			'name'=>'中文版',
			'type'=>'richtext',
			'null'=>true
		)
	),
	'list_fields' => array(
		'clause_id'=>array(
			'name'=>'ID',
			'width'=>'30',
			'sort'=>true,
			'align'=>'center',
		),
		'activity_id'=>array(
			'name'=>'活动ID',
			'search'=>true,
			'type'=>'text'
		),
		'_options'=>array(
			'name'=>'操作',
			'width'=>100,
			'filter'=>'show_wt_clause_options'
		)
	)
);



$end_rights[] = array(
	'name'=>'wt_clause',
	'description'=>'选项',
	'rights'=>array('view','delete','update','add')
);

function show_wt_clause_options($item)
{
	$id = 'clause_id';
	end_show_view_button($item[$id]);
	end_show_edit_button($item[$id]);
	end_show_delete_button($item[$id]);
}
