<?php
/*
=====================================================
 Skins - you look nice today !| AuthLib
-----------------------------------------------------
 https://arcjetsystems.ru/
-----------------------------------------------------
 Copyright (c) 2016-2021  FoxesWorld
-----------------------------------------------------
 This code is reserved
-----------------------------------------------------
 File: skins.class.php
-----------------------------------------------------
 version: 0.1.8 WIP
-----------------------------------------------------
 Usage: Parses skins&Cloaks
=====================================================
*/
	header('Content-Type: text/html; charset=utf-8');
	define('INCLUDE_CHECK',true);
	define('CONFIG', true);
	require ('../config.php');
	include("../database.php");

	if(isset($_GET['user'])){
		$skin = new skin($_GET['user');
	} else {
		die ("No request!");
	}

	class skin {

		private $md5User;
		private $skinUrl;
		private $cloakUrl;
		private $skinStatus;
		private $cloakStatus;
		private $realUser;
		private $skin;
		private $cloak;
		private $spl;

		function __construct($md5) {
			global $config, $skinsArray;

		try {
				$this->md5User = $this->pregMatch($md5);
				$this->getRealUser();
				$this->skinUrl 	   = $skinsArray['skinUrl'].$this->realUser.'png';
				$this->cloakUrl    = $skinsArray['capeUrl'].$this->realUser.'.png';
				$this->skinStatus  = file_exists(SITE_ROOT.$config['uploaddirp'].'/'.$this->realUser.'.png');
				$this->cloakStatus = file_exists(SITE_ROOT.$config['uploaddirs'].'/'.$this->realUser.'.png');
				
				$this->JSONoutput();
			} catch(PDOException $pe) {
				die($pe);
			}
		}

		private function getRealUser($debug = false){
			global $config;
				$db = new db($config['db_user'],$config['db_pass'],$config['db_database']);
				$stmt = $db->prepare("SELECT user FROM usersession WHERE md5= :md5");
				$stmt->bindValue(':md5', $this->md5User);
				$stmt->execute();
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				$this->realUser = $row['user'];
				
				if($debug === true){
					echo "<b>RealUser: </b>".$this->realUser;
				} else {
					if($this->realUser == null) {
						exit('Real user does not match!');
					}
				}
		}

		private function JSONoutput(){
			global $config, $skinsArray;
				if ($this->cloakStatus) {
					$this->cloak = '"CAPE":{"url":"'.$this->cloakUrl.'"}';
				} else {
					$this->cloak = '';
				}

				if ($this->skinStatus) {
					$this->skin ='"SKIN":{"url":"'.$this->skinUrl.'"}';
				} else {
					$this->skin ='"SKIN":{"url":"'.$skinsArray['skinUrl'].'default.png"}';
				}

				if ($this->skinStatus && $this->cloakStatus) {
					$this->spl = ',';
				} else {
					$this->spl = '';
				}

				$base64 ='{"timestamp":"'.CURRENT_TIME.'","profileId":"'.$this->md5User.'","profileName":"'.$this->realUser.'","textures":{'.$this->skin.$this->spl.$this->cloak.'}}';
				echo '{"id":"'.$this->md5User.'","name":"'.$this->realUser.'","properties":[{"name":"textures","value":"'.base64_encode($base64).'","signature":"'.$config['letterHeadLine'].'"}]}';
		}

		private function pregMatch($String){
			if (!preg_match("/^[a-zA-Z0-9_-]+$/", $String)){
				exit;
			} else {
				return $String;
			}
		}
		
	}