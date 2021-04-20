<?php
/*
=====================================================
 Functions
-----------------------------------------------------
 https://Foxesworld.ru/
-----------------------------------------------------
 Copyright (c) 2016-2021  FoxesWorld
-----------------------------------------------------
 Данный код защищен авторскими правами
-----------------------------------------------------
 Файл: functions.inc.php
-----------------------------------------------------
 Версия: 0.0.23.14 Release Candidate
-----------------------------------------------------
 Назначение: Различные функции
=====================================================
*/
	verifySSL();
	if(!defined('INCLUDE_CHECK')) {
		require ($_SERVER['DOCUMENT_ROOT'].'/index.php');
	}
	
	if(defined('DEBUG_LOGS')){
		Error_Reporting(E_ALL);
		Ini_Set('display_errors', true);
	}
	
	if(defined('NO_DEBUG')){
		error_reporting(0);
	}

//================================================================
			header('Content-Type: text/html; charset=utf-8');
			$_TIME = time();
			$dateToday = date("d.m.Y");

			function xorencode($str, $key) {
					while(strlen($key) < strlen($str)) {
						$key .= $key;
					}
					return $str ^ $key;
			}

			function strtoint($text) {
					$res = "";
					for ($i = 0; $i < strlen($text); $i++) $res .= ord($text{$i}) . "-";
					$res = substr($res, 0, -1);
					return $res;
			}

			function strlen_8bit($binary_string) {
					if (function_exists('mb_strlen')) {
						return mb_strlen($binary_string, '8bit');
					}
					return strlen($binary_string);
			}
	
			function substr_8bit($binary_string, $start, $length) {
					if (function_exists('mb_substr')) {
						return mb_substr($binary_string, $start, $length, '8bit');
					}
					return substr($binary_string, $start, $length);
			}

			function pass_get_info($hash) {
					$return = true;
					if (substr_8bit($hash, 0, 4) == '$2y$' && strlen_8bit($hash) == 60) {
						$return = false;
					}
					return $return;
			}
	
			function pass_verify($password, $hash) {
					$ret = crypt($password, $hash);
					
					if (!is_string($ret) || strlen_8bit($ret) != strlen_8bit($hash) || strlen_8bit($ret) <= 13) {
						return false;
					}
					$status = 0;
					for ($i = 0; $i < strlen_8bit($ret); $i++) {
						$status |= (ord($ret[$i]) ^ ord($hash[$i]));
					}
					return $status === 0;
			}

			//Full JSON (Answers in JSON (2 rows))
			function JSONanswer($typeName, $typeValue, $messageName, $messageValue){
				$array = array($typeName => $typeValue,$messageName => $messageValue);
				$array = json_encode($array);
				return $array;
			}

			function dbPrepare(){
				global $config;
				$db = new db($config['db_user'],$config['db_pass'],$config['dbname_launcher']);
				try {
					$stmt = $db->prepare("
					CREATE TABLE IF NOT EXISTS `usersession` (
					`user` varchar(255) DEFAULT 'user',
					`session` varchar(255) DEFAULT NULL,
					`server` varchar(255) DEFAULT NULL,
					`token` varchar(255) DEFAULT NULL,
					`realmoney` int(255) DEFAULT '0',
					`md5` varchar(255) DEFAULT '0',
					PRIMARY KEY (`user`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
					");
					$stmt->execute();
					$stmt = $db->prepare("
					CREATE TABLE IF NOT EXISTS `sip` (
					  `time` varchar(255) NOT NULL,
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `sip` varchar(16) DEFAULT NULL,
					  PRIMARY KEY (`id`) USING BTREE
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=0;
					");
					$stmt->execute();
					$stmt = $db->prepare("
					CREATE TABLE IF NOT EXISTS `servers` (
					  `id` int(100) NOT NULL,
					  `Server_name` varchar(120) NOT NULL,
					  `adress` varchar(100) NOT NULL,
					  `port` int(90) NOT NULL,
					  `srv_image` varchar(100) NOT NULL,
					  `version` varchar(100) NOT NULL,
					  `story` varchar(900) NOT NULL,
					  `srv_group` int(100) NOT NULL,
					  `enabled` int(1) NOT NULL DEFAULT 1
					) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
					ALTER TABLE `servers`  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT");
					$stmt->execute();
				} catch(PDOException $pe) {
					$query = strval($stmt->queryString);
					die(display_error($pe->getMessage(), $error_num = 200, $query));
				}
			}

			//Checking ability to write cacheFiles
			function checkWriteRights(){
				$selector = "SELECT * FROM servers";
				$counter = 0;
				$srvList = serversListArray($selector);
				    foreach ($srvList as $key){
					$clientPath = FILES_DIR.'clients/clients/';
					$clientPath .= $srvList[$counter]['serverName'];
						if($rights = substr(decoct(fileperms($clientPath)), -4) != 777) {
							echo "<b>".$srvList[$counter]['serverName']."</b> can't create a config file the rights are - <b>".$rights."</b><br>";
						}
					$counter++;
				}
			}

			function hash_name($ncrypt, $realPass, $postPass, $salt) {
					$cryptPass = false;

					if ($ncrypt === 'hash_md5' or $ncrypt === 'hash_launcher') {
							$cryptPass = md5($postPass);
					}

					if ($ncrypt === 'hash_foxy') {
						if(pass_get_info($realPass)) {
							$cryptPass = md5(md5($postPass));
						} else {
							if(pass_verify($postPass, $realPass)) {
								$cryptPass = $realPass;
							} else {
								$cryptPass = "0";
							}
						}
					}
					return $cryptPass;
			}
		
			function generateLoginHash(){
				if(function_exists('openssl_random_pseudo_bytes')) {
				$stronghash = md5(openssl_random_pseudo_bytes(15));
				} else { 
				$stronghash = md5(uniqid( mt_rand(), TRUE )); }
				$salt = sha1( str_shuffle("abcdefghjkmnpqrstuvwxyz0123456789") . $stronghash );
				$hash = '';					
				for($i = 0; $i < 9; $i ++) {
					$hash .= $salt[mt_rand( 0, 39 )];
				}
				$hash = md5( $hash );
				
				return $hash;
			}

			//Parses servers for any needs
			function serversListArray($selector){
				global $config;
				$serversList = array();
				$counter = 0;
				$db = new db($config['db_user'],$config['db_pass'],$config['dbname_launcher']);
				$data = $db->getRows($selector);
				foreach ($data as $row) {
					if($row['enabled']){
						$serversList[] = array(
						'serverName' => $row['Server_name'],
						'adress' => $row['adress'],
						'port' => $row['port'],
						'version' => $row['version'],
						'serverImage' => $row['srv_image'],
						'story' => $row['story'],
						'status' => $row['enabled']);
						$counter++;
					}
				}
				return $serversList;
			}

			//Full JSON
			function availableServers($login){
				if(!$login){
					$userGroup = 4;
				} else {
					$userGroup = getUserData($login,'user_group');
				}
					if($userGroup != 4){
						$userGroup = json_decode($userGroup);
						if($userGroup -> type == 'error'){
							$userGroup = 4;
						} else {
						$type = $userGroup -> type;
							if($type == "error"){
								$userGroup = 4;
							} else {
								$userGroup = $userGroup -> user_group;
							}
						}
					}
				//Deciding what to show in case of that user's group
				//By default user has user_group - '4'
				switch ($userGroup){
					case 1:
					$query = "SELECT * FROM servers";
					break;
					
					case 4:
					$query = "SELECT * FROM servers WHERE srv_group = 4";
					break;
					
					default:
					$query = "SELECT * FROM servers WHERE srv_group = 4";
				}
				//====================================================
				return $query;
			}
			
			function serversParser($selector) {
				$counter = 0;
				$srvCount = 0;
				$serversList = serversListArray(availableServers($selector));
				$srvCount = count($serversList);
				while($counter < $srvCount) {
					if($serversList[$counter]['status'] == 1) {
							echo $serversList[$counter]['serverName'] . "& "; 
							echo $serversList[$counter]['adress'] . "& "; 
							echo $serversList[$counter]['port'] . "& "; 
							echo $serversList[$counter]['version']  . "& ";  
							echo $serversList[$counter]['serverImage']  . "& ";  
							echo $serversList[$counter]['story'] . "<::>"; 		
					}
					$counter++;
				}
			}
			
			function serversParserJSON($login){
				$JSONServers = array();
				if($login != 'test') { 
					$selector = availableServers($login);
				} else {
					$selector = "SELECT * FROM servers";
				}
				$serversList = serversListArray($selector);
				$srvCount = count($serversList);
				$counter = 0;
				while($counter < $srvCount) { 
						if($serversList[$counter]['status'] == 1) {
							$JSONServers[] = array(
							'serverNum' => "Server-$counter",
							'serverName' => $serversList[$counter]['serverName'],
							'adress' => $serversList[$counter]['adress'],
							'port' => $serversList[$counter]['port'],
							'version' => $serversList[$counter]['version'],
							'serverImage' => $serversList[$counter]['serverImage'],
							'story' => $serversList[$counter]['story']);
							$counter++;
						}
				}

				return json_encode($JSONServers);
			}

			//No JSON (Will be removed)
			function checkfiles($path) {
					$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);
					$massive = "";
						foreach($objects as $name => $object) {
							$basename = basename($name);
							$isdir = is_dir($name);
							if ($basename!="." and $basename!=".." and !is_dir($name)){
								$str = str_replace('files/clients/', "", str_replace($basename, "", $name));
								$massive = $massive.$str.$basename.':>'.md5_file($name).':>'.filesize($name)."<:>";
							}
						}
				return $massive;
			}

			//No JSON (Will be removed)
			function checkfilesRoot($client) {
					$path = 'files/clients/'.$client;
					if(!is_dir($path)) {
						die("ERROR! \nDirectory - $path doesn't exist!");
					}
					$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);
					$massive = "";
					$arrayOut = array();
						foreach($objects as $name => $object) {
							$basename = basename($name);
							$isdir = is_dir($name);
							if ($basename!="." and $basename!=".." and !is_dir($name)){
								$str = str_replace('files/clients/', "", str_replace($basename, "", $name));
								$massive = $massive.$str.$basename.':>'.md5_file($name).':>'.filesize($name)."<:>";
								$arrayOut[] = array('fileName' => $str.$basename,
													'fileMd5' => md5_file($name),
													'fileSize' => filesize($name));
							}
						}
						return $massive;
			}

			//Full JSON (Need migration)
			function checkfilesJSON($path) {
					$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);
					$fileOBJ = array();
						foreach($objects as $name => $object) {
							$basename = basename($name);
							$isdir = is_dir($name);
							if ($basename!="." and $basename!=".." and !is_dir($name)){
								$str = str_replace('files/clients/', "", str_replace($basename, "", $name));
								$fileOBJ[] = array (
									'filename' => $str.$basename,
								  'fileInfo' => 
								  array (
									'hash' => md5($name),
									'size' => filesize($name),
								  )
								); 
							}
						}
				$fileOBJ = json_encode($fileOBJ);
				return $fileOBJ;
			}

			//Full JSON
			function checkfilesRootJSON($client) {
				$path = 'files/clients/clients/'.$client;
				$files = array();
				if(!is_dir($path)) {
					die("ERROR! \nDirectory - $path doesn't exist!");
					exit();
				}
				$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);
					foreach($objects as $name => $object) {
						$basename = basename($name);
						$isdir = is_dir($name);
						if ($basename!="." and $basename!=".." and !is_dir($name)){
							$str = str_replace('files/clients/', "", str_replace($basename, "", $name));
							$files[] = array (
								'filename' => $str.$basename,
							  'fileInfo' => 
							  array (
								'hash' => md5($name),
								'size' => filesize($name),
							  )
							);		
						}
					}
					$files = json_encode($files);
					return $files;
			}
			
			function uuidFromString($string) {
				$val = md5($string, true);
				$byte = array_values(unpack('C16', $val));
			 
				$tLo = ($byte[0] << 24) | ($byte[1] << 16) | ($byte[2] << 8) | $byte[3];
				$tMi = ($byte[4] << 8) | $byte[5];
				$tHi = ($byte[6] << 8) | $byte[7];
				$csLo = $byte[9];
				$csHi = $byte[8] & 0x3f | (1 << 7);
			 
				if (pack('L', 0x6162797A) == pack('N', 0x6162797A)) {
					$tLo = (($tLo & 0x000000ff) << 24) | (($tLo & 0x0000ff00) << 8) | (($tLo & 0x00ff0000) >> 8) | (($tLo & 0xff000000) >> 24);
					$tMi = (($tMi & 0x00ff) << 8) | (($tMi & 0xff00) >> 8);
					$tHi = (($tHi & 0x00ff) << 8) | (($tHi & 0xff00) >> 8);
				}
			 
				$tHi &= 0x0fff;
				$tHi |= (3 << 12);
			   
				$uuid = sprintf(
					'%08x-%04x-%04x-%02x%02x-%02x%02x%02x%02x%02x%02x',
					$tLo, $tMi, $tHi, $csHi, $csLo,
					$byte[10], $byte[11], $byte[12], $byte[13], $byte[14], $byte[15]
				);
				return $uuid;
			}
 
			function uuidConvert($string) {
				$string = uuidFromString("OfflinePlayer:".$string);
				return $string;
			}

			function token() {
					$chars="0123456789abcdef";
					$max=64;
					$size=StrLen($chars)-1;
					$password=null;
					while($max--)
					$password.=$chars[rand(0,$size)];
					return $password;
			}

			//New function for clients (Gets the client version and loads the proper version of the game) P.S WIP	
			function hashcVersion($client) {
				global $config;
					$serverInfo = serversListArray("SELECT * FROM `servers` WHERE Server_name = '$client'");
					$version = $serverInfo[0]['version'];
					$versionPath = 'files/clients/versions/'.$version;
					$clientPath = 'files/clients/clients/'.$client;
					$hash_md5    = str_replace("\\", "/",checkfiles($versionPath).checkfilesRoot('clients/'.$client).checkfiles($config['clientsDir'].'assets')).'<::>assets/indexes<:b:>assets/objects<:b:>versions/'.$version.'<:b:>clients/'.$client.'/mods<:b:>clients/'.$client.'/config<:b:>clients/'.$client.'/resourcepacks<:b:>clients/'.$client.'/shaderpacks';
				
				return $hash_md5;
			}

			//Full JSON
			function getyText(){
					global $config;
					$randPhrase = array();
					$selector = "SELECT * FROM randPhrases";
					$db = new db($config['db_user'],$config['db_pass'],$config['dbname_launcher']);
					$data = $db->getRows($selector);
					foreach ($data as $row) {				
						$randPhrase[] = $row['phrase'];
						$rarity[] = $row['rarity'];
					}
					$ArrayNum = count($randPhrase) - 1;
					$rand = rand (0,$ArrayNum);
					$text = $randPhrase[$rand];
					$TextRarity = $rarity[$rand];
					
					$textGet = array(
					'type' => 'getText',
					'rarity' => $TextRarity,
					'Message' => $text);
					$textGet = json_encode($textGet);	
					
					return $textGet;
			}

			//Full JSON
			function getUserData($login,$data){
				global $config;
				$query = "SELECT $data FROM dle_users WHERE name = '$login'";
				$db = new db($config['db_user'],$config['db_pass'],$config['db_database']);
				$selectedValue = $db->getRow($query);
					if($selectedValue["$data"]){
							$gotData = $selectedValue["$data"];
							$answer = array('type' => 'success', 'username' => $login, $data => $gotData);
							$answer = json_encode($answer);
						} else {
							$answer = JSONanswer('type', 'error', 'message', 'login not found');
						}
				return $answer;
			}
			
			function countFilesNum($dirPath, $fileMask){
				$count = 0;
				$dir = opendir($dirPath);
				while($file = readdir($dir)){
					if($file == '.' || $file == '..' || is_dir($dir.'/' . $file)){
						continue;
					} elseif(strpos($file, $fileMask)){
						$count++;
					}
					
				}
				return $count;
			}
			
			function getRandomName(){
				$array = array('Феспис',
							   'Неизвестная личность',
							   'Безимянный Лис',
							   'Таинственный незнакомец',
							   'Тот чьё имя нельзя называть',
							   'Скрытный незнакомец',
							   'Шпиён');
				$arraySize = count($array)-1;
				$randWord = rand(0, $arraySize);
				$word = $array[$randWord];
				
				return $word;
			}
		
			function getRealName($login){
				if(isset($login)){
					$answer = getUserData($login,'fullname');
					$decodedAnswer = json_decode($answer);
					$type = $decodedAnswer -> type;
					if($type == "error"){
						$answer = JSONanswer('type', 'success', 'fullname', getRandomName());
					} else {
						$fullname = $decodedAnswer -> fullname;
						if(empty($fullname)){
							$answer = JSONanswer('type', 'success', 'fullname', getRandomName());
						}
					}
				} else {
					$answer = JSONanswer('type', 'error', 'message', 'Invalid login');
				}
				return $answer;
			}

			function userBGArray($login){
				global $config;
				$query = "SELECT Images FROM `userBgImg` WHERE  userlogin = '$login'";
				$db = new db($config['db_user'],$config['db_pass'],$config['db_name_userdata']);
				$data = $db->getRow($query);
				$usersImages = $data['Images'];
				return $usersImages;
			}

			//Full JSON
			function usersBackgrounds($login){
				global $config;
				$counter = 0;
				$ImagesJSON = array();
				if(!empty($login)){
					$usersImages = userBGArray($login);
					if(empty($usersImages)){
						die(JSONanswer('type', 'error', 'message', 'No login found!'));
					}
					$usersImagesArray = explode(",",$usersImages);
					$countImages = count($usersImagesArray);
					while ($counter < $countImages){
						$CurrentImage = $usersImagesArray[$counter];
						$query = "SELECT * FROM `BgImagesList` WHERE FileName = '$CurrentImage'";
						$db = new db($config['db_user'],$config['db_pass'],$config['db_name_userdata']);
						$data = $db->getRows($query);
						foreach ($data as $item) {
							$rarity = $item['Rarity'];
						}
						$ImagesJSON[] = array(
						'ImageName' => $CurrentImage,
						'ImageRarity' => $rarity);
						$counter++;
					}
					$ImagesJSON = json_encode($ImagesJSON);
					
				} else {
					$ImagesJSON = JSONanswer('type', 'error', 'message', 'No login to search!');
				}
				return $ImagesJSON;
			}
			
			function selectedUserBg($login){
				$usersSelectedBG = getUserData($login,'profilePhoto');
				die($usersSelectedBG);
			}

			//Gets the version of Java
			function scanRuntimeDir($bitDepth){
				if($bitDepth) {
					$directory = FILES_DIR.'runtime/';
					$scandir = scandir($directory);
					for ($i=0; $i<count($scandir); $i++) {
						if ($scandir[$i] != '.' && $scandir[$i] != '..' && strpos($scandir[$i], $bitDepth)) {
						  $outputJREArch = $scandir[$i];
						  $outputJREArch = explode('.',$outputJREArch);
						  if($outputJREArch[1] == "zip") {
							$outputJRE = $outputJREArch[0];
						  }
						}
					}
					$outputJSON = array('type' => 'success','JREname' => $outputJRE);
					$outputJRE = json_encode($outputJSON);
				} else {
					$outputJRE = JSONanswer('type', 'error', 'message', 'Not specifyed JRE bit depth!');
				}
				return $outputJRE;
			}

			function parse_online($host, $port){
				$socket = @fsockopen($host, $port, $tes, $offline, 0.1);

					if ($socket !== false) {
					@fwrite($socket, "\xFE");
					$data = "";
					$data = @fread($socket, 256);
					@fclose($socket);
			 
				if ($data == false || substr($data, 0, 1) != "\xFF") return;{
				  $info= substr($data, 3);
				  $info = iconv('UTF-16BE', 'UTF-8', $info);

					 if($info[1] === "\xA7" && $info[2] === "\x31" ) {
					 $info = explode( "\x00", $info);
					 $playersOnline=IntVal($info[4]);
					 $playersMax = IntVal($info[5]);
						} else {
					 $info = Explode("\xA7", $info);
					 $playersOnline=IntVal($info[1]);
					 $playersMax = IntVal($info[2]);
						}
					if(!$info[1] || !$info[2]){
						return 'offline';
					}

						$answer = "$playersOnline&$playersMax";
						return ($answer);
						}
					} else {
						$answer = 'offline';
						return $answer;
					}
			}

			function parse_onlineJSON($host, $port){
				$socket = @fsockopen($host, $port, $tes, $offline, 0.1);

					if ($socket !== false) {
					@fwrite($socket, "\xFE");
					$data = "";
					$data = @fread($socket, 256);
					@fclose($socket);
			 
				if ($data == false || substr($data, 0, 1) != "\xFF") return;{
				  $info= substr($data, 3);
				  $info = iconv('UTF-16BE', 'UTF-8', $info);

					 if($info[1] === "\xA7" && $info[2] === "\x31" ) {
					 $info = explode( "\x00", $info);
					 $playersOnline=IntVal($info[4]);
					 $playersMax = IntVal($info[5]);
						} else {
					 $info = Explode("\xA7", $info);
					 $playersOnline=IntVal($info[1]);
					 $playersMax = IntVal($info[2]);
						}
					if(!$info[1] || !$info[2]){
						return 'offline';
					}
						$answer = array('host' => $host,'port' => $port,'status' => 'online','currentOnline' => "$playersOnline",'maxOnline' => "$playersMax");
						$answer = json_encode($answer);
						return ($answer);
						}
					} else {
						$answer = array('host' => $host,'port' => $port,'status' => 'offline');
						$answer = json_encode($answer);
						return $answer;
					}
			}

			function clearMD5Cache($logFile){
				$selector = "SELECT * FROM servers";
				$srvList = serversListArray($selector);
				$counter = 0;
				$srvCount = count($srvList);
				while($counter < $srvCount) {  
					$openDir = FILES_DIR.'clients/clients/'.$srvList[$counter]['serverName'];
					if ($handle = opendir($openDir)) {
						while (false !== ($file = readdir($handle)))   {
							if ($file != "." && $file != ".." && $file == $srvList[$counter]){
							$delPath = FILES_DIR.'clients/clients/'.$file."/".$file;
								if (file_exists($delPath)) {
									$unlink = unlink($delPath);
									if($unlink == true){
										$deletedFile = '	-	Deleted: '.$delPath;
										echo $deletedFile."<br>";
										writeLogFile($logFile,$deletedFile);
									} else {
										$CantDelete =  '	-	'.$delPath.' couldn`t be deleted';
										writeLogFile($logFile,$CantDelete);
										echo $CantDelete."<br>";
									}
								} else {
									$noFileToDelete =  '	-	'.$delPath.' was not found';
									writeLogFile($logFile,$noFileToDelete);
								}
							}
						}
						closedir($handle);
					}
					$counter++;
				}
			}

			function writeLogFile($file,$text){					
				$fp = fopen($file, "a+");
					if($fp) {
						fwrite($fp,$text."\n");
					} else {
						echo "Error writing file ".$file;
					}
				fclose($fp);
			}

			function clearLogFile($file) {
				$fp = fopen($file,"a+");
					if($fp) {
						ftruncate($fp,0);
					} else {
						$this->error = "Error truncating file ".$file;
					}
				fclose($fp);
			}

			//Full JSON
			function ImgHash($img) {
					$file_link = $_SERVER['DOCUMENT_ROOT']."/launcher/files/img/$img.png";
					if(file_exists($file_link)){
						$ImgHash = md5_file($file_link);
						$answer = array('type' => 'success', 'ImgName' => $img, 'ImgHash' => $ImgHash);	//Future JSON migration
						$answer = json_encode($answer);
					} else {
						$answer = JSONanswer('type', 'error', 'message', 'Unable to continue!');
				}
				return $answer;
			}

			function verifySSL(){
				if (!(isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||  isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'))
				{
				   $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
				   header('HTTP/1.1 301 Moved Permanently');
				   header('Location: ' . $redirect);
				   exit();
				}
				return true;
			}

			function eventNow(){
				global $config;
					//Vars definition
					$soundsPath = FILES_DIR."eventSounds";
					$pathJSON = "/launcher/files/eventSounds";
					$musFilesNum = countFilesNum($soundsPath.'/mus', '.mp3');					//Count of ordinary Music
					$easterMusFilesNum = countFilesNum($soundsPath.'/mus/easterMusic', '.mp3'); //Count of Easter Music
					$eventName;
					
					//Date explosion
					$dateExploded = explode ('.',CURRENT_DATE);
					$dayToday = $dateExploded[0];
					$monthToday = $dateExploded[1];
					$yearToday = $dateToday[2];
					
				//Checking each of 12 monthes
				switch($monthToday){
					case 1:
						switch($dayToday){
							case($dayToday < 12):
								$eventName = "winterHolidays";
							break;
						}
					break;
					
					case 2:
					break;
					
					case 3:
					break;
					
					case 4:
						//$musRange ="1/1";
					break;
					
					case 5:
					break;
					
					case 6:
					break;
					
					case 7:
					break;
					
					case 8:
					break;
					
					case 9:
					break;
					
					case 10:
					break;
					
					case 11:
					break;
					
					case 12:
						switch($dayToday){
							case($dayToday < 31 && $dayToday != 20 && $dayToday != 31):
								$eventName = "winterHolidays";
							break;
							
							case 20:
								$eventName = "twistOfTheSun";
							break;
							
							case 31:
								$eventName = "newYear";
								$musRange ="1/8";
							break;
							
							default:
							
						}
					break;
				}

				//Generating a Mus File
					$easterMusFile = easterEgg($config['easterMusRarity']);
					if(isset($musRange)){
						$musRange = explode('/',$musRange);
						$minRange = $musRange[0];
						$maxRange = $musRange[1];
					}

					if(isset($minRange) && isset($maxRange)) {
						$RandMusic = rand($minRange,$maxRange);
					} else {
						$RandMusic = rand(1,$musFilesNum);
					}
					$selectedMusic = "mus".$RandMusic.".mp3";
					$selectedSound;
					if($easterMusFile == "YES"){
						$RandMusic = rand(1, $easterMusFilesNum);
						$selectedMusic = "easterMusic/mus".$RandMusic.".mp3";
					}

				$musMd5 = md5_file($soundsPath.'/mus/'.$selectedMusic);
				//************************

				if(isset($eventName)){
					$eventSoundsNum = countFilesNum($soundsPath.'/'.$eventName, '.mp3');	
					$selectedSound = rand(1,$eventSoundsNum);
					$selectedSound = $eventName.$selectedSound.'.mp3';
					$thisSoundMd5 = md5_file($soundsPath.'/'.$eventName.'/'.$selectedSound);
					$outputArray = array("Status" => "Event",
									     "filesDir" => $pathJSON.'/',"eventName" => $eventName,
										 "soundMd5" => $thisSoundMd5,
										 "selectedSound" => $selectedSound,
										 "MusicMd5" => $musMd5,
										 "selectedMusic" => $selectedMusic);
				} else {
					$eventName = 'common';
					$commonFilesNum = countFilesNum($soundsPath.'/'.$eventName, '.mp3');
					$selectedSound = rand(1,$commonFilesNum);
					$selectedSound = "voice".$selectedSound.".mp3";
					$thisSoundMd5 = md5_file($soundsPath.'/'.$eventName.'/'.$selectedSound);
					$outputArray = array("Status" => "noEvent",
										 "filesDir" => $pathJSON.'/',
										 "soundMd5" => $thisSoundMd5,
										 "selectedSound" => $selectedSound,
										 "MusicMd5" => $musMd5,
										 "selectedMusic" => $selectedMusic,
										 "eventName" => $eventName);
				}

				return json_encode($outputArray);
			}
			
			function easterEgg($chance){
				$minRange = 1;
				$maxRange = 1000;
				if (mt_rand($minRange, $maxRange) <= $chance){
					$returnText = 'YES';
				} else {
					$returnText= 'NO';
				}
				return $returnText;
			}

			function display_error($error ='No errors', $error_num = 100, $query) {
					$error = htmlspecialchars($error, ENT_QUOTES, 'ISO-8859-1');
					$trace = debug_backtrace();

					$level = 0;
					if ($trace[1]['function'] == "query" ) $level = 1;
					$trace[$level]['file'] = str_replace(ROOT_DIR, "", $trace[$level]['file']);

					echo '
							<?xml version="1.0" encoding="iso-8859-1"?>
							<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
							<html xmlns="http://www.w3.org/1999/xhtml">
							<head>
							<title>MySQL Fatal Error | Arvind</title>
							<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
							<style type="text/css">
							<!--
							body {
								font-family: Verdana, Arial, Helvetica, sans-serif;
								font-size: 11px;
								font-style: normal;
								color: #000000;
							}
							.top {
							  color: #ffffff;
							  font-size: 15px;
							  font-weight: bold;
							  padding-left: 20px;
							  padding-top: 10px;
							  padding-bottom: 10px;
							  text-shadow: 0 1px 1px rgba(0, 0, 0, 0.75);
							  background-image: -moz-linear-gradient(top, #ab8109, #998f5a);
							  background-image: -ms-linear-gradient(top, #ab8109, #998f5a);
							  background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#ab8109), to(#998f5a));
							  background-image: -webkit-linear-gradient(top, #ab8109, #998f5a);
							  background-image: -o-linear-gradient(top, #ab8109, #998f5a);
							  background-image: linear-gradient(top, #ab8109, #998f5a);
							  filter: progid:DXImageTransform.Microsoft.gradient( startColorstr="#ab8109", endColorstr="#998f5a",GradientType=0); 
							  background-repeat: repeat-x;
							  border-bottom: 1px solid #ffffff;
							}
							.box {
								margin: 10px;
								padding: 4px;
								background-color: #EFEDED;
								border: 1px solid #DEDCDC;

							}
							-->
							</style>
							</head>
							<body>
								<div style="width: 700px;margin: 20px; border: 1px solid #D9D9D9; background-color: #F1EFEF; -moz-border-radius: 5px; -webkit-border-radius: 5px; border-radius: 5px; -moz-box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.3); -webkit-box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.3); box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.3);" >
									<div class="top" >MySQ: Error! | Arvind</div>
									<div class="box" ><b>MySQL error</b> in file: <b>'.$trace[$level]['file'],'</b> at line <b>'.$trace[$level]['line'].'</b></div>
									<div class="box" >Error Number: <b>'.$error_num.'</b></div>
									<div class="box" >The Error returned was: <b>'.$error.'</b></div>
									<div class="box" ><b>SQL query:</b><br />'.$query.'</div>
									</div>		
							</body>
							</html>
					';
				exit();
			}