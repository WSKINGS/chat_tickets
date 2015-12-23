<?php
class MODEL_CATEGORY extends MODEL
{
	var $categories_list;
	function MODEL_CATEGORY()
	{
		$this->table = END_MYSQL_PREFIX.'category';
		$this->order_id = 'order_id';
		$this->id = 'category_id';
	}

	function get_cats($a)
	{
		global $db;
		$_key = (is_numeric($s))?$this->id:'url';
		return $db->get_all("SELECT * FROM `$this->table` WHERE `parent_id`=(SELECT `$this->id` FROM `$this->table` WHERE `$_key`='$a') ORDER BY `$this->order_id` DESC,`$this->id` ASC");
	}

	function getbyurl($a)
	{
		return parent::get_one(array('url'=>$a));
	}

	function delete($id)
	{
		check_allowed_category($id,END_RESPONSE == 'text');
		$cat = $this->get_one($id);
		if ($cat['system'] == 'yes' && END_DEBUG == false) return false;
		return parent::delete($id);
	}
	
	function add($data=array())
	{
		if ($data['parent_id'])
		{
			check_allowed_category($data['parent_id'],END_RESPONSE == 'text');
		}
		
		if (!$data['url']) $data['url'] =  $data['name']?$data['name']:date('Y-m-d-H-i-s');
		$data['url'] = $this->unique_url($data['url']);
		
		if (!$data['create_time']) $data['create_time'] = time();
		$re = parent::add($data);
		return $re;
	}

	function update($id,$data=array())
	{
		check_allowed_category($id,END_RESPONSE == 'text');
		if (!$data['update_time']) $data['update_time'] = time();
		if (isset($data['url']))
		{
			$data['url'] = $this->unique_url($data['url'],$id);
		}
		
		$re = parent::update($id,$data);
		if ($re && $data['url'] && defined('MAKE_HTML') && MAKE_HTML)
		{
			end_mkdir(END_ROOT.$data['url']);
		}
		
		return $re;
	}

	function position_category($id)
	{
		$cond = is_array($id)?$id:array($this->id=>$id); 
		$re = array();
		while($cond[$this->id])
		{
			$arr = $this->get_one($cond);
			if (!$arr || (is_array($arr) && count($arr)==0)) break;
			$id = $arr['category_id'];
			if (END_MODULE == 'admin' && $_SESSION['login_user']['rights']['limit_category_id'] && $_SESSION['login_user']['allowed_categories'])
			{
				if (strpos(','.$_SESSION['login_user']['allowed_categories'].',',','.$id.',') !== false)
					$re[] = $arr;
			}
			else
				$re[] = $arr;
			$cond[$this->id] = $arr['parent_id'];
		}
		$re = array_reverse($re);
		return $re;
	}
	
	function tree_category($data=0)
	{
		if (!is_array($data)) $data = array('parent_id'=>intval($data));
		if (!$data['parent_id']) $data['parent_id'] = 0;
		$parent_id = intval($data['parent_id']);
		unset($data['parent_id']);
		if ($data['status'])
		{
			if (!$data['where']) $data['where'] = ' 1=1 ';
			//$data['where'] .= " AND (status='".$data['status']."' OR status NOT LIKE '%_list') ";
			unset($data['status']);
		}
		$_all = $this->get_list($data);
		$all_categories = array();
		foreach($_all as $_cat)
		{
			$all_categories[$_cat['category_id']] = $_cat;
		}
		unset($_all);
		return $this->_tree_category($all_categories,$parent_id);
	}
	
	function _tree_category($all_categories,$parent_id,$depth = 0)
	{
		$re = array();
		foreach($all_categories as $cat)
		{
			if ($cat['parent_id'] == $parent_id)
			{
				$re[$cat['category_id']] = $cat;
				$re[$cat['category_id']]['depth'] = $depth;
				$re[$cat['category_id']]['children'] = $this->_tree_category($all_categories,$cat['category_id'],$depth+1);
			}
		}
		return $re;
	}

	function flat_tree($tree,&$re = array())
	{
		foreach($tree as $c)
		{
			$re[] = $c;
			$re[count($re)-1]['children'] = null;
			if (count($c['children'])>0) $this->flat_tree($c['children'],$re);
		}
	}
	
	function get_list($data=array())
	{
		if (END_MODULE == 'admin' && $_SESSION['login_user']['rights']['limit_category_id'] && $_SESSION['login_user']['allowed_categories'])
		{
			$data['where'] && $data['where'] .= ' AND ';
			$data['where'] .= 'category_id IN ('.$_SESSION['login_user']['allowed_categories'].')';
		}
		if (!isset($data['order'])) $data['order'] = 'order_id DESC,category_id ASC';
		return parent::get_list($data);
	}
	
	function unique_url($url,$id=0)
	{
		if (!$url) $url = date('Y-m-d-H-i-s');
		$_i = 2;
		$_url = $url;
		while($this->exists(array('url'=>$url,'where'=>$this->id.'!='.$id)))
		{
			$url = $_url.$_i;
			$_i++;
		}
		return $url;
	}
}
?>