<?php
/*
=====================================================
 Radio PDO
-----------------------------------------------------
 https://arcjetsystems.ru/
-----------------------------------------------------
 Copyright (c) 2016-2019 Foxesworld
-----------------------------------------------------
 Данный код защищен авторскими правами
-----------------------------------------------------
 Файл: radio.php
-----------------------------------------------------
 Версия: 0.0.5 Alpha
-----------------------------------------------------
 Назначение: Вывод списка радиостанций в лаунчере
=====================================================
*/ 
	$DBH = new PDO("mysql:host=$db_host;dbname=$dbname;charset=UTF8", $db_user, $db_pass); 
	$STH = $DBH->query('SELECT * FROM radio');  
	$STH->setFetchMode(PDO::FETCH_OBJ);  

	while($row = $STH->fetch()) {
		$id = $row->id;
		$radio_name = $row->radio_name;
		$radio_adress = $row->radio_adress;
		$radio_mount = $row->radio_mount;
		
		$idArray[] = $id;
		$radioNameArray[] = $radio_name;
		$radio_adressArray[] = $radio_adress;
		$radio_mountArray[] = $radio_mount; 

	}
	$STH = null;
	
	$counter = 0;
	$radioCount = count($idArray);
	while ($counter < $radioCount){
		$counter++;
		
	}
	
/*
	$textGet = array(
	'type' => 'getText',
	'Message' => $text);
	$textGet = json_encode($textGet);	
		
		$idArray[] = $id;
		$radioNameArray[] = $radio_name;
		$radio_adressArray[] = $radio_adress;
		$radio_mountArray[] = $radio_mount; 

		//echo $id . "\n";  
		//echo $radio_name . "\n";  
		//echo $radio_adress . "\n";
		//echo $radio_mount . "\n";	
		$radioCount = count($idArray);
		
		$radioGet = array(
		'type' => 'getRadio',
		'id' => $id,
		'radio_name' => $radio_name,
		'radio_adress' => $radio_adress,
		'radio_mount' => $radio_mount);*/
		//$radioGet = json_encode($radioGet);
?>