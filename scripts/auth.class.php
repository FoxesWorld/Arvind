<?php
/*
=====================================================
 Hey you! Come here! | Auth
-----------------------------------------------------
 https://FoxesWorld.ru/
-----------------------------------------------------
 Copyright (c) 2016-2021  FoxesWorld
-----------------------------------------------------
 This code is reserved
-----------------------------------------------------
 File: auth.class.php
-----------------------------------------------------
 Version: 0.0.1.0 Experimental
-----------------------------------------------------
 Usage: Authorisation
=====================================================
*/
	class auth {
		
		private $debug = false;
		protected $inputLogin;
		protected $realUser;
		protected $inputPassword;
		protected $realPass;
		protected $checkPass;
		protected $launchermd5;
		
		private $accessToken;
		
		function __construct($ctoken, $login, $postPass, $launchermd5, $debug){
			global $config, $db;
			$this->debug 			= $debug;
			$this->inputLogin 		= $this->pregMatch($login);
			$this->inputPassword 	= $this->pregMatch($postPass);
			$this->$launchermd5 	= $this->pregMatch($launchermd5);
			
			if($ctoken == "null") {
				if($config['crypt'] === 'hash_md5' || $config['crypt'] === 'hash_foxy') {
					$this->selectPassword($this->inputLogin);
				}
				$this->checkPass = hash_name($config['crypt'], $this->realPass, $this->inputPassword, @$salt);
				$this->antiBrute();
				$this->accessToken = token();
			} else {
				$this->accessToken = $this->inputPassword;
			}

			$sessid = token();
			$stmt = $db->prepare("SELECT user, token FROM usersession WHERE user = :login");
			$stmt->bindValue(':login', $this->realUser);
			$stmt->execute();
			$rU = $stmt->fetch(PDO::FETCH_ASSOC);
			if($rU['user'] != null) {
				$this->realUser = $rU['user'];
			}

			if($ctoken != "null") {
				if($rU['token'] != $this->accessToken || $this->inputLogin != $this->realUser) {
						exit(Security::encrypt("errorLogin<$>", $config['key1']));
				}
			}
				$this->setSession($this->realUser);
			}

		private function selectPassword($login) {
			global $db, $config;
				$stmt = $db->prepare("SELECT ".$config['db_columnUser'].",".$config['db_columnPass']." FROM ".$config['db_table']." WHERE BINARY ".$config['db_columnUser']." = :login");
				$stmt->bindValue(':login', $login);
				$stmt->execute();
				$stmt->bindColumn($config['db_columnPass'], $this->realPass);
				$stmt->bindColumn($config['db_columnUser'], $this->realUser);
				if($this->debug === true) {
					echo '<div style="border: 1px solid black; padding: 5px; border-radius: 10px; width: fit-content; margin: 15px;">
				'.'<h1 style="font-size: large;margin: 0;">Selected data</h1><br>'.
				'Login: <b>'.$this->realUser.'</b>'.
				'Pass: <b>'. $this->realPass.'</b>';
				}
				$stmt->fetch();	
		}
		
		private function setSession ($userToSet){
			global $db;
			if($this->inputLogin == $userToSet) {
				if($ctoken == "null") {
					$stmt = $db->prepare("UPDATE usersession SET session = '$sessid', token = :token WHERE user= :login");
					$stmt->bindValue(':token', $this->accessToken);
				} else {
					$stmt = $db->prepare("UPDATE usersession SET session = '$sessid' WHERE user = :login");
				}

				$stmt->bindValue(':login', $this->realUser);
				$stmt->execute();
			} else {
				if($ctoken == "null" || $this->inputLogin != $userToSet) {
					$stmt = $db->prepare("INSERT INTO usersession (user, session, md5, token) VALUES (:login, '$sessid', :md5, '$acesstoken')");
					$stmt->bindValue(':login', $this->realUser);
					$stmt->bindValue(':md5', str_replace('-', '', uuidConvert($this->realUser)));
					$stmt->execute();
				}
			}
		}
		
		private function pregMatch($toCheck){
			global $config;
			if (!preg_match("/^[a-zA-Z0-9_-]+$/", $toCheck)) {
				exit(Security::encrypt("errorLogin<$>", $config['key1']));
			}
			
			return $toCheck;
		}

		private function antiBrute(){
			global $config, $db;
				if($config['useantibrut'] === true) {
				$stmt = $db->getRow("SELECT sip,time FROM sip WHERE sip='".REMOTE_IP."' And time >'".CURRENT_TIME."'");
				$bannedIP = $stmt['sip'];
				if(REMOTE_IP == $bannedIP) {
					$stmt = $db->run("DELETE FROM sip WHERE time < '".CURRENT_TIME."';");
					exit(Security::encrypt("temp<$>", $config['key1']));
				}
					
				if ($this->inputLogin != $this->realUser) {
					$stmt = $db->run("INSERT INTO sip (sip, time)VALUES ('".REMOTE_IP."', '".$config['bantime']."')");
					exit(Security::encrypt("errorLogin<$>", $config['key1']));
				}
				
				if(!strcmp($this->realPass,$this->checkPass) == 0 || !$this->realPass) {
					$stmt = $db->run("INSERT INTO sip (sip, time)VALUES ('".REMOTE_IP."', '".$config['bantime']."')");
					exit(Security::encrypt("errorLogin<$>", $config['key1']));
				}

				} else {
					if ($this->checkPass != $this->realPass) {
					die(Security::encrypt('errorLogin<$>', $config['key1']));
				}
			}
		}
	}