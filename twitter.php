<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/vendor/autoload.php'; // change path as needed


if(isset($_SERVER['HTTPS'])){
    $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
}
else{
    $protocol = 'http';
}
$baseurl = $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];


$twttr_key = '';
$twttr_secret_key = '';
$twttr_token = '';

use Abraham\TwitterOAuth\TwitterOAuth;
if (isset($_REQUEST['oauth_verifier'])) {
	$twitter = new TwitterOAuth($twttr_key, $twttr_secret_key, $_SESSION['twitter_oauth_token'], $_SESSION['twitter_oauth_token_secret'] );
} else if( $twttr_token!= '' ){
	$tw_access_token = json_decode($twttr_token,true);
	$twitter = new TwitterOAuth($twttr_key, $twttr_secret_key, $tw_access_token['oauth_token'], $tw_access_token['oauth_token_secret'] );
} else {

	$twitter = new TwitterOAuth($twttr_key, $twttr_secret_key);
}

if($_GET['auth-status'] == 'success' &&  $_GET['auth-from'] == 'twitter'){
	echo 'Twitter successfully!';
} 
elseif ($segments[1] == 'twitter') {
	if (isset($_SESSION['twitter_oauth_token']) && $_SESSION['twitter_oauth_token'] !== $_REQUEST['oauth_token']) 
	{
	    header('Location: '.$baseurl.'?auth-status=error&auth-from=twitter');
	} else {
			
			$access_token = $twitter->oauth("oauth/access_token", ["oauth_verifier" => $_REQUEST['oauth_verifier']]);
			header('Location: '.$baseurl.'?auth-status=success&auth-from=twitter');
		}
	}
}



$returnUrl = $baseurl."twitter";
$request_token = $twitter->oauth("oauth/request_token", array("oauth_callback" => $returnUrl));
$_SESSION['twitter_oauth_token'] = $token = $request_token['oauth_token'];
$_SESSION['twitter_oauth_token_secret'] = $request_token['oauth_token_secret'];
// REDIRECTING TO THE URL
$t_loginUrl = $twitter->url("oauth/authorize", array("oauth_token" => $request_token['oauth_token']));