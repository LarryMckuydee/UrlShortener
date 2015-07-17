<?php
session_start();
require_once('include/UrlShortener.php');
if(isset($_POST['url'])){
	$UrlShortener = new UrlShortener();
	if($UrlShortener){
		if($url = $UrlShortener->shortenUrl($_POST['url']))
			$_SESSION['msg']=$url;
		else
			$_SESSION['msg']=$UrlShortener->getErrorMsg();
	}
}

header('Location: home.php');
?>