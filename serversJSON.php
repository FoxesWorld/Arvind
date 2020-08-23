<?php
/*
=====================================================
 Servers PDO
-----------------------------------------------------
 https://Foxesworld.ru/
-----------------------------------------------------
 Copyright (c) 2016-2020  FoxesWorld
-----------------------------------------------------
 Данный код защищен авторскими правами
-----------------------------------------------------
 Файл: servers.php
-----------------------------------------------------
 Версия: 0.1.9 Alpha
-----------------------------------------------------
 Назначение: Вывод списка серверов в лаунчере
=====================================================
*/ 
	header("Content-Type: text/plain; charset=UTF-8");
	define('INCLUDE_CHECK', true );
	require ("database.php");
	
	$selector = "SELECT * FROM servers";
	
	$STH = $LauncherDB ->query("$selector");  
	$STH->setFetchMode(PDO::FETCH_ASSOC);  
	$counter = 0;
	while($row = $STH->fetch()) { 
			if($row['enabled'] == 1) {
				$serverName = $row['Server_name'];
				$adress = $row['adress'];
				$port = $row['port'];
				$version = $row['version'];
				$serverImage = $row['srv_image'];
				$story = $row['story'];
				
				$JSONServers = array(
				'serverNum' => "Server-$counter",
				'serverName' => "$serverName",
				'adress' => "$adress",
				'port' => "$port",
				'version' => "$version",
				'serverImage' => "$serverImage",
				'story' => "$story");
				$JSONServers = json_encode($JSONServers);
				echo $JSONServers;
				echo "\n";

				$counter++;
			}


	}
	$STH = null;