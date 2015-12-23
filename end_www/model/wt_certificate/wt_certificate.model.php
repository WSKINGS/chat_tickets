<?php
/**
 * certificate model class
 *
 * @author lidongxu @ 2014-5-28
 */

class MODEL_WT_CERTIFICATE extends MODEL
{
	function MODEL_WT_CERTIFICATE()
	{
		$this->table = END_MYSQL_PREFIX.'wt_certificate_data';
		$this->id = 'certificate_id';
	}

	function add($data=array())
	{
		$data['create_time'] = time();
		return parent::add($data);
	}
}