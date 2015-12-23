<?php
/**
 * activity model class
 *
 * @author lidongxu
 */

class MODEL_WT_ACTIVITY extends MODEL
{
	function MODEL_WT_ACTIVITY()
	{
		$this->table = END_MYSQL_PREFIX.'wt_activity_data';
		$this->id = 'activity_id';
	}

	function add($data=array())
	{
		$data['create_time'] = time();
		return parent::add($data);
	}
}