<?php
class MODEL_CONFIG extends MODEL
{
	function MODEL_CONFIG()
	{
		$this->table = END_MYSQL_PREFIX.'config';
		$this->id = 'config_id';
		$this->order_id = 'order_id';
	}
	
	function get($name)
	{
		return parent::get_one( array('name'=>$name));
	}

	function get_one($id)
	{
		return parent::get_one($id);
	}

	function get_list($data = array())
	{
		!$data['order'] && $data['order'] = "order_id DESC,$this->id ASC";
		return parent::get_list($data);
	}
}
