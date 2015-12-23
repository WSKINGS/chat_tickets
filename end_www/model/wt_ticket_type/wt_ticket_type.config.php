<?php
/**
 * ticket type model config
 *
 * @author lidongxu @ 2014-5-23
 */

$ticket_type_status_options = array(
	-1 =>'<span style="color:grey">无效</span>',
	1 =>'<span style="color:green">有效</span>'
);

$ticket_type_yesno_options = array(
	-1 =>'<span style="color:grey">否</span>',
	1 =>'<span style="color:green">是</span>'
);

$ticket_type_publish_options = array(
	-1 =>'<span style="color:grey">未开放</span>',
	1 =>'<span style="color:green">开放</span>',
	-2 =>'<span style="color:grey">关闭</span>'
);

$end_models['wt_ticket_type'] = array(
	'type' => 'list', //表示这是一个列表型的模型，对应一个数据库的表
	'name' => '门票列表',	//某型的名字，可以把一个栏目配置成某个模型
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
			'name'=>'门票名称',
			'type'=>'text',
			'null'=>false
		),
		'order_id'=>array(
			'name'=>'排序',
			'type'=>'text',
			'null'=>true,
			'description'=>'输入浮点数，数字越小越靠前'
		),
		'price'=>array(
			'name'=>'单价',
			'type'=>'text',
			'null'=>true
		),
		'gross'=>array(
			'name'=>'总数量',
			'type'=>'text',
			'null'=>true,
			'description'=>'发送总量无限制则不填写'
		),
		'booked_gross'=>array(
			'name'=>'已订数量',
			'type'=>'text',
			'null'=>true,
			'description'=>'已经卖出的门票数量'
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
		'buy_min'=>array(
			'name'=>'购买最小量',
			'type'=>'text',
			'null'=>true
		),
		'buy_max'=>array(
			'name'=>'购买最大量',
			'type'=>'text',
			'null'=>true
		),
		'show_description'=>array(
			'name'=>'显示描述',
			'type'=>'select',
			'options'=>$ticket_type_yesno_options,
			'null'=>false
		),
		'description'=>array(
			'name'=>'描述',
			'type'=>'textarea',
			'null'=>true
		),
		'need_attendee_info'=>array(
			'name'=>'需参会者信息',
			'type'=>'select',
			'options'=>$ticket_type_yesno_options,
			'null'=>false
		),
		'need_approve'=>array(
			'name'=>'需审核',
			'type'=>'select',
			'options'=>$ticket_type_yesno_options,
			'null'=>false
		),
		'give_discount'=>array(
			'name'=>'给折扣',
			'type'=>'select',
			'options'=>$ticket_type_yesno_options,
			'null'=>false
		),
		'discount_type_id'=>array(
			'name'=>'折扣方式ID',
			'type'=>'text',
			'null'=>true,
			'description'=>'用于标记本票种折扣方式'
		),
		'discount'=>array(
			'name'=>'折扣率',
			'type'=>'text',
			'null'=>true
		),
		'channel'=>array(
			'name'=>'购票渠道',
			'type'=>'text',
			'null'=>true
		),
		'publish'=>array(
			'name'=>'开放状态',
			'type'=>'select',
			'options'=>$ticket_type_publish_options,
			'null'=>false
		),
		'activity_id'=>array(
			'name'=>'活动ID',
			'type'=>'text',
			'null'=>false,
			'description'=>'用于标记本票种归属'
		),
		'attendee_type_id'=>array(
			'name'=>'参会者类型ID',
			'type'=>'text',
			'null'=>false,
			'description'=>'用于标记门票的参会者类型'
		),
		'status' => array(
			'name' => '有效',
			'type' => 'select',
			'options'=>$ticket_type_status_options,
			'null' => false
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
			'name'=>'门票名称',
			'width'=>'auto',
			'type'=>'text',
			'search'=>true
		),
		'activity_id'=>array(
			'name'=>'活动ID',
			'width'=>'auto',
			'type'=>'text',
			'search'=>true
		),
		'price'=>array(
			'name'=>'单价',
			'edit'=>true,
			'type'=>'text'
		),
		'gross'=>array(
			'name'=>'总量',
			'width'=>'auto',
			'type'=>'text'
		),
		'booked_gross'=>array(
			'name'=>'已订数量',
			'width'=>'auto',
			'type'=>'text'
		),
		'start_time'=>array(
			'name'=>'开始时间',
			'width'=>'auto',
			'filter'=>'end_show_int2date',
			'edit'=>true
		),
		'end_time'=>array(
			'name'=>'结束时间',
			'width'=>'auto',
			'filter'=>'end_show_int2date',
			'edit'=>true
		),
		'need_approve'=>array(
			'name'=>'需审核',
			'width'=>'auto',
			'type'=>'select',
			'options'=>$ticket_type_yesno_options
		),
		'give_discount'=>array(
			'name'=>'给折扣',
			'width'=>'auto',
			'type'=>'select',
			'options'=>$ticket_type_yesno_options
		),
		'channel'=>array(
			'name'=>'购票渠道',
			'width'=>'auto',
			'type'=>'text',
			'search'=>true
		),
		'publish'=>array(
			'name'=>'开放状态',
			'width'=>'auto',
			'type'=>'select',
			'options'=>$ticket_type_publish_options,
			'eidt'=>true
		),
		'status'=>array(
			'name'=>'有效',
			'edit'=>true,
			'type'=>'select',
			'options'=>$ticket_type_status_options
		),
		'create_time'=>array(
			'name'=>'创建时间',
			'filter'=>'end_show_int2date'
		),
		'_options'=>array(
			'name'=>'操作',
			'width'=>100,
			'filter'=>'show_wt_ticket_type_options'
		)
	)
);

$end_rights[] = array(
	'name'=>'wt_ticket_type',
	'description'=>'门票种类',
	'rights'=>array('view','delete','update','add')
);

function show_wt_ticket_type_options($item)
{
	$id = 'type_id';
	end_show_view_button($item[$id]);
	end_show_edit_button($item[$id]);
	end_show_delete_button($item[$id]);
}

