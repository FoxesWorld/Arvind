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
 Версия: 0.0.4 Alpha
-----------------------------------------------------
 Назначение: Работы для кронтаба
=====================================================
*/
define('INCLUDE_CHECK',true);
define('DEBUG_LOGS',true);
require ('../database.php');
//===================================================
	$key = trim(str_replace($not_allowed_symbol,'',strip_tags(stripslashes($_GET['key']))));
	if($key == $cronPass && $key !== null){
	//===========Running CronTab Jobs================
		clearMD5Cache();
	
	//===============================================
	} else {
		require ('../../index.html');
	}

