<?php
/**
 * attendee model class
 *
 * @author lidongxu @ 2014-5-26
 */

class MODEL_WT_ATTENDEE extends MODEL
{
	function MODEL_WT_ATTENDEE()
	{
		$this->table = END_MYSQL_PREFIX.'wt_attendee_data';
		$this->id = 'attendee_id';
	}

	function add($data=array())
	{
		$data['create_time'] = time();
		$data['update_time'] = $data['create_time'];
		return parent::add($data);
	}

	function update($id, $data = array())
	{
		
		$data['update_time'] = time();
		return parent::update($id, $data);
	}
}