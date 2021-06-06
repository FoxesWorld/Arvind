<?php
/*
=====================================================
 This is my core! | Launcher class
-----------------------------------------------------
 https://FoxesWorld.ru/
-----------------------------------------------------
 Copyright (c) 2016-2021  FoxesWorld
-----------------------------------------------------
 This code is reserved
-----------------------------------------------------
 File: launcher.php
-----------------------------------------------------
 Version: 0.1.0.0 Experimental
-----------------------------------------------------
 Usage: All the functions of Arvind can be obtained in here
=====================================================
*/

	Error_Reporting(E_ALL);
	Ini_Set('display_errors', true);

	header('Content-Type: text/html; charset=utf-8');
	define('INCLUDE_CHECK',true);
	define('NO_DEBUG',true);
	define('CONFIG', true);
	require ('config.php');
	require (SCRIPTS_DIR.'functions.inc.php');
	require (SITE_ROOT.'/database.php');
	require (SCRIPTS_DIR.'actionScript.class.php');
	require (SCRIPTS_DIR.'auth.class.php');
//===================================================

	if(isset($_POST['action'])) {
		$launcher = new launcher($_POST['action']);
	} else {
		exit;
	}
	
	class launcher {

		function __construct($postAction){
			global $config;
			$inputValue = Security::decrypt(str_replace(" ", "+", $postAction), $config['key2']);

			if($inputValue == null) {
				die('No info!');
			} else {
				//$LauncherDB = new db($config['db_user'],$config['db_pass'],$config['dbname_launcher']);
				$db = new db($config['db_user'],$config['db_pass'],$config['db_database']);
				dbPrepare($db);
			}

			if($config['authJSON'] === false) {
				list($action, $client, $login, $postPass, $launchermd5, $ctoken) = explode(':', $inputValue);
			} else {
				list($action, $client, $login, $postPass, $launchermd5, $ctoken) = $this->getJSONinput($inputValue);
			}
			
				if($action == 'auth') {
					$auth = new auth($action, $client, $login, $postPass, $launchermd5, $ctoken, $db);
				}
		}
		
		private function getJSONinput($JSON){
			$String = json_decode($JSON);

			$arrayOut = array($String['action'], $String['client'], $String['login'], $String['postPass'], $String['launchermd5'], $String['token']);
			return $arrayOut;
		}
	}