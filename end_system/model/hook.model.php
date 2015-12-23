<?php
class MODEL_HOOK extends MODEL
{
	function MODEL_HOOK()
	{
		$this->table = END_MYSQL_PREFIX.'hook';
		$this->order_id = NULL;
		$this->id = 'hook_id';
	}
}
?>