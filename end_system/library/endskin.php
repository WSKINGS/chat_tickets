<?php


class EndSkin
{
	public $vars = array();
	public $template_dir = './';
	public $cache_dir = './';
	public $default_template = '';
	public $compile_hook = '';
	function EndSkin($template_dir = NULL,$cache_dir = NULL)
	{
		if ($template_dir) $this->template_dir = $template_dir;
		if ($cache_dir) $this->cache_dir = $cache_dir;
		if (substr($this->template_dir,-1) != '/') $this->template_dir.='/';
		if (substr($this->cache_dir,-1) != '/') $this->cache_dir.='/';
		if (!is_dir($this->template_dir)) mkdir($this->template_dir);
		if (!is_dir($this->cache_dir)) mkdir($this->cache_dir);
	}
	
	function assign($key,$data = NULL)
	{
		if (is_array($key))
		{
			foreach($key as $_k=>$_v) $this->vars[$_k] = $_v;
		}
		else
		{
			$this->vars[$key] = $data;
		}
	}
	
	function display($__endskin__template = '')
	{
		if (!$__endskin__template) $__endskin__template = $this->default_template;
		extract($this->vars);
		include($this->_compile($__endskin__template));
	}
	
	function result($template = '')
	{
		if (!$template) $template = $this->default_template;
		ob_start();
	    $this->display($template);
	    $s  =  ob_get_contents();
	    ob_end_clean();
	    return $s;
	}
	
	private function _compile($template)
	{
		$filename = $this->template_dir.$template;
		$cache_filename = $this->cache_dir.md5($template);
		//if (file_exists($cache_filename) && filemtime($cache_filename) > filemtime($filename)) return $cache_filename;
		$page = $this->_get_template($template);

		//匹配变量
		if (preg_match_all('/\{(\$[a-zA-Z\_][a-zA-Z0-9\_\.\-\>\[\]\'\"]*)\}/',$page,$ms))
		{
			foreach($ms[1] as $cnt=>$tag)
			{
				$tag = $this->_replace_var_name($tag);
				$code = '<'.'?php echo '.$tag.'; ?'.'>';
				$page = str_replace($ms[0][$cnt],$code,$page);
			}
		}
		
		//匹配foreach
		if (preg_match_all('/\{(foreach\s*\([^}]+\))\}/',$page,$ms))
		{
			foreach($ms[1] as $cnt=>$tag)
			{
				$tag = $this->_replace_var_name($tag);
				$code = '<'.'?php '.$tag.': ?>';
				$page = str_replace($ms[0][$cnt],$code,$page);
			}
		}

		//匹配if,elseif
		if (preg_match_all('/\{((else)?if\s*\([^}]+\))\}/',$page,$ms))
		{
			foreach($ms[1] as $cnt=>$tag)
			{
				$tag = $this->_replace_var_name($tag);
				$code = '<'.'?php '.$tag.':?>';
				$page = str_replace($ms[0][$cnt],$code,$page);
			}
		}

		//匹配函数调用
		if (preg_match_all('/\{([a-zA-Z0-9\_]+\([^\}]*\))\}/',$page,$ms))
		{
			foreach($ms[1] as $cnt=>$tag)
			{
				$tag = $this->_replace_var_name($tag);
				if (!preg_match('/\;\s*$/',$tag)) $tag.=';';
				$code = '<'.'?php echo '.$tag.'?>';
				$page = str_replace($ms[0][$cnt],$code,$page);
			}
		}

		$page = preg_replace('/\{\/foreach\}/i','<?php endforeach; ?>',$page);
		$page = preg_replace('/\{else\}/i','<?php else: ?>',$page);
		$page = preg_replace('/\{\/if\}/i','<?php endif; ?>',$page);
		
		if ($this->compile_hook && function_exists($this->compile_hook))
		{
			$_hook = $this->compile_hook;
			$page = $_hook($page);
		}
		
		file_put_contents($cache_filename,$page);
		return $cache_filename;
	}
	
	private function _replace_var_name($s)
	{
		preg_match_all('/\$([a-zA-Z\_][a-zA-Z\_0-9]*)\.([\.a-zA-Z\_0-9]+)/',$s,$ms);
		foreach($ms[1] as $i=>$v)
		{
			$parts = explode('.',$ms[2][$i]);
			$code = '$'.$ms[1][$i];
			foreach($parts as $_p)
			{
				$code.= '[\''.$_p.'\']';
			}
			$s = str_replace($ms[0][$i],$code,$s);
		}
		return $s;
	}
	
	private function _get_template($template)
	{
		$filename = $this->template_dir.$template;
		$s = file_get_contents($filename);
		preg_match_all('/<\!\-\-\s+INCLUDE\s+(.*?)\s*\-\-\>/i', $s, $ms);
		foreach($ms[1] as $_key=>$_temp)
		{
			$s = str_replace($ms[0][$_key],$this->_get_template(trim($_temp)),$s);
		}
		return $s;
	}
}