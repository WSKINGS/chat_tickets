<?php

/*
 * Weifen Image Class
 * */
class CImage
{
    public $ImgResouce;
    public $Width;
    public $Height;
    
    /*
     * the original constructor
     * */
    function __construct()
    {
        $nums = func_num_args();
        if ($nums<1)    return FALSE;
        
        $args = func_get_args();
        $arg = func_get_arg(0);
        
        if (method_exists($this, $f = '__construct_'.$nums))
        {
            return call_user_func_array(array($this,$f), $args);
        }
       
    }
    
    function __construct_1($filestring)
    {
        $img = imagecreatefromstring($filestring);
        if ($img)
        {
            $this->ImgResouce = $img;
        }
        else
        {
            return FALSE;
        }
        
        list($this->Width, $this->Height) = getimagesizefromstring($filestring);
    }
    
    function __construct_2($filename, $imgType) // imgType not use currently
    {
        $data = file_get_contents($filename);        
        $img = imagecreatefromstring($data);
        if ($img)
        {
            $this->ImgResouce = $img;
        }
        else
        {
            return FALSE;
        }
        
        $php_ver = (int)substr(PHP_VERSION, 2,1);
        if ($php_ver >= 4)
            list($this->Width, $this->Height) = getimagesizefromstring($data);
        else
            list($this->Width, $this->Height) = getimagesize($filename);
    }
    
    function __construct_3($img, $width, $height)
    {
        $this->Width = $width;
        $this->Height = $height;
        $this->ImgResouce = $img;
    }

    
    function __destruct()
    {
        imagedestroy($this->ImgResouce);
    }
    
    /*
     * resize the image 
     * */
    function Resize($toWidth, $toHeight)
    {
        $dstImg = imagecreatetruecolor($toWidth, $toHeight);
        if (imagecopyresized($dstImg, $this->ImgResouce, 0, 0, 0, 0, $toWidth, $toHeight, $this->Width, $this->Height))
        {
            return $dstImg;
        }
        else 
        {
            return FALSE;    
        }
    }
    
    /*
     * get big(720), medium(360), small images(180): 
     * */
    function Get3Images($BigSize = 720, $MediumSize = 360, $SmallSize = 180)
    {
        //$BigSize=720; $MediumSize = 360; $SmallSize = 180;
        
        $imgs = array('bigImg'=>0, 'mediumImg'=>1, 'smallImg'=>2);
        if ($this->Width > $BigSize)
        {
            $img = $this->Resize($BigSize, $BigSize);
            if ($img)
                $imgs['bigImg'] = $img;
            else
                return FALSE;
        }
        else 
            $imgs['bigImg'] = $this->ImgResouce;
        
        if ($this->Width > $MediumSize) 
        {
            $img = $this->Resize($MediumSize, $MediumSize);
            if ($img)
                $imgs['mediumImg'] = $img;
            else
                    return FALSE;
            }
        else
            $imgs['mediumImg'] = $this->ImgResouce;
        
        if ($this->Width > $SmallSize)
        {
            $img = $this->Resize($SmallSize, $SmallSize);
            if ($img)
                $imgs['smallImg'] = $img;
            else
                return FALSE;
        }
        else
            $imgs['smallImg'] = $this->ImgResouce;
        
        return $imgs;
            
    }
}


// $data = file_get_contents('../image/test.png');
// $srcImg = imagecreatefromstring($data);
// list($width, $height) = getimagesizefromstring($data);
// $dstImg = imagecreatetruecolor($width/2, $height/2);
// ImageResize($data, $dstImg, $width/2, $height/2);
// imagejpeg($dstImg, 'D:/OutTest/test.jpg');

// $data = file_get_contents('../image/test1.png');
// $img  = imagecreatefromstring($data);
// list($width,$height) = getimagesizefromstring($data);
// $img = new CImage($img,$width,$height);
// $dstImg = $img->Get3Images();

// foreach ($dstImg as $key => $value)
// {
//     echo $key . '\r\n';
//      imagejpeg($value,'D:/OutTest/test2_'.$key.'.jpg');
// }
// echo 'over';

// test base64 encoding
// $data = file_get_contents('../image/test.png');
// $base64String = base64_encode($data);
// file_put_contents('D:/OutTest/base64.txt', $base64String);


// $data = file_get_contents('D:/OutTest/base64.txt');
// $data = base64_decode($data);
// file_put_contents('D:/OutTest/img.png', $data);

// echo 'over';


?>