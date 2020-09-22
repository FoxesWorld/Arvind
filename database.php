<?php
if(!defined('INCLUDE_CHECK')) {
	require ($_SERVER['DOCUMENT_ROOT'].'/index.html');
	exit();
}
	
	include_once("scripts/functions.inc.php");
	if (extension_loaded('openssl')) {
		include_once("scripts/security/security_openssl.php");
	} else if(extension_loaded('mcrypt')){
		include_once("scripts/security/security_mcrypt.php");
	} else {
		exit("Отсутствуют расширения mcrypt и openssl!");
	}
	
	define('ROOT_DIR', $_SERVER['DOCUMENT_ROOT']);
	define('SCRIPTS_DIR', ROOT_DIR.'/launcher/scripts/');
	define('SITE_ROOT', ROOT_DIR.'/launcher');
	
	/*		 DB_Config		 */
	$db_host			= 'localhost'; 
	$db_port			= '3306';
	$db_user			= 'root'; 
	$db_pass			= 'P$Ak$O2sJZSu$aAKOBqkokf@Vs5%YCj'; 
	$db_database		= 'fox_dle';
	$dbname_launcher    = 'fox_launcher';
	$db_table       	= 'dle_users'; 
	$db_columnId  		= 'user_id'; 
	$db_columnUser  	= 'name';
	$db_columnPass  	= 'password';
    $db_columnIp  		= 'logged_ip';
	$db_columnDatareg   = 'reg_date';
	$db_columnMail      = 'email';
	
	
	$banlist            = 'banlist';
	$clientsDir = "files/clients/";
	$crypt 				= 'hash_foxy';
	
	$useban             =  false; //Бан на сервере = бан в лаунчере (Не готовая разработка) //Будет убрано 
	$useantibrut        =  true; //Защита от частых подборов пароля (Пауза 1 минута)
	$temp               = true; //Хранение кеша файлов во временных файлах
	
	/*		 Cryptography		 */
	$masterversion  	= 'final_RC4'; //версия лаунчера (Не пригодилась, md5 всё сам решил, в будущих релизах уберем) //Будет убрано 
	$protectionKey		= 'VBHJvbgUh*uyy8gJUgkjufgkhjgkj'; 
	$key1               = "R2zwuwmv~YZSIJ21";  //16 Character Key 
	$key2               = "oPCwB9S6z{*rEh%V"; //16 Character  Key
	$md5launcherjar     = @md5_file("files/launcher/launcher.jar");  // Сверяем MD5 //Будет убрано так как сверяем иначе

	/*		 Skins&Cloaks Configuration 		*/
	$uploaddirs         = 'MinecraftSkins';  //Папка скинов
	$uploaddirp         = 'MinecraftCloaks'; //Папка плащей
    $skinurl            = 'https://login.foxesworld.ru/launcher/'.$uploaddirs.'/'; //Ссылка на скины 
    $capeurl            = 'https://login.foxesworld.ru/launcher/'.$uploaddirp.'/'; //Ссылка на плащи	

	require_once ('scripts/databasePrepare.php');
	
	try { 
		$LauncherDB = new PDO("mysql:host=$db_host;dbname=$dbname_launcher;charset=UTF8", $db_user, $db_pass); 
	} catch(PDOException $e) { 
		die($e->getMessage());
	}
	
	try {
		$FoxSiteDB = new PDO("mysql:host=$db_host;dbname=$db_database;charset=UTF8", $db_user, $db_pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
	} catch(PDOException $e) { 
		die($e->getMessage());
	}

	//dbPrepare($db_host, $db_port, $db_database, $db_user, $db_pass);