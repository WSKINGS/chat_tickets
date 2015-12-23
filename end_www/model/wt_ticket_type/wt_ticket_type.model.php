<?php
/**
 * ticket type model class
 *
 * @author lidongxu @ 2014-5-23
 */

class MODEL_WT_TICKET_TYPE extends MODEL
{
	function MODEL_WT_TICKET_TYPE()
	{
		$this->table = END_MYSQL_PREFIX.'wt_ticket_type_data';
		$this->id = 'type_id';
	}

	function add($data=array())
	{
		$data['create_time'] = time();
		return parent::add($data);
	}
}