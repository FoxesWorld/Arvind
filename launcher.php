<?php
/*
=====================================================
 Launcher - this is my core!
-----------------------------------------------------
 https://arcjetsystems.ru/
-----------------------------------------------------
 Copyright (c) 2016-2020  FoxesWorld
-----------------------------------------------------
 Данный код защищен авторскими правами
-----------------------------------------------------
 Файл: launcher.php
-----------------------------------------------------
 Версия: 0.0.17.1 Stable Alpha
-----------------------------------------------------
 Назначение: Ядро вебчасти, сочетающее в себе всю её функциональность
=====================================================
*/
header('Content-Type: text/html; charset=utf-8');
$_TIME = time();
define('INCLUDE_CHECK',true); //Security Define
define('DEBUG_LOGS',true);
include ("scripts/functions.inc.php");  //All Functions
include_once ("scripts/actionScript.php");  //Action requests
//================================================================
	if(!$_REQUEST){
		require ('../index.php');
	}

	if(isset($_POST['action'])) {
		include("database.php");
		$db = new db($config['db_user'],$config['db_pass'],$config['db_database']);
		$x  = $_POST['action'];
		$x = str_replace(" ", "+", $x);
		$yd = Security::decrypt($x, $config['key2']);
		
		if($yd == null) {
			die('Access Error!');
			exit;
		}
		
		@list($action, $client, $login, $postPass, $launchermd5, $ctoken) = explode(':', $yd);
	} else {
		exit;
	}

	try {
	if (!preg_match("/^[a-zA-Z0-9_-]+$/", $login) || !preg_match("/^[a-zA-Z0-9_-]+$/", $postPass) || !preg_match("/^[a-zA-Z0-9_-]+$/", $action)) {
		exit(Security::encrypt("errorLogin<$>", $config['key1']));
    }
	
	if(!file_exists($config['uploaddirs'])) {
		die ("Путь к скинам не является папкой!");
	}
	
	if(!file_exists($config['uploaddirp'])) {
		die ("Путь к плащам не является папкой!");
	}
	

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

			if($config['useantibrut']) {
				$ip  = getenv('REMOTE_ADDR');	
				$time = time();
				$bantime = $time+(10);
				$stmt = $db->prepare("Select sip,time From sip Where sip='$ip' And time>'$time'");
				$stmt->execute();
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				$real = $row['sip'];
				if($ip == $real) {
					$stmt = $db->prepare("DELETE FROM sip WHERE time < '$time';");
					$stmt->execute();
					exit(Security::encrypt("temp<$>", $config['key1']));
				}
				
				if ($login != $realUser) {
					$stmt = $db->prepare("INSERT INTO sip (sip, time)VALUES ('$ip', '$bantime')");
					$stmt->execute();
					exit(Security::encrypt("errorLogin<$>", $config['key1']));
				}
				if(!strcmp($realPass,$checkPass) == 0 || !$realPass) {
					$stmt = $db->prepare("INSERT INTO sip (sip, time)VALUES ('$ip', '$bantime')");
					$stmt->execute();
					exit(Security::encrypt("errorLogin<$>", $config['key1']));
				}

			} else {
				if($checkPass !=  $realPass)  die(Security::encrypt('errorLogin<$>', $config['key1']));
			}
	}

    if($ctoken == "null") {
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
            }
            else {
            	$stmt = $db->prepare("UPDATE usersession SET session = '$sessid' WHERE user= :login");
            }
			$stmt->bindValue(':login', $login);
			$stmt->execute();
		}
		else if($ctoken == "null" || $login != $rU['user']) {
			$stmt = $db->prepare("INSERT INTO usersession (user, session, md5, token) VALUES (:login, '$sessid', :md5, '$acesstoken')");
			$stmt->bindValue(':login', $realUser);
			$stmt->bindValue(':md5', str_replace('-', '', uuidConvert($realUser)));
			$stmt->execute();
		}
		$ip=$_SERVER['REMOTE_ADDR'];
		$hash = generateLoginHash();
		$db->query("UPDATE LOW_PRIORITY dle_users SET lastdate='{$_TIME}', logged_ip='$ip',hash='$hash' WHERE name='$login'");
		if($action == 'auth') {
			//$geoBase = new IPGeoBase();
			//$geoBase->getIP($ip);

		if(
		!file_exists($config['clientsDir']."assets")||
		!file_exists($config['clientsDir'].$client."/bin/")||
		!file_exists($config['clientsDir'].$client."/mods/")||
		!file_exists($config['clientsDir'].$client."/natives/")||
		!file_exists($config['clientsDir'].$client."/servers.dat")) {
			die(Security::encrypt("client<$> $client", $config['key1']));
		}
		
		if(file_exists($config['clientsDir'].$client."/coremods/")) {
			$arg = "checkfiles(".$config['clientsDir'].".$client."/coremods/")";
		}

        $md5user  = strtoint(xorencode(str_replace('-', '', uuidConvert($realUser)), $config['protectionKey']));
        $md5zip	  = @md5_file($config['clientsDir'].$client."/servers.dat");
        $md5ass	  = @md5_file($config['clientsDir']."assets.zip");
        $sizezip  = @filesize($config['clientsDir'].$client."/servers.dat");
        $sizeass  = @filesize($config['clientsDir']."assets.zip");
		$usrsessions = $config['masterversion']."<:>".$md5user."<:>".$md5zip."<>".$sizezip."<:>".$md5ass."<>".$sizeass."<br>".$realUser.'<:>'.strtoint(xorencode($sessid, $config['protectionKey'])).'<br>'.$acesstoken.'<br>';

        if($config['temp']) {
	        $filecache = $config['clientsDir'].$client.'/'.$client;
			if (file_exists($filecache)) {
				 $fp = fopen($filecache, "r");
				 $hash_md5 = fgets($fp);
				 fclose($fp);
			} else {
				$hash_md5 = hashc($client,$config['clientsDir']);
				try {
				$fp = fopen($filecache, "w");
				fwrite($fp, $hash_md5);
				fclose($fp);
				} catch(Exception $e){
					die($e);
				}
			}
	    } else {
	    	$hash_md5 = hashc($client,$config['clientsDir']);
	    }
        echo Security::encrypt($usrsessions.$hash_md5, $config['key1']);
	}
	
	} catch(PDOException $pe) {
		die(Security::encrypt("errorsql<$>", $config['key1']).$pe);
	}