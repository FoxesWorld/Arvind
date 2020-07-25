<?php
/*
=====================================================
 Add some action with the actionScript!
-----------------------------------------------------
 https://Foxesworld.ru/
-----------------------------------------------------
 Copyright (c) 2016-2020 FoxesWorld
-----------------------------------------------------
 Данный код защищен авторскими правами
-----------------------------------------------------
 Файл: actionScript.php
-----------------------------------------------------
 Версия: 0.0.8 Alpha
-----------------------------------------------------
 Назначение: Действия при определенных запросах
=====================================================
*/ 
header("Content-Type: text/plain; charset=UTF-8");

  if(!defined('INCLUDE_CHECK')) {
		die("Hacking Attempt!");
  } else {
		require_once ('functions.inc.php');
  }
		
		$dbname = 'fox_launcher';
	if(isset($_GET['adress']) && isset($_GET['port'])){
			$host = $_GET['adress'];
			$port = $_GET['port'];
			die(parse_online($host, $port));
			
	} elseif(isset($_GET['radio'])){
			//require_once (SCRIPTS_DIR."radio.php");
			die("Not supported yet.");
			
	} elseif(isset($_GET['getText'])){
			die(getyText());
	
	} elseif(isset($_GET['Image'])){
			$Image = $_GET['Image'];
			die (ImgHash($Image));
			
	} elseif(isset($_GET['getRealname'])){
			require ($_SERVER['DOCUMENT_ROOT'].'/launcher/database.php');
			$login = $_GET['getRealname'];
			die(getRealname($login, $db_host, $db_database, $db_user, $db_pass));
			
	} elseif (isset($_GET['configFiles'])){
			$clientName = $_GET['configFiles'];
			die(checkConfig($clientName));
	}