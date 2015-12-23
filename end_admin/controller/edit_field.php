<?php
/*
传入 $_fields

输出 $data , $errors

*/
if (!is_array($_fields)) $fields = array();

foreach($_fields as $name=>$attr)
{
	//过滤掉开始为__的字段
	if (preg_match('/^__/',$name)) continue;
	//默认输入类型为text
	if (!$attr['type']) $attr['type'] = 'text';
	
	//处理文件或者图片上传
	if ($attr['type'] == 'file' || $attr['type'] == 'image')
	{
		//如果选择了文件
		if ($_FILES[$name] && $_FILES[$name]['tmp_name'])
		{
			$data[$name] = end_upload_file($name,$_FILES[$name],$attr);
		}
		//如果本来就有值，表示没有修改
		else if ($_POST[$name])
		{
			$data[$name] = $_POST[$name];
		}
		else if (!$attr['null'])
		{
			$errors[$name] = lang('is_empty');
		}
	}
	elseif ($attr['type'] == 'filelist' || $attr['type'] == 'imagelist')
	{
		$data[$name] = array();
		$seperator = $data['seperator']?$data['seperator']:'|';
		
		if ($_POST[$name] && is_array($_POST[$name]))
		{
			foreach($_POST[$name] as $_f)
			{
				$data[$name][] = $_f;
			}
		}
		
		if ($_FILES[$name] && is_array($_FILES[$name]['tmp_name']))
		{
			for($i=0;$i<count($_FILES[$name]['tmp_name']);$i++)
			{
				if (trim($_FILES[$name]['tmp_name'][$i]) == "") continue;
				$file = array(
					'name'=>$_FILES[$name]['name'][$i],
					'tmp_name'=>$_FILES[$name]['tmp_name'][$i],
					'type'=>$_FILES[$name]['type'][$i],
					'error'=>$_FILES[$name]['error'][$i],
					'size'=>$_FILES[$name]['size'][$i]
				);
				$data[$name][] = end_upload_file($name,$file,$attr);
			}
		}
		
		$data[$name] = join($seperator,$data[$name]);
		
		if (!$attr['null'] && !$data[$name])
		{
			$errors[$name] = lang('is_empty');
		}
	}
	else if ($attr['type'] == 'datetime')
	{
		$t = $_POST[$name];
		if (is_array($t))
		{
			$data[$name] = mktime($t['h'],$t['i'],$t['s'],$t['m'],$t['d'],$t['y']);
		}
		else
		{
			## by liudan
			#$data[$name] = 0; 
			$data[$name] = strtotime($t);
		}
	}
	//其他类型数据
	else
	{
		$data[$name] = $_POST[$name];
		if ($attr['filter']) $data[$name] = $attr['filter']($data[$name]);
		if ($attr['type'] == 'checkbox') $data[$name] = $data[$name]?'1':'0';
		if ($attr['type'] == 'textarea')
		{
			$data[$name] = str_replace("\r",'',$data[$name]);
			$data[$name] = str_replace(array("\n"," "),array('<br>','&nbsp;'),$data[$name]);
		}
		if ($attr['type'] == 'datetime')
		{
			$data[$name] = strtotime($data[$name]);
		}
		if (!$attr['null'] && !$data[$name] && $attr['type'] != 'checkbox') 
		{
			$errors[$name] = lang('is_empty');
		}
	}
}//end of foreach


function end_upload_file($name,$file,$attr)
{
	global $errors,$config;
	$ftype = getext($file['name']);
	 
	//echo $ftype;
	//var_dump($config);  var_dump($attr); die();
	//如果是图片，那么只能上传这几种文件类型
	if ($attr['type'] == 'image' || $attr['type'] == 'imagelist')
		$attr['filetype'] = array('jpg','jpeg','png','gif');
	
	//验证文件类型
	if (!$config['upload_file_types'])
	{
		$errors[$name] = lang('need_config_upload_file_types');
	}
	else if (!$attr['filetype'])
	{
		$errors[$name] = lang('file_type_not_configed');
	}
	else if (
		!preg_match("/\*\.$ftype;/i",$config['upload_file_types'])
		#||
		&& # by liudan 
		!in_array($ftype,$attr['filetype']) )
	{
		$errors[$name] = lang('not_allowed_file_type');
	}
	else
	{
		$file_url = $file['name'];
		//如果文件名是一般的字母数字和-_，则不改变文件名
		if (preg_match('/^[a-z0-9\_\-\s\.]+$/i',$file_url))
			$file_url = preg_replace('/\s+/','_',$file_url);
		else //否则改成时间和随机数
			$file_url = date('Y_m_d_H_i_s_').rand(1111,9999).'.'.$ftype;

		if (!$file_url) 
		{
			$errors[$name] = 'error';
		}
		
		//保存到什么地方
		if (!$attr['saveto'])
		{
			end_mkdir(END_ROOT.END_UPLOAD_DIR);
			$file_url = END_UPLOAD_DIR.$file_url;
		}
		else
		{
			end_mkdir(END_ROOT.$attr['saveto']);
			$file_url = $attr['saveto'].$file_url;
		}
		
		//避免重名
		while (file_exists(END_ROOT.$file_url))
		{
			$file_url = dirname($file_url).'/'.preg_replace('/\.[a-z0-9]+$/i','',basename($file_url)).rand(1111,9999).'.'.$ftype;
		}
		
		//保存文件
		if (@move_uploaded_file($file["tmp_name"],END_ROOT.$file_url))
		{

			if ($attr['filter']) $file_url  = $attr['filter']($file_url);
			//$data[$name] = $file_url;
			//更改图片尺寸
			if ($attr['type'] == 'image' && is_array($attr['resize']))
			{
				foreach($attr['resize'] as $_r)
				{
					if (is_array($_r) && $_r['width'] && $_r['height'])
					{
						//调整图片尺寸，保存为
						$__re = thumb($file_url,$_r['width'],$_r['height']); 
						if ($_r['saveas'])
						{
							$data[$_r['saveas']] = $__re;
						}
					}
				}
			}
			
			if (($attr['type'] == 'image' || $attr['type'] == 'imagelist') && $attr['max_width'])
			{
				include_once(END_ROOT.'end_system/library/image.php');
				$img = new Image;
				$img->filepath = END_ROOT.$file_url;
				$img->resize_width($attr['max_width']);
			}
		}
		
		if (!file_exists(END_ROOT.$file_url))
		{
			$errors[$name] = lang('upload_error');
		}
		
		if (!is_writable(END_ROOT.dirname($file_url)))
		{
			$errors[$name] = dirname($file_url).' '.lang('is not writable');
		}
		return $file_url;
	}
}