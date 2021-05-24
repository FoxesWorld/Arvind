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
 Version: 0.0.22.0 Experimental
-----------------------------------------------------
 Usage: All the functions of Arvind can be obtained in here
=====================================================
*/
	header('Content-Type: text/html; charset=utf-8');
	define('INCLUDE_CHECK',true);
	define('NO_DEBUG',true);
	include ("scripts/actionScript.php");
	require ('scripts/auth.class.php');
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

	if($action == 'auth') {
		$auth = new auth($action, $client, $login, $postPass, $launchermd5, $ctoken);
	}