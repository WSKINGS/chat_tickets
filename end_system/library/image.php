<?php


class Image
{
	var $filepath;					//图片完整路径	e.g. eee/aaa/bbb.jpg
	var $filename;					//图片名			e.g. bbb
	var $filetype;					//图片格式		e.g. jpg
	var $dirname;					//路径			e.g. eee/aaa
	var $quality = 85;				//jpeg质量
	var $big_box_width = 500;		//大缩略图长宽最大值
	var $small_box_width = 90; 		//小缩略图长宽最大值
	var $small_filepath;			//小缩略图地址
	var $big_filepath;				//大缩略图地址
	var $max;
	var $orig_width;
	var $orig_height;
	var $inited = false;
	var $mime;

	
	function image($path=false)
	{
		if ($path && file_exists($path))
		{
			$this->filepath = $path;
		}
		elseif ($path)
		{
			die('file not found:'.$path);
		}
	}
	
	function init()
	{
		$_arr = explode('.',$this->filepath);
		$this->filename = basename($this->filepath);
		$this->filetype = $_arr[count($_arr)-1];
		$this->dirname = dirname($this->filepath);
		$arr = @getimagesize($this->filepath);
		$this->orig_width = $arr[0];
		$this->orig_height = $arr[1];
		$this->mime = str_replace("image/","",$arr['mime']);
		$this->mime == "bmp" && $this->mime = "wbmp";
		$this->inited = true;
	}
	
	function make_big_preview()
	{
		if (!$this->inited) $this->init();
		$this->big_filepath = $this->dirname.'/'.$this->filename.'_big.'.$this->filetype;
		if (@file_put_contents($this->big_filepath,$this->resize_to_box($this->big_box_width)))
			return $this->big_filepath;
		else
			return false;
	}
	
	function make_small_preview()
	{
		if (!$this->inited) $this->init();
		$this->small_filepath = $this->dirname.'/'.$this->filename.'_small.'.$this->filetype;
		if (@file_put_contents($this->small_filepath,$this->resize_to_box($this->small_box_width)))
			return $this->small_filepath;
		else
			return false;
	}
	
	function resize_to_box($max=0)
	{
		if (!$this->inited) $this->init();
		if ($max) $this->max = $max;
		if ($this->orig_width < $this->orig_height && $this->orig_height>$this->max)
		{
			$height = $this->max;
			$width = ($this->max / $this->orig_height) * $this->orig_width;
		}
		else if ($this->orig_width>$this->orig_height && $this->orig_width>$this->max)
		{
			$width = $max;
			$height = ($this->max/$this->orig_width)*$this->orig_height;
		}
		else
		{
			return file_get_contents($this->filepath);
		}
		
		$image_p = @imagecreatetruecolor($width, $height);
		@eval('$_image = @imagecreatefrom'.$this->mime.'("'.$this->filepath.'");');
		@imagecopyresampled($image_p, $_image, 0, 0, 0, 0, $width, $height, $this->orig_width, $this->orig_height);

		//header('Content-type: image/'.$this->mime);
		//header("Content-Length: ".filesize($this->filepath));
		ob_start();
		@eval('@image'.$this->mime.'($image_p, null,'.$this->quality.');');
		$out = ob_get_contents();
		ob_end_clean();
		return $out;
	}
	
	function resize_width($max=0)
	{
		if (!$this->inited) $this->init();
		if ($max) $this->max = $max;
		if ($this->orig_width > $max)
		{
			$width = $max;
			$height = ($max/$this->orig_width)*$this->orig_height;
		}
		else
		{
			return true;
		}
		
		$image_p = @imagecreatetruecolor($width, $height);
		@eval('$_image = @imagecreatefrom'.$this->mime.'("'.$this->filepath.'");');
		@imagecopyresampled($image_p, $_image, 0, 0, 0, 0, $width, $height, $this->orig_width, $this->orig_height);

		ob_start();
		@eval('@image'.$this->mime.'($image_p, null,'.$this->quality.');');
		$out = ob_get_contents();
		ob_end_clean();
		return file_put_contents($this->filepath,$out);
	}
}
?>