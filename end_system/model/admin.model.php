<?php

class MODEL_ADMIN extends MODEL
{
	function MODEL_ADMIN()
	{
		$this->table = END_MYSQL_PREFIX.'admin';
		$this->id = 'admin_id';
		$this->order_id = 'admin_id';
	}
	
	function check_password($name,$password)
	{
		return parent::get_one( array('name'=>$name,'password'=>$password));
	}
	
	function get_array()
	{
		$arr = $this->get_list();
		$re = array();
		foreach($arr as $user)
		{
			$re[$user[$this->id]] = $user['name'];
		}
		return $re;
	}
}
