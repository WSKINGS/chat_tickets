<?php
/**
 * order model config
 *
 * @author lidongxu @ 2014-5-23
 */

$order_status_options = array(
	-1 =>'<span style="color:grey">无效</span>',
	1 =>'<span style="color:green">有效</span>'
);

$order_yesno_options = array(
	-1 =>'<span style="color:grey">否</span>',
	1 =>'<span style="color:green">是</span>'
);

$order_charge_status_options = array(
	-2 => '<span style="color:grey">待付款</span>',
	-1 => '<span style="color:grey">付款失败</span>',
	1 => '<span style="color:grey">已付款</span>',
	2 => '<span style="color:grey">无需付款</span>'
);

$end_models['wt_order'] = array(
	'type' => 'list', //表示这是一个列表型的模型，对应一个数据库的表
	'name' => '订单列表',	//某型的名字，可以把一个栏目配置成某个模型
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
		'activity_id'=>array(
			'name'=>'活动ID',
			'type'=>'text',
			'null'=>false,
			'description'=>'标识订单所属的活动ID'
		),
		'order_serial'=>array(
			'name'=>'订单号',
			'type'=>'text',
			'null'=>false
		),
		'tickets'=>array(
			'name'=>'门票',
			'type'=>'text',
			'null'=>false,
			'description'=>'json格式储存门票信息'
		),
		'contact_name'=>array(
			'name'=>'联系人',
			'type'=>'text',
			'null'=>true
		),
		'contact_email'=>array(
			'name'=>'联系人email',
			'type'=>'text',
			'null'=>true
		),
		'contact_phone'=>array(
			'name'=>'联系人电话',
			'type'=>'text',
			'null'=>true
		),
		'company_name'=>array(
			'name'=>'公司',
			'type'=>'text',
			'null'=>true
		),
		'company_address'=>array(
			'name'=>'公司地址',
			'type'=>'text',
			'null'=>true
		),
		'company_zipcode'=>array(
			'name'=>'公司邮编',
			'type'=>'text',
			'null'=>true
		),
		'company_phone'=>array(
			'name'=>'公司电话',
			'type'=>'text',
			'null'=>true
		),
		'company_fax'=>array(
			'name'=>'公司传真',
			'type'=>'text',
			'null'=>true
		),
		'company_website'=>array(
			'name'=>'公司网站',
			'type'=>'text',
			'null'=>true
		),
		'company_field_id'=>array(
			'name'=>'公司行业ID',
			'type'=>'text',
			'null'=>true,
			'description'=>'标识公司所属行业ID'
		),
		'need_invoice'=>array(
			'name'=>'需要发票',
			'type'=>'select',
			'options'=>$order_yesno_options,
			'null'=>false
		),
		'invoice_title'=>array(
			'name'=>'发票抬头',
			'type'=>'text',
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
			'null'=>true,
			'description'=>'json格式储存自定义扩展信息'
		),
		'show_attendee_info'=>array(
			'name'=>'显示参会者信息',
			'type'=>'select',
			'options'=>$order_yesno_options,
			'null'=>true
		),
		'charge_status'=>array(
			'name'=>'付款信息',
			'type'=>'select',
			'options'=>$order_charge_status_options,
			'null'=>true
		),
		'status' => array(
			'name' => '有效',
			'type' => 'select',
			'options'=>$order_status_options,
			'null' => false
		)
	),
	'list_fields' => array(
		'order_id'=>array(
			'name'=>'ID',
			'width'=>'30',
			'sort'=>true,
			'align'=>'center',
		),
		'order_serial'=>array(
			'name'=>'订单号',
			'width'=>'auto',
			'search'=>true
		),
		'contact_name'=>array(
			'name'=>'联系人',
			'width'=>'auto',
			'search'=>true
		),
		'contact_phone'=>array(
			'name'=>'联系人电话',
			'width'=>'auto',
			'search'=>true
		),
		'contact_email'=>array(
			'name'=>'联系人email',
			'width'=>'auto',
			'search'=>true
		),
		'company_name'=>array(
			'name'=>'公司名',
			'width'=>'auto',
			'search'=>true
		),
		// 'company_phone'=>array(
		// 	'name'=>'公司电话',
		// 	'width'=>'auto'
		// ),
		'need_invoice'=>array(
			'name'=>'需发票',
			'width'=>'auto',
			'type'=>'select',
			'options'=>$order_yesno_options
		),
		'show_attedee_info'=>array(
			'name'=>'显示参会者',
			'width'=>'auto',
			'type'=>'select',
			'options'=>$order_yesno_options
		),
		'charge_status'=>array(
			'name'=>'付款信息',
			'width'=>'auto',
			'type'=>'select',
			'options'=>$order_charge_status_options
		),
		'charge_time'=>array(
			'name'=>'付款时间',
			'filter'=>'end_show_int2date'
		),
		'charge_amount'=>array(
			'name'=>'付款金额',
			'type'=>'text'
		),
		'status'=>array(
			'name'=>'有效',
			'edit'=>true,
			'type'=>'select',
			'options'=>$order_status_options
		),
		'create_time'=>array(
			'name'=>'创建时间',
			'filter'=>'end_show_int2date'
		),
		'_options'=>array(
			'name'=>'操作',
			'width'=>100,
			'filter'=>'show_wt_order_options'
		)
	)
);

$end_rights[] = array(
	'name'=>'wt_order',
	'description'=>'订单信息',
	'rights'=>array('view','delete','update','add')
);

function show_wt_order_options($item)
{
	$id = 'order_id';
	end_show_view_button($item[$id]);
	end_show_edit_button($item[$id]);
	end_show_delete_button($item[$id]);
}

