<?php
/*
=====================================================
 Add some action with the actionScript!
-----------------------------------------------------
 https://Foxesworld.ru/
-----------------------------------------------------
 Copyright (c) 2016-2021 FoxesWorld
-----------------------------------------------------
 Данный код защищен авторскими правами
-----------------------------------------------------
 Файл: actionScript.php
-----------------------------------------------------
 Версия: 0.2.17 Alpha
-----------------------------------------------------
 Назначение: Действия при определенных запросах
=====================================================
*/

  if(!defined('INCLUDE_CHECK')) {
		require ('../../index.php');
		exit();
  } else {
		require ($_SERVER['DOCUMENT_ROOT'].'/launcher/database.php');
  }
  foreach ($_GET as $key => $value) {
	   switch ($key) {
		   case 'radio' :
			die("А не послушаешь ты его...");
		   break;
		   
		   case 'getText' :
			$getText = trim(str_replace($config['not_allowed_symbol'],'',strip_tags(stripslashes($_GET['getText']))));
			die(getyText());
		   break;
		   
		   case 'Image' :
			$Image = trim(str_replace($config['not_allowed_symbol'],'',strip_tags(stripslashes($_GET['Image']))));
			die(ImgHash($Image));//Security::encrypt( , $config['key1'])
		   break;
		   
		   case 'getRealname' :
			$login = trim(str_replace($config['not_allowed_symbol'],'',strip_tags(stripslashes($_GET['getRealname'])))) ?? null;
			die(getRealName($login));
		   break;
		   
		   case 'getProfileBG' :
			$getProfileBG = trim(str_replace($config['not_allowed_symbol'],'',strip_tags(stripslashes($_GET['getProfileBG'])))) ?? null;
			if($getProfileBG !== null) {
			die(usersBackgrounds($getProfileBG));
			} else {
				die(Security::encrypt(JSONanswer('type', 'error', 'message', 'No login to search!'), $config['key1']));
			}
		   break;
		   
		   case 'userSelected' :
			$userSelected = trim(str_replace($config['not_allowed_symbol'],'',strip_tags(stripslashes($_GET['userSelected'])))) ?? null;
			die(selectedUserBg($userSelected));
		   break;
		   
		   case 'serversList' :
			$ServersLogin = trim(str_replace($config['not_allowed_symbol'],'',strip_tags(stripslashes($_GET['serversList'])))) ?? null;
			if($config['serversOut'] === true) {
				die(serversParserJSON($ServersLogin));
			} else {
				die(serversParser($ServersLogin));
			}
		   break;
		   
		   case 'JREnames' :
			$bitDepth = trim(str_replace($config['not_allowed_symbol'],'',strip_tags(stripslashes($_GET['JREnames'])))) ?? null;
			die(scanRuntimeDir($bitDepth));
		   break;
		   
		   case 'startUpSound' :
			$startSound = new startUpSound(false);
			die($startSound->generateAudio());
		   break;
		   
		   case 'show' :
			require 'SkinViewer2D.class.php';
			header("Content-type: image/png");
			$skin_dir = $_SERVER['DOCUMENT_ROOT'] . '/launcher/MinecraftSkins/';
			$cloak_dir = $_SERVER['DOCUMENT_ROOT'] . '/launcher/MinecraftCloaks/';
			$show = trim(str_replace($config['not_allowed_symbol'],'',strip_tags(stripslashes($_GET['show'])))) ?? null;
			$file_name = trim(str_replace($config['not_allowed_symbol'],'',strip_tags(stripslashes($_GET['file_name'])))) ?? null;
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
		   break;
		   
		   case 'adress':
			$host = trim(str_replace($config['not_allowed_symbol'],'',strip_tags(stripslashes($_GET['adress']))));
			$port = trim(str_replace($config['not_allowed_symbol'],'',strip_tags(stripslashes($_GET['port']))));
			if($config['parseOnline'] === true) {
				die(Security::encrypt(parse_onlineJSON($host, $port), $config['key1']));
			} else {
				die(Security::encrypt(parse_online($host, $port), $config['key1']));
			}
		   break;
	   }
  }
