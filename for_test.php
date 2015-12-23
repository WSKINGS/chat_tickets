<?php

define('END_MODULE','www');

//获得并设置系统执行的路径
$_system_folder = dirname(__FILE__);
$_system_folder = str_replace("\\", "/", $_system_folder);
//END_SYSTEM_DIR 是指end_system的路径
define('END_SYSTEM_DIR',$_system_folder.'/system/');