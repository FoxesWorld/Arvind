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
	define('CONFIG', true);
	require ('config.php');
	require (SCRIPTS_DIR.'functions.inc.php');
	require (SITE_ROOT.'/database.php');
	require (SCRIPTS_DIR.'actionScript.php');
	require (SCRIPTS_DIR.'auth.class.php');
//===================================================
	if(!$_REQUEST){
		die("No request!");
	}

	if(isset($_POST['action'])) {
		$db = new db($config['db_user'],$config['db_pass'],$config['db_database']);
		$inputValue  = $_POST['action'];
		$inputValue = str_replace(" ", "+", $inputValue);
		$inputValueDecrypted = Security::decrypt($inputValue, $config['key2']);

		if($inputValueDecrypted == null) {
			die('No info!');
			exit;
		}

		list($action, $client, $login, $postPass, $launchermd5, $ctoken) = explode(':', $inputValueDecrypted);
	} else {
		exit;
	}

	if($action == 'auth') {
		$auth = new auth($action, $client, $login, $postPass, $launchermd5, $ctoken);
	}