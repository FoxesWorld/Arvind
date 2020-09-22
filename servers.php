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
 Версия: 0.2.0 Alpha
-----------------------------------------------------
 Назначение: Вывод списка серверов в лаунчере
=====================================================
*/ 
	header("Content-Type: text/plain; charset=UTF-8");
	define('INCLUDE_CHECK', true );
	require ("database.php");
	die(serversParser("SELECT * FROM servers"));


	/*$JSONServers = array(
	'serverNum' => "Server-$counter",
	'serverName' => "$serverName",
	'adress' => "$adress",
	'port' => "$port",
	'version' => "$version",
	'serverImage' => "$serverImage",
	'story' => "$story"); */
	//$JSONServers = json_encode($JSONServers);
	//echo $JSONServers;