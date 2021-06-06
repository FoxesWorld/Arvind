<?php

	header('Content-Type: text/html; charset=utf-8');
	define('INCLUDE_CHECK',true);
	define('CONFIG', true);
	require ('../config.php');
	include("../database.php");
	
	$skins = new skins('miomoor');

	class skins {
		
		private $userLogin;
		private $skinUrl;
		private $cloakUrl;
		private $skinStatus;
		private $cloakStatus;
		private $skin;
		private $cloak;
		private $spl;
		private $md5User;

		function __construct($userLogin) {
			global $config, $skinsArray;
			
			$this->userLogin = $this->pregMatch($userLogin);
			$this->skinUrl 	   = $skinsArray['skinUrl'].$this->realUser.'png';
			$this->cloakUrl    = $skinsArray['capeUrl'].$this->realUser.'.png';
			$this->skinStatus  = file_exists(SITE_ROOT.$config['uploaddirp'].'/'.$this->realUser.'.png');
			$this->cloakStatus = file_exists(SITE_ROOT.$config['uploaddirs'].'/'.$this->realUser.'.png');
			$this->getRealUser;
			$this->base64Gen();
		}
		
		private function getSkin(){
			if ($this->skinStatus) {
				$skin = '"SKIN":
						{
							"url":"'.$this->skinUrl.'"
						}';
			} else {
				$skin = '"SKIN":{"url":"'.$skinsArray['skinUrl'].'default.png"}';
			}
			
			return $skin;
		}
		
		private function getCape(){
			if ($this->cloakStatus) {
				$cape = 
					',
				"CAPE":
				{
					"url":"'.$this->cloakUrl.'"
				}';
			} else {
				$cape = '';
			}
			
			return $cape;
		}
		
		private function getRealUser($debug = false){
			global $config;
				$db = new db($config['db_user'],$config['db_pass'],$config['db_database']);
				$stmt = $db->prepare("SELECT user FROM usersession WHERE md5= :md5");
				$stmt->bindValue(':md5', md5($this->userLogin));
				$stmt->execute();
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				$this->userLogin = $row['user'];
				
				if($debug === true){
					echo "<b>RealUser: </b>".$this->userLogin;
				} else {
					if($this->userLogin == null) {
						exit('Real user does not match!');
					}
				}
		}
		
		private function base64Gen(){
			global $config;
			$base64 = '{
				"timestamp":"'.CURRENT_TIME.'","profileId":"'.$this->md5User.'","profileName":"'.$this->userLogin.'","textures":
				{
				"SKIN":
					{
						"url":"'.$this->skinUrl.'"
					}'
					.$this->getCape().'
				}
				}';
				
				echo '{"id":"'.$this->md5User.'","name":"'.$this->userLogin.'","properties":[{"name":"textures","value":"'.base64_encode($base64).'","signature":"'.$config['letterHeadLine'].'"}]}';
		}

		private function pregMatch($String){
			if (!preg_match("/^[a-zA-Z0-9_-]+$/", $String)){
				exit;
			} else {
				return $String;
			}
		}
		
	}