<?php
    Error_Reporting(E_ALL | E_STRICT);
    Ini_Set('display_errors', true);

	if(!defined('INCLUDE_CHECK')) die("You don't have permissions to run this");
	include_once("loger.php");
	
	if (extension_loaded('openssl')) {
		include_once("security/security_openssl.php");
	} else if(extension_loaded('mcrypt')){
		include_once("security/security_mcrypt.php");
	} else {
		exit("Отсутствуют расширения mcrypt и openssl! Установите одно из двух.");
	}

	$crypt 				= 'hash_dle';
	$db_host			= 'localhost'; 
	$db_port			= '3306';
	$db_user			= 'root'; 
	$db_pass			= 'P$Ak$O2sJZSu$aAKOBqkokf@Vs5%YCj'; 
	$db_database		= 'fc_dle';
	$db_table       	= 'dle_users'; 
	$db_columnId  		= 'user_id'; 
	$db_columnUser  	= 'name';
	$db_columnPass  	= 'password';
	$db_tableOther 		= 'xf_user_authenticate';
	$db_columnSalt  	= 'members_pass_salt';
    $db_columnIp  		= 'logged_ip';
	$db_columnDatareg   = 'reg_date';
	$db_columnMail      = 'email';
	$banlist            = 'banlist';
	
	$useban             =  false; //Бан на сервере = бан в лаунчере, Ultrabans плагин
	$useantibrut        =  true; //Защита от частых подборов пароля (Пауза 1 минута при неправильном пароле)
	
	$masterversion  	= 'final_RC4'; //Мастер-версия лаунчера
	$protectionKey		= 'VBHJvbgUh*uyy8gJUgkjufgkhjgkj'; 
	$key1               = "R2zwuwmv~YZSIJ21";  //16 Character Key Ключ пост запросов
	$key2               = "oPCwB9S6z{*rEh%V"; //16 Character  Key  Ключ пост запросов
    $checklauncher      = false; //Проверка хеша лаунчера
	$md5launcherexe     = @md5_file("launcher/fix.exe");  // Сверяем MD5
	$md5launcherjar     = @md5_file("launcher/fix.jar");  // Сверяем MD5
	$temp               = false;  //Использовать файлы кеширования для ускорение авторизации и снижение нагрузки на вебсервер.
	                             //Удаляем файл хеша после обновления клиента на сервере в папке /temp/ИмяКлиента!

	$assetsfolder       = true; //Скачивать assets из папки, или из архива (true=из папки false=из архива)

//========================= Настройки ЛК =======================//	

	$uploaddirs         = 'MinecraftSkins';  //Папка скинов
	$uploaddirp         = 'MinecraftCloaks'; //Папка плащей
    $skinurl            = 'http://login.foxesworld/site/'.$uploaddirs.'/'; //Ссылка на скины для клиентов 1.7.+
    $capeurl            = 'http://login.foxesworld/site/'.$uploaddirp.'/'; //Ссылка на плащи для клиентов 1.7.+
	
	$usePersonal 		=  true; //Использовать личный кабинет
	$canUploadSkin		=  true; //Можно ли заливать скины
	$canUploadCloak		=  true; //Можно ли заливать плащи
	$canBuyVip			=  true; //Можно ли покупать VIP
	$canBuyPremium		=  true; //Можно ли покупать Premium
	$canBuyUnban		=  true; //Можно ли покупать разбан
	$canActivateVaucher =  true; //Можно ли активировать ваучер
	$canExchangeMoney   =  true; //Можно ли обменивать Realmoney -> IConomy
	$canUseJobs			=  true; //Можно ли использовать работы
	$usecreg			=  true; //Можно ли использовать регистрацию в лаунчере
	
	$cloakPrice			=  0;   //Цена плаща (В рублях)
	$vipPrice			=  100;  //Цена випа (В руб/мес)
	$premiumPrice		=  250;  //Цена премиума (В руб/мес)
	$unbanPrice			=  150;  //Цена разбана (В рублях)
	
	$initialIconMoney	=  30;  //Сколько денег дается при регистрации в IConomy
	$exchangeRate		=  200; //Курс обмена Realmoney -> IConomy
	
	//ВСЕ ЧТО НИЖЕ - НЕ ТРОГАТЬ!
	try {
		$db = new PDO("mysql:host=$db_host;port=$db_port;dbname=$db_database", $db_user, $db_pass);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$db->exec("set names utf8");
    } catch(PDOException $pe) {
		die(Security::encrypt("errorsql", $key1).$logger->WriteLine($log_date."Ошибка подключения (Хост, Логин, Пароль)"));
	}
	try {
		$stmt = $db->prepare("
        CREATE TABLE IF NOT EXISTS `usersession` (
	    `user` varchar(255) DEFAULT 'user',
	    `session` varchar(255) DEFAULT NULL,
	    `server` varchar(255) DEFAULT NULL,
	    `token` varchar(255) DEFAULT NULL,
 	    `realmoney` int(255) DEFAULT '0',
 	    `md5` varchar(255) DEFAULT '0',
	    PRIMARY KEY (`user`)
	    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
		");
		$stmt->execute();
		$stmt = $db->prepare("
		CREATE TABLE IF NOT EXISTS `sip` (
		  `time` varchar(255) NOT NULL,
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `sip` varchar(16) DEFAULT NULL,
		  PRIMARY KEY (`id`) USING BTREE
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=0;
		");
		$stmt->execute();
		$stmt->execute();
	} catch(PDOException $pe) {
		die(Security::encrypt("errorsql", $key1).$logger->WriteLine($log_date.$pe));  //вывод ошибок MySQL в m.log
	}