<?php
/*
=====================================================
 This is my core! | Launcher
-----------------------------------------------------
 https://arcjetsystems.ru/
-----------------------------------------------------
 Copyright (c) 2016-2021  FoxesWorld
-----------------------------------------------------
 Данный код защищен авторскими правами
-----------------------------------------------------
 Файл: launcher.php
-----------------------------------------------------
 Версия: 0.0.19.7 Stable Alpha
-----------------------------------------------------
 Назначение: Ядро вебчасти, сочетающее в себе всю её функциональность
=====================================================
*/
	header('Content-Type: text/html; charset=utf-8');
	define('INCLUDE_CHECK',true);
	define('NO_DEBUG',true);
	include ("scripts/actionScript.php");  //Action requests
//===================================================
	if(!$_REQUEST){
		die("No request!");
	}

	if(isset($_POST['action'])) {
		$db = new db($config['db_user'],$config['db_pass'],$config['db_database']);
		$x  = $_POST['action'];
		$x = str_replace(" ", "+", $x);
		$yd = Security::decrypt($x, $config['key2']);

		if($yd == null) {
			die('Access Error!');
			exit;
		}

		list($action, $client, $login, $postPass, $launchermd5, $ctoken) = explode(':', $yd);
	} else {
		exit;
	}

	try {
	if (!preg_match("/^[a-zA-Z0-9_-]+$/", $login) || !preg_match("/^[a-zA-Z0-9_-]+$/", $postPass) || !preg_match("/^[a-zA-Z0-9_-]+$/", $action)) {
		exit(Security::encrypt("errorLogin<$>", $config['key1']));
    }

			if(!file_exists($config['uploaddirs'])) {
				die ("Skins path is not a folder!");
			}

			if(!file_exists($config['uploaddirp'])) {
				die ("Cloak path is not a folder!");
			}

	//If auth token was not set - authorisation
    if($ctoken == "null") {
			if($config['crypt'] === 'hash_md5' || $config['crypt'] === 'hash_foxy') {
				$stmt = $db->prepare("SELECT ".$config['db_columnUser'].",".$config['db_columnPass']." FROM ".$config['db_table']." WHERE BINARY ".$config['db_columnUser']." = :login");
				$stmt->bindValue(':login', $login);
				$stmt->execute();
				$stmt->bindColumn($config['db_columnPass'], $realPass);
				$stmt->bindColumn($config['db_columnUser'], $realUser);
				$stmt->fetch();
			}
			$checkPass = hash_name($config['crypt'], $realPass, $postPass, @$salt);

					//If usung Antibrut
					if($config['useantibrut'] === true) {
						$stmt = $db->getRow("SELECT sip,time FROM sip WHERE sip='".REMOTE_IP."' And time >'".CURRENT_TIME."'");
						$bannedIP = $stmt['sip'];
							if(REMOTE_IP == $bannedIP) {
								$stmt = $db->run("DELETE FROM sip WHERE time < '".CURRENT_TIME."';");
								exit(Security::encrypt("temp<$>", $config['key1']));
							}
						
							if ($login != $realUser) {
								$stmt = $db->run("INSERT INTO sip (sip, time)VALUES ('".REMOTE_IP."', '".$config['bantime']."')");
								exit(Security::encrypt("errorLogin<$>", $config['key1']));
							}
							
							if(!strcmp($realPass,$checkPass) == 0 || !$realPass) {
								$stmt = $db->run("INSERT INTO sip (sip, time)VALUES ('".REMOTE_IP."', '".$config['bantime']."')");
								exit(Security::encrypt("errorLogin<$>", $config['key1']));
							}

					} else {
						if ($checkPass != $realPass) {
							die(Security::encrypt('errorLogin<$>', $config['key1']));
						}
					}
					//*********************
			
		$acesstoken = token();
    } else {
		$acesstoken = $postPass;
    }

    $sessid = token();
    $stmt = $db->prepare("SELECT user, token FROM usersession WHERE user= :login");
    $stmt->bindValue(':login', $login);
    $stmt->execute();
    $rU = $stmt->fetch(PDO::FETCH_ASSOC);
    if($rU['user'] != null) {
        $realUser = $rU['user'];
    }

    if($ctoken != "null") {
		if($rU['token'] != $acesstoken || $login != $realUser) {
				exit(Security::encrypt("errorLogin<$>", $config['key1']));
		}
    }

    if($login == $rU['user']) {
			if($ctoken == "null") {
				$stmt = $db->prepare("UPDATE usersession SET session = '$sessid', token = :token WHERE user= :login");
				$stmt->bindValue(':token', $acesstoken);
			} else {
				$stmt = $db->prepare("UPDATE usersession SET session = '$sessid' WHERE user = :login");
			}

			$stmt->bindValue(':login', $login);
			$stmt->execute();
		} else {
			if($ctoken == "null" || $login != $rU['user']) {
				$stmt = $db->prepare("INSERT INTO usersession (user, session, md5, token) VALUES (:login, '$sessid', :md5, '$acesstoken')");
				$stmt->bindValue(':login', $realUser);
				$stmt->bindValue(':md5', str_replace('-', '', uuidConvert($realUser)));
				$stmt->execute();
			}
		}

    //$hash = generateLoginHash();
    $db->run("UPDATE LOW_PRIORITY dle_users SET lastdate='".CURRENT_TIME."', logged_ip='".REMOTE_IP."' WHERE name='$login'"); //,hash='$hash'
    if($action == 'auth') {
		require_once ('scripts/geoIP.class.php');
		$geoplugin = new geoPlugin();	//GeoPosition
		require_once ("scripts/loadFiles.php"); //LoadFileList
    }

} catch(PDOException $pe) {
	die(Security::encrypt("errorsql<$>", $config['key1']).$pe);
}