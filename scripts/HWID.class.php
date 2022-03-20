<?php

class HWID {
					
		private $check;
		private $login;
		private $HWID;
		private $realHWID;
		private $debug;
		private $DB;
					
		function __construct($login, $HWID, $debug = false){
			global $config;
			$this->debug = $debug;
			$this->check = false;
			$this->login = $login;
			$this->HWID = $HWID;
			$this->realHWID = $this->getHWID();
		}
					
		function getHWID(){
				global $config;
				$this->DB = new db($config['db_user'],$config['db_pass'],$config['dbname_launcher']);
				$query = "SELECT * FROM usersHWID WHERE login = '".$this->login."'";
				$selectedValue = $this->DB->getRow($query);
				$this->realHWID = $selectedValue["hwid"];
				$realUser = $selectedValue["login"];
						
				return $this->realHWID;
		}
					
		function insertHWID(){
			$query = "INSERT INTO `usersHWID`(`login`, `hwid`) VALUES ('".$this->login."','".$this->HWID."')";
			$this->DB->run($query);
		}
					
		function checkHWID(){
				if($this->HWID !== $this->realHWID) {
					$this->check = false;
					if($this->realHWID === null){
							$this->check = true;
							$this->insertHWID();
							if($this->debug) {
								echo('{"message": "Setting '.$this->HWID.' as '.$this->login.'`s new HWID"}');
						}
					} else {
						if($this->debug) {
							echo('{"message": "Incorrect HWID!"}');
						}
					}
				} else {
					$this->check = true;
					if($this->debug) {
						echo('{"message": "'.$this->HWID.' HWID is correct for '.$this->login.'"}');
					}
				}
				return $this->check;
			}
		}