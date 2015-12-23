<?php
/**
* EndDB
* the file system based NoSQL database
*/

/**
* EndDB
* 作者: Longbill  longbill.cn@gmail.com  http://php.js.cn
* 2011-03-04
* 用法：
* $enddb = new EndDB('/var/tmp');
* $enddb->set('key1','string1');
* $enddb->set('key2',$_COOKIE);
* print_r($enddb->get('key1'));
* $enddb->delete('key2');
*/

class EndDB
{
	public $path;
	public $default_type;
	
	/**
	* $path: EndDB数据文件存储的根目录，需要php具有读写权限
	* $default_type: 如果是json，那么存储和读取数据的时候默认会执行json_encode和json_decode操作，默认是json。
	*/
	function EndDB($path,$default_type = 'json')
	{
		if (substr($path,-1) != '/') $path.='/';
		$this->path = $path;
		$this->default_type = $default_type;
	}
	
	/**
	* 存储数据
	* $key: 数据的key，可以是任意字符串，因为会被md5一次。
	* $data: 数据，如果$type是json，那么$data可以为array，否则只能是string
	* 可选 $type: 数据的类型，默认等于$this->default_type
	*/
	public function set($key,$data,$type = '')
	{
		$hash = md5($key);
		if ($type === '') $type = $this->default_type;
		if ($type == 'json') $data = json_encode($data);
		return file_put_contents($this->_get_dir($hash),$data);
	}
	
	/**
	* 读取数据
	* $key: 数据的key
	* 可选 $type: 数据的类型，默认等于$this->default_type
	*/
	public function get($key,$type = '')
	{
		$hash = md5($key);
		if ($type === '') $type = $this->default_type;
		$filename = $this->_get_dir($hash);
		if (!file_exists($filename)) return false;
		$data = file_get_contents($filename);
		if ($type == 'json') $data = json_decode($data,true);
		return $data;
	}
	
	/**
	* 删除
	*/
	public function delete($key)
	{
		$hash = md5($key);
		$filename = $this->_get_dir($hash);
		if (!file_exists($filename)) return true;
		return unlink($filename);
	}
	
	/**
	* 内部函数，获得key对应的文件路径
	* 目前采用的是md5(key)的前2个字符作为一级路径，2到4个字符作为二级路径。 如果数据量比较大，可以考虑增加三级甚至四级路径。
	*/
	private function _get_dir($hash)
	{
		$dir = $this->path.substr($hash, 0, 2).'/';
		if (!is_dir($dir)) mkdir($dir);
		$dir.= substr($hash,2,2).'/';
		if (!is_dir($dir)) mkdir($dir);
		return $dir.$hash;
	}
}

