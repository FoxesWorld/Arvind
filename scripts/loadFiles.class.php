<?php
/*
=====================================================
 Let's load your data! | loadFiles
-----------------------------------------------------
 https://Foxesworld.ru/
-----------------------------------------------------
 Copyright (c) 2016-2021  FoxesWorld
-----------------------------------------------------
 This code is reserved
-----------------------------------------------------
 File: loadFiles.class.php
-----------------------------------------------------
 Version: 0.0.2.4 Alpha
-----------------------------------------------------
 Usage: Files out + crypt + size
=====================================================
*/

	if(!defined('INCLUDE_CHECK')) {
		die ("Hacking Attempt!");
	}

	class loadFiles {
		
		private $client;
		private $vesion;
	
		function __construct($client, $sessid, $acesstoken, $realUser){
			global $config;
				$this->client = $client;
				$serverInfo = serversListArray("SELECT * FROM `servers` WHERE Server_name = '".$this->client."'");
				$this->version = $serverInfo[0]['version'];
				$this->checkStructure();

				$md5user  		  = strtoint(xorencode(str_replace('-', '', uuidConvert($realUser)), $config['protectionKey']));
				$md5ServersDat	  = @md5_file($config['clientsDir']."clients/".$client."/servers.dat");
				$sizeServersDat   = @filesize($config['clientsDir']."clients/".$client."/servers.dat");
				$usrsessions 	  = $config['md5launcherjar']."<:>".$md5user."<:>".$md5ServersDat."<>".$sizeServersDat."<:><br>".$realUser.'<:>'.strtoint(xorencode($sessid, $config['protectionKey'])).'<br>'.$acesstoken.'<br>';
				$usrsessionsJSON  = '{
					"launcherMD5":  "'.$config['md5launcherjar'].'",
					"userMD5": "'.$md5user.'",
					"md5ServersDat": "'.$md5ServersDat.'".
					"serversDatSize": "'.$sizeServersDat.'",
					"userLogin": "'.$realUser.'",
					"strToInt": "'.strtoint(xorencode($sessid, $config['protectionKey'])).'",
					"accessToken": "'.$acesstoken.'"}';

			if($config['temp'] === true) {
				checkWriteRights();
				$this->writeTempFile($client);
			} else {
				$filesAndDirs = hashcVersion($client);
			}
			$jsonOUT = '{"userData": "'.$usrsessions.'", "filesAndDirs": "'.$filesAndDirs.'"}';
			echo Security::encrypt(JSONanswer('type', 'success', 'message', $jsonOUT), $config['key1']);
		}

		private function checkStructure() {
			global $config;

			$clientStructureCheck = array($config['clientsDir']."assets",
			$config['clientsDir']."versions/".$this->version, 
			$config['clientsDir']."versions/".$this->version."/libraries",
			$config['clientsDir']."versions/".$this->version."/".$this->version.".jar",
			$config['clientsDir']."versions/".$this->version."/natives/");
			
			foreach($clientStructureCheck as $key) {
				if(!file_exists($key)) {
					die(Security::encrypt(JSONanswer('type', 'error', 'message', "client<$> ".$this->client), $config['key1']));
				}
			}
		}
		
		private function writeTempFile($client){
			global $config;
			$filecache = $config['clientsDir']."clients/".$client.'/'.$client;
			
			if (file_exists($filecache)) {
				$fp = fopen($filecache, "r");
				$hash_md5 = fgets($fp);
				fclose($fp);
			} else {
				$hash_md5 = hashcVersion($client);
				try {
					$fp = fopen($filecache, "w");
					fwrite($fp, $hash_md5);
					fclose($fp);
				} catch(Exception $e){
					die($e);
				}
			}
		}
	}