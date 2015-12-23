<?php

include_once "manage_include.php";

function export_csv($filename, $data) { 
	ob_end_clean();	# 清除文件之前的所有输出
    header("Content-type:text/csv");

    header("Content-Disposition:attachment;filename=".$filename);

    header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
    header('Expires:0');
    header('Pragma:public');
    echo $data; 
    die();
}

function decode_phone($s) {
	$p = json_decode($s, true);
	return " +".$p['country_code']."-".$p['city_code']."-".$p['number'];
}

function decode_telephone($s) {
	$p = json_decode($s, true);
	return " +".$p['country_code']."-".$p['number'];
}

function get_data($activity_id, $active)
{
	global $db;
	if ($active) {
		$query = "SELECT a.attendee_id AS id,
				from_unixtime(o.create_time, '%Y-%m-%d %H:%i:%s') AS create_time, 
				from_unixtime(o.charge_time, '%Y-%m-%d %H:%i:%s') AS charge_time,
				o.tickets AS ticket_id,
				o.tickets AS ticket_name,
				o.company_field_id AS business_mainfield,
				o.company_name AS company_name_cn,
				o.company_name AS company_name_en,
				a.name AS name_cn,
				a.name AS name_en_firstname,
				a.name AS name_en_lastname,
				a.phone AS phone,
				a.email AS email,
				a.title AS title_cn,
				a.title AS title_en,
				o.company_address AS company_country,
				o.company_address AS company_province,
				o.company_address AS company_city,
				o.company_address AS company_address,
				o.company_zipcode AS company_zipcode,
				a.telephone AS telephone,
				o.company_fax AS company_fax,
				o.company_website AS company_website,
				o.contact_name AS contact_name,
				o.contact_email AS contact_email,
				o.contact_phone AS contact_phone,
				o.need_invoice AS need_invoice,
				o.invoice_title AS invoice_title,
				o.tickets AS price,
				o.tickets AS amount,
				a.extra_info AS option_1,
				a.active AS attendee_status
			FROM ( end_wt_attendee_data a INNER JOIN end_wt_order_data o ON a.order_id = o.order_id )
			WHERE o.status = '1' AND a.active = '1' AND a.activity_id = ".$activity_id." 
			ORDER BY o.charge_time DESC";
	} 
	else {
		$query = "SELECT a.attendee_id AS id,
				from_unixtime(o.create_time, '%Y-%m-%d %H:%i:%s') AS create_time, 
				from_unixtime(o.charge_time, '%Y-%m-%d %H:%i:%s') AS charge_time,
				o.tickets AS ticket_id,
				o.tickets AS ticket_name,
				o.company_field_id AS business_mainfield,
				o.company_name AS company_name_cn,
				o.company_name AS company_name_en,
				a.name AS name_cn,
				a.name AS name_en_firstname,
				a.name AS name_en_lastname,
				a.phone AS phone,
				a.email AS email,
				a.title AS title_cn,
				a.title AS title_en,
				o.company_address AS company_country,
				o.company_address AS company_province,
				o.company_address AS company_city,
				o.company_address AS company_address,
				o.company_zipcode AS company_zipcode,
				a.telephone AS telephone,
				o.company_fax AS company_fax,
				o.company_website AS company_website,
				o.contact_name AS contact_name,
				o.contact_email AS contact_email,
				o.contact_phone AS contact_phone,
				o.need_invoice AS need_invoice,
				o.invoice_title AS invoice_title,
				o.tickets AS price,
				o.tickets AS amount,
				a.extra_info AS option_1,
				a.active AS attendee_status
			FROM ( end_wt_attendee_data a INNER JOIN end_wt_order_data o ON a.order_id = o.order_id )
			WHERE o.status = '1' AND a.active = '-1' AND a.activity_id = ".$activity_id." 
			ORDER BY o.create_time DESC";
	}
			
	# 调整数据
	$query = $db->query($query);
	$items = array();
	global $activity_id;
	$tickets = model('wt_ticket_type')->get_list(array('activity_id'=>$activity_id));
	$ticket_arr = array();
	foreach ($tickets as $key => $value) {
		$tickets[$key]['name'] = json_decode($value['name'], true);
		$ticket_arr[$tickets[$key]['type_id']] = $tickets[$key];
	}
	$company_fields = model('wt_option')->get_list(array('key_name'=>'company_field'));
	$fields_arr = array();
	foreach ($company_fields as $key => $value) {
		$company_fields[$key]['name'] = json_decode($value['name'], true);
		$fields_arr[$company_fields[$key]['option_id']] = $company_fields[$key];
	}
	while($item = $db->fetch_array($query))
	{
		$t = json_decode($item['ticket_id'], true);
		$t = $t[0];
		$item['ticket_id'] = $t['ticket_id'];
		$item['ticket_name'] = $ticket_arr[$t['ticket_id']]['name']['zh'];
		$item['business_mainfield'] = $fields_arr[$item['business_mainfield']]['name']['zh'];

		$company_name = json_decode($item['company_name_cn'], true);
		$item['company_name_cn'] = $company_name['zh'];
		$item['company_name_en'] = $company_name['en'];

		$name = json_decode($item['name_cn'], true);
		$name['en'] = json_decode($name['en'], true);
		$item['name_cn'] = $name['zh'];
		$item['name_en_firstname'] = $name['en']['firstname'];
		$item['name_en_lastname'] = $name['en']['lastname'];

		$item['phone'] = decode_phone($item['phone']);

		$title = json_decode($item['title_cn'], true);
		$item['title_cn'] = $title['zh'];
		$item['title_en'] = $title['en'];

		$address = json_decode($item['company_address'], true);
		$item['company_country'] = $address['country'];
		$item['company_province'] = $address['province'];
		$item['company_city'] = $address['city'];
		$item['company_address'] = $address['details'];

		$item['telephone'] = decode_telephone($item['telephone']);
		$item['company_fax'] = decode_phone($item['company_fax']);

		$item['contact_phone'] = decode_telephone($item['contact_phone']);

		$item['price'] = $ticket_arr[$t['ticket_id']]['price'];
		$item['amount'] = floatval($item['price'])*floatval($t['quantity']);

		$item['need_invoice'] = str_replace('-1', '否', $item['need_invoice']);
		$item['need_invoice'] = str_replace('1', '是', $item['need_invoice']);

		$option_1 = json_decode($item['option_1'], true);
		$item['option_1'] = $option_1['need_room'];
		$item['option_1'] = str_replace('-1', '否', $item['option_1']);
		$item['option_1'] = str_replace('1', '是', $item['option_1']);
		
		//### other question added by wangshuai @ 20150303
		/*$item['option_2'] = $option_1['attend_14'];
		$item['option_2'] = str_replace('-1', '否', $item['option_2']);
		$item['option_2'] = str_replace('1', '是', $item['option_2']);
		
		$item['option_3'] = $option_1['attend_16'];
		$item['option_3'] = str_replace('-1', '否', $item['option_3']);
		$item['option_3'] = str_replace('1', '是', $item['option_3']);
		
		$item['option_4'] = $option_1['attend_oversea'];
		$item['option_4'] = str_replace('-1', '否', $item['option_4']);
		$item['option_4'] = str_replace('1', '是', $item['option_4']);*/
		//### end #######

		$item['attendee_status'] = str_replace('-1', '未生效', $item['attendee_status']);
		$item['attendee_status'] = str_replace('1', '已生效', $item['attendee_status']);

		/*if ($active){
			unset($item['create_time']);
		}
		else {
			unset($item['charge_time']);
		}*/

		$items[] = $item;
	}
	
	# 输出数据
	if (count($items))
	{
		# 中文字段
		$data_field = array(
			'id'=>'ID',
			'create_time'=>'购票时间',
			'charge_time'=>'付款时间',
			'ticket_id'=>'门票ID',
			'ticket_name'=>'门票种类',
			'business_mainfield'=>'公司类型',
			'company_name_cn'=>'中文公司名',
			'company_name_en'=>'英文公司名',
			'name_cn'=>'中文全名',
			'name_en_firstname'=>'英文名',
			'name_en_lastname'=>'英文姓',
			'phone'=>'座机',
			'email'=>'电子邮件',
			'title_cn'=>'职位中文',
			'title_en'=>'职位英文',
			'company_country'=>'国家',
			'company_province'=>'省',
			'company_city'=>'市',
			'company_address'=>'地址',
			'company_zipcode'=>'邮政编码',
			'telephone'=>'手机',
			'company_fax'=>'传真',
			'company_website'=>'网址',
			'contact_name'=>'订票联系人',
			'contact_email'=>'联系人邮箱',
			'contact_phone'=>'联系人电话',
			'need_invoice'=>'是否需要发票',
			'invoice_title'=>'发票抬头',
			'price'=>'单价',
			'amount'=>'订单金额',
			'option_1'=>'是否入住酒店？',
			//'option_2'=>'是否参加4月8日14:00的酒店资产管理理论篇？',
			//'option_3'=>'是否参加4月8日16:15的酒店资产管理理论篇？',
			//'option_4'=>'您是否有兴趣参加4月8日的“境外交易面面观”环节？',
			'attendee_status'=>'用户状态'
			);
		$data = '';
		//为每个字段添加双引号，为了输出原始数据中的逗号
		foreach ($data_field as $key => $value) {
			$value = str_replace("&nbsp;", " ", $value);
			$value = htmlspecialchars_decode($value);
			//$value = str_replace(',', ' ', $value);
			$value =  preg_replace("/[\r\n]+/", ' ', $value);
			$data .= '"'.$value.'"' . ',';
		}
		$data .= "\n";

		foreach ($items as $item) {
			foreach ($data_field as $key => $value) {
				$item[$key] = str_replace("&nbsp;", " ", $item[$key]);
				$item[$key] = htmlspecialchars_decode($item[$key]);
				//$item[$key] = str_replace(',', ' ', $item[$key]);
				$item[$key] =  preg_replace("/[\r\n]+/", ' ', $item[$key]);
				$data .= '"'.$item[$key].'"' .',';
			}
			$data .= "\n";
		}

		return $data;
	}
	else
	{
		return 'no data';
	}
}

if ($_GET['active'] == 'true') {
	$user_data = get_data($activity_id, true);
}
else {
	$user_data = get_data($activity_id, false);	
}

$user_data = mb_convert_encoding($user_data, 'GBK', 'UTF-8'); 

if ($_GET['active'] == 'true') {
	$filename = 'actived_user-'.date('Ymd').'.csv'; 
}
else {
	$filename = 'processing_user-'.date('Ymd').'.csv'; 
}

$filename = iconv('UTF-8', 'GBK', $filename);

export_csv($filename, $user_data);