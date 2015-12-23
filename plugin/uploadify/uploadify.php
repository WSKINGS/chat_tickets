<?php
/*
Uploadify
Copyright (c) 2012 Reactive Apps, Ronnie Garcia
Released under the MIT License <http://www.opensource.org/licenses/mit-license.php> 
*/

// Define a destination
$targetFolder = '/../../public'; // Relative to the root

$verifyToken = md5('unique_salt' . $_POST['timestamp']);

if (!empty($_FILES) && $_POST['token'] == $verifyToken) {
	$tempFile = $_FILES['Filedata']['tmp_name'];
	$targetPath = dirname(__FILE__) . $targetFolder;
	
	// Validate the file type
	$fileTypes = array('jpg','jpeg','gif','png'); // File extensions
	$fileParts = pathinfo($_FILES['Filedata']['name']);
	
	if (in_array($fileParts['extension'],$fileTypes)) {
		//$size = getimagesize($_FILES['Filedata']['tmp_name']);
		//if ($size[0] != 1920 || $size[1] != 1440) {
		//	echo '请上传一张图片大小为1920*1440的背景图片!';
		//	return ;
		//}
		$targetFile = rtrim($targetPath,'/') . '/' . $_POST['lottery_id'] . '.' . $fileParts['extension'];
		move_uploaded_file($tempFile,$targetFile);
		echo '1';
	} else {
		echo '无合法的文件类型.';
	}
}
?>