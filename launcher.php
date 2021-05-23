<?php
/*
=====================================================
 This is my core! | Launcher
-----------------------------------------------------
 https://FoxesWorld.ru/
-----------------------------------------------------
 Copyright (c) 2016-2021  FoxesWorld
-----------------------------------------------------
 This code is reserved
-----------------------------------------------------
 File: launcher.php
-----------------------------------------------------
 Version: 0.0.20.0 Experimental
-----------------------------------------------------
 Usage: All the functions of Arvind can be obtained in here
=====================================================
*/
	header('Content-Type: text/html; charset=utf-8');
	define('INCLUDE_CHECK',true);
	define('NO_DEBUG',true);
	include ("scripts/actionScript.php");
//===================================================
	if(!$_REQUEST){
		die("No request!");
	}

	if(isset($_POST['action'])) {
		$db = new db($config['db_user'],$config['db_pass'],$config['db_database']);
		$x  = $_POST['action'];
		$x = str_replace(" ", "+", $x);
		$yd = Security::decrypt($x, $config['key2']);

		if($yd == null) {
			die('Access Error!');
			exit;
		}

		list($action, $client, $login, $postPass, $launchermd5, $ctoken) = explode(':', $yd);
	} else {
		exit;
	}

	try {
			//To merge
			if(!file_exists($config['uploaddirs'])) {
				die ("Skins path is not a folder!");
			}

			if(!file_exists($config['uploaddirp'])) {
				die ("Cloak path is not a folder!");
			}
			//******

			require ('scripts/auth.class.php');
			$auth = new auth($ctoken, $login, $postPass, $launchermd5, false);

    //$hash = generateLoginHash();
    $db->run("UPDATE LOW_PRIORITY dle_users SET lastdate='".CURRENT_TIME."', logged_ip='".REMOTE_IP."' WHERE name='$login'"); //,hash='$hash'
    if($action == 'auth') {
		require_once ('scripts/geoIP.class.php');
		$geoplugin = new geoPlugin();
		require_once ("scripts/loadFiles.php");
    }

} catch(PDOException $pe) {
	die(Security::encrypt("errorsql<$>", $config['key1']).$pe);
}