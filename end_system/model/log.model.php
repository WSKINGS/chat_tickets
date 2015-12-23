<?php
class MODEL_LOG extends MODEL
{
	function MODEL_LOG()
	{
		$this->table = END_MYSQL_PREFIX.'log';
		$this->order_id = NULL;
		$this->id = 'log_id';
	}
	
	function clear_old($admin_id,$n)
	{
		global $db;
		$lastone = $db->get_one("SELECT `$this->id` as `id` FROM `$this->table` WHERE `admin_id`='$admin_id' ORDER BY `$this->id` DESC LIMIT $n,1");
		$db->query("DELETE FROM `$this->table` WHERE `admin_id`='$admin_id' AND `$this->id`<'".$lastone['id']."';");
	}
	
	function count_menu($n = 10)
	{
		global $db;
		return $db->get_all("SELECT count(`time`) as `hittime`,`url`,`info` FROM `$this->table` where `menu`='1' AND `admin_id`='".get_admin_id()."' GROUP BY `url` ORDER BY `hittime` DESC LIMIT $n");
	}
}
?>