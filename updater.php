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
 Версия: 0.1.13 Alpha
-----------------------------------------------------
 Назначение: Проверка хеша лаунчера, апдейтера и библиотек 
=====================================================
*/ 
header("Content-Type: text/plain; charset=UTF-8");
define('INCLUDE_CHECK',true);
include_once ("scripts/functions.inc.php");
	$get_request =  $_SERVER['QUERY_STRING'];
	if($get_request === ''){
		die("Hacking Attempt!");
	}
	
	$updater_type = $_GET['updater_type'] ?? null;
	$updater_hash = $_GET['hash'] ?? null;
	$launcher_hash = $_GET['ver'] ?? null;
	$lib_load = $_GET['lib_load'] ?? null;
	$lib_hash = $_GET['lib_hash'] ?? null;
	$download = $_GET['download'] ?? null;

	//Хеш-код апдейтера, если обновление есть скрипт отвечает YES, иначе NO
	if(isset($updater_type)){
		if($updater_type == 'jar'){
			die($updater_hash == md5_file("files/updater/updater.jar") ? "NO" : "YES");
		} elseif($updater_type == 'exe'){
			die($updater_hash == md5_file("files/updater/updater.exe") ? "NO" : "YES");
		} elseif ($updater_type != 'exe' || $updater_type != 'jar') {
			die("Unknown updater type!");
		}
	}
	
	//Хеш-код лаунчера, если обновление есть скрипт отвечает YES, иначе NO
	if(isset($launcher_hash)){
		die($launcher_hash == md5_file("files/launcher/launcher.jar") ? "NO" : "YES");
	}

	/*//Библиотеки	
	if(isset($lib_load)){
	if ($handle = opendir('files/launcher/lib')) {
	while (false !== ($file = readdir($handle)))   {
		if ($file != "." && $file != "..")
		{
		echo "$file"; 
		echo "\n";
		if(!$file){ 
			die("ERROR\n no Lib Found!");
			}
		}									}
		closedir($handle);
		}
	}

	//Хеш библиотеки	
	if(isset($lib_hash)){
		if (file_exists("files/launcher/lib/$lib_hash")) {
			
		function lib_hash($lib_name){
		$hash = md5_file("files/launcher/lib/$lib_name");		
		return $hash;}
		
		die (lib_hash($lib_hash));
		} else {
			die("Library does not exist");
		}
	} */
	
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