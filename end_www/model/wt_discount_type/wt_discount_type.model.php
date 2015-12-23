<?php
/**
 * discount type model class
 *
 * @author lidongxu @ 2014.5.22
 */

class MODEL_WT_DISCOUNT_TYPE extends MODEL
{
	function MODEL_WT_DISCOUNT_TYPE()
	{
		$this->table = END_MYSQL_PREFIX.'wt_discount_type_data';
		$this->id = 'type_id';
	}

	function add($data=array())
	{
		$data['create_time'] = time();
		return parent::add($data);
	}
}