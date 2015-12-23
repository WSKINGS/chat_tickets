<?php

include_once __DIR__."/ticket_include.php";

$view_data['title'] = 'Manage Ticket';

$order_list = model('wt_order')->get_list(array('contact_email'=>$_SESSION['booking_user']));

$orders = array();

foreach ($order_list as $o) {
	$orders[$o['order_id']] = new Order();

	$set_ret = $orders[$o['order_id']]->set_order($o['order_id']);
	if ($set_ret['r'] == 'error') {
		die(json_encode($set_ret));
	}
}

$orders_data = array();

$charge_status_arr = array(
	'-2' => 'wait',
	'-1' => 'wait',
	'1' => 'already',
	'2' => 'no_need'
	);

foreach ($orders as $key => $o) {
	if ($o->is_ok()) {
		$orders_data[$key] = $o->get_order_data();
		//加载门票信息
		$o_tickets = $o->get_tickets();
		$orders_data[$key]['tickets'][0]['ticket_data'] = $o_tickets['tickets'][0]->get_ticket_data();
		//加载价格信息
		$price_ret = $o->get_price();
		$orders_data[$key]['amount'] = $price_ret['msg'];

		$orders_data[$key]['charge_status'] = $charge_status_arr[$orders_data[$key]['charge_status']];

		$orders_data[$key]['approve_status'] = ($o->is_approved())?"already":'wait';
	}
}
$view_data['orders_data'] = $orders_data;

if ($_GET['do'] == 'manage_order') {
	$ret = array();
	if (!isset($_GET['order'])) {
		$ret['r'] = 'error';
		$ret['msg'] = '10001:Argument error.';

		die(json_encode($ret));
	}

	$order_id = $_GET['order'];

	$html = "";

	$html .= '<div class="block-header">';
	$html .= '<h4>'.$texts['booking_ticket_manage'].'</h4>';
	$html .= '</div>';
	$html .= '<div class="block-body">';
	$html .= '<div class="row">';
	$html .= '<div class="col-md-2">'.$texts['order_serial'].$texts['colon'].'</div>';
	$html .= '<div class="col-md-4">'.$orders_data[$order_id]['order_serial'].'</div>';
	$html .= '<div class="col-md-2">'.$texts['ticket_type'].$texts['colon'].'</div>';
	$html .= '<div class="col-md-4">'.$orders_data[$order_id]['tickets'][0]['quantity']." ".$orders_data[$order_id]['tickets'][0]['ticket_data']['name'][$orders_data[$order_id]['language']].'</div>';
	$html .= '</div><br/>';
	$html .= '<div class="row">';
	$html .= '<div class="col-md-2">'.$texts['amount'].$texts['colon'].'</div>';
	$html .= '<div class="col-md-4"><span class="add-money">'.$orders_data[$order_id]['amount'].'</span></div>';
	$html .= '<div class="col-md-2">'.$texts['status'].$texts['colon'].'</div>';
	$html .= '<div class="col-md-4">'.$texts['approve_'.$orders_data[$order_id]['approve_status']]."&nbsp;&nbsp;".$texts['charge_'.$orders_data[$order_id]['charge_status']].'</div>';
	$html .= '</div><br/>';
		
	$html .= '<div class="text-middle">';
	
    //支付按钮
    if ( $orders[$order_id]->is_ok() 
        && $orders[$order_id]->is_approved() 
        && !$orders[$order_id]->is_charged() ) {
        $check_ret = $orders[$order_id]->check_pay();

        if ($check_ret['r'] == 'ok') {
            $pay_button = $booking_activity->get_payment()->pay($orders[$order_id], $booking_activity,$orders_data[$order_id]['language']);
        }
    }
    $html .= $pay_button;
	$html .= '</div><br/>';
	
	
	$html .= '<div class="text-left">';
	$html .= '<div class="row">';
	$html .= '<div class="col-md-2">';

    //修改信息按钮
    $html .= '<button onclick="update_info('.$order_id.')" class="btn btn-info">'.$texts['update_info'].'</button>';
	
	$html .= "</div>";
	$html .= '</div>';
	$html .= '</div>';	
	$html .= '</div>';

	$ret['r'] = 'ok';
	$ret['msg'] = $html;

	die(json_encode($ret));
}

if ($_GET['do'] == 'update_info') {
	$ret = array();
	if (!isset($_GET['order'])) {
		$ret['r'] = 'error';
		$ret['msg'] = '10001:Argument error.';

		die(json_encode($ret));
	}

	$order_id = $_GET['order'];

	$edit_order_data = $orders[$order_id]->get_order_data();

	$attendees = $orders[$order_id]->get_attendees();

	$attendees_data = array();
	foreach ($attendees as $a) {
		$attendees_data[] = $a->get_attendee_data();
	}

	$company_fields = model('wt_option')->get_list(array('key_name'=>'company_field', 'status'=>'1', 'order' => 'order_id asc'));

	$html = "";

	//form起始
	$html .= '<form id="update_form" method="POST" role="from">';
    $html .= '<h5 class="text-danger">'.$texts['update_info_explain'].'</h5>';
	//联系人信息
	$html .= '<div class="content-block">';
	//header
	$html .= '<div class="block-header">';
	$html .= '<h4>'.$texts['contact_info'].'<small class="text-danger">'.$texts['required_explain'].'</small></h4>';
	$html .= '</div>';
	//body
	$html .= '<div class="block-body">';
	// $html .= '<div class="description text-warning">';
	// $html .= $texts['contact_info_warning'];
	// $html .= '</div>';
	$html .= '<div class="body-content">';
	$html .= '<div class="row">';
	$html .= '<div class="col-md-6">';
	//contact_name
	$html .= '<div class="form-group">';
	$html .= '<label for="contact_name">'.$texts['contact_name'].'<span class="text-danger"> * </span>'.$texts['colon'].'</label>';
	$html .= '<input type="text" class="form-control input-sm required" id="contact_name" name="contact_name" placeholder="'.$texts['contact_name'].'" value="'.$edit_order_data['contact_name'].'" required>';
	$html .= '</div>';
	$html .= '</div>';
	$html .= '<div class="col-md-6">';

	if ($orders_data[$order_id]['need_invoice'] == '1') {
		//invoice_title
		$html .= '<div class="form-group">';
		$html .= '<label for="invoice_title">'.$texts['invoice_title'].'<span class="text-danger"> * </span>'.$texts['colon'].'</label>';
		$html .= '<input type="text" class="form-control input-sm required" id="invoice_title" name="invoice_title" placeholder="'.$texts['invoice_title'].'" value="'.$edit_order_data['invoice_title'].'"  required>';
		$html .= '</div>';
	}

	$html .= '</div>';
	$html .= '</div>';
	$html .= '<div class="row">';
	$html .= '<div class="col-md-6">';
	//contact_email
	$html .= '<div class="form-group">';
	$html .= '<label for="contact_email">'.$texts['contact_email'].'<span class="text-danger"> * </span>'.$texts['colon'].'</label>';
	$html .= '<input type="email" class="form-control input-sm required" id="contact_email" name="contact_email" placeholder="'.$texts['contact_email'].'" value="'.$edit_order_data['contact_email'].'" disabled="deisbaled" required>';
	// $html .= '<span class="help-block"><p class="text-danger">'.$texts['contact_email_explain'].'</p></span>';
	$html .= '</div>';
	$html .= '</div>';
	$html .= '<div class="col-md-6">';
	//contact_phone
	$edit_order_data['contact_phone'] = json_decode($edit_order_data['contact_phone'], true);
	$html .= '<div class="form-group">';
	$html .= '<label for="contact_phone">'.$texts['contact_phone'].'<span class="text-danger"> * </span>'.$texts['colon'].'</label>';
	$html .= '<div>';
	$html .= '<span class="pull-left herizon-span">+</span>';
	$html .= '<span class="pull-left col-md-4">';
	$html .= '<input type="text" class="form-control input-sm required" id="contact_phone_country_code" name="contact_phone[country_code]" placeholder="'.$texts['country_code'].'" value="'.$edit_order_data['contact_phone']['country_code'].'" required>';
	$html .= '</span>';
	$html .= '<span class="pull-left herizon-span">-</span>';
	$html .= '<span class="pull-left col-md-7">';
	$html .= '<input type="text" class="form-control input-sm required" id="contact_phone_phone_number" name="contact_phone[number]" placeholder="'.$texts['telephone_number'].'" value="'.$edit_order_data['contact_phone']['number'].'" required>';
	$html .= '</span>';
	$html .= '</div>';
	$html .= '</div>';
	$html .= '</div>';
	$html .= '</div>';
	$html .= '</div>';
	$html .= '</div>';
    $html .=  '</div>';
    //公司信息
    $html .= '<div class="content-block">';
    $html .= '<div class="block-header">';
    $html .= '<h4>'.$texts['company_info'].'<small class="text-danger">'.$texts['required_explain'].'</small></h4>';
    $html .= '</div>';
    $html .= '<div class="block-body">';
    $html .= '<div class="body-content">';
    //company_fields_id
    $html .= '<div class="form-group">';
    $html .= '<div class="row">';
    $html .= '<div class="col-md-3">';
    $html .= '<label class="herizon-label" for="company_field_id">'.$texts['company_field'].$texts['colon'].'</label>';
    $html .= '</div>';
    $html .= '<div class="col-md-6">';
    $html .= '<select class="form-control input-sm" id="company_field_id" name="company_field_id">';

    //company_fields_options
    $company_fields = model('wt_option')->get_list(array('key_name'=>'company_field', 'status'=>'1', 'order' => 'order_id asc'));
    for ($i=0; $i<count($company_fields); $i++) {
        $company_fields[$i]['name'] = json_decode($company_fields[$i]['name'],true);
    }
    foreach ($company_fields as $field) {
    	$selected = ($field['option_id'] == $edit_order_data['company_field_id'])?'selected':'';
    	$html .= '<option '.$selected.' value="'.$field['option_id'].'">'.$field['name'][$lang].'</option>';
    }
    $html .= '</select>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    //company_name
    $edit_order_data['company_name'] = json_decode($edit_order_data['company_name'], true);
    $html .= '<div class="form-group">';
    $html .= '<div class="row">';
    $html .= '<div class="col-md-7">';
    $html .= '<label>'.$texts['company_name'].'<span class="text-danger"> * </span>'.$texts['colon'].'</label>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '<div class="row">';
    $html .= '<div class="col-md-3">';
    $html .= '<label class="herizon-label" for="company_name_zh">'.$texts['chinese'].$texts['colon'].'</label>';
    $html .= '</div>';
    $html .= '<div class="col-md-6">';
    $html .= '<input type="text" class="form-control input-sm required" id="company_name_zh" name="company_name[zh]" placeholder="'.$texts['company_name_chinese_blur'].'" value="'.$edit_order_data['company_name']['zh'].'" onblur="show_name_zh()" required>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '<div class="row">';
    $html .= '<div class="col-md-3">';
    $html .= '<label class="herizon-label" for="company_name_en">'.$texts['english'].$texts['colon'].'</label>';
    $html .= '</div>';
    $html .= '<div class="col-md-6">';
    $html .= '<input type="text" class="form-control input-sm required" id="company_name_en" name="company_name[en]" placeholder="'.$texts['english'].'" value="'.$edit_order_data['company_name']['en'].'" onblur="show_name_en()" required>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    //company_address
    $edit_order_data['company_address'] = json_decode($edit_order_data['company_address'], true);
    $html .= '<div class="form-group">';
    $html .= '<div class="row">';
    $html .= '<div class="col-md-3">';
    $html .= '<label class="herizon-label">'.$texts['company_address'].'<span class="text-danger"> * </span>'.$texts['colon'].'</label>';
    $html .= '</div>';
    $html .= '<div class="col-md-6">';
    $html .= '<div class="row">';
    $html .= '<span class="col-md-4 pull-left">';
    $html .= '<input type="text" class="form-control input-sm required" id="company_address_country" name="company_address[country]" placeholder="'.$texts['company_address_country'].'" value="'.$edit_order_data['company_address']['country'].'" required>';
    $html .= '</span>';
    $html .= '<span class="col-md-4 pull-left">';
    $html .= '<input type="text" class="form-control input-sm required" id="company_address_province" name="company_address[province]" placeholder="'.$texts['company_address_province'].'" value="'.$edit_order_data['company_address']['province'].'" required>';
    $html .= '</span>';
    $html .= '<span class="col-md-4 pull-left">';
    $html .= '<input type="text" class="form-control input-sm required" id="company_address_city" name="company_address[city]" placeholder="'.$texts['company_address_city'].'" value="'.$edit_order_data['company_address']['city'].'" required>';
    $html .= '</span>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '<div class="row">';
    $html .= '<div class="col-md-3">';
    $html .= '</div>';
    $html .= '<div class="col-md-6">';
    $html .= '<input type="text" class="form-control input-sm required" id="company_address_details" name="company_address[details]" placeholder="'.$texts['company_address_holder'].'" value="'.$edit_order_data['company_address']['details'].'" required>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    //company_zipcode
    $html .= '<div class="form-group">';
    $html .= '<div class="row">';
    $html .= '<div class="col-md-3">';
    $html .= '<label class="herizon-label" for="company_zipcode">'.$texts['zipcode'].$texts['colon'].'</label>';
    $html .= '</div>';
    $html .= '<div class="col-md-6">';
    $html .= '<input type="text" class="form-control input-sm" id="company_zipcode" name="company_zipcode" placeholder="'.$texts['zipcode'].'" value="'.$edit_order_data['company_zipcode'].'">';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    //company_phone
    $edit_order_data['company_phone'] = json_decode($edit_order_data['company_phone'],true);
    $html .= '<div class="form-group">';
    $html .= '<div class="row">';
    $html .= '<div class="col-md-3">';
    $html .= '<label class="herizon-label">'.$texts['office_phone'].'<span class="text-danger"> * </span>'.$texts['colon'].'</label>';
    $html .= '</div>';
    $html .= '<div class="col-md-7">';
    $html .= '<span class="pull-left herizon-span">+</span>';
    $html .= '<span class="pull-left col-md-3">';
    $html .= '<input type="text" class="form-control input-sm required" id="company_phone_country_code" name="company_phone[country_code]" placeholder="'.$texts['country_code'].'" value="'.$edit_order_data['company_phone']['country_code'].'" required>';
    $html .= '</span>';
    $html .= '<span class="pull-left herizon-span">-</span>';
    $html .= '<span class="pull-left col-md-3">';
    $html .= '<input type="text" class="form-control input-sm required" id="company_phone_city_code" name="company_phone[city_code]" placeholder="'.$texts['city_code'].'" value="'.$edit_order_data['company_phone']['city_code'].'" required>';
    $html .= '</span>';
    $html .= '<span class="pull-left herizon-span">-</span>';
    $html .= '<span class="pull-left col-md-4">';
    $html .= '<input type="text" class="form-control input-sm required" id="company_phone_number" name="company_phone[number]" placeholder="'.$texts['office_phone_holder'].'" value="'.$edit_order_data['company_phone']['number'].'" required>';
    $html .= '</span>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    //company_fax
    $edit_order_data['company_fax'] = json_decode($edit_order_data['company_fax'], true);
    $html .= '<div class="form-group">';
    $html .= '<div class="row">';
    $html .= '<div class="col-md-3">';
    $html .= '<label class="herizon-label">'.$texts['fax'].''.$texts['colon'].'</label>';
    $html .= '</div>';
    $html .= '<div class="col-md-7">';
    $html .= '<span class="pull-left herizon-span">+</span>';
    $html .= '<span class="pull-left col-md-3">';
    $html .= '<input type="text" class="form-control input-sm" id="company_fax_country_code" name="company_fax[country_code]" placeholder="'.$texts['country_code'].'" value="'.$edit_order_data['company_fax']['country_code'].'">';
    $html .= '</span>';
    $html .= '<span class="pull-left herizon-span">-</span>';
    $html .= '<span class="pull-left col-md-3">';
    $html .= '<input type="text" class="form-control input-sm" id="company_fax_city_code" name="company_fax[city_code]" placeholder="'.$texts['city_code'].'" value="'.$edit_order_data['company_fax']['city_code'].'">';
    $html .= '</span>';
    $html .= '<span class="pull-left herizon-span">-</span>';
    $html .= '<span class="pull-left col-md-4">';
    $html .= '<input type="text" class="form-control input-sm" id="company_fax_number" name="company_fax[number]" placeholder="'.$texts['fax'].'" value="'.$edit_order_data['company_fax']['number'].'">';
    $html .= '</span>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    //company_website
    $html .= '<div class="form-group">';
    $html .= '<div class="row">';
    $html .= '<div class="col-md-3">';
    $html .= '<label class="herizon-label" for="company_website">'.$texts['website'].$texts['colon'].'</label>';
    $html .= '</div>';
    $html .= '<div class="col-md-6">';
    $html .= '<input type="text" class="form-control input-sm" id="company_website" name="company_website" placeholder="'.$texts['website'].'" value="'.$edit_order_data['company_website'].'">';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    //参会者信息
    for ($i = 0; $i < count($attendees_data); $i++) {
      $html .= '<div class="content-block">';
      $html .= '<div class="block-header">';
      $html .= '<h4>'.$texts['attendee_info']." #".($i+1).'<small class="text-danger">'.$texts['required_explain'].'</small></h4>';
      $html .= '</div>';
      $html .= '<div class="block-body">';
      $html .= '<div class="description text-warning">';
      $html .= $texts['attendee_info_warning'];
      $html .= '</div>';
      $html .= '<div class="body-content">';
      $html .= '<div class="row">';
      $html .= '<div class="col-md-8">';
      //name
      $attendees_data[$i]['name'] = json_decode($attendees_data[$i]['name'], true);
      $attendees_data[$i]['name']['en'] = json_decode($attendees_data[$i]['name']['en'], true);
      $html .= '<div class="form-group">';
      $html .= '<div class="row">';
      $html .= '<div class="col-md-7">';
      $html .= '<label>'.$texts['attendee_name'].'<span class="text-danger"> * </span>'.$texts['colon'].'</label>';
      $html .= '</div>';
      $html .= '</div>';
      $html .= '<div class="row">';
      $html .= '<span class="pull-left col-md-3">';
      $html .= '<label class="herizon-label" for="attendee_'.$i.'_name_zh">'.$texts['chinese'].'</label>';
      $html .= '</span>';
      $html .= '<span class="pull-left col-md-9">';
      $html .= '<input type="text" class="form-control input-sm required" id="attendee_'.$i.'_name_zh" name="attendee['.$i.'][name][zh]" placeholder="'.$texts['attendee_name_chinese_blur'].'" onblur="on_bind('.$i.')" value="'.$attendees_data[$i]['name']['zh'].'" required>';
      $html .= '</span>';
      $html .= '</div>';
      $html .= '<div class="row">';
      $html .= '<span class="pull-left col-md-3">';
      $html .= '<label class="herizon-label" for="attendee_'.$i.'_name_en">'.$texts['english'].'</label>';
      $html .= '</span>';
      $html .= '<span class="pull-left col-md-9">';
      $html .= '<div class="row">';
      $html .= '<span class="pull-left col-md-5">';
      $html .= '<input type="text" class="form-control input-sm required" id="attendee_'.$i.'_name_en_firstname" name="attendee['.$i.'][name][en][firstname]" placeholder="'.$texts['attendee_first_name'].'" onblur="on_bind_enname('.$i.')" value="'.$attendees_data[$i]['name']['en']['firstname'].'" required>';
      $html .= '</span>';
      $html .= '<span class="pull-left herizon-span">-</span>';
      $html .= '<span class="pull-left col-md-5">';
      $html .= '<input type="text" class="form-control input-sm required" id="attendee_'.$i.'_name_en_lastname" name="attendee['.$i.'][name][en][lastname]" placeholder="'.$texts['attendee_last_name'].'" onblur="on_bind_enname('.$i.')" value="'.$attendees_data[$i]['name']['en']['lastname'].'" required>';
      $html .= '</span>';
      $html .= '</div>';
      $html .= '</span>';
      $html .= '</div>';
      $html .= '</div>';
      //title
      $attendees_data[$i]['title'] = json_decode($attendees_data[$i]['title'], true);
      $html .= '<div class="form-group">';
      $html .= '<div class="row">';
      $html .= '<div class="col-md-7">';
      $html .= '<label>'.$texts['attendee_title'].'<span class="text-danger"> * </span>'.$texts['colon'].'</label>';
      $html .= '</div>';
      $html .= '</div>';
      $html .= '<div class="row">';
      $html .= '<span class="pull-left col-md-3">';
      $html .= '<label class="herizon-label" for="attendee_'.$i.'_title_zh">'.$texts['chinese'].'</label>';
      $html .= '</span>';
      $html .= '<span class="pull-left col-md-9">';
      $html .= '<input type="text" class="form-control input-sm required" id="attendee_'.$i.'_title_zh" name="attendee['.$i.'][title][zh]" placeholder="'.$texts['chinese'].'" value="'.$attendees_data[$i]['title']['zh'].'" required>';
      $html .= '</span>';
      $html .= '</div>';
      $html .= '<div class="row">';
      $html .= '<span class="pull-left col-md-3">';
      $html .= '<label class="herizon-label" for="attendee_'.$i.'_title_en">'.$texts['english'].'</label>';
      $html .= '</span>';
      $html .= '<span class="pull-left col-md-9">';
      $html .= '<input type="text" class="form-control input-sm required" id="attendee_'.$i.'_title_en" name="attendee['.$i.'][title][en]" placeholder="'.$texts['english'].'" value="'.$attendees_data[$i]['title']['en'].'" required>';
      $html .= '</span>';
      $html .= '</div>';
      $html .= '</div>';
      // $html .= '<div class="form-group">';
      // $html .= '<div class="row">';
      // $html .= '<span class="col-md-3">';
      // $html .= '<label class="herizon-label" for="attendee_'.$i.'_title">'.$texts['attendee_title'].'<span class="text-danger"> * </span>'.$texts['colon'].'</label>';
      // $html .= '</span>';
      // $html .= '<span class="col-md-9">';
      // $html .= '<input type="text" class="form-control input-sm required" id="attendee_'.$i.'_title" name="attendee['.$i.'][title]" placeholder="'.$texts['attendee_title'].'" value="'.$attendees_data[$i]['title'].'" required>';
      // $html .= '</span>';
      // $html .= '</div>';
      // $html .= '</div>';
      //email
      $html .= '<div class="form-group">';
      $html .= '<div class="row">';
      $html .= '<span class="col-md-3">';
      $html .= '<label class="herizen-label" for="attendee_'.$i.'_email">'.$texts['attendee_email'].'<span class="text-danger"> * </span>'.$texts['colon'].'</label>';
      $html .= '</span>';
      $html .= '<span class="col-md-9">';
      $html .= '<input type="email" class="form-control input-sm required" id="attendee_'.$i.'_email" name="attendee['.$i.'][email]" placeholder="'.$texts['attendee_email'].'" value="'.$attendees_data[$i]['email'].'" disabled="disabled" required>';
      $html .= '</span>';
      $html .= '</div>';
      $html .= '</div>';
      //telephone
      $attendees_data[$i]['telephone'] = json_decode($attendees_data[$i]['telephone'], true);
      $html .= '<div class="form-group">';
      $html .= '<div class="row">';
      $html .= '<div class="col-md-3">';
      $html .= '<label class="herizon-label">'.$texts['attendee_telephone'].'<span class="text-danger"> * </span>'.$texts['colon'].'</label>';
      $html .= '</div>';
      $html .= '<div class="col-md-9">';
      $html .= '<span class="pull-left herizon-span">+</span>';
      $html .= '<span class="pull-left col-md-4">';
      $html .= '  <input type="text" class="form-control input-sm required" id="attendee_'.$i.'_telephone_country_code" name="attendee['.$i.'][telephone][country_code]" placeholder="'.$texts['country_code'].'" value="'.$attendees_data[$i]['telephone']['country_code'].'" required>';
      $html .= '</span>';
      $html .= '<span class="pull-left herizon-span">-</span>';
      $html .= '<span class="pull-left col-md-7">';
      $html .= '  <input type="text" class="form-control input-sm required" id="attendee_'.$i.'_telephone_number" name="attendee['.$i.'][telephone][number]" placeholder="'.$texts['telephone_number_holder'].'" value="'.$attendees_data[$i]['telephone']['number'].'" required>';
      $html .= '</span>';
      $html .= '</div>';
      $html .= '</div>';
      $html .= '</div>';
      //phone
      $attendees_data[$i]['phone'] = json_decode($attendees_data[$i]['phone'], true);
      $html .= '<div class="form-group">';
      $html .= '<div class="row">';
      $html .= '<div class="col-md-3">';
      $html .= '<label class="herizon-label">'.$texts['attendee_phone'].'<span class="text-danger"> * </span>'.$texts['colon'].'</label>';
      $html .= '</div>';
      $html .= '<div class="col-md-9">';
      $html .= '<span class="pull-left herizon-span">+</span>';
      $html .= '<span class="pull-left col-md-3">';
      $html .= '  <input type="text" class="form-control input-sm required" id="attendee_'.$i.'_phone_country_code" name="attendee['.$i.'][phone][country_code]" placeholder="'.$texts['country_code'].'" value="'.$attendees_data[$i]['phone']['country_code'].'" required>';
      $html .= '</span>';
      $html .= '<span class="pull-left herizon-span">-</span>';
      $html .= '<span class="pull-left col-md-3">';
      $html .= '  <input type="text" class="form-control input-sm required" id="attendee_'.$i.'_phone_city_code" name="attendee['.$i.'][phone][city_code]" placeholder="'.$texts['city_code'].'" value="'.$attendees_data[$i]['phone']['city_code'].'" required>';
      $html .= '</span>';
      $html .= '<span class="pull-left herizon-span">-</span>';
      $html .= '<span class="pull-left col-md-5">';
      $html .= '  <input type="text" class="form-control input-sm required" id="attendee_'.$i.'_phone_city_code" name="attendee['.$i.'][phone][number]" placeholder="'.$texts['attendee_phone'].'" value="'.$attendees_data[$i]['phone']['number'].'" required>';
      $html .= '</span>';
      $html .= '</div>';
      $html .= '</div>';
      $html .= '</div>';
      //selects
      $attendees_data[$i]['extra_info'] = json_decode($attendees_data[$i]['extra_info'],true);
	  // ### hide hotel information by wangshuai @ 20150320 ##
	  
	  $html .= '<div class="form-group">';
      $html .= '<label>'.$texts['attendee_select_1'].'</label>';
      $html .= '<label class="radio-inline">';
      $checked = ($attendees_data[$i]['extra_info']['need_room'] == '1')?"checked":"";
      $html .= '<input type="radio" name="attendee['.$i.'][extra_info][need_room]" value="1" '.$checked.' />'.$texts['attendee_select_1_option_1'].'';
      $html .= '</label>';
      $html .= '<label class="radio-inline">';
      $checked = ($attendees_data[$i]['extra_info']['need_room'] == '-1')?"checked":"";
      $html .= '<input type="radio" name="attendee['.$i.'][extra_info][need_room]" value="-1" '.$checked.' />'.$texts['attendee_select_1_option_2'].'';
      $html .= '</label>';
      $html .= '</div>';
	  
	  //### end ####
	  //### other questions added by wangshuai @ 20150303 ####
	  /*$html .= '<div class="form-group">';
      $html .= '<label>'.$texts['attendee_select_2'].'</label>';
      $html .= '<label class="radio-inline">';
      $checked = ($attendees_data[$i]['extra_info']['attend_14'] == '1')?"checked":"";
      $html .= '<input type="radio" name="attendee['.$i.'][extra_info][attend_14]" value="1" '.$checked.' />'.$texts['attendee_select_1_option_1'].'';
      $html .= '</label>';
      $html .= '<label class="radio-inline">';
      $checked = ($attendees_data[$i]['extra_info']['attend_14'] == '-1')?"checked":"";
      $html .= '<input type="radio" name="attendee['.$i.'][extra_info][attend_14]" value="-1" '.$checked.' />'.$texts['attendee_select_1_option_2'].'';
      $html .= '</label>';
      $html .= '</div>';
	  
	  $html .= '<div class="form-group">';
      $html .= '<label>'.$texts['attendee_select_3'].'</label>';
      $html .= '<label class="radio-inline">';
      $checked = ($attendees_data[$i]['extra_info']['attend_16'] == '1')?"checked":"";
      $html .= '<input type="radio" name="attendee['.$i.'][extra_info][attend_16]" value="1" '.$checked.' />'.$texts['attendee_select_1_option_1'].'';
      $html .= '</label>';
      $html .= '<label class="radio-inline">';
      $checked = ($attendees_data[$i]['extra_info']['attend_16'] == '-1')?"checked":"";
      $html .= '<input type="radio" name="attendee['.$i.'][extra_info][attend_16]" value="-1" '.$checked.' />'.$texts['attendee_select_1_option_2'].'';
      $html .= '</label>';
      $html .= '</div>';
	  
	  $html .= '<div class="form-group">';
      $html .= '<label>'.$texts['attendee_select_4'].'</label>';
      $html .= '<label class="radio-inline">';
      $checked = ($attendees_data[$i]['extra_info']['attend_oversea'] == '1')?"checked":"";
      $html .= '<input type="radio" name="attendee['.$i.'][extra_info][attend_oversea]" value="1" '.$checked.' />'.$texts['attendee_select_1_option_1'].'';
      $html .= '</label>';
      $html .= '<label class="radio-inline">';
      $checked = ($attendees_data[$i]['extra_info']['attend_oversea'] == '-1')?"checked":"";
      $html .= '<input type="radio" name="attendee['.$i.'][extra_info][attend_oversea]" value="-1" '.$checked.' />'.$texts['attendee_select_1_option_2'].'';
      $html .= '</label>';
      $html .= '</div>';*/
	  
	  //### end ######################
	  
      $html .= '</div>';
      $html .= '<div class="col-md-4">';
      $html .= '<div class="badge-example text-center">';
      $html .= '<div class="title">';
      $html .= ''.$texts['badge_description'].'';
      $html .= '</div>';
      $html .= '<div id="" class="badge-name badge_name_'.$i.'_zh">';
      $html .= '</div>';
      $html .= '<div id="" class="badge-name badge_name_'.$i.'_en"></div>';
      $html .= '<div id="" class="badge-company-first badge-company badge_company_name_zh">';
      $html .= '</div>';
      $html .= '<div id="" class="badge-company badge_company_name_en">';
      $html .= '</div>';
      $html .= '</div>';
      $html .= '</div>';
      $html .= '</div>';
      $html .= '</div>';
      $html .= '</div>';
      $html .= '</div>';
    }
    $html .= '<div class="text-center">';
    $html .= '<button type="button" class="btn btn-default" onclick="close_update()">'.$texts['close_button'].'</button>';
    $html .= '&nbsp;&nbsp;<button type="button" class="btn btn-primary" onclick="submit_update('.$order_id.')">'.$texts['submit'].'</button>';
    $html .= '</div>';
    $html .= '</form>';

	$ret['r'] = 'ok';
	$ret['msg'] = $html;

	die(json_encode($ret));
}

if ($_GET['do'] == 'update_order') {
    $ret = array();
    if (!isset($_GET['order'])) {
        $ret['r'] = 'error';
        $ret['msg'] = '10001:Argument error.';

        die(json_encode($ret));
    }
    $order_id = $_GET['order'];

    //检查post数据是否为空
    if (!isset($_POST)) {
        $ret['r'] = 'error';
        $ret['msg'] = '10001:Post data can not be empty';

        die(json_encode($ret));
    }

    ##数据检查参数单
    $order_config = array(
        'contact_name' => array(
            'operate' => 'trim',
            'required' => 'is_require'
            ),
        'invoice_title' => array(
            'operate' => 'trim',
            'required' => 'besides',
            'required_argument' => true
            ),
        'contact_phone' => array(
            'operate' => 'trim',
            'required' => 'is_require'
            ),
        'company_field_id' => array(
            'operate' => 'trim',
            'required' => 'is_require'
            ),
        'company_name' => array(
            'operate' => 'trim',
            'required' => 'is_require'
            ),
        'company_address' => array(
            'operate' => 'trim',
            'required' => 'is_require'
            ),
        'company_zipcode' => array(
            'operate' => 'trim',
            'required' => 'not_require'
            ),
        'company_phone' => array(
            'operate' => 'trim',
            'required' => 'is_require'
            ),
        'company_fax' => array(
            'operate' => 'trim',
            'required' => 'not_require'
            ),
        'company_website' => array(
            'operate' => 'trim',
            'required' => 'not_require'
            ),
        'company_phone' => array(
            'operate' => 'trim',
            'required' => 'is_require'
            ),
        'tickets' => array(
            'operate' => 'trim',
            'required' => 'is_require'
            ),
        'need_invoice' => array(
            'operate' => 'trim',
            'required' => 'is_require'
            ),
        );

    ##参会者数据检查参数单
    $attendee_config = array(
        'name' => array(
            'operate' => 'trim',
            'required' => 'is_require'
            ),
        'title' => array(
            'operate' => 'trim',
            'required' => 'is_require'
            ),
        'eamil' => array(
            'operate' => 'trim',
            'required' => 'is_require'
            ),
        'telephone' => array(
            'operate' => 'trim',
            'required' => 'is_require'
            ),
        'phone' => array(
            'operate' => 'trim',
            'required' => 'is_require'
            ),
        'extra_info' => array(
            'operate' => 'trim',
            'required' => 'not_require'
            )
        );

    $order_data = $_POST;
    //禁止修改联系人email
    if (isset($order_data['contact_email'])) {
        unset($order_data['contact_email']);
    }
    //写入数据库数据
    $write_order = array();

    //处理参会者数据
    if (isset($order_data['attendee'])) {
        $attendees_data = $order_data['attendee'];
        unset($order_data['attendee']);
    }
    else {
        $attendees_data = array();
    }

    ##检查订单数据
    //检查不为空数据
    $check_ret = check_data($order_data, $order_config);
    if ($check_ret['r'] == 'error') {
        die(json_encode($check_ret));
    }
    $write_order = $order_data;

    ##检查参会者数据
    //取出参会者
    $attendees = $orders[$order_id]->get_attendees();
    if (count($attendees_data) != count($attendees)) {
        $ret['r'] = 'error';
        $ret['msg'] = '30001:Illegal data.';

        die(json_encode($ret));
    }
    foreach ($attendees_data as $key => $attendee_data) {
        //处理firstname & lastname
        $attendee_data['name']['en']['firstname'] = trim($attendee_data['name']['en']['firstname']);
        $attendee_data['name']['en']['lastname'] = trim($attendee_data['name']['en']['lastname']);
        //检查firstname & lastname
        if ($attendee_data['name']['en']['firstname'] == ''
            || $attendee_data['name']['en']['lastname'] == '') {
            $ret['r'] = 'error';
            $ret['msg'] = '10001:'.$texts['required_field_empty'];

            die(json_encode($ret));
        }
        else {
            $attendees_data[$key]['name']['en'] = json_encode($attendees_data[$key]['name']['en']);
        }
        //检查attendee数据
        $check_ret = check_data($attendees_data[$key], $attendee_config);

        if ($check_ret['r'] == 'error') {
            die(json_encode($check_ret));
        }
        else {
            $attendees_data[$key] = $check_ret['msg'];
        }

        ##检查email
        // foreach ($attendees_data as $k => $v) {
        //     if ( ($attendee_data['email'] == $v['email']) 
        //     && ($key != $k) ) {
        //         $ret['r'] = 'error';
        //         $ret['msg'] = '40002:'.$texts['email_repeat_error_1'];

        //         die(json_encode($ret));
        //     }
        // }
        // $check_ret = Attendee::check_email_exists($activity_id, $attendee_data['email']);
        // if ($check_ret['r'] == 'error') {
        //     $ret['r'] = 'error';
        //     $ret['msg'] = '40002:'.$attendee_data['email'].' '.$texts['email_repeat_error_2'];

        //     die(json_encode($ret));
        // }

        ##禁止修改email
        if (isset($attendees_data[$key]['email'])) {
            unset($attendees_data[$key]['email']);
        }

        $attendees_data[$key]['company'] = $order_data['company_name'];
    }

    //更新订单
    $update_ret = $orders[$order_id]->update($write_order);
    if ($update_ret['r'] == 'error') {
        die(json_encode($update_ret));
    }

    //更新参会者
    for ($i=0; $i < count($attendees_data); $i++) { 
        $update_ret = $attendees[$i]->update($attendees_data[$i]);
        if ($update_ret['r'] == 'error') {
            die(json_encode($update_ret));
        }
    }

    $ret['r'] = 'ok';
    $ret['msg'] = $texts['save_success'];

    die(json_encode($ret));
}