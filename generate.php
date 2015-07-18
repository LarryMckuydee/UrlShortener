<?php
session_start();
require_once('include/UrlShortener.php');
if(isset($_POST['url'])){
	$UrlShortener = new UrlShortener();
	if($UrlShortener){
		if($url = $UrlShortener->shortenUrl($_POST['url']))
			$_SESSION['msg']='Generated shortened link: <a href="'.'http://' . $_SERVER['HTTP_HOST'] ."/UrlShortener"."/".$url.'">'.'http://' . $_SERVER['HTTP_HOST'] ."/".$url.'</a>';
		else
			$_SESSION['msg']=$UrlShortener->getErrorMsg();
	}
}

header('Location: home.php');
?>