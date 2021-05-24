<?php
/*
=====================================================
 Come here, I may know you! | auth
-----------------------------------------------------
 https://Foxesworld.ru/
-----------------------------------------------------
 Copyright (c) 2016-2021  FoxesWorld
-----------------------------------------------------
 This code is reserved
-----------------------------------------------------
 File: auth.class.php
-----------------------------------------------------
 Version: 0.0.2.2 Experimental
-----------------------------------------------------
 Usage: Auth + SetSession + LoadFiles
=====================================================
*/

	class auth {
		
		protected $action;
		protected $client;
		protected $inputUser;
		protected $inputPass;
		protected $ctoken;
		
		/* RealData */
		protected $realPass;
		protected $realUser;
		protected $accessToken;
		protected $sessID;
		
		/* ToCheck */
		protected $checkPass;
		protected $launchermd5;
		
		function __construct ($action, $client, $login, $postPass, $launchermd5, $ctoken){
			require_once (SCRIPTS_DIR.'loadFiles.class.php');
			require_once (SCRIPTS_DIR.'geoIP.class.php');
			global $db, $config;
					try {
						
						$this->launchermd5  = $this->pregMatch($launchermd5);
						$this->action 		= $this->pregMatch($action);
						$this->inputUser 	= $this->pregMatch($login);
						$this->inputPass 	= $this->pregMatch($postPass);
						$this->ctoken		= $this->pregMatch($ctoken);
						if($this->launchermd5 === $config['md5launcherjar']) {
							if($this->ctoken == "null") {
									if($config['crypt'] === 'hash_md5' || $config['crypt'] === 'hash_foxy') {
										$this->selectRealData($this->inputUser);
									}
									$this->checkPass = hash_name($config['crypt'], $this->realPass, $this->inputPass);
									$this->antiBrute();
									$this->accessToken = token();
							} else {
								$this->accessToken = $this->inputPass;
							}

							$this->sessID = token();
							$stmt = $db->prepare("SELECT user, token FROM usersession WHERE user= :login");
							$stmt->bindValue(':login', $this->inputUser);
							$stmt->execute();
							$rU = $stmt->fetch(PDO::FETCH_ASSOC);
							if($rU['user'] != null) {
								$this->realUser = $rU['user'];
							}

							if($this->ctoken != "null") {
								if($rU['token'] != $this->accessToken || $this->inputUser != $this->realUser) {
										exit(Security::encrypt("errorLogin<$>", $config['key1']));
								}
							}
							$this->setSession();

							$db->run("UPDATE LOW_PRIORITY ".$config['db_table']." SET lastdate='".CURRENT_TIME."', logged_ip='".REMOTE_IP."' WHERE name='$this->inputUser'");
							if($this->action == 'auth') {
								$geoplugin = new geoPlugin();
								$loadFiles = new loadFiles($client, $this->sessID, $this->accessToken, $this->realUser);
							}
						}
			} catch(PDOException $pe) {
				die(Security::encrypt("errorsql<$>", $config['key1']).$pe);
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
						
					if ($this->inputUser != $this->realUser) {
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
		
		private function setSession() {
			global $db;
			if($this->inputUser == $this->realUser) {
				if($this->ctoken == "null") {
					$stmt = $db->prepare("UPDATE usersession SET session = '".$this->sessID."', token = :token WHERE user = :login");
					$stmt->bindValue(':token', $this->accessToken);
				} else {
					$stmt = $db->prepare("UPDATE usersession SET session = '".$this->sessID."' WHERE user = :login");
				}

				$stmt->bindValue(':login', $this->realUser);
				$stmt->execute();

			} else {
				if($this->ctoken == "null" || $this->inputUser != $this->realUser) {
					$stmt = $db->prepare("INSERT INTO usersession (user, session, md5, token) VALUES (:login, '".$this->sessID."', :md5, '".$this->accessToken."')");
					$stmt->bindValue(':login', $this->realUser);
					$stmt->bindValue(':md5', str_replace('-', '', uuidConvert($this->realUser)));
					$stmt->execute();
				}
			}
		}
		
		private function selectRealData($login) {
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
	}