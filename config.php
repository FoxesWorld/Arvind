<?php
/*
=====================================================
 Config
-----------------------------------------------------
 https://Foxesworld.ru/
-----------------------------------------------------
 Copyright (c) 2016-2020  FoxesWorld
-----------------------------------------------------
 Данный код защищен авторскими правами
-----------------------------------------------------
 Файл: servers.php
-----------------------------------------------------
 Версия: 0.1.3 Alpha
-----------------------------------------------------
 Назначение: Настройки веб сервиса
=====================================================
*/
if(!defined('CONFIG')) {
	require ($_SERVER['DOCUMENT_ROOT'].'/index.php');
	exit();
}

	define('ROOT_DIR', $_SERVER['DOCUMENT_ROOT']);
	define('SCRIPTS_DIR', ROOT_DIR.'/launcher/scripts/');
	define('FILES_DIR', ROOT_DIR.'/launcher/files/');
	define('SITE_ROOT', ROOT_DIR.'/launcher');
	
$config = array(
	/* Database Settings*/
	'db_host' 			=> 'localhost',
	'db_port' 			=> '3306',
	'db_user' 			=> 'root',
	'db_pass' 			=> 'P$Ak$O2sJZSu$aAKOBqkokf@Vs5%YCj',
	'db_table' 			=> 'dle_users',
	'db_database' 		=> 'fox_dle',
	'dbname_launcher' 	=> 'fox_launcher',
	'db_name_userdata' 	=> 'fox_userdata',
	'db_columnId' 		=> 'user_id',
	'db_columnUser' 	=> 'name',
	'db_columnPass' 	=> 'password',
	'db_columnIp' 		=> 'logged_ip',
	'db_columnDatareg' 	=> 'reg_date',
	'db_columnMail' 	=> 'email',
	
	/* Clients Settings */
	'clientsDir' 		=> 'files/clients/',
	'temp' 				=> false, //Use temporary files
	'useban' 			=> false, //Doesn't work
	'useantibrut' 		=>  true,
	
	/* Skins&Cloaks Configuration */
	'uploaddirs'  		=> 'MinecraftSkins',  
	'uploaddirp'  		=> 'MinecraftCloaks',
	
	/* Cryptography */
	'protectionKey'		=> 'VBHJvbgUh*uyy8gJUgkjufgkhjgkj', 
	'key1'              => "R2zwuwmv~YZSIJ21",  //Encryption Key 1
	'key2'              => "oPCwB9S6z{*rEh%V", //Encryption Key 2
	'md5launcherjar'    => @md5_file("files/launcher/launcher.jar"),
	'not_allowed_symbol'=> array ("\x22", "\x60", "\t", '\n', '\r', "\n", "\r", '\\', ",", "/", "¬", "#", ";", ":", "~", "[", "]", "{", "}", ")", "(", "*", "^", "%", "$", "<", ">", "?", "!", '"', "'", " ", "&" ),
	'cronPass' 			=> 'Tess2556308',//CronTab secure key
	'crypt' 			=> 'hash_foxy',
	
	/* Updater */
	'launcherRepositoryPath' => "files/launcher/launcher.jar",
	'updaterRepositoryPath' => "files/updater/updater.",
	
	/* E-mail */
	'adminEmail'		=> 'lisssicin@ya.ru',
	'letterHeadLine' 	=> 'FoxesWorld | Arvind',
	'sendMethod' 		=> 'SMTP',
	'sendHost'			=> 'smtp.yandex.ru',
	'SMTPport'			=> 465,
	'SMTPMail'			=> 'no-reply@foxesworld.ru',
	'SMTPpass'			=> 'dvhbdxutiscpbmof',
	'SMTPsecProtocol'	=> 'SSL');
    $skinurl            = 'https://login.foxesworld.ru/launcher/'.$config['uploaddirs'].'/'; //Ссылка на скины 
    $capeurl            = 'https://login.foxesworld.ru/launcher/'.$config['uploaddirp'].'/'; //Ссылка на плащи
	
	//echo $config['launcherMD5'].' '.$config['md5launcherjar'];