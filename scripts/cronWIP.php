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
 Версия: 0.0.5 Alpha
-----------------------------------------------------
 Назначение: Работы для кронтаба
=====================================================
*/
define('INCLUDE_CHECK',true);
require ('../database.php');
//===================================================

	$key = trim(str_replace($config['not_allowed_symbol'],'',strip_tags(stripslashes($_GET['key']))));
	if($key == $config['cronPass']){
		$dateHIS = date("H:i:s");
		$file = SCRIPTS_DIR.'cronLogs.log';
		writeLogFile($file,'[CRON] Started CRON worklist at '.$dateToday.' ['.$dateHIS.']');
	//===========Running CronTab Jobs================
			
			clearMD5Cache($file);

	
	//===============================================
		$messageExecuted = "[CRON] Done cron job at ".$dateToday." in [".$dateHIS."]";
		writeLogFile($file,$messageExecuted);
	} else {
		require ('../../index.php');
		exit();
	}

