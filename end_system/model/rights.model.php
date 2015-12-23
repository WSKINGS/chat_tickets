<?php
class MODEL_RIGHTS extends MODEL
{
	function MODEL_RIGHTS()
	{
		$this->table = END_MYSQL_PREFIX.'rights';
		$this->id = 'rights_id';
		$this->order_id = 'order_id';
	}
}
