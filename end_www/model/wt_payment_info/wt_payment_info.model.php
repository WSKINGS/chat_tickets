<?php
/**
 * clause model class
 *
 * @author lidongxu @ 2015.1.10
 */

class MODEL_WT_PAYMENT_INFO extends MODEL
{
	function MODEL_WT_PAYMENT_INFO()
	{
		$this->table = END_MYSQL_PREFIX.'wt_payment_info';
		$this->id = 'info_id';
	}
}