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
 Версия: 0.1.12 Alpha
-----------------------------------------------------
 Назначение: Действия при определенных запросах
=====================================================
*/

  if(!defined('INCLUDE_CHECK')) {
		require ('../../index.php');
  } else {
		require_once ('functions.inc.php');
		$not_allow_symbol = array ("\x22", "\x60", "\t", '\n', '\r', "\n", "\r", '\\', ",", "/", "¬", "#", ";", ":", "~", "[", "]", "{", "}", ")", "(", "*", "^", "%", "$", "<", ">", "?", "!", '"', "'", " ", "&" );
  }
		
	if(isset($_GET['adress']) && isset($_GET['port'])){
			$host = trim(str_replace($not_allow_symbol,'',strip_tags(stripslashes($_GET['adress']))));
			$port = trim(str_replace($not_allow_symbol,'',strip_tags(stripslashes($_GET['port']))));
			die(parse_online($host, $port));
			
	} elseif(isset($_GET['radio'])){
			$radio = trim(str_replace($not_allow_symbol,'',strip_tags(stripslashes($_GET['radio']))));
			//require_once (SCRIPTS_DIR."radio.php");
			die("Not supported yet.");
			
	} elseif(isset($_GET['getText'])){
			$getText = trim(str_replace($not_allow_symbol,'',strip_tags(stripslashes($_GET['getText']))));
			die(getyText());
	
	} elseif(isset($_GET['Image'])){
			$Image = trim(str_replace($not_allow_symbol,'',strip_tags(stripslashes($_GET['Image']))));
			die (ImgHash($Image));
			
	} elseif(isset($_GET['getRealname'])){
			require ($_SERVER['DOCUMENT_ROOT'].'/launcher/database.php');
			$login = trim(str_replace($not_allow_symbol,'',strip_tags(stripslashes($_GET['getRealname'])))) ?? null;
			if($login != null){
				die(getUserData($login,'fullname'));
			} else {
				die(JSONanswer('type', 'error', 'message', 'Invalid login'));
			}
		
	} elseif(isset($_GET['show'])) {
			require 'SkinViewer2D.class.php';
			header("Content-type: image/png");
			$skin_dir = $_SERVER['DOCUMENT_ROOT'] . '/launcher/MinecraftSkins/';
			$cloak_dir = $_SERVER['DOCUMENT_ROOT'] . '/launcher/MinecraftCloaks/';
			$show = trim(str_replace($not_allow_symbol,'',strip_tags(stripslashes($_GET['show'])))) ?? null;
			$file_name = trim(str_replace($not_allow_symbol,'',strip_tags(stripslashes($_GET['file_name'])))) ?? null;
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
		$getProfileBG = trim(str_replace($not_allow_symbol,'',strip_tags(stripslashes($_GET['getProfileBG'])))) ?? null;
		if($getProfileBG !== null) {
		die(usersBackgrounds($getProfileBG));
		} else {
			die("No data!");
		}
		
	} elseif(isset($_GET['rootJSON'])) {
		die(checkfilesRootJSON($_GET['rootJSON']));
		
	} elseif(isset($_GET['serversJSON'])){
		require ($_SERVER['DOCUMENT_ROOT'].'/launcher/database.php');
		die(serversParserJSON($_GET['serversJSON']));
	
	} elseif (isset($_GET['tes'])){
		die (checkfilesJSON('files/clients/Classic/bin/'));
	}
	