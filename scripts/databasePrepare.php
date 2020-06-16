<?php 
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