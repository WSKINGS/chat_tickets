<?php
class END_Loader
{
	var $loaded = array();
	
	function model($f)
	{
		$_file = END_SYSTEM_DIR.'model/'.$f;
		if (strpos($f,'.php') === false) $_file.='.php';
		if ($this->loaded['model/'.$_file]) return $this->loaded['model/'.$_file];
		if (file_exists($_file))
		{
			include_once($_file);
			if (class_exists('END_'.$f))
			{
				eval('$view_data = new END_'.$f.';');
				$this->loaded['model/'.$_file] = $view_data;
				return $view_data;
			}
			else
			{
				die("Loader Error! Class not found in file $_file");
			}
		}
		else die("Loader Error! Model file not found:$_file");
	}
	
	function helper($f)
	{
		$_file = END_SYSTEM_DIR.'helper/'.$f;
		if (strpos($f,'.php') === false) $_file.='.php';
		if ($this->loaded['helper/'.$_file]) return true;
		if (file_exists($_file))
		{
			include_once($_file);
			$this->loaded['helper/'.$_file] = true;
			return true;
		}
		else 
			die("Loader Error! Helper file not found:$_file");
	}
	
	function library($f)
	{
		$_file = END_SYSTEM_DIR.'library/'.$f;
		if (strpos($f,'.php') === false) $_file.='.php';
		if ($this->loaded['library/'.$_file]) return true;
		if (file_exists($_file))
		{
			include_once($_file);
			$this->loaded['library/'.$_file] = true;
			return true;
		}
		else 
			die("Loader Error! Library file not found:$_file");
	}
}