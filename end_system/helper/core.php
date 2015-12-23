<?php


/*
end_mail( to email address, subject,body[,reply to email address, reply to name ])
*/
function end_mail($to,$subject,$body,$replyto='',$replyto_name='')
{
	if (file_exists(END_SYSTEM_DIR.'config/smtp.config.php'))
		require(END_SYSTEM_DIR.'config/smtp.config.php');
	else
		$config = array('use_smtp' => false);
		
	
	include_once(END_SYSTEM_DIR.'plugin/phpmailer/class.phpmailer.php');
	try {
		$mail = new PHPMailer(true); //New instance, with exceptions enabled
		$body = preg_replace('/\\\\/','', $body); //Strip backslashes
		
		
		if ($config['use_smtp']) 
		{
			$mail->IsSMTP();
			$mail->SMTPDebug = false;
			$mail->CharSet = 'utf-8';
			$mail->SMTPAuth   = true; 
			if ($config['smtp_port']) $mail->Port  = $config['smtp_port']; 
			if ($config['smtp_secure']) $mail->SMTPSecure = $config['smtp_secure'];
			if ($config['smtp_host']) $mail->Host = $config['smtp_host'];
			if ($config['smtp_username']) $mail->Username  = $config['smtp_username'];
			if ($config['smtp_password']) $mail->Password  = $config['smtp_password'];
		}
		else
		{
			$mail->IsMail();
		}
		$mail->From = $config['smtp_fullemail'];
		$mail->FromName = $config['smtp_fullname'];
		!$replyto && $replyto = $config['smtp_fullemail'];
		!$replyto_name && $replyto_name = $cofnig['smtp_fullname'];
		$mail->AddReplyTo($replyto,$replyto_name);
		$mail->AddAddress($to);
		$mail->Subject  = $subject;
		$mail->WordWrap   = 80; // set word wrap
		$mail->MsgHTML($body);
		$mail->IsHTML(true); // send as HTML
		$mail->Send();
		return true;
	} catch (phpmailerException $e) {
		echo $e->errorMessage();
		return false;
	}
}


/**
 * 载入一个model
 *
 * @param string $f 
 * @return model class
 */
function model($f,$path = false)
{
	//缓存
	if (isset($GLOBALS['end_model_instance_'.$f])) return $GLOBALS['end_model_instance_'.$f];

	if ($path && file_exists($_file = $path.'/'.$f.'.model.php'))
		include_once($_file);
	else if (file_exists($_file = END_MODULE_DIR.'model/'.$f.'/'.$f.'.model.php'))
		include_once($_file);
	else if (file_exists($_file = END_SYSTEM_DIR.'model/'.$f.'.model.php'))
		include_once($_file);
	else if (file_exists($_file = END_ROOT.'end_www/model/'.$f.'/'.$f.'.model.php'))
		include_once($_file);
	else
		die("load model error! Model file not found: $f.model.php");
	
	if (class_exists($_class_name = 'MODEL_'.strtoupper($f)))
		return $GLOBALS['end_model_instance_'.$f] = new $_class_name;
	else
		die("Load model error! Class '$_class_name' not found in file $_file");
}

function simple_model($table,$id = false)
{
	$obj = isset($GLOBALS['end_model_instance_simple_model'])?$GLOBALS['end_model_instance_simple_model']:new MODEL;
	$GLOBALS['end_model_instance_simple_model'] = $obj;
	$obj->table = END_MYSQL_PREFIX.$table;
	if ($id) $id = $table.'_id';
	$obj->id = $id;
	$obj->sort_id = NULL;
	return $obj;
}

function helper($f,$mute = false)
{
	$loaded = false;
	if (file_exists($_file = END_MODULE_DIR.'helper/'.$f.'.php'))
	{
		include_once($_file);
		$loaded = true;
	}
	if (file_exists($_file = END_SYSTEM_DIR.'helper/'.$f.'.php'))
	{
		include_once($_file);
		$loaded = true;
	}
	if (!$loaded && !$mute) echo "<span style='color:red'>load helper error! File not found: $f.php</span>";
}


/**
 * 创建一个模板对象
 *
 * @param string $f 
 * @param string [$viewdir]
 * @return template class
 */
function template($f,$viewdir = false)
{
	include_once(END_SYSTEM_DIR.'library/endskin.php');
	if (!$viewdir) $viewdir = END_VIEWER_DIR;
	$_template = new EndSkin($viewdir,END_SYSTEM_DIR.'cache/template/');
	$_template->compile_hook = 'endskin_replace_language';
	$_template->default_template = $f;
	return $_template;
}

function endskin_replace_language($page)
{
	preg_match_all('/\{([A-Z\_][A-Z\_0-9]+)\}/',$page,$ms);
	foreach($ms[1] as $i=>$v)
	{
		$page = str_replace($ms[0][$i],lang($v),$page);
	}
	return $page;
}

/**
 * 非对称加密
 *
 * @param string $s 
 * @return string encoded string
 */
function end_encode($s)
{
	$s = md5($s).$s.'something very very very very very very very very long';
	return sha1($s);
}


/**
 *过滤数组
 *	input example: md5:key1,key2,base64_encode:key3!,intval:key4
 *	! represents must, if it equals null then return false
 *	function_name:key, handle key to function_name
 *	key2=key1, rename key1 to key2
 *	
 * 	e.g. id=intval:item_id!
*/
function filter_array($arr,$keys,$write_global = false)
{
	$re = array();
	$key_arr = explode(',',$keys);
	foreach($key_arr as $key)
	{
		$key = trim($key);
		if (!$key) continue;
		$_must = false;
		$_func = false;
		$_key = false;
		if (strpos($key,'=') !== false)
		{
			$_arr = explode('=',$key);
			$_key = $_arr[0];
			$key = $_arr[1];
		}
		if (strpos($key,'!') !== false)
		{
			$_must = true;
			$key = str_replace('!','',$key);
		}
		if (strpos($key,':') !== false)
		{
			$_arr = explode(':',$key);
			$_func = $_arr[0];
			$key = $_arr[1];
		}
		!$_key && $_key = $key;
		if ($_func)
			$arr[$key] = $_func($arr[$key]);
		if ($_must && !$arr[$key]) 
			return false;
		else
			$re[$_key] = isset($arr[$key])?$arr[$key]:NULL;
	}
	if ($write_global)
	{
		foreach($re as $key => $val)
		{
			$GLOBALS[$key] = $val;
		}
	}
	return $re;
}

/**
 * 载入一个语言文件
 *
 * @param string $path 
 * @return void
 */
function language($path)
{
	if (!END_ENABLE_LANGUAGE) return;
	if (strpos($path,'.') === false) $path.= '.lang';
	$_f = END_LANGUAGE_DIR.END_LANGUAGE.'/'.$path;

	if (file_exists($_f))
	{
		$lines = file($_f);
		foreach($lines as $line)
		{
			$line = str_replace(array("\r","\n"),'',$line);
			if (preg_match('/^([a-zA-Z]{1}[a-zA-Z0-9_]*)\s*=(.*)$/',$line,$ms))
				define('LANG_'.strtoupper($ms[1]),$ms[2]);
		}
	}
	else
	{
		//die('language file not found:'.$_f);
	}
}

/**
 * 获得一个语言字符串
 *
 * @param string $key 
 * @return string language string
 */
function lang($key)
{
	$name = 'LANG_'.strtoupper($key);
	$name = preg_replace('/\s+/','_',$name);
	if (defined($name))
		return constant($name);
	else
	{
		//here do some thing log
		return $key;
	}
}



function end_gzip($s)
{ 
	if( !headers_sent() && extension_loaded("zlib") && strstr($_SERVER["HTTP_ACCEPT_ENCODING"],"gzip"))
	{
		$s = gzencode($s,9);
		header("Content-Encoding: gzip"); 
		header("Vary: Accept-Encoding");
		header("Content-Length: ".strlen($s));
	}
	return $s;
}


function end_add_hook($module,$hook,$func_file,$func_name,$settings,$create_by = '',$title = '',$status='running')
{
	return model('hook')->add(array(
		'module'=>$module,
		'hook'=>$hook,
		'func_file'=>$func_file,
		'func_name'=>$func_name,
		'settings'=>$settings,
		'create_time'=>time(),
		'create_by'=>$create_by,
		'title'=>$title,
		'status'=>$status,
	));
}