<?php
END_MODULE != 'admin' && die('Access Denied');

load_modules_config();

$view_data['modules'] = $end_module;

$action = $_GET['action'];
$module = $_GET['module'];
$extension = $_GET['extension'];

if ($module)
{
	get_extensions('end_'.$module);
	$view_data['page_name'] = $end_module[$module]['name'];
}
else
{
	get_extensions();
	$view_data['page_name'] = lang('all_extension');
}


if ($action == 'edit')
{
	if ($_GET['delete'])
	{
		if ($ext = $end_extension[$_GET['delete']])
		{
			if (end_rmdir(END_ROOT.$ext['path']))
			{
				end_exit(lang('delete_success'),'admin.php?p=extension&action=edit',1);
			}
			else
			{
				end_exit(lang('delete_failed'),'admin.php?p=extension&action=edit',3);
			}
		}
	}
}
else if ($action == 'running')
{
	if ($hid = intval($_GET['pause']))
	{
		if (model('hook')->update($hid,array('status'=>'pause')))
			$view_data['info'] = lang('Success');
		else
			$view_data['info'] = lang('Failed');
	}
	if ($hid = intval($_GET['resume']))
	{
		if (model('hook')->update($hid,array('status'=>'running')))
			$view_data['info'] = lang('Success');
		else
			$view_data['info'] = lang('Failed');
	}
	if ($hid = intval($_GET['delete']))
	{
		if (model('hook')->delete($hid))
			$view_data['info'] = lang('Success');
		else
			$view_data['info'] = lang('Failed');
	}
	
	$view_data['running'] = model('hook')->get_list(array('order'=>'create_time desc'));
}

$view_data['exts'] = $end_extension;

if ($extension && $end_extension[$extension])
{
	$view_data['extension'] = $end_extension[$extension];
}

//删除目录
function end_rmdir($p)
{
	if (is_dir($p))
	{
		$h = opendir($p);
		while( ($v = readdir($h)) != false)
		{
			if ($v == '.' || $v == '..') continue;
			if (is_file($p.'/'.$v)) unlink($p.'/'.$v);
			else end_rmdir($p.'/'.$v);
		}
		rmdir($p);
	}
	return !is_dir($p);
}


function load_modules_config()
{
	global $end_module;
	$h = opendir(END_ROOT);
	while(( $v = readdir($h)) !== false)
	{
		if (!preg_match('/^end\_/i',$v)) continue;
		if (in_array($v,array('end_system','end_content'))) continue;
		$p = END_ROOT.$v;
		if (!is_dir($p)) continue;
		if (!file_exists($p.'/config.php')) continue;
		include_once($p.'/config.php');
	}
	closedir($h);
}


function get_extensions($path = false)
{
	global $end_module,$end_extension;
	if ($path !== false) $p = array($path);
	else
	{
		$p = array(
			'end_content',
			'end_admin',
			'end_system'
		);
		foreach($end_module as $name=>$attr)
		{
			$p[] = 'end_'.$name;
		}
	}
	$p = array_unique($p);
	foreach($p as $path)
	{
		$path = $path.'/extension';
		if (is_dir(END_ROOT.$path))
		{
			$h = opendir(END_ROOT.$path);
			while(($v = readdir($h))!==false)
			{
				if ($v == '.' || $v == '..') continue;
				if (is_dir(END_ROOT.$path.'/'.$v) && file_exists($config_file = END_ROOT.$path.'/'.$v.'/config.php'))
				{
					include_once($config_file);
					if (is_array($end_extension[$v]))
					{
						$end_extension[$v]['path'] = $path.'/'.$v.'/';
						if (!$end_extension[$v]['icon'])
						{
							$end_extension[$v]['icon'] = 'end_admin/view/images/default_extension_icon.png';
						}
						else
						{
							$end_extension[$v]['icon'] = $end_extension[$v]['path'].$end_extension[$v]['icon'];
						}
					}
				}
			}
		}
	}
}