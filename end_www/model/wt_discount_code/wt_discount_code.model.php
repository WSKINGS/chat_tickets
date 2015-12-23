<?php
/**
 * discount code model class
 *
 * @author lidongxu
 */

class MODEL_WT_DISCOUNT_CODE extends MODEL
{
	function MODEL_WT_DISCOUNT_CODE()
	{
		$this->table = END_MYSQL_PREFIX.'wt_discount_code_data';
		$this->id = 'code_id';
	}

	function add($data=array())
	{
		$data['create_time'] = time();
		return parent::add($data);
	}
}