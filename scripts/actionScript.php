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
 Версия: 0.0.10 Alpha
-----------------------------------------------------
 Назначение: Действия при определенных запросах
=====================================================
*/

  if(!defined('INCLUDE_CHECK')) {
		die("Hacking Attempt!");
  } else {
		require_once ('functions.inc.php');
  }
		
		//$dbname = 'fox_launcher';
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
			$login = $_GET['getRealname'] ?? null;
			if($login != null){
				die(getRealname($login, $FoxSiteDB));
			} else {
				die("Invalid login!");
			}
		
	} elseif(isset($_GET['show'])) {
    require 'SkinViewer2D.class.php';
    header("Content-type: image/png");
    $skin_dir = $_SERVER['DOCUMENT_ROOT'] . '/launcher/MinecraftSkins/';											//?show=body&file_name=miomoor&side=front
    $cloak_dir = $_SERVER['DOCUMENT_ROOT'] . '/launcher/MinecraftCloaks/';											//show (body,head)
    $show = $_GET['show'];																							//file_name (username)
	$name =  empty($_GET['file_name']) ? 'default' : $_GET['file_name'];											//side (front,back)
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
	}