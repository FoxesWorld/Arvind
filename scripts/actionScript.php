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
 Версия: 0.0.7 Alpha
-----------------------------------------------------
 Назначение: Действия при определенных запросах
=====================================================
*/ 
header("Content-Type: text/plain; charset=UTF-8");
define('ROOT_DIR', $_SERVER['DOCUMENT_ROOT']);
define('SCRIPTS_DIR', ROOT_DIR.'/launcher/scripts/');
define('SITE_ROOT', ROOT_DIR.'/launcher');

  if(!defined('INCLUDE_CHECK')) {
		die("Hacking Attempt!");
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
		}