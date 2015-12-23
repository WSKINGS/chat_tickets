<?php

/*
find a image url from a text
*/
function get_first_image_url($s,$min_width = 150,$min_height = 80)
{
	preg_match_all('/<img[^>]*src=[\'\"]([^\'\"]{1,})[\'\"]/i',$s,$ms);
	foreach($ms[1] as $img)
	{
		//$info = getimagesize($img);
		//if ($info && $info[0] >= $min_width && $info[1] >= $min_height) return $img;
		if (preg_match('/\.(jpg|jpeg)$/i',$img)) return $img;
	}
	return false;
}

function show_description_text($s,$len)
{
	$s = strip_tags($s);
	$s = preg_replace('/&nbsp;/',' ',$s);
	$s = preg_replace('/\s{2,}/','',$s);
	$_s = cn_substr($s,0,$len);
	return ($s == $_s)?$s:$_s.'...';
}

function html_pager($url,$total_page,$page = 1)
{
	
	$page_span = 4;
	$pager = '';
	$sep=(preg_match('/\?/',$url))?'&':'?';
	// if ($page>1)
	// 	$pager .= ' <a href="'.$url.$sep.'page=1">'.LANG_PAGER_FIRST.'</a> ';
	// else
	// 	$pager .= ' <a href="javascript:;" class="grey">'.LANG_PAGER_FIRST.'</a> ';
	if ($page>1)
		$pager.= ' <a href="'.$url.$sep.'page='.($page-1).'">'.LANG_PAGER_PREV.'</a> ';
	else
		$pager.= ' <a href="javascript:;" class="grey">'.LANG_PAGER_PREV.'</a> ';
	
	$numbers = '';
	if ($page>$page_span)
		$numbers.= ' <a href="'.$url.'">1</a>';
	if ($page>$page_span+1)
		$numbers.= '...';
	
	for($i=$page-$page_span+1;$i<$page+$page_span;$i++)
	{
		if ($i<=0 || $i>$total_page) continue;
		if ($page == $i)
			$numbers.= " [{$i}] ";
		else
			$numbers.= ' <a href="'.$url.$sep.'page='.$i.'">'.$i.'</a> ';
	}
	
	if ($total_page-$page>$page_span)
		$numbers.= '...';
	if ($total_page-$page>$page_span-1) 
		$numbers.= '<a href="'.$url.$sep.'page='.$total_page.'">'.$total_page.'</a> ';

	$GLOBALS['END_PAGER_NUMBERS'] = $numbers;
	$pager.= $numbers;
	
	if ($page<$total_page)
		$pager.= ' <a href="'.$url.$sep.'page='.($page+1).'">'.LANG_PAGER_NEXT.'</a> ';
	else
		$pager .= ' <a href="javascript:;" class="grey">'.LANG_PAGER_NEXT.'</a> ';
	// if ($page<$total_page)
	// 	$pager .= ' <a href="'.$url.$sep.'page='.$total_page.'">'.LANG_PAGER_LAST.'</a> ';
	// else
	// 	$pager .= ' <a href="javascript:;" class="grey">'.LANG_PAGER_LAST.'</a> ';
	return $pager;
}


function end_page($obj,$cond = array(),$per_page)
{
	global $view_data;
	$orig_select = $cond['select'] ? $cond['select'] : '*';
	$cond['select'] = 'count(1)';
	$total = $obj->get_list( $cond );
	$total = $total[0]['count(1)'];
	$page = isset($_GET['page'])?intval($_GET['page']):0;
	$per_page <= 0 && $per_page = 20;
	!$page && $page=1;
	$total_page = ceil($total/$per_page);
	!$total_page && $total_page=1;
	$page>$total_page && $page = $total_page;
	$cond['select'] = $orig_select;
	$cond['from'] = ($page-1)*$per_page;
	$cond['total'] = $per_page;
	$pager = LANG_PAGER_TOTAL.$total.LANG_PAGER_ITEMS.'<br />';
	$GLOBALS['END_PAGER_ITEM_TOTAL'] = $total;
	$GLOBALS['END_PAGER_PAGE_TOTAL'] = $total_page;
	
	$url = $_SERVER['REQUEST_URI'];
	$url = preg_replace('/\??&?page=[0-9]{1,}/','',$url);
	$pager.=html_pager($url,$total_page,$page);
	$view_data['pager'] = $pager;
	$sep=(preg_match('/\?/',$url))?'&':'?';
	$view_data['older_entries'] = ($page == $total_page)?'':"<a href='{$url}{$sep}page=".($page+1)."'>".LANG_OLDER_ENTRIES."</a>";
	$view_data['newer_entries'] = ($page == 1)?'':"<a href='{$url}{$sep}page=".($page-1)."'>".LANG_NEWER_ENTRIES."</a>";
	$GLOBALS['END_PAGER_PAGER'] = $pager;
	$GLOBALS['END_PAGER_OLDER'] = $view_data['older_entries'];
	$GLOBALS['END_PAGER_NEWER'] = $view_data['newer_entries'];
	return $obj->get_list( $cond );
}

function pager_next($s = "older entries")
{
	return str_replace(LANG_OLDER_ENTRIES,$s,$GLOBALS['END_PAGER_OLDER']);
}
function pager_prev($s = "newer entries")
{
	return str_replace(LANG_NEWER_ENTRIES,$s,$GLOBALS['END_PAGER_NEWER']);
}
function pager_numbers()
{
	if ($GLOBALS['END_PAGER_PAGE_TOTAL'] > 1)
		return $GLOBALS['END_PAGER_NUMBERS'];
	else 
		return '';
}

/**
* make empty folder only
* params: $path 
*/
function end_mkdir($path)
{
	$arr = explode('/',$path);
	for($i=0;$i<=count($arr);$i++)
	{
		$p.= $arr[$i].'/';
		if (!is_dir($p)) mkdir($p);
	}
	return $p;
}



/*
获得过多少天aa
*/
function get_past_day($t)
{
	$yeargap = date('Y')  - date('Y',$t);
	$thisyear = ($yeargap == 0);
	$monthgap = date('m') - date('m',$t);
	$thismonth = $thisyear?($monthgap == 0):false;
	$daygap = date('z') - date('z',$t);
	$thisday = $thismonth?($daygap == 0):false;
	
	if ($thisday) return '今天';
	
	if ($thismonth)
	{
		if ($daygap == 1) return '昨天';
		if ($daygap == 2) return '前天';
	}
	
	if ($thisyear)
	{
		if ($daygap < 20) return $daygap.'天前';
		
		if ($thismonth) return '本月'.date('d',$t).'号';
		if ($monthgap == 1) return '上月'.date('d',$t).'号';
		
		return date('m月d日',$t);
	}
	
	if ($yeargap == 1) return '去年'.date('m月d日',$t);
	if ($yeargap == 2) return '前年'.date('m月d日',$t);
	
	return date('Y年m月d号',$t);
}


/*
send download header and output filedata
*/
function download_file($filepath, $filename = '')
{
	if(!$filename) $filename = basename($filepath);
	if(is_ie()) $filename = rawurlencode($filename);
	$filetype = strtolower(trim(substr(strrchr($filename, '.'), 1, 10)));
	$filesize = sprintf("%u", filesize($filepath));
	if(@ob_get_length() !== false) @ob_end_clean();
	header('Pragma: public');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s') . ' GMT');
	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Cache-Control: pre-check=0, post-check=0, max-age=0');
	header('Content-Transfer-Encoding: binary');
	header('Content-Encoding: none');
	header('Content-type: '.$filetype);
	header('Content-Disposition: attachment; filename="'.$filename.'"');
	header('Content-length: '.$filesize);
	readfile($filepath);
	exit;
}

function print_space($length = 1)
{
	$re = '';
	for($i=0;$i<$length;$i++) $re.='&nbsp;&nbsp;';
	return $re;
}

function print_category_tree($arr,$category_id=0,$excluded=array(),$depth=0)
{
	$re = '';
	if (!is_array($arr)) return;
	if ($excluded && !is_array($excluded)) $excluded = array($excluded);
	if ($depth > 100) return;
	foreach($arr as $c)
	{
		if ($c['category_id'] && in_array($c['category_id'],$excluded)) continue;
		if ($c['status'] == 'folder' && END_CONTROLLER != 'category')
		{
			$re.="<optgroup label='".print_space($depth).$c['name']."'>";
			$re.=print_category_tree($c['children'],$category_id,$excluded,$depth+1);
			$re.="</optgroup>";
		}
		else
		{
			$re.= "<option status='".$c['status']."' value='".$c['category_id']."' ";
			if ($category_id && $c['category_id'] == $category_id) $re.= " selected='selected' ";
			$re.=">".print_space($depth).$c['name']."</option>\n";
			$re.=print_category_tree($c['children'],$category_id,$excluded,$depth+1);
		}
	}
	return $re;
}

function print_category_tree_link($url,$arr,$category_id=0,$depth=0)
{
	$re = '<ul>';
	if (!is_array($arr) || count($arr) == 0) return '';
	foreach($arr as $c)
	{
		$re.= "<li><a title='".$c['description']."' href='$url".$c['category_id']."' class='status_".$c['status'];
		if ($category_id && $c['category_id'] == $category_id) $re.= " tree_on";
		$re .="'>".$c['name']."</a></li>\n";
		$_re = print_category_tree_link($url,$c['children'],$category_id,$depth+1);
		$_re && $re .= '<li style="display:none">'.$_re.'</li>';
	}
	$re.='</ul>';
	return $re;
}

function en_substr($s,$end)
{
	if (strlen($s)<= $end) return $s;
	if (substr($str,$end,1) !=' ')
	{
		for($i=1;$i<20;$i++)
		{
			if (substr($s,$end+$i,1) == ' ') break;
		}
		$end+=$i;
	}
	return substr($s,0,$end).'...';
}

/*
get a substring of UTF-8 words
*/
function cn_substr($str, $start, $len,$dotted='')
{
	$str = htmlspecialchars_decode(strip_tags($str));
	$str = str_replace('&nbsp;',' ',$str);
	$str = preg_replace('/\s{2,}/',' ',$str);

	$tmpstr = "";
	$strlen = strlen($str);
	$cnt = 0;
	$istr = '';
	for($i = 0; $i < $strlen; $i++) 
	{
        if(ord(substr($str, $i, 1)) > 127) 
		{
            $istr = substr($str, $i, 3);
            $i+=2;
        }
		else if (ord(substr($str, $i+1, 1)) <= 127)
		{
            $istr = substr($str, $i, 2);
			$i+=1;
		}
		else
		{
			$istr = substr($str, $i, 1);
		}
		if ( $cnt >= $start && $cnt < $start+$len)
		{
			$tmpstr .= $istr;
		}
		$cnt++;
    }
    $re = ($str == $tmpstr)?$tmpstr:$tmpstr.$dotted;
	return htmlspecialchars($re);
}


/*
get client ip address
*/
function ip()
{
	if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown'))
	{
		$ip = getenv('HTTP_CLIENT_IP');
	}
	elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown'))
	{
		$ip = getenv('HTTP_X_FORWARDED_FOR');
	}
	elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown'))
	{
		$ip = getenv('REMOTE_ADDR');
	}
	elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown'))
	{
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	return preg_match("/[\d\.]{7,15}/", $ip, $matches) ? $matches[0] : 'unknown';
}
