<?php

/*
=====================================================
 Add some action with the actionScript!
-----------------------------------------------------
 https://arcjetsystems.ru/
-----------------------------------------------------
 Copyright (c) 2016-2020 FoxesWorld
-----------------------------------------------------
 Данный код защищен авторскими правами
-----------------------------------------------------
 Файл: actionScript.php
-----------------------------------------------------
 Версия: 0.0.3 Alpha
-----------------------------------------------------
 Назначение: Действия при определенных запросах
=====================================================
*/ 
header("Content-Type: text/plain; charset=UTF-8");
define('ROOT_DIR', $_SERVER['DOCUMENT_ROOT']);
define('SCRIPTS_DIR', ROOT_DIR.'/site/scripts/');

  if(!defined('INCLUDE_CHECK')) {
		die("Hacking Attempt!");
  } 
		
	$dbname = 'fox_launcher';
		if(isset($_GET['adress']) && isset($_GET['port'])){
			$host = $_GET['adress'];
			$port = $_GET['port'];
			die(parse_online($host, $port));
			
		} elseif(isset($_POST['radio'])){
			require_once (ROOT_DIR."/site/database.php");
			require_once (SCRIPTS_DIR."radio.php");
		
		} elseif(isset($_GET['getText'])){
			require_once (SCRIPTS_DIR."randomText.php");
		} 