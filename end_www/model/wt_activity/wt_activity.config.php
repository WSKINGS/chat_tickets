<?php
/**
 * user model config
 *
 * @author lidongxu
 */

$activity_status_options = array(
	-1 =>'<span style="color:grey">无效</span>',
	1 =>'<span style="color:green">有效</span>'
);

$activity_publish_options = array(
	-1 =>'<span style="color:grey">未开放</span>',
	1 =>'<span style="color:green">开放</span>',
	-2 =>'<span style="color:grey">关闭</span>'
);

$end_models['wt_activity'] = array(
	'type' => 'list', //表示这是一个列表型的模型，对应一个数据库的表
	'name' => '活动列表',	//某型的名字，可以把一个栏目配置成某个模型
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
		'name'=>array(
			'name'=>'名称',
			'type'=>'text',
			'null'=>false
		),
		'logo'=>array(
			'name'=>'Logo',
			'type'=>'image',
			'null'=>true
		),
		'start_time'=>array(
			'name'=>'开始时间',
			'type'=>'datetime',
			'null'=>true
		),
		'end_time'=>array(
			'name'=>'结束时间',
			'type'=>'datetime',
			'null'=>true
		),
		'description'=>array(
			'name'=>'活动描述',
			'type'=>'textarea',
			'null'=>true
		),
		'language'=>array(
			'name'=>'语言',
			'type'=>'text',
			'null'=>true
		),
		'extra_info'=>array(
			'name'=>'扩展信息',
			'type'=>'text',
			'null'=>true
		),
		'publish'=>array(
			'name'=>'开放状态',
			'type'=>'select',
			'options'=>$activity_publish_options
		),
		'order_create_notify'=>array(
			'name'=>'订单创建通知',
			'type'=>'text',
			'null'=>true,
			'description'=>'填写需发送邮件的key name'
		),
		'order_wait_approve_notify'=>array(
			'name'=>'等待审核通知',
			'type'=>'text',
			'null'=>true,
			'description'=>'填写需发送邮件的key name'
		),
		'host_approve_notify'=>array(
			'name'=>'主办方审核通知',
			'type'=>'text',
			'null'=>true,
			'description'=>'填写需发送邮件的key name'
		),
		'order_approved_notify'=>array(
			'name'=>'审核通过通知',
			'type'=>'text',
			'null'=>true,
			'description'=>'填写需发送邮件的key name'
		),
		'order_success_notify_contact'=>array(
			'name'=>'订票完成联系人通知',
			'type'=>'text',
			'null'=>true,
			'description'=>'填写需发送邮件的key name'
		),
		'order_success_notify_attendee'=>array(
			'name'=>'订单完成参会者通知',
			'type'=>'text',
			'null'=>true,
			'description'=>'填写需发送邮件的key name'
		),
		'order_success_notify_host'=>array(
			'name'=>'订单完成组办方通知',
			'type'=>'text',
			'null'=>true,
			'description'=>'填写需发送邮件的key name'
		),
		'user_id'=>array(
			'name'=>'用户ID',
			'type'=>'text',
			'null'=>false,
			'description'=>'用于标记本活动归属'
		),
		'payment_channel_id'=>array(
			'name'=>'支付渠道ID',
			'type'=>'text',
			'null'=>false,
			'description'=>'用于标记本活动的支付渠道'
		),
		'status' => array(
			'name' => '有效',
			'type' => 'select',
			'options'=>$activity_status_options,
			'null' => false
		)
	),
	'list_fields' => array(
		'activity_id'=>array(
			'name'=>'ID',
			'width'=>'30',
			'sort'=>true,
			'align'=>'center',
		),
		'name'=>array(
			'name'=>'活动名称',
			'width'=>'auto',
			'search'=>true
		),
		'start_time'=>array(
			'name'=>'开始时间',
			'width'=>'auto',
			'filter'=>'end_show_int2date'
		),
		'end_time'=>array(
			'name'=>'结束时间',
			'width'=>'auto',
			'filter'=>'end_show_int2date'
		),
		'publish'=>array(
			'name'=>'开放状态',
			'width'=>'auto',
			'type'=>'select',
			'options'=>$activity_publish_options,
			'edit'=>true
		),
		'status'=>array(
			'name'=>'有效',
			'edit'=>true,
			'type'=>'select',
			'options'=>$activity_status_options
		),
		'create_time'=>array(
			'name'=>'创建时间',
			'sort'=>true,
			'filter'=>'end_show_int2date'
		),
		'_options'=>array(
			'name'=>'操作',
			'width'=>100,
			'filter'=>'show_wt_activity_options'
		)
	)
);

$end_rights[] = array(
	'name'=>'wt_activity',
	'description'=>'用户数据',
	'rights'=>array('view','delete','update','add')
);

function show_wt_activity_options($item)
{
	$id = 'activity_id';
	end_show_view_button($item[$id]);
	end_show_edit_button($item[$id]);
	end_show_delete_button($item[$id]);
}

