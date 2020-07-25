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
 Версия: 0.0.15 Alpha
-----------------------------------------------------
 Назначение: Ядро вебчасти, сочетающее в себе всю её функциональность
=====================================================
*/
header('Content-Type: text/html; charset=utf-8');
Error_Reporting(E_ALL);
Ini_Set('display_errors', true);
define('INCLUDE_CHECK',true); //Security Define
include ("scripts/functions.inc.php");  //All Functions
include_once ("scripts/actionScript.php");  //Action requests
//================================================================

	if(isset($_POST['action'])) {
		include("database.php");
		$x  = $_POST['action'];
		$x = str_replace(" ", "+", $x);
		$yd = Security::decrypt($x, $key2);
		
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
		exit(Security::encrypt("errorLogin<$>", $key1));
    }
	
	if(!file_exists($uploaddirs)) {
		die ("Путь к скинам не является папкой!");
	}
	
	if(!file_exists($uploaddirp)) {
		die ("Путь к плащам не является папкой!");
	}
	

    if($ctoken == "null") {
			if($crypt === 'hash_md5' || $crypt === 'hash_foxy') {
				$stmt = $db->prepare("SELECT $db_columnUser,$db_columnPass FROM $db_table WHERE BINARY $db_columnUser = :login");
				$stmt->bindValue(':login', $login);
				$stmt->execute();
				$stmt->bindColumn($db_columnPass, $realPass);
				$stmt->bindColumn($db_columnUser, $realUser);
				$stmt->fetch();
			}
			$checkPass = hash_name($crypt, $realPass, $postPass, @$salt);

			if($useantibrut) {
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
					exit(Security::encrypt("temp<$>", $key1));
				}
				
				if ($login != $realUser) {
					$stmt = $db->prepare("INSERT INTO sip (sip, time)VALUES ('$ip', '$bantime')");
					$stmt->execute();
					exit(Security::encrypt("errorLogin<$>", $key1));
				}
				if(!strcmp($realPass,$checkPass) == 0 || !$realPass) {
					$stmt = $db->prepare("INSERT INTO sip (sip, time)VALUES ('$ip', '$bantime')");
					$stmt->execute();
					exit(Security::encrypt("errorLogin<$>", $key1));
				}

			} else {
				if($checkPass !=  $realPass)  die(Security::encrypt('errorLogin<$>', $key1));
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
	        	exit(Security::encrypt("errorLogin<$>", $key1));
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
    
	if($action == 'auth') {
		
		if(
		!file_exists($clientsDir."assets")||
		!file_exists($clientsDir.$client."/bin/")||
		!file_exists($clientsDir.$client."/mods/")||
		!file_exists($clientsDir.$client."/natives/")||
		!file_exists($clientsDir.$client."/config.zip")) {
			die(Security::encrypt("client<$> $client", $key1));
		}
		
		if(file_exists($clientsDir.$client."/coremods/")) {
			$arg = "checkfiles($clientsDir.$client.'/coremods/')";
		}

        $md5user  = strtoint(xorencode(str_replace('-', '', uuidConvert($realUser)), $protectionKey));
        $md5zip	  = @md5_file($clientsDir.$client."/config.zip");
        $md5ass	  = @md5_file($clientsDir."assets.zip");
        $sizezip  = @filesize($clientsDir.$client."/config.zip");
        $sizeass  = @filesize($clientsDir."assets.zip");
		$usrsessions = "$masterversion<:>$md5user<:>".$md5zip."<>".$sizezip."<:>".$md5ass."<>".$sizeass."<br>".$realUser.'<:>'.strtoint(xorencode($sessid, $protectionKey)).'<br>'.$acesstoken.'<br>';

        if($temp) {
	        $filecache = $clientsDir.$client.'/'.$client;
			if (file_exists($filecache)) {
				 $fp = fopen($filecache, "r");
				 $hash_md5 = fgets($fp);
				 fclose($fp);
			} else {
				$hash_md5 = hashc($client,$clientsDir);
				$fp = fopen($filecache, "w");
				fwrite($fp, $hash_md5);
				fclose($fp);
			}
	    } else {
	    	$hash_md5 = hashc($client,$clientsDir);
	    }
        echo Security::encrypt($usrsessions.$hash_md5, $key1);
	}
	
	
	} catch(PDOException $pe) {
		die(Security::encrypt("errorsql<$>", $key1).$pe);
	}