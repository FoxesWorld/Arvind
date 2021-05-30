<?php
/*
=====================================================
 Skins - you look nice today !| AuthLib
-----------------------------------------------------
 https://arcjetsystems.ru/
-----------------------------------------------------
 Copyright (c) 2016-2020  FoxesWorld
-----------------------------------------------------
 Данный код защищен авторскими правами
-----------------------------------------------------
 Файл: skins.php
-----------------------------------------------------
 Версия: 0.0.7 Stable Alpha
-----------------------------------------------------
 Назначение: Парсит скины и плащи
=====================================================
*/
header('Content-Type: text/html; charset=utf-8');
define('INCLUDE_CHECK',true);
@$md5 = $_GET['user'];
$exists1;
	
	try {
	if (!preg_match("/^[a-zA-Z0-9_-]+$/", $md5)){
		exit;
	}

	define('CONFIG', true);
	require ('../config.php');
	include("../database.php");
			
			if(!file_exists(SITE_ROOT.'/'.$config['uploaddirs'])) {
				die ("Skins path is not a folder!");
			}

			if(!file_exists(SITE_ROOT.'/'.$config['uploaddirp'])) {
				die ("Cloak path is not a folder!");
			}

	$db = new db($config['db_user'],$config['db_pass'],$config['db_database']);
	$stmt = $db->prepare("SELECT user FROM usersession WHERE md5= :md5");
	$stmt->bindValue(':md5', $md5);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$realUser = $row['user'];
	if($realUser==null) {
		exit;
	}

		$file1 = $config['capeUrl'].$realUser.'.png';
		$exists1 = file_exists(SITE_ROOT.$config['uploaddirp'].'/'.$realUser.'.png');
		$file2 = $config['skinUrl'].$realUser.'.png';
		$exists2 = file_exists(SITE_ROOT.$config['uploaddirs'].'/'.$realUser.'.png');
		
		//If cape exists
		if ($exists1) {
		    $cape = '"CAPE":{"url":"'.$config['capeUrl'].'?/'.$realUser.'$"}';
		} else {
			$cape = '';
		}
		//If skin exists
		if ($exists2) {
		    $skin ='"SKIN":{"url":"'.$config['skinUrl'].$realUser.'.png"}';
		} else {
			$skin ='"SKIN":{"url":"'.$config['skinUrl'].'default.png"}';
		}
		//If both of them are found
		if ($exists1 && $exists2) {
			$spl = ',';
		} else {
			$spl = '';
		}

		$base64 ='{"timestamp":"'.CURRENT_TIME.'","profileId":"'.$md5.'","profileName":"'.$realUser.'","textures":{'.$skin.$spl.$cape.'}}';
		echo '{"id":"'.$md5.'","name":"'.$realUser.'","properties":[{"name":"textures","value":"'.base64_encode($base64).'","signature":"'.$config['letterHeadLine'].'"}]}';
	} catch(PDOException $pe) {
			die($pe);
	}