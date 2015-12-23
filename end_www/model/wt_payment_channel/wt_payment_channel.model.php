<?php
/**
 * option model class
 *
 * @author lidongxu @ 2014.5.22
 */

class MODEL_WT_PAYMENT_CHANNEL extends MODEL
{
	function MODEL_WT_PAYMENT_CHANNEL()
	{
		$this->table = END_MYSQL_PREFIX.'wt_payment_channel_data';
		$this->id = 'channel_id';
	}
}