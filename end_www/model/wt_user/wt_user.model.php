<?php
/**
 * user model class
 *
 * @author lidongxu
 */

class MODEL_WT_USER extends MODEL
{
	function MODEL_WT_USER()
	{
		$this->table = END_MYSQL_PREFIX.'wt_user_data';
		$this->id = 'user_id';
	}
	
	function add($data=array())
	{
		## 检查用户名是否已经存在
		$item = parent::get_one(array('username'=>$data['username'], 'status'=>'1'));
		#var_dump($item); die();
		if ($item)
			return false;
		##
		if ($data['password'])
		{
			$data['password'] = end_encode($data['password']);
		}
		else
		{
			unset($data['password']);
		}
		$data['create_time'] = time();
		return parent::add($data);
	}

	function update($id,$data=array())
	{
		if ($data['password'])
		{
			$data['password'] = end_encode($data['password']);
		}
		else
		{
			unset($data['password']);
		}
		return parent::update($id,$data);
	}
}