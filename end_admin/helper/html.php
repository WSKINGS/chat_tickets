<?php

/**
* 文本输入框
* 接受参数:
* [width]: 输入框宽度，数字
* [height]: 输入框高度，数字
* [disabled]: 是否禁止改动，boolean
* [null]: 是否允许为空 (默认为false)
* [description]: 输入框后的描述文字
*/
/*
以下为系统使用的输入类型描述，不是注释
ENDCMS_INPUT_TYPE{text}
{
	style:string;
	width:number;
	disabled:boolean;
	allowBlank:boolean;
	description:string;
	default_value:
}
*/
function endcms_input_text($data)
{
	$name = $data['field_name'];
	$_style = $data['style'];
	if (!$data['width']) $_style .= 'width:600px;';
	if ($data['hidden']) $_style.="display:none;";
	if ($data['disabled'])
	{
		echo "<input type='hidden' name='$name' readonly='readonly' style='$_style' value='".$data['content']."' />";
	}
	else
	{
		echo "<input type='text' name='$name' style='$_style' value='".$data['content']."' />";
	}
	echo $data['description'];
}

/*
ENDCMS_INPUT_TYPE{checkbox}
{
	style:string;
	width:number;
	disabled:boolean;
	description:string;
}
*/
function endcms_input_checkbox($data)
{
	echo "<input type='checkbox' name='".$data['field_name']."' ";
	if ($data['content']) echo " checked='checked' ";
	if ($data['disabled']) echo ' disabled="disabled" ';
	echo ' style="'.$data['style'].'" ';
	echo " />";
	echo $data['description'];
}

function endcms_input_file($data)
{
	if ($data['content'])
	{
		echo '<a href="'.$data['content'].'" target="_blank">'.lang("uploaded_file").$data['content']."</a><br />";
		echo "<input type='hidden' name='".$data['field_name']."' value='".$data['content']."' />";
	}
	echo "<input type='file' name='".$data['field_name']."' lala='a' style='";
	if ($data['width']) echo $data['size_css'];
	echo "' />";
	if (!$data['description'] && $data['type'] == 'image')
		$data['description'] = lang('upload_image_descriptioin');
	echo $data['description'];
}

function endcms_input_image($data)
{
	endcms_input_file($data);
}

function endcms_input_filelist($data)
{
	$__s = $data['content'];
	$name = $data['field_name'].'[]';
	$seperator = $data['seperator']?$data['seperator']:'|';
	$add_text = $data['add_text']?$data['add_text']:lang('Add row');
	if (is_array($__s))
		$__arr = $__s;
	else
		$__arr = explode($seperator,$__s);
	
	if ($__s && $__arr)
	{
		foreach($__arr as $__v)
		{
			echo '<div class="list-row-wrapper">';
			echo '<a href="'.$__v.'" target="_blank">'
				.lang("uploaded_file")
				.$__v
				."</a>";
			echo "<input type='hidden' name='$name' value='".$__v."' />";
			echo '<span class="del-row-bt">'.lang('Remove').'</span>';
			echo '</div>';
		}
	}
	echo '<div class="list-row-wrapper">';
	echo "<input type='file' name='$name' style='";
	if ($data['width']) echo $data['size_css'];
	echo "' />";
	echo '<span class="add-row-bt" remove="'.lang('Remove').'">'.$add_text.'</span>';
	echo "</div>";
	if (!$data['description'] && $data['type'] == 'imagelist')
		$data['description'] = lang('upload_image_descriptioin');
}

function endcms_input_imagelist($data)
{
	endcms_input_filelist($data);
}

function endcms_input_textarea($data)
{
	$_style = $data['style'];
	if (!$data['height']) $_style .= 'height:500px;';
	if (!$data['width']) $_style .= 'width:600px;';
	if ($data['description']) echo $data['description'].'<br>';
	echo "<textarea name='".$data['field_name']."' style='$_style'>";
	$data['content'] = str_replace(array("\n","\r"),array("",""),$data['content']);
 	$data['content'] = str_replace(array('<br>','&nbsp;'),array("\n",' '),$data['content']);
	echo htmlspecialchars($data['content']);
	echo "</textarea>";
}

function endcms_input_select($data)
{
	echo "<select name='".$data['field_name']."' style='".$data['style']."'>";
	foreach($data['options'] as $_val=>$_name)
	{
		echo "<option ";
		if ($data['content'] == $_val) echo " selected='selected' ";
		echo " value='$_val'>$_name</option>";
	}
	echo "</select>";
}

function endcms_input_datetime($data)
{
	$name = $data['field_name'];
	$_style = $data['style'];
	echo "<input type='text' name='$name' style='$_style' value='".date('Y-m-d H:i:s',$data['content'])."' />";
	if (!$data['description']) $data['description'] = lang('eg').'.'.date('Y-m-d H:i:s');
	echo $data['description'];
}

function endcms_input_teacher_role($data)
{
	if($role_list = model('user_role')->get_list(array()))
	{
	$name = $data['field_name'];
	echo "<select name='".$name."' style='".$data['style']."'>";
	
		foreach($role_list as $tr)
		{
			if($data['content']==$tr['user_role_id'])
			{
				echo "<option value=".$tr['user_role_id']." selected>".$tr['name']."</option>";
			}
			else
			{
				echo "<option value=".$tr['user_role_id'].">".$tr['name']."</option>";
			}
		}
	}
	echo "</select>";
}

function endcms_input_select_user_right($data)
{
	$name = $data['field_name'];
	echo "<input type='hidden' name='$name' value='".$data['content']."' />";
	if($role_list = model('user_right')->get_list(array()))
	{
		foreach($role_list as $rl)
		{
			echo "<input type=checkbox class='select_to_string' id='user_right_".$rl['user_right_id']."' data-name='".$rl['name']."'>";
			echo "<label for='user_right_".$rl['user_right_id']."'>".$rl['name']."</label>";
		}
	}
	echo "&nbsp;&nbsp;&nbsp;&nbsp;".$data['description'];
}

function endcms_input_date($data)
{
	$name = $data['field_name'];
	$_style = $data['style'];
	echo "<input type='text' name='$name' style='$_style' value='".$data['content']."' />";
	if (!$data['description']) $data['description'] = lang('eg. 2010-11-23');
	echo $data['description'];
}

function endcms_input_richtext($data)
{
	$_style = $data['style'];
	if (!$data['height']) $_style .= 'height:500px;';
	if (!$data['width']) $_style .= 'width:700px;';
	
	echo "<textarea style='$_style' name='".$data['field_name']."' id='editor_".$data['field_name']."' rich='true'>";
	echo htmlspecialchars($data['content']);
	echo "</textarea>";
	echo "</td></tr><tr><Td>".lang("Upload Attachment")."</td><td>";
	echo '<iframe src="admin.php?p=upload&for='.$data['field_name'].'" style="width:700px;height:30px;border:0;text-align:left;" border="0" frameborder="no" scrolling="no"></iframe>';
}