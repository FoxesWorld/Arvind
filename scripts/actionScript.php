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
 Версия: 0.1.14 Alpha
-----------------------------------------------------
 Назначение: Действия при определенных запросах
=====================================================
*/

  if(!defined('INCLUDE_CHECK')) {
		require ('../../index.php');
		exit();
  } else {
		require_once ('functions.inc.php');
		if(!isset($_POST['action'])){
			require ($_SERVER['DOCUMENT_ROOT'].'/launcher/database.php');
		}
  }
		
	if(isset($_GET['adress']) && isset($_GET['port'])){
			$host = trim(str_replace($not_allowed_symbol,'',strip_tags(stripslashes($_GET['adress']))));
			$port = trim(str_replace($not_allowed_symbol,'',strip_tags(stripslashes($_GET['port']))));
			die(Security::encrypt(parse_online($host, $port), $key1));
			
	} elseif(isset($_GET['radio'])){
			$radio = trim(str_replace($not_allowed_symbol,'',strip_tags(stripslashes($_GET['radio']))));
			die(JSONanswer('type', 'error', 'message', 'Not supported yet: '.$radio));
			
	} elseif(isset($_GET['getText'])){
			$getText = trim(str_replace($not_allowed_symbol,'',strip_tags(stripslashes($_GET['getText']))));
			die(Security::encrypt(getyText(), $key1));
	
	} elseif(isset($_GET['Image'])){
			$Image = trim(str_replace($not_allowed_symbol,'',strip_tags(stripslashes($_GET['Image']))));
			die(Security::encrypt(ImgHash($Image), $key1));
			
	} elseif(isset($_GET['getRealname'])){
			$login = trim(str_replace($not_allowed_symbol,'',strip_tags(stripslashes($_GET['getRealname'])))) ?? null;
			die(getRealName($login));
		
	} elseif(isset($_GET['show'])) {
			require 'SkinViewer2D.class.php';
			header("Content-type: image/png");
			$skin_dir = $_SERVER['DOCUMENT_ROOT'] . '/launcher/MinecraftSkins/';
			$cloak_dir = $_SERVER['DOCUMENT_ROOT'] . '/launcher/MinecraftCloaks/';
			$show = trim(str_replace($not_allowed_symbol,'',strip_tags(stripslashes($_GET['show'])))) ?? null;
			$file_name = trim(str_replace($not_allowed_symbol,'',strip_tags(stripslashes($_GET['file_name'])))) ?? null;
			$name =  empty($file_name) ? 'default' : $file_name;
			$skin =  $skin_dir . $name . '.png';
			$cloak =  $cloak_dir . $name . '.png';
			if (!skinViewer2D::isValidSkin($skin)) {
				$skin = $skin_dir . 'default.png';
			}
			if ($show !== 'head') {
				$side = isset($_GET['side']) ? $_GET['side'] : false;
				$img = skinViewer2D::createPreview($skin, $cloak, $side);
			} else {
				$img = skinViewer2D::createHead($skin, 64);
			}
			imagepng($img);
			
	} elseif (isset($_GET['getProfileBG'])){
		$getProfileBG = trim(str_replace($not_allowed_symbol,'',strip_tags(stripslashes($_GET['getProfileBG'])))) ?? null;
		if($getProfileBG !== null) {
		die(Security::encrypt(usersBackgrounds($getProfileBG), $key1));
		} else {
			die(Security::encrypt(JSONanswer('type', 'error', 'message', 'No login to search!'), $key1));
		}
		
	} elseif(isset($_GET['rootJSON'])) {
		die(checkfilesRootJSON($_GET['rootJSON']));
		
	} elseif(isset($_GET['serversJSON'])){
		$ServersJSONlogin = trim(str_replace($not_allowed_symbol,'',strip_tags(stripslashes($_GET['serversJSON'])))) ?? null;
		die(serversParserJSON($ServersJSONlogin));
	
	} elseif(isset($_GET['JREnames'])){
		$bitDepth = trim(str_replace($not_allowed_symbol,'',strip_tags(stripslashes($_GET['JREnames'])))) ?? null;
		die(scanRuntimeDir($bitDepth));
	}
	