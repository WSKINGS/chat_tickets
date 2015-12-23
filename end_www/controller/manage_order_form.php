<?php 

include_once "manage_include.php";
include_once "order_form_text.php";

$texts = $texts['zh'];
$view_data['texts'] = $texts;

function create_form_html($texts, $edit_order_data, $attendees_data, $edit) {
	$disabled = ($edit)?"":'disabled="disabled"';

	$html = "";

	//form起始
	$html .= '<form id="order_form_update" data-order="'.$edit_order_data['order_id'].'" method="POST" role="from">';
	//联系人信息
	$html .= '<div class="content-block">';
	//header
	$html .= '<div class="block-header">';
	$html .= '<h4>'.$texts['contact_info'].'<small class="text-danger">'.$texts['required_explain'].'</small></h4>';
	$html .= '</div>';
	//body
	$html .= '<div class="block-body">';
	$html .= '<div class="body-content">';
	$html .= '<div class="row">';
	$html .= '<div class="col-md-6">';
	//contact_name
	$html .= '<div class="form-group">';
	$html .= '<label for="contact_name">'.$texts['contact_name'].'<span class="text-danger"> * </span>'.$texts['colon'].'</label>';
	$html .= '<input type="text" class="form-control input-sm required" id="contact_name" name="contact_name" placeholder="'.$texts['contact_name'].'" value="'.$edit_order_data['contact_name'].'" '.$disabled.' required>';
	$html .= '</div>';
	$html .= '</div>';
	$html .= '<div class="col-md-6">';

	if ($edit_order_data['need_invoice'] == '1') {
		//invoice_title
		$html .= '<div class="form-group">';
		$html .= '<label for="invoice_title">'.$texts['invoice_title'].'<span class="text-danger"> * </span>'.$texts['colon'].'</label>';
		$html .= '<input type="text" class="form-control input-sm required" id="invoice_title" name="invoice_title" placeholder="'.$texts['invoice_title'].'" value="'.$edit_order_data['invoice_title'].'" '.$disabled.' required>';
		$html .= '</div>';
	}

	$html .= '</div>';
	$html .= '</div>';
	$html .= '<div class="row">';
	$html .= '<div class="col-md-6">';
	//contact_email
	$html .= '<div class="form-group">';
	$html .= '<label for="contact_email">'.$texts['contact_email'].'<span class="text-danger"> * </span>'.$texts['colon'].'</label>';
	$html .= '<input type="email" class="form-control input-sm required" id="contact_email" name="contact_email" placeholder="'.$texts['contact_email'].'" value="'.$edit_order_data['contact_email'].'" '.$disabled.' required>';
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
	$html .= '<input type="text" class="form-control input-sm required" id="contact_phone_country_code" name="contact_phone[country_code]" placeholder="'.$texts['country_code'].'" value="'.$edit_order_data['contact_phone']['country_code'].'" '.$disabled.' required>';
	$html .= '</span>';
	$html .= '<span class="pull-left herizon-span">-</span>';
	$html .= '<span class="pull-left col-md-7">';
	$html .= '<input type="text" class="form-control input-sm required" id="contact_phone_phone_number" name="contact_phone[number]" placeholder="'.$texts['telephone_number'].'" value="'.$edit_order_data['contact_phone']['number'].'" '.$disabled.' required>';
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
    $html .= '<select class="form-control input-sm" id="company_field_id" name="company_field_id"  '.$disabled.'>';

    //company_fields_options
    $company_fields = model('wt_option')->get_list(array('key_name'=>'company_field', 'status'=>'1', 'order' => 'order_id asc'));
    for ($i=0; $i<count($company_fields); $i++) {
        $company_fields[$i]['name'] = json_decode($company_fields[$i]['name'],true);
    }
    foreach ($company_fields as $field) {
    	$selected = ($field['option_id'] == $edit_order_data['company_field_id'])?'selected':'';
    	$html .= '<option '.$selected.' value="'.$field['option_id'].'">'.$field['name']['zh'].'</option>';
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
    $html .= '<input type="text" class="form-control input-sm required" id="company_name_zh" name="company_name[zh]" placeholder="'.$texts['company_name_chinese_blur'].'" value="'.$edit_order_data['company_name']['zh'].'" onblur="show_name_zh()" '.$disabled.' required>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '<div class="row">';
    $html .= '<div class="col-md-3">';
    $html .= '<label class="herizon-label" for="company_name_en">'.$texts['english'].$texts['colon'].'</label>';
    $html .= '</div>';
    $html .= '<div class="col-md-6">';
    $html .= '<input type="text" class="form-control input-sm required" id="company_name_en" name="company_name[en]" placeholder="'.$texts['english'].'" value="'.$edit_order_data['company_name']['en'].'" onblur="show_name_en()" '.$disabled.' required>';
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
    $html .= '<input type="text" class="form-control input-sm required" id="company_address_country" name="company_address[country]" placeholder="'.$texts['company_address_country'].'" value="'.$edit_order_data['company_address']['country'].'" '.$disabled.' required>';
    $html .= '</span>';
    $html .= '<span class="col-md-4 pull-left">';
    $html .= '<input type="text" class="form-control input-sm required" id="company_address_province" name="company_address[province]" placeholder="'.$texts['company_address_province'].'" value="'.$edit_order_data['company_address']['province'].'" '.$disabled.' required>';
    $html .= '</span>';
    $html .= '<span class="col-md-4 pull-left">';
    $html .= '<input type="text" class="form-control input-sm required" id="company_address_city" name="company_address[city]" placeholder="'.$texts['company_address_city'].'" value="'.$edit_order_data['company_address']['city'].'" '.$disabled.' required>';
    $html .= '</span>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '<div class="row">';
    $html .= '<div class="col-md-3">';
    $html .= '</div>';
    $html .= '<div class="col-md-6">';
    $html .= '<input type="text" class="form-control input-sm required" id="company_address_details" name="company_address[details]" placeholder="'.$texts['company_address_holder'].'" value="'.$edit_order_data['company_address']['details'].'" '.$disabled.' required>';
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
    $html .= '<input type="text" class="form-control input-sm" id="company_zipcode" name="company_zipcode" placeholder="'.$texts['zipcode'].'" value="'.$edit_order_data['company_zipcode'].'" '.$disabled.'>';
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
    $html .= '<input type="text" class="form-control input-sm required" id="company_phone_country_code" name="company_phone[country_code]" placeholder="'.$texts['country_code'].'" value="'.$edit_order_data['company_phone']['country_code'].'" '.$disabled.' required>';
    $html .= '</span>';
    $html .= '<span class="pull-left herizon-span">-</span>';
    $html .= '<span class="pull-left col-md-3">';
    $html .= '<input type="text" class="form-control input-sm required" id="company_phone_city_code" name="company_phone[city_code]" placeholder="'.$texts['city_code'].'" value="'.$edit_order_data['company_phone']['city_code'].'" '.$disabled.' required>';
    $html .= '</span>';
    $html .= '<span class="pull-left herizon-span">-</span>';
    $html .= '<span class="pull-left col-md-4">';
    $html .= '<input type="text" class="form-control input-sm required" id="company_phone_number" name="company_phone[number]" placeholder="'.$texts['office_phone_holder'].'" value="'.$edit_order_data['company_phone']['number'].'" '.$disabled.' required>';
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
    $html .= '<input type="text" class="form-control input-sm" id="company_fax_country_code" name="company_fax[country_code]" placeholder="'.$texts['country_code'].'" value="'.$edit_order_data['company_fax']['country_code'].'" '.$disabled.'>';
    $html .= '</span>';
    $html .= '<span class="pull-left herizon-span">-</span>';
    $html .= '<span class="pull-left col-md-3">';
    $html .= '<input type="text" class="form-control input-sm" id="company_fax_city_code" name="company_fax[city_code]" placeholder="'.$texts['city_code'].'" value="'.$edit_order_data['company_fax']['city_code'].'" '.$disabled.'>';
    $html .= '</span>';
    $html .= '<span class="pull-left herizon-span">-</span>';
    $html .= '<span class="pull-left col-md-4">';
    $html .= '<input type="text" class="form-control input-sm" id="company_fax_number" name="company_fax[number]" placeholder="'.$texts['fax'].'" value="'.$edit_order_data['company_fax']['number'].'" '.$disabled.'>';
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
    $html .= '<input type="text" class="form-control input-sm" id="company_website" name="company_website" placeholder="'.$texts['website'].'" value="'.$edit_order_data['company_website'].'" '.$disabled.'>';
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
      $html .= '<input type="text" class="form-control input-sm required" id="attendee_'.$i.'_name_zh" name="attendee['.$i.'][name][zh]" placeholder="'.$texts['attendee_name_chinese_blur'].'" onblur="on_bind('.$i.')" value="'.$attendees_data[$i]['name']['zh'].'" '.$disabled.' required>';
      $html .= '</span>';
      $html .= '</div>';
      $html .= '<div class="row">';
      $html .= '<span class="pull-left col-md-3">';
      $html .= '<label class="herizon-label" for="attendee_'.$i.'_name_en">'.$texts['english'].'</label>';
      $html .= '</span>';
      $html .= '<span class="pull-left col-md-9">';
      $html .= '<div class="row">';
      $html .= '<span class="pull-left col-md-5">';
      $html .= '<input type="text" class="form-control input-sm required" id="attendee_'.$i.'_name_en_firstname" name="attendee['.$i.'][name][en][firstname]" placeholder="'.$texts['attendee_first_name'].'" onblur="on_bind_enname('.$i.')" value="'.$attendees_data[$i]['name']['en']['firstname'].'" '.$disabled.' required>';
      $html .= '</span>';
      $html .= '<span class="pull-left herizon-span">-</span>';
      $html .= '<span class="pull-left col-md-5">';
      $html .= '<input type="text" class="form-control input-sm required" id="attendee_'.$i.'_name_en_lastname" name="attendee['.$i.'][name][en][lastname]" placeholder="'.$texts['attendee_last_name'].'" onblur="on_bind_enname('.$i.')" value="'.$attendees_data[$i]['name']['en']['lastname'].'" '.$disabled.' required>';
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
      $html .= '<input type="text" class="form-control input-sm required" id="attendee_'.$i.'_title_zh" name="attendee['.$i.'][title][zh]" placeholder="'.$texts['chinese'].'" value="'.$attendees_data[$i]['title']['zh'].'" '.$disabled.' required>';
      $html .= '</span>';
      $html .= '</div>';
      $html .= '<div class="row">';
      $html .= '<span class="pull-left col-md-3">';
      $html .= '<label class="herizon-label" for="attendee_'.$i.'_title_en">'.$texts['english'].'</label>';
      $html .= '</span>';
      $html .= '<span class="pull-left col-md-9">';
      $html .= '<input type="text" class="form-control input-sm required" id="attendee_'.$i.'_title_en" name="attendee['.$i.'][title][en]" placeholder="'.$texts['english'].'" value="'.$attendees_data[$i]['title']['en'].'" '.$disabled.' required>';
      $html .= '</span>';
      $html .= '</div>';
      $html .= '</div>';
      //email
      $html .= '<div class="form-group">';
      $html .= '<div class="row">';
      $html .= '<span class="col-md-3">';
      $html .= '<label class="herizen-label" for="attendee_'.$i.'_email">'.$texts['attendee_email'].'<span class="text-danger"> * </span>'.$texts['colon'].'</label>';
      $html .= '</span>';
      $html .= '<span class="col-md-9">';
      $html .= '<input type="email" class="form-control input-sm required" id="attendee_'.$i.'_email" name="attendee['.$i.'][email]" placeholder="'.$texts['attendee_email'].'" value="'.$attendees_data[$i]['email'].'"  '.$disabled.' required>';
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
      $html .= '  <input type="text" class="form-control input-sm required" id="attendee_'.$i.'_telephone_country_code" name="attendee['.$i.'][telephone][country_code]" placeholder="'.$texts['country_code'].'" value="'.$attendees_data[$i]['telephone']['country_code'].'" '.$disabled.' required>';
      $html .= '</span>';
      $html .= '<span class="pull-left herizon-span">-</span>';
      $html .= '<span class="pull-left col-md-7">';
      $html .= '  <input type="text" class="form-control input-sm required" id="attendee_'.$i.'_telephone_number" name="attendee['.$i.'][telephone][number]" placeholder="'.$texts['telephone_number_holder'].'" value="'.$attendees_data[$i]['telephone']['number'].'" '.$disabled.' required>';
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
      $html .= '  <input type="text" class="form-control input-sm required" id="attendee_'.$i.'_phone_country_code" name="attendee['.$i.'][phone][country_code]" placeholder="'.$texts['country_code'].'" value="'.$attendees_data[$i]['phone']['country_code'].'" '.$disabled.' required>';
      $html .= '</span>';
      $html .= '<span class="pull-left herizon-span">-</span>';
      $html .= '<span class="pull-left col-md-3">';
      $html .= '  <input type="text" class="form-control input-sm required" id="attendee_'.$i.'_phone_city_code" name="attendee['.$i.'][phone][city_code]" placeholder="'.$texts['city_code'].'" value="'.$attendees_data[$i]['phone']['city_code'].'" '.$disabled.' required>';
      $html .= '</span>';
      $html .= '<span class="pull-left herizon-span">-</span>';
      $html .= '<span class="pull-left col-md-5">';
      $html .= '  <input type="text" class="form-control input-sm required" id="attendee_'.$i.'_phone_city_code" name="attendee['.$i.'][phone][number]" placeholder="'.$texts['attendee_phone'].'" value="'.$attendees_data[$i]['phone']['number'].'" '.$disabled.' required>';
      $html .= '</span>';
      $html .= '</div>';
      $html .= '</div>';
      $html .= '</div>';
      //selects
      $attendees_data[$i]['extra_info'] = json_decode($attendees_data[$i]['extra_info'],true);
      
	  //### hide hotel information by wangshuai @ 20150320
	  
	  $html .= '<div class="form-group">';
      $html .= '<label>'.$texts['attendee_select_1'].'</label>';
      $html .= '<div>';
      $html .= '<label class="radio-inline">';
      $checked = ($attendees_data[$i]['extra_info']['need_room'] == '1')?"checked":"";
      $html .= '<input type="radio" name="attendee['.$i.'][extra_info][need_room]" value="1" '.$checked.' '.$disabled.' />'.$texts['attendee_select_1_option_1'].'';
      $html .= '</label>';
      $html .= '<label class="radio-inline">';
      $checked = ($attendees_data[$i]['extra_info']['need_room'] == '-1')?"checked":"";
      $html .= '<input type="radio" name="attendee['.$i.'][extra_info][need_room]" value="-1" '.$checked.' '.$disabled.' />'.$texts['attendee_select_1_option_2'].'';
      $html .= '</label>';
      $html .= '</div>';
      $html .= '</div>';
	  
	  
	  //### other questions added by wangshuai @20150303
	  /*$html .= '<div class="form-group">';
      $html .= '<label>'.$texts['attendee_select_2'].'</label>';
      $html .= '<div>';
      $html .= '<label class="radio-inline">';
      $checked = ($attendees_data[$i]['extra_info']['attend_14'] == '1')?"checked":"";
      $html .= '<input type="radio" name="attendee['.$i.'][extra_info][attend_14]" value="1" '.$checked.' '.$disabled.' />'.$texts['attendee_select_1_option_1'].'';
      $html .= '</label>';
      $html .= '<label class="radio-inline">';
      $checked = ($attendees_data[$i]['extra_info']['attend_14'] == '-1')?"checked":"";
      $html .= '<input type="radio" name="attendee['.$i.'][extra_info][attend_14]" value="-1" '.$checked.' '.$disabled.' />'.$texts['attendee_select_1_option_2'].'';
      $html .= '</label>';
      $html .= '</div>';
      $html .= '</div>';
	  
	  $html .= '<div class="form-group">';
      $html .= '<label>'.$texts['attendee_select_3'].'</label>';
      $html .= '<div>';
      $html .= '<label class="radio-inline">';
      $checked = ($attendees_data[$i]['extra_info']['attend_16'] == '1')?"checked":"";
      $html .= '<input type="radio" name="attendee['.$i.'][extra_info][attend_16]" value="1" '.$checked.' '.$disabled.' />'.$texts['attendee_select_1_option_1'].'';
      $html .= '</label>';
      $html .= '<label class="radio-inline">';
      $checked = ($attendees_data[$i]['extra_info']['attend_16'] == '-1')?"checked":"";
      $html .= '<input type="radio" name="attendee['.$i.'][extra_info][attend_16]" value="-1" '.$checked.' '.$disabled.' />'.$texts['attendee_select_1_option_2'].'';
      $html .= '</label>';
      $html .= '</div>';
      $html .= '</div>';
	  
	  $html .= '<div class="form-group">';
      $html .= '<label>'.$texts['attendee_select_4'].'</label>';
      $html .= '<div>';
      $html .= '<label class="radio-inline">';
      $checked = ($attendees_data[$i]['extra_info']['attend_oversea'] == '1')?"checked":"";
      $html .= '<input type="radio" name="attendee['.$i.'][extra_info][attend_oversea]" value="1" '.$checked.' '.$disabled.' />'.$texts['attendee_select_1_option_1'].'';
      $html .= '</label>';
      $html .= '<label class="radio-inline">';
      $checked = ($attendees_data[$i]['extra_info']['attend_oversea'] == '-1')?"checked":"";
      $html .= '<input type="radio" name="attendee['.$i.'][extra_info][attend_oversea]" value="-1" '.$checked.' '.$disabled.' />'.$texts['attendee_select_1_option_2'].'';
      $html .= '</label>';
      $html .= '</div>';
      $html .= '</div>';*/
	  //#### end ############
	  
      $html .= '</div>';
      $html .= '</div>';
      $html .= '</div>';
      $html .= '</div>';
      $html .= '</div>';
    }
    $html .= '<div class="text-center">';
    $html .= '</div>';
    $html .= '</form>';

    return $html;
}

$order_id = $_GET['order'];
$view_data['order_id'] = $order_id;

$order = new Order();

$order->set_order($order_id);

$order_data = $order->get_order_data();

$attendees = $order->get_attendees();

$attendees_data = array();
foreach ($attendees as $a) {
	$attendees_data[] = $a->get_attendee_data();
}

$edit = ($_GET['do'] == 'edit')?true:false;

$html = create_form_html($texts, $order_data, $attendees_data, $edit);

$ret = array();
$ret['r'] = 'ok';
$ret['msg'] = $html;

die(json_encode($ret));