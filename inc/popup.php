<?php
	define("MSG_OK_ONLY",0,true);
	define("MSG_YES_NO",1,true);
	define("MSG_YES_NO_CANCEL",2,true);
	
	$title=$_GET['title'];
	$msg=$_GET['message'];
	if (isset($_GET['style'])){
		$style=$_GET['style'];
	} else {
		$style=MSG_OK_ONLY;
	}
?>

