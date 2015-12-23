<?php 
$html = "";

	$html .= '<form id="info_form" method="POST" role="from">';
	$html .= '<div class="content-block">';
	$html .= '<div class="block-header">';
	$html .= '<h4>'.$texts['contact_info'].'<small class="text-danger">'.$texts['required_explain'].'</small></h4>';
	$html .= '</div>';
	$html .= '<div class="block-body">';
	$html .= '<div class="description text-warning">';
	$html .= $texts['contact_info_warning'];
	$html .= '</div>';
	$html .= '<div class="body-content">';
	$html .= '<div class="row">';
	$html .= '<div class="col-md-6">';
	$html .= '<div class="form-group">';
	$html .= '<label for="contact_name">'.$texts['contact_name'].'<span class="text-danger"> * </span>'.$texts['colon'].'</label>';
	$html .= '<input type="text" class="form-control input-sm required" id="contact_name" name="contact_name" placeholder="'.$texts['contact_name'].'" required>';
	$html .= '</div>';
	$html .= '</div>';
	$html .= '<div class="col-md-6">';

	if ($orders_data[$order_id]['need_invoice'] == '1') {
		$html .= '<div class="form-group">';
		$html .= '<label for="invoice_title">'.$texts['invoice_title'].'<span class="text-danger"> * </span>'.$texts['colon'].'</label>';
		$html .= '<input type="text" class="form-control input-sm required" id="invoice_title" name="invoice_title" placeholder="'.$texts['invoice_title'].'" required>';
		$html .= '</div>';
	}

	$html .= '</div>';
	$html .= '</div>';
	$html .= '<div class="row">';
	$html .= '<div class="col-md-6">';
	$html .= '<div class="form-group">';
	$html .= '<label for="contact_email">'.$texts['contact_email'].'<span class="text-danger"> * </span>'.$texts['colon'].'</label>';
	$html .= '<input type="email" class="form-control input-sm required" id="contact_email" name="contact_email" placeholder=".'$texts['contact_email'].'" required>';
	$html .= '<span class="help-block"><p class="text-danger">'.$texts['contact_email_explain'].'</p></span>';
	$html .= '</div>';
	$html .= '</div>';
	$html .= '<div class="col-md-6">';
	$html .= '<div class="form-group">';
	$html .= '<label for="contact_phone">'.$texts['contact_phone'].'<span class="text-danger"> * </span>'.$texts['colon'].'</label>';
	$html .= '<div>';
	$html .= '<span class="pull-left herizon-span">+</span>';
	$html .= '<span class="pull-left col-md-4">';
	$html .= '<input type="text" class="form-control input-sm required" id="contact_phone_country_code" name="contact_phone[country_code]" placeholder="'.$texts['country_code'].'" required>';
	$html .= '</span>';
	$html .= '<span class="pull-left herizon-span">-</span>';
	$html .= '<span class="pull-left col-md-7">';
	$html .= '<input type="text" class="form-control input-sm required" id="contact_phone_phone_number" name="contact_phone[number]" placeholder="'.$texts['telephone_number'].'" required>';
	$html .= '</span>';
	$html .= '</div>';
	$html .= '</div>';
	$html .= '</div>';
	$html .= '</div>';
	$html .= '</div>';
	$html .= '</div>';
    $html .=  '</div>';
    $html .= '<div class="content-block">';
    $html .= '<div class="block-header">';
    $html .= '<h4>'.$texts['company_info'].'<small class='text-danger'>'.$texts['required_explain'].'</small></h4>';
    $html .= '</div>';
    $html .= '<div class="block-body">';
    $html .= '<div class="body-content">';
    $html .= '<div class="form-group">';
    $html .= '<div class="row">';
    $html .= '<div class="col-md-3">';
    $html .= '<label class="herizon-label" for="company_field_id">'.$texts['company_field'].$texts['colon'].'</label>';
    $html .= '</div>';
    $html .= '<div class="col-md-6">';
    $html .= '<select class="form-control input-sm" id="company_field_id" name="company_field_id">';

    //company_fields
    $company_fields = model('wt_option')->get_list(array('key_name'=>'company_field', 'status'=>'1', 'order' => 'order_id asc'));
    foreach ($company_fields as $field) {
    	$html .= '<option value="'.$field['option_id'].'">'.$field['name'].'</option>';
    }
    $html .= '</select>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
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
    $html .= '<input type="text" class="form-control input-sm required" id="company_name_zh" name="company_name[zh]" placeholder="'.$texts['chinese'].'" required>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '<div class="row">';
    $html .= '<div class="col-md-3">';
    $html .= '<label class="herizon-label" for="company_name_en">'.$texts['english'];.$texts['colon'].'</label>';
    $html .= '</div>';
    $html .= '<div class="col-md-6">';
    $html .= '<input type="text" class="form-control input-sm required" id="company_name_en" name="company_name[en]" placeholder="'.$texts['english'].'" required>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '<div class="form-group">';
    $html .= '<div class="row">';
    $html .= '<div class="col-md-3">';
    $html .= '<label class="herizon-label" for="company_address">'.$texts['company_address'].'<span class="text-danger"> * </span>'.$texts['colon'].'</label>';
    $html .= '</div>';
    $html .= '<div class="col-md-6">';
    $html .= '<input type="text" class="form-control input-sm required" id="company_address" name="company_address" placeholder="'.$texts['company_address'].'" required>'
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '<div class="form-group">';
    $html .= '<div class="row">';
    $html .= '<div class="col-md-3">';
    $html .= '<label class="herizon-label" for="company_zipcode">'.$texts['zipcode'].$texts['colon'].'</label>';
    $html .= '</div>';
    $html .= '<div class="col-md-6">';
    $html .= '<input type="text" class="form-control input-sm" id="company_zipcode" name="company_zipcode" placeholder="'.$texts['zipcode'].'">';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '<div class="form-group">';
    $html .= '<div class="row">';
    $html .= '<div class="col-md-3">';
    $html .= '<label class="herizon-label">'.$texts['office_phone'].'<span class="text-danger"> * </span>'.$texts['colon'].'</label>';
    $html .= '</div>';
    $html .= '<div class="col-md-7">';
    $html .= '<span class="pull-left herizon-span">+</span>';
    $html .= '<span class="pull-left col-md-3">';
    $html .= '<input type="text" class="form-control input-sm required" id="company_phone_country_code" name="company_phone[country_code]" placeholder="'.$texts['country_code'].'" required>';
    $html .= '</span>';
    $html .= '<span class="pull-left herizon-span">-</span>';
    $html .= '<span class="pull-left col-md-3">';
    $html .= '<input type="text" class="form-control input-sm required" id="company_phone_city_code" name="company_phone[city_code]" placeholder="'.$texts['city_code'].'" required>';
    $html .= '</span>';
    $html .= '<span class="pull-left herizon-span">-</span>';
    $html .= '<span class="pull-left col-md-4">';
    $html .= '<input type="text" class="form-control input-sm required" id="company_phone_number" name="company_phone[number]" placeholder="'.$texts['office_phone'].'" required>';
    $html .= '</span>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '<div class="form-group">';
    $html .= '<div class="row">';
    $html .= '<div class="col-md-3">';
    $html .= '<label class="herizon-label">'.$texts['fax'].'<span class="text-danger"> * </span>'.$texts['colon'].'</label>';
    $html .= '</div>';
    $html .= '<div class="col-md-7">';
    $html .= '<span class="pull-left herizon-span">+</span>';
    $html .= '<span class="pull-left col-md-3">';
    $html .= '<input type="text" class="form-control input-sm required" id="company_fax_country_code" name="company_fax[country_code]" placeholder="'.$texts['country_code'].'" required>';
    $html .= '</span>';
    $html .= '<span class="pull-left herizon-span">-</span>';
    $html .= '<span class="pull-left col-md-3">';
    $html .= '<input type="text" class="form-control input-sm required" id="company_fax_city_code" name="company_fax[city_code]" placeholder="'.$texts['city_code'].'" required>';
    $html .= '</span>';
    $html .= '<span class="pull-left herizon-span">-</span>';
    $html .= '<span class="pull-left col-md-4">';
    $html .= '<input type="text" class="form-control input-sm required" id="company_fax_number" name="company_fax[number]" placeholder="'.$texts['fax'].'" required>';
    $html .= '</span>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '<div class="form-group">';
    $html .= '<div class="row">';
    $html .= '<div class="col-md-3">';
    $html .= '<label class="herizon-label" for="company_website">'.$texts['website'].$texts['colon'].'</label>';
    $html .= '</div>';
    $html .= '<div class="col-md-6">';
    $html .= '<input type="text" class="form-control input-sm" id="company_website" name="company_website" placeholder="'.$texts['website'].'">';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    //联系人信息
    for ($i = 0; $i < $attendee_form_number; $i++) {
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
      $html .= '<div class="col-md-7">';
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
      $html .= '<input type="text" class="form-control input-sm required" id="attendee_'.$i.'_name_zh" name="attendee['.$i.'][name][zh]" placeholder=".'$texts['chinese'].'" onblur="on_bind('.$i.')" required>';
      $html .= '</span>';
      $html .= '</div>';
      $html .= '<div class="row">';
      $html .= '<span class="pull-left col-md-3">';
      $html .= '<label class="herizon-label" for="attendee_'.$i.'_name_en">'.$texts['english'].'</label>';
      $html .= '</span>';
      $html .= '<span class="pull-left col-md-9">';
      $html .= '<div class="row">';
      $html .= '<span class="pull-left col-md-5">';
      $html .= '<input type="text" class="form-control input-sm required" id="attendee_'.$i.'_name_en_firstname" name="attendee['.$i.'][name][en][firstname]" placeholder="'.$texts['attendee_first_name'].'" onblur="on_bind_enname('.$i.'" required>';
      $html .= '</span>';
      $html .= '<span class="pull-left herizon-span">-</span>';
      $html .= '<span class="pull-left col-md-5">';
      $html .= '<input type="text" class="form-control input-sm required" id="attendee_'.$i.'_name_en_lastname" name="attendee['.$i.'][name][en][lastname]" placeholder="'.$texts['attendee_last_name'].'" onblur="on_bind_enname('.$i.')" required>';
      $html .= '</span>';
      $html .= '</div>';
      $html .= '</span>';
      $html .= '</div>';
      $html .= '</div>';
      $html .= '<div class="form-group">';
      $html .= '<div class="row">';
      $html .= '<span class="col-md-3">';
      $html .= '<label class="herizon-label" for="attendee_'.$i.'_title">'.$texts['attendee_title'].'<span class="text-danger"> * </span>'.$texts['colon'].'</label>';
      $html .= '</span>';
      $html .= '<span class="col-md-9">';
      $html .= '<input type="text" class="form-control input-sm required" id="attendee_'.$i.'_title" name="attendee['.$i.'][title]" placeholder="'.$texts['attendee_title'].'" required>';
      $html .= '</span>';
      $html .= '</div>';
      $html .= '</div>';
      $html .= '<div class="form-group">';
      $html .= '<div class="row">';
      $html .= '<span class="col-md-3">';
      $html .= '<label class="herizen-label" for="attendee_'.$i.'_email">'.$texts['attendee_email'].'<span class="text-danger"> * </span>'.$texts['colon'].'</label>';
      $html .= '</span>';
      $html .= '<span class="col-md-9">';
      $html .= '<input type="email" class="form-control input-sm required" id="attendee_'.$i.'_email" name="attendee['.$i.'][email]" placeholder="'.$texts['attendee_email'].'" required>';
      $html .= '</span>';
      $html .= '</div>';
      $html .= '</div>';
      $html .= '<div class="form-group">';
      $html .= '<div class="row">';
      $html .= '<div class="col-md-3">';
      $html .= '<label class="herizon-label">'.$texts['attendee_telephone'].'<span class="text-danger"> * </span>'.$texts['colon'].'</label>';
      $html .= '</div>';
      $html .= '<div class="col-md-9">';
      $html .= '<span class="pull-left herizon-span">+</span>';
      $html .= '<span class="pull-left col-md-4">';
      $html .= '  <input type="text" class="form-control input-sm required" id="attendee_'.$i.'_telephone_country_code" name="attendee['.$i.'][telephone][country_code]" placeholder="'.$texts['country_code'].'" required>';
      $html .= '</span>';
      $html .= '<span class="pull-left herizon-span">-</span>';
      $html .= '<span class="pull-left col-md-7">';
      $html .= '  <input type="text" class="form-control input-sm required" id="attendee_'.$i.'_telephone_number" name="attendee['.$i.'][telephone][number]" placeholder="'.$texts['telephone_number'].'" required>';
      $html .= '</span>';
      $html .= '</div>';
      $html .= '</div>';
      $html .= '</div>';
      $html .= '<div class="form-group">';
      $html .= '<div class="row">';
      $html .= '<div class="col-md-3">';
      $html .= '<label class="herizon-label">'.$texts['attendee_phone'].'<span class="text-danger"> * </span>'.$texts['colon'].'</label>';
      $html .= '</div>';
      $html .= '<div class="col-md-9">';
      $html .= '<span class="pull-left herizon-span">+</span>';
      $html .= '<span class="pull-left col-md-3">';
      $html .= '  <input type="text" class="form-control input-sm required" id="attendee_'.$i.'_phone_country_code" name="attendee['.$i.'][phone][country_code]" placeholder="'.$texts['country_code'].'" required>';
      $html .= '</span>';
      $html .= '<span class="pull-left herizon-span">-</span>';
      $html .= '<span class="pull-left col-md-3">';
      $html .= '  <input type="text" class="form-control input-sm required" id="attendee_'.$i.'_phone_city_code" name="attendee['.$i.'][phone][city_code]" placeholder="'.$texts['city_code'].'" required>';
      $html .= '</span>';
      $html .= '<span class="pull-left herizon-span">-</span>';
      $html .= '<span class="pull-left col-md-5">';
      $html .= '  <input type="text" class="form-control input-sm required" id="attendee_'.$i.'_phone_city_code" name="attendee['.$i.'][phone][number]" placeholder="'.$texts['attendee_phone'].'" required>';
      $html .= '</span>';
      $html .= '</div>';
      $html .= '</div>';
      $html .= '</div>';
      $html .= '<div class="form-group">';
      $html .= '<label>'.$texts['attendee_select_1'].'</label>';
      $html .= '<label class="radio-inline">';
      $html .= '<input type="radio" name="attendee['.$i.'][extra_info][need_room]" value="1" checked />'.$texts['attendee_select_1_option_1'].'';
      $html .= '</label>';
      $html .= '<label class="radio-inline">';
      $html .= '<input type="radio" name="attendee['.$i.'][extra_info][need_room]" value="-1" />'.$texts['attendee_select_1_option_2'].'';
      $html .= '</label>';
      $html .= '</div>';
      $html .= '</div>';
      $html .= '<div class="col-md-5">';
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
    $html .= '<button id="cancel_book" type="button" class="btn btn-default">'.$texts['cancel_booking'].'</button>';
    $html .= '<button type="submit" class="btn btn-primary">'.$texts['submit'].'</button>';
    $html .= '</div>';
    $html .= '</form>';
    
                          
                          
                          
                            
                          
                        
                      
                    
                  
                  
                    
                      
                        
                      