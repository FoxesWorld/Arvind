<?php
/*
=====================================================
 Servers PDO
-----------------------------------------------------
 https://arcjetsystems.ru/
-----------------------------------------------------
 Copyright (c) 2016-2020  FoxesWorld
-----------------------------------------------------
 Данный код защищен авторскими правами
-----------------------------------------------------
 Файл: servers.php
-----------------------------------------------------
 Версия: 0.1.6 Alpha
-----------------------------------------------------
 Назначение: Вывод списка серверов в лаунчере
=====================================================
*/ 
	header("Content-Type: text/plain; charset=UTF-8");
	define('INCLUDE_CHECK', true );
	require ("database.php");
	
	$selector = "SELECT * FROM servers";
	$dbname = 'fox_launcher';
	try { 
	$DBH = new PDO("mysql:host=$db_host;dbname=$dbname;charset=UTF8", $db_user, $db_pass); 
	} catch(PDOException $e) { 
	die($e->getMessage()); 
	}
	
	$STH = $DBH->query("$selector");  
	$STH->setFetchMode(PDO::FETCH_ASSOC);  
	while($row = $STH->fetch()) {  
	echo $row['Server_name'] . ", "; 
	echo $row['adress'] . ", "; 
	echo $row['port'] . ", "; 
	echo $row['version'] . "<::>";
	}
	$STH = null;



//echo $row['id'] . "\n";  
//echo $row['srv_image'] . "\n"; 
//echo $row['story'] . "\n"; 
?>