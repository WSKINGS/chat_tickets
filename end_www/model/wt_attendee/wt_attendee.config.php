<?php
/**
 * attendee model config
 *
 * @author lidongxu @ 2014-5-26
 */

$attendee_status_options = array(
	-1 =>'<span style="color:grey">无效</span>',
	1 =>'<span style="color:green">有效</span>'
);

$attendee_active_options = array(
	-1 =>'<span style="color:grey">未激活</span>',
	1 =>'<span style="color:green">已激活</span>'
);

$attendee_host_check_options = array(
	-2 => '<span style="color:red">待审核</span>',
	-1 => '<span style="color:grey">未通过</span>',
	1 => '<span style="color:green">已通过</span>',
	2 => '<span style="color:green">不需审核</span>'
);

$end_models['wt_attendee'] = array(
	'type' => 'list', //表示这是一个列表型的模型，对应一个数据库的表
	'name' => '参会者列表',	//某型的名字，可以把一个栏目配置成某个模型
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
		'order_id'=>array(
			'name'=>'订单ID',
			'type'=>'text',
			'null'=>false,
			'description'=>'标识参会者所属的订单ID'
		),
		'activity_id'=>array(
			'name'=>'活动ID',
			'type'=>'text',
			'null'=>false,
			'description'=>'标识参会者所属的活动ID'
		),
		'ticket_id'=>array(
			'name'=>'门票ID',
			'type'=>'text',
			'null'=>false,
			'description'=>'标识参会者所属的门票ID'
		),
		'name'=>array(
			'name'=>'姓名',
			'type'=>'text',
			'null'=>true,
			'description'=>'json格式保存姓名'
		),
		'company'=>array(
			'name'=>'公司',
			'type'=>'text',
			'null'=>true,
			'description'=>'json格式保存公司'
		),
		'title'=>array(
			'name'=>'职位',
			'type'=>'text',
			'null'=>true,
			'description'=>'json格式保存职位'
		),
		'phone'=>array(
			'name'=>'电话',
			'type'=>'text',
			'null'=>true
		),
		'telephone'=>array(
			'name'=>'手机',
			'type'=>'text',
			'null'=>true
		),
		'email'=>array(
			'name'=>'Email',
			'type'=>'text',
			'null'=>true
		),
		'extra_info'=>array(
			'name'=>'扩展信息',
			'type'=>'text',
			'null'=>true
		),
		'host_check'=> array(
			'name' => '审核',
			'type' => 'select',
			'options'=>$attendee_host_check_options,
			'null' => false
		),
		'active'=> array(
			'name' => '激活',
			'type' => 'select',
			'options'=>$attendee_active_options,
			'null' => false
		),
		'status' => array(
			'name' => '有效',
			'type' => 'select',
			'options'=>$attendee_status_options,
			'null' => false
		)
	),
	'list_fields' => array(
		'attendee_id'=>array(
			'name'=>'ID',
			'width'=>'30',
			'sort'=>true,
			'align'=>'center',
		),
		'order_id'=>array(
			'name'=>'订单ID',
			'width'=>'auto',
			'search'=>true
		),
		'activity_id'=>array(
			'name'=>'活动ID',
			'width'=>'auto',
			'search'=>true
		),
		'ticket_id'=>array(
			'name'=>'门票ID',
			'width'=>'auto',
			'search'=>true
		),
		'name'=>array(
			'name'=>'姓名',
			'width'=>'auto',
			'search'=>true
		),
		'company'=>array(
			'name'=>'公司',
			'width'=>'auto',
			'search'=>true
		),
		'phone'=>array(
			'name'=>'手机',
			'width'=>'auto',
			'search'=>true
		),
		'email'=>array(
			'name'=>'Email',
			'width'=>'auto',
			'search'=>true
		),
		'host_check'=>array(
			'name'=>'审核',
			'width'=>'auto',
			'type'=>'select',
			'options'=>$attendee_host_check_options,
			'edit'=>true
		),
		'active'=>array(
			'name'=>'激活',
			'width'=>'auto',
			'type'=>'select',
			'options'=>$attendee_active_options,
			'edit'=>true
		),
		'status'=>array(
			'name'=>'有效',
			'edit'=>true,
			'type'=>'select',
			'options'=>$attendee_status_options
		),
		'update_time'=>array(
			'name'=>'更新时间',
			'filter'=>'end_show_int2date'
		),
		'create_time'=>array(
			'name'=>'创建时间',
			'filter'=>'end_show_int2date'
		),
		'_options'=>array(
			'name'=>'操作',
			'width'=>100,
			'filter'=>'show_wt_attendee_options'
		)
	)
);

$end_rights[] = array(
	'name'=>'wt_attendee',
	'description'=>'订单信息',
	'rights'=>array('view','delete','update','add')
);

function show_wt_attendee_options($item)
{
	$id = 'attendee_id';
	end_show_view_button($item[$id]);
	end_show_edit_button($item[$id]);
	end_show_delete_button($item[$id]);
}

