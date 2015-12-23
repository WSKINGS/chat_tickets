<?php
/**
 * order model class
 *
 * @author lidongxu @ 2014-5-23
 */

class MODEL_WT_ORDER extends MODEL
{
	function MODEL_WT_ORDER()
	{
		$this->table = END_MYSQL_PREFIX.'wt_order_data';
		$this->id = 'order_id';
	}

	function add($data=array())
	{
		$data['create_time'] = time();
		$data['update_time'] = $data['create_time'];
		return parent::add($data);
	}

	function update($id, $data= array()) 
	{
		$data['update_time'] = time();
		return parent::update($id, $data);
	}
}