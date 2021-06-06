<?php
/*
=====================================================
 HasJoined | AuthLib
-----------------------------------------------------
 https://Foxesworld.ru/
-----------------------------------------------------
 Copyright (c) 2016-2021  FoxesWorld
-----------------------------------------------------
 This code is reserved
-----------------------------------------------------
 File: hasJoind.php
-----------------------------------------------------
 Version: 0.1.7 Alpha
-----------------------------------------------------
 Usage: UserJoin on server
=====================================================
*/
	define('INCLUDE_CHECK',true);
	define('CONFIG', true);
	require ('../config.php');
	include ("../database.php");
	
	if(isset($_GET['username']) && isset($_GET['serverId'])) {
		$hasJoined = new hasJoined($_GET['username'], $_GET['serverId']);
	}

	class hasJoined {
		
		private $debug;
		private $user;
		private $realUser;
		private $md5User;
		private $serverid;
		private $badLogin;
		
		function __construct($user, $serverid, $debug = false){
			try {
				$this->debug	= $debug;
				$this->user 	= $this->pregMatch($user);
				$this->serverid = $this->pregMatch($serverid);
				$this->realUserGet();
				$this->userCheck();
			} catch(PDOException $pe) {
				die("Ошибка".$pe);
			}
		}
		
		private function pregMatch($String){
			if (!preg_match("/^[a-zA-Z0-9_-]+$/", $String)){
				exit($this->answerConstructor('Bad login','Левые символы в нике!'));
			} else {
				if($this->debug === true){
					echo $String.' - passed<br>';
				}
				return $String;
			}
		}
		
		private function realUserGet(){
			global $config;
				$db = new db($config['db_user'],$config['db_pass'],$config['db_database']);
				$stmt = $db->prepare("SELECT user,md5 FROM usersession WHERE user = :user and server = :serverid");
				$stmt->bindValue(':user', $this->user);
				$stmt->bindValue(':serverid', $this->serverid);
				$stmt->execute();
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				$this->realUser = $row['user'];
				$this->md5User  = $row['md5'];
		}
		
		private function userCheck(){
			global $config, $skinsArray;

			if($this->user == $this->realUser)	{

				if (file_exists($skinsArray['cloaksAbsolute'].$this->realUser.'.png')) {
					$cape = 
				',
						"CAPE":
						{
							"url":"'.$$skinsArray['capeUrl'].'?/'.$this->realUser.'$"
						}';
				} else {
					$cape = '';
				}
				$base64 ='
				{
					"timestamp":"'.CURRENT_TIME.'","profileId":"'.$this->md5User.'","profileName":"'.$this->realUser.'","textures":
					{
						"SKIN":
						{
							"url":"'.$skinsArray['skinUrl'].$this->realUser.'.png"
						}'.$cape.'
					}
				}';
				echo '
				{
					"id":"'.$this->md5User.'","name":"'.$this->realUser.'","properties":
					[
						{
							"name":"textures","value":"'.base64_encode($base64).'","signature":"'.$config['letterHeadLine'].'"
						}
					]
				}';
				
			} else { 
				exit($this->answerConstructor("Bad login", "Bad login"));
			}
		}

		private function answerConstructor($title, $message){
			$answer = array('error' => $title,'errorMessage' => $message);

			return json_encode($answer);
		}
	}