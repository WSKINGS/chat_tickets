<?php
/**
 * option model class
 *
 * @author lidongxu @ 2014.5.22
 */

class MODEL_WT_OPTION extends MODEL
{
	function MODEL_WT_OPTION()
	{
		$this->table = END_MYSQL_PREFIX.'wt_option_data';
		$this->id = 'option_id';
	}
}