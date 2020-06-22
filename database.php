<?php
	if(!defined('INCLUDE_CHECK')) {
		die("Hacking Attempt!");
	}
	
	
	include_once("scripts/functions.inc.php");
	if (extension_loaded('openssl')) {
		include_once("scripts/security/security_openssl.php");
	} else if(extension_loaded('mcrypt')){
		include_once("scripts/security/security_mcrypt.php");
	} else {
		exit("Отсутствуют расширения mcrypt и openssl!");
	}

	$crypt 				= 'hash_foxy';
	$db_host			= 'localhost'; 
	$db_port			= '3306';
	$db_user			= 'root'; 
	$db_pass			= 'P$Ak$O2sJZSu$aAKOBqkokf@Vs5%YCj'; 
	$db_database		= 'fox_dle';
	$db_table       	= 'dle_users'; 
	$db_columnId  		= 'user_id'; 
	$db_columnUser  	= 'name';
	$db_columnPass  	= 'password';
    $db_columnIp  		= 'logged_ip';
	$db_columnDatareg   = 'reg_date';
	$db_columnMail      = 'email';
	$banlist            = 'banlist';
	
	$useban             =  false; //Бан на сервере = бан в лаунчере (Не готовая разработка) //Будет убрано 
	$useantibrut        =  true; //Защита от частых подборов пароля (Пауза 1 минута, увеличим рост блокировки в геометрической прогрессии))))
	
	$masterversion  	= 'final_RC4'; //версия лаунчера (Не пригодилась, md5 всё сам решил, в будущих релизах уберем) //Будет убрано 
	$protectionKey		= 'VBHJvbgUh*uyy8gJUgkjufgkhjgkj'; 
	$key1               = "R2zwuwmv~YZSIJ21";  //16 Character Key 
	$key2               = "oPCwB9S6z{*rEh%V"; //16 Character  Key
	$md5launcherjar     = @md5_file("launcher/fix.jar");  // Сверяем MD5 //Будет убрано 
	$temp               = false;  //Использовать файлы кеширования для ускорение авторизации и снижение нагрузки на вебсервер.
	$assetsfolder       = true; //Скачивать assets из папки, или из архива (true=из папки false=из архива) //Будет убрано 


	$uploaddirs         = 'MinecraftSkins';  //Папка скинов
	$uploaddirp         = 'MinecraftCloaks'; //Папка плащей
    $skinurl            = 'http://login.foxesworld/site/'.$uploaddirs.'/'; //Ссылка на скины 
    $capeurl            = 'http://login.foxesworld/site/'.$uploaddirp.'/'; //Ссылка на плащи для клиентов 1.7.+	
	
	//dbPrepare($db_host, $db_port, $db_database, $db_user, $db_pass);
	require_once ('scripts/databasePrepare.php');