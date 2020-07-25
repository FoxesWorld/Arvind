<?php 
/*
=====================================================
 Cron - automizes your life!
-----------------------------------------------------
 https://Foxesworld.ru/
-----------------------------------------------------
 Copyright (c) 2016-2020  FoxesWorld
-----------------------------------------------------
 Данный код защищен авторскими правами
-----------------------------------------------------
 Файл: cron.php
-----------------------------------------------------
 Версия: 0.0.2 Alpha
-----------------------------------------------------
 Назначение: Работы для кронтаба
=====================================================
*/
define('INCLUDE_CHECK',true);
header("Content-Type: text/plain; charset=UTF-8");
Error_Reporting(E_ALL);
Ini_Set('display_errors', true);
require ('../database.php');
//================================================================
	$selector = "SELECT Server_name FROM servers";
	$dbname = 'fox_launcher';
	try { 
	$DBH = new PDO("mysql:host=$db_host;dbname=$dbname;charset=UTF8", $db_user, $db_pass); 
	} catch(PDOException $e) { 
	die($e->getMessage());
	}
	
	$STH = $DBH->query("$selector");  
	$STH->setFetchMode(PDO::FETCH_ASSOC);  
	while($row = $STH->fetch()) {  
		if ($handle = opendir(SITE_ROOT.'/files/clients/'.$row['Server_name'])) {
			while (false !== ($file = readdir($handle)))   {
				if ($file != "." && $file != ".." && $file != "mods" && $file != "bin" && $file != "config" && $file != "natives" && $file != "config.zip"){
				$delPath = '../files/clients/'.$file."/".$file;
					if (file_exists($delPath)) {
						$unlink = unlink($delPath);
						if($unlink == true){
							echo "Deleted: $delPath\n";
						} else {
							echo "Error deleting $delPath\n";
						}
					}
				}
			}
			closedir($handle);
			}
	}