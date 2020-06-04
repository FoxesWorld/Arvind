<?php
/*
=====================================================
 LauncherNlibHash
-----------------------------------------------------
 https://arcjetsystems.ru/
-----------------------------------------------------
 Copyright (c) 2016-2019 FoxesWorld
-----------------------------------------------------
 Данный код защищен авторскими правами
-----------------------------------------------------
 Файл: updater.php
-----------------------------------------------------
 Версия: 0.1.1 Alpha
-----------------------------------------------------
 Назначение: Проверка хеша лаунчера, апдейтера и библиотек 
=====================================================
*/ 
header("Content-Type: text/plain; charset=UTF-8"); 

//Хеш-код апдейтера, если обновление есть скрипт отвечает YES, иначе NO
	if($_GET['updater_type']){
		if($_GET['updater_type']=='jar'){die($_GET['hash']==md5_file("files/updater/updater.jar") ? "NO" : "YES");}
		if($_GET['updater_type']=='exe'){die($_GET['hash']==md5_file("files/updater/updater.exe") ? "NO" : "YES");}
	}
	
//Хеш-код лаунчера, если обновление есть скрипт отвечает YES, иначе NO
	if(isset($_GET['ver'])){
		die($_GET['ver']==md5_file("files/launcher/launcher.jar") ? "NO" : "YES");
	}

//Библиотеки	
	if(isset($_GET['lib_load'])){
	if ($handle = opendir('files/launcher/lib')) {
	while (false !== ($file = readdir($handle)))   {
		if ($file != "." && $file != "..")
		{
		echo "$file"; 
		echo "\n";
		if(!$file){ 
			die("Error!");
			}
		}
													}
		closedir($handle);
		}
	}

//Хеш библиотеки	
	if($_GET['lib_hash']){
		if (file_exists("files/launcher/lib/$_GET[lib_hash]")) {
		$lib_hash = $_GET['lib_hash'];
			
		function lib_hash($lib_name){
		$hash = md5_file("files/launcher/lib/$lib_name");		
		return $hash;}
		
		die (lib_hash($lib_hash));
		} else die("Library does not exist");
	}
	
//Download (Скачивание клиента с сайта)
if($_GET['download']){
	$download = $_GET['download'];
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
	}
}
?>