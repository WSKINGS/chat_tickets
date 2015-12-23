<?php
/**
 * clause model class
 *
 * @author lidongxu @ 2014.5.22
 */

class MODEL_WT_CLAUSE extends MODEL
{
	function MODEL_WT_CLAUSE()
	{
		$this->table = END_MYSQL_PREFIX.'wt_clause_data';
		$this->id = 'clause_id';
	}
}