<?php 

	class antiBrute {
		
		private $ip;
		private $db;
		private $inputUser;
		private $realUser;
		private $realPass;
		private $checkPass;
		private $currentTime;
		
		/* Ban data */
		private $bannedIP;
		private $bannedAttempts;
		private $bannedID;
		private $bannedTime;
		
		function __construct($ip, $db, $inputUser, $realUser, $realPass, $checkPass, $time){
				/* INIT */
				$this->ip 		   = $ip;
				$this->db 		   = $db;
				$this->inputUser   = $inputUser;
				$this->realUser    = $realUser;
				$this->realPass	   = $realPass;
				$this->checkPass   = $checkPass;
				$this->currentTime = $time;
				$this->selectBanData();
				$this->checkBan();
		}
		
		private function selectBanData(){
			$query = "SELECT * FROM sip WHERE sip='".$this->ip."' And time >'".$this->currentTime."'";
			$stmt = $this->db->getRow($query);
			$this->bannedTime 		= $stmt['time'];
			$this->bannedID 		= $stmt['id'];
			$this->bannedIP 		= $stmt['sip'];
			$this->bannedAttempts   = $stmt['attempts'];
		}
		
		private function checkBan(){
			if($this->ip == $this->bannedIP && $this->bannedAttempts >= 3 && $this->ip === $this->bannedIP) {
				$this->antiBriteAttemptsIncrease();
				$stmt = $this->db->run("DELETE FROM sip WHERE time < '".$this->currentTime."';");
				exit(Security::encrypt("temp<$>", $config['key1']));
			} else {
				$this->insertBan();
			}
		}
		
		private function insertBan(){
			global $config;

			if ($this->inputUser != $this->realUser && $this->bannedIP !== $this->ip) {
				$stmt = $this->db->run("INSERT INTO sip (sip, time)VALUES ('".$this->ip."', '".$config['bantime']."')");
				exit(Security::encrypt("errorLogin<$>", $config['key1']));
			} else {
				if(!strcmp($this->realPass,$this->checkPass) == 0 || !$this->realPass) {
					if($this->bannedIP !== $this->ip) {
						$stmt = $this->db->run("INSERT INTO sip (sip, time)VALUES ('".$this->ip."', '".$config['bantime']."')");
						exit(Security::encrypt("errorLogin<$>", $config['key1']));
					}
				}
			}
		}
			
		private function antiBriteAttemptsIncrease(){
				$query = "UPDATE sip SET attempts=attempts+1 WHERE sip='".$this->ip."'";
				$stmt = $this->db->run($query);
		}
	}