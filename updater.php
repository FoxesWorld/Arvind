<?php
/*
=====================================================
 LauncherNlibHash
-----------------------------------------------------
 https://Foxesworld.ru/
-----------------------------------------------------
 Copyright (c) 2016-2019 FoxesWorld
-----------------------------------------------------
 Данный код защищен авторскими правами
-----------------------------------------------------
 Файл: updater.php
-----------------------------------------------------
 Версия: 0.1.17 Alpha
-----------------------------------------------------
 Назначение: Проверка хеша лаунчера и апдейтера
=====================================================
*/ 
header("Content-Type: text/plain; charset=UTF-8");
define('INCLUDE_CHECK',true);
define('CONFIG', true);
include_once ("config.php");
include_once ("scripts/functions.inc.php");
	$get_request =  $_SERVER['QUERY_STRING'];
	if($get_request === ''){
		die("Hacking Attempt!");
	}

	$updater_type = $_GET['updater_type'] ?? null;
	$updater_hash = $_GET['hash'] ?? null;
	$launcher_hash = $_GET['ver'] ?? null;
	$download = $_GET['download'] ?? null;

	//Хеш-код апдейтера, если обновление есть скрипт отвечает YES, иначе NO
	if(isset($updater_type)){
		$file = "updater";
		if($updater_type === 'jar'){
			$fileName = $file.'.'.$updater_type;
			$updaterHashLocal = md5_file($config['updaterRepositoryPath'].$updater_type);
			$updateState = $updater_hash == $updaterHashLocal ? "NO" : "YES";
			
		} elseif($updater_type === 'exe'){
			$fileName = $file.'.'.$updater_type;
			$updaterHashLocal = md5_file($config['updaterRepositoryPath'].$updater_type);
			$updateState = $updater_hash == $updaterHashLocal ? "NO" : "YES";
			
		} elseif ($updater_type != 'exe' || $updater_type != 'jar') {
			$updateState = "Unknown updater type!";
		}
		$answer = array('fileName' => $fileName, 'fileHash' => $updaterHashLocal, 'updateState' => $updateState);
		$answer = json_encode($answer);
		die($answer);
	}
	
	//Хеш-код лаунчера, если обновление есть скрипт отвечает YES, иначе NO
	if(isset($launcher_hash)){
		$launcherRepositoryHash = md5_file($config['launcherRepositoryPath']);
		$launcherState = $launcher_hash == $launcherRepositoryHash  ? "NO" : "YES";
		$answer = array('fileName' =>$config['launcherRepositoryPath'], 'hash' => $launcherRepositoryHash, 'updateState' => $launcherState);
		$answer = json_encode($answer);
		die($answer);
	}
	
	//Download (Скачивание апдейтера с сайта)
	if(isset($download)){
		if ($download == "jar"){
			$file = "files//updater//updater.jar";
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename=' . basename("Foxesworld.jar"));
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			readfile($file);
			exit;
		} elseif($download == "exe"){
			$file = "files//updater//updater.exe";
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename=' . basename("Foxesworld.exe"));
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			readfile($file);
			exit;
		} elseif ($download != "jar" || $download == "exe"){
			die ("Unknown request!");
		}
	}
?>