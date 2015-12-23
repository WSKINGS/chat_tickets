<?php
/**********************************
*     		EndCMS
*       www.endcms.com
*         ©2008-now
* under Creative Commons License
**********************************/

class MODEL
{
	var $table; // table name
	var $id;  //unique id
	var $order_id; //order column
	
	function MODEL($table,$id=false,$order_id = false)
	{
		if ($table)
		{
			$this->table = END_MYSQL_PREFIX.$table;
			if (!$id) $id = $table.'_id';
			if (!$order_id) $order_id = 'order_id';
			$this->id = $id;
			$this->order_id = $order_id;
		}
	}
	
	
	//add one row, returns the new row's id if insert succeed
	function add( $data = array())
	{
		$table = isset($data['table'])?$data['table']:$this->table;
		if (isset($data['table'])) unset($data['table']);
		$sql = $this->make_insert_sql($table,$data); 
		if ($GLOBALS['db']->query($sql))
		{
			return $GLOBALS['db']->insert_id();
		}
		else return false;
	}
	
	//delete one record by its id
	function delete($id)
	{
		$sql = "DELETE FROM $this->table WHERE $this->id = '$id' LIMIT 1";
		return ($GLOBALS['db']->query($sql));
	}
	
	//check if one record exists, input an array,such as : array('name'=>'xxx','sex'=>'M')
	function exists($arr)
	{
		if (!is_array($arr)) $arr = array($this->id=>$arr);
		$arr['select'] = 'count(1) as total';
		$r = $this->get_one($arr);
		return intval($r['total'])>0;
	}
	
	function set($val,$cond)
	{
		$old = $this->get_one($cond);
		if ($old && $old[$this->id])
		{
			$old_id = $old[$this->id];
			$this->update($old_id,$val);
		}
		else
		{
			$old_id = $this->add($val);
		}
		return $old_id;
	}
	
	//update a row by its id
	function update( $id, $data = array())
	{
		$table = isset($data['table'])?$data['table']:$this->table;
		unset($data['table']);
		$sql = $this->make_update_sql($table,$data, array($this->id => $id)); 
		return ($GLOBALS['db']->query($sql));
	}
	
	function truncate()
	{
		return $GLOBALS['db']->query('TRUNCATE TABLE `'.$this->table.'`');
	}
	
	//get one row
	function get_one($id)
	{
		//check_allowed(str_replace(END_MYSQL_PREFIX,'',$this->table),'read',END_RESPONSE == 'text');
		if (is_array($id)) // if by condition
		{
			$id['total'] = '1';
			$arr = $this->get_list($id);
			return $arr[0];
		}
		else
		{
			$sql = "SELECT * FROM $this->table WHERE $this->id = '$id' LIMIT 1";
		}
		return $GLOBALS['db']->get_one($sql);
	}
	
	//get rows of one page
	function get_list($data=array())
	{
		//allow custom sql
		if (isset($data['_custom_sql']))
		{
			$sql = $data['_custom_sql'];
			//page control
			if ($data['from'] && $data['total'])
				$sql.=" LIMIT ".$data['from'].",".$data['total'];
			else if ($data['total'])
				$sql.=" LIMIT ".$data['total'];
			$sql.= ';';

			if ($data['select'] == 'count(1)') //total query
			{
				if ($data['_custom_count_sql'])
					$sql = $data['_custom_count_sql'];
				else
					$sql = preg_replace('/SELECT.*?FROM/i','SELECT count(1) FROM',$sql);
			}

		}
		else
		{

			$sql = '';
			//check_allowed(str_replace(END_MYSQL_PREFIX,'',$this->table),'read',END_RESPONSE == 'text');
			//select what
			$_select = isset($data['select'])?$data['select']:'*';
			$_table = isset($data['table'])?$data['table']:$this->table;
			
			//user defined conditions such as `name` LIKE '%xxx%' ....
			if (isset($data['where'])) $sql.= " AND ".$data['where'];
			
			//order control
			if (!isset($data['order']) && $this->order_id)
				$sql.= " ORDER BY $this->order_id DESC,$this->id DESC";
			else if (isset($data['order']))
				$sql.= " ORDER BY ".$data['order'].'';
			else 
				$sql.=" ORDER BY $this->id DESC";
		
			
			//page control
			if (isset($data['from']) && $data['total'])
				$sql.=" LIMIT ".$data['from'].",".$data['total'];
			else if (isset($data['total']))
				$sql.=" LIMIT ".$data['total'];
			$sql.= ';';

			//unset control columns
			unset($data['select']);
			unset($data['table']);
			unset($data['where']);
			unset($data['order']);
			unset($data['total']);
			unset($data['from']);
			
			$sql = $this->make_select_sql($_table,$data,$_select).$sql;
		}
		
		return $GLOBALS['db']->get_all($sql);
	}
	
	function make_table_sql($table)
	{
		if (is_array($table))
		{
			$r = array();
			foreach($table as $alias=>$table)
			{
				$r[] = "`".END_MYSQL_PREFIX."{$table}` `$alias`";
			}
			return join(',',$r);
		}
		else return '`'.$table.'`';
	}
	
	function make_insert_sql($table,$data)
	{
		$cols = array();
		$vals = array();
		foreach($data as $key => $val)
		{
			$cols[] = $key;
			$vals[] = "'".mysql_real_escape_string($val)."'";
		}
		return "INSERT INTO ".$this->make_table_sql($table)." (".join(',',$cols).") VALUES(".join(',',$vals).") ";
	}
	
	function make_select_sql($table,$data = array(),$_select = '*')
	{
		$cond = array();
		foreach($data as $key => $val)
			$cond[] = "$key = '".mysql_real_escape_string($val)."' ";
		$sql = "SELECT $_select FROM ".$this->make_table_sql($table);
		if (count($cond) > 0)
			$sql.= " WHERE ".join(' AND ',$cond);
		else
			$sql.= " WHERE 1=1 ";
		return $sql;
	}


	function make_update_sql($table,$data,$cond_arr)
	{
		$values = array();
		$cond = array();
		foreach($data as $key => $val)
			$values[] = "$key = '".mysql_real_escape_string($val)."'";
		foreach($cond_arr as $key => $val)
			$cond[] = "$key = '".mysql_real_escape_string($val)."'";
		return "UPDATE ".$this->make_table_sql($table)." SET ".join(',',$values)." WHERE ".join(' AND ',$cond);
	}
	
	function change_table($table)
	{
		$this->table = END_MYSQL_PREFIX.$table;
	}
}
