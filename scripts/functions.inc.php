<?php
/*
=====================================================
 Functions
-----------------------------------------------------
 https://Foxesworld.ru/
-----------------------------------------------------
 Copyright (c) 2016-2020  FoxesWorld
-----------------------------------------------------
 Данный код защищен авторскими правами
-----------------------------------------------------
 Файл: functions.inc.php
-----------------------------------------------------
 Версия: 0.0.15 Alpha
-----------------------------------------------------
 Назначение: Различные функции
=====================================================
*/
	verifySSL();
	if(!defined('INCLUDE_CHECK')) {
			require ($_SERVER['DOCUMENT_ROOT'].'/index.php');
	}
//================================================================
header('Content-Type: text/html; charset=utf-8');			
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
							
			function dbPrepare(){
				global $db_host, $db_port, $db_database, $db_user, $db_pass;
				try {
					$db = new PDO("mysql:host=$db_host;port=$db_port;dbname=$db_database", $db_user, $db_pass);
					$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					$db->exec("set names utf8");
				} catch(PDOException $pe) {
					die(Security::encrypt("errorsql")."Ошибка подключения (Хост, Логин, Пароль)");
				}
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
				} catch(PDOException $pe) {
					die(Security::encrypt("errorsql")); 
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
			function serversParser($selector){
				global $LauncherDB;
				$STH = $LauncherDB ->query("$selector");  
				$STH->setFetchMode(PDO::FETCH_ASSOC);  
				$counter = 0;
				while($row = $STH->fetch()) { 
						if($row['enabled'] == 1) {
							$serverName = $row['Server_name'];
							$adress = $row['adress'];
							$port = $row['port'];
							$version = $row['version'];
							$serverImage = $row['srv_image'];
							$story = $row['story'];
							
							echo $serverName . "& "; 
							echo $adress . "& "; 
							echo $port . "& "; 
							echo $version  . "& ";  
							echo $serverImage  . "& ";  
							echo $story . "<::>"; 
							$counter++;
						}
				}
				$STH = null;
			}
			
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
			
			function checkfilesRoot($client) {
					$path = 'files/clients/'.$client;
					if(!is_dir($path)) {
						die("ERROR! \nDirectory - $path doesn't exist!");
					}
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
			
			function checkfilesRootJSON($client) {
					$path = $_SERVER['DOCUMENT_ROOT'].'/launcher/files/clients/'.$client;
					if(!is_dir($path)) {
						die("ERROR! \nDirectory - $path doesn't exist!");
					}
					$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);
					$massive = "";
						foreach($objects as $name => $object) {
							$basename = basename($name);
							$isdir = is_dir($name);
							if ($basename!="." and $basename!=".." and !is_dir($name)){
								$str = str_replace('files/clients/', "", str_replace($basename, "", $name));
								$array = array (
								  $str => 
								  array (
									'hash' => md5($str),
									'size' => filesize($str),
								  )
								);
								$array = json_encode($array);
								$JSON[] = $array;
								//$JSON[] = implode(',', $array);
							}
						}
						return $JSON;
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
			
			function hashc($client ,$clientsDir) {
					$hash_md5    = str_replace("\\", "/",checkfiles($clientsDir.$client.'/bin/').checkfilesRoot($client).checkfiles($clientsDir.$client.'/mods/').checkfiles($clientsDir.$client.'/natives/').checkfiles($clientsDir.'assets')).'<::>assets/indexes<:b:>assets/objects<:b:>assets/virtual<:b:>'.$client.'/bin<:b:>'.$client.'/mods<:b:>'.$client.'/natives<:b:>'; //.$client.'/coremods<:b:>'
				return $hash_md5;
			}
			
			function getyText(){
					require ('database.php');
					$randPhrase = array();
					$selector = "SELECT * FROM randPhrases";
					$STH = $LauncherDB->query("$selector");  
					$STH->setFetchMode(PDO::FETCH_ASSOC);  
					while($row = $STH->fetch()) { 
						$randPhrase[] = $row['phrase'];
					}
					$ArrayNum = count($randPhrase) - 1;
					$rand = rand (0,$ArrayNum);
					$text = $randPhrase[$rand];
					
					$textGet = array(
					'type' => 'getText',
					'Message' => $text);
					$textGet = json_encode($textGet);	
					
					return $text;
			}
			
			function getUserData($login,$data){
				global $FoxSiteDB;
				$query = "SELECT * FROM dle_users WHERE name = '$login'";
				$STH = $FoxSiteDB->query($query);  
				$STH->setFetchMode(PDO::FETCH_OBJ);
				$row = $STH->fetch();
				$answer = $row -> {$data};
				
				return $answer;
			}
			
			function usersBackgrounds($login){
				//global $UDT;
				require ('database.php');
				$query = "SELECT Images FROM `userBgImg` WHERE  userlogin = '$login'";
				$STB = $UDT -> query($query);
				$STB->setFetchMode(PDO::FETCH_OBJ);
				$row = $STB->fetch();
				
				return $row;
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
						return ("$playersOnline&$playersMax");
						}} else {
						return 'offline';
					}
			}
			
			function clearMD5Cache(){
				global $LauncherDB;
				$selector = "SELECT Server_name FROM servers";
				$STH = $LauncherDB->query("$selector");  
				$STH->setFetchMode(PDO::FETCH_ASSOC);  
				while($row = $STH->fetch()) {  
					if ($handle = opendir(SITE_ROOT.'/files/clients/'.$row['Server_name'])) {
						while (false !== ($file = readdir($handle)))   {
							if ($file != "." && $file != ".." && $file != "mods" && $file != "bin" && $file != "config" && $file != "natives" && $file != "config.zip"){
							$delPath = '../files/clients/'.$file."/".$file;
								if (file_exists($delPath)) {
									$unlink = unlink($delPath);
									if($unlink == true){
										echo "Deleted: $delPath<br>";
									} else {
										echo "Error deleting $delPath<br>";
									}
								}
							}
						}
						closedir($handle);
					}
				}
			}
				
			function ImgHash($img) {
					$file_link = $_SERVER['DOCUMENT_ROOT']."/launcher/files/img/$img.png";
					if(file_exists($file_link)){
						$answer = md5_file($file_link);
					} else {
						$answer = "Image not found!";
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

		class IPGeoBase {
			private $fhandleCIDR, $fhandleCities, $fSizeCIDR, $fsizeCities;
			
			private function ipLocation($ip){
					$ip_data = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$ip)); 
					if($ip_data && $ip_data->geoplugin_countryName != null){
						$ipLocation = $ip_data->geoplugin_countryCode;
						$ipRegion = $ip_data->geoplugin_region;
					} else {
						$ipLocation = 'Unknown';
						$ipRegion = $ip_data->geoplugin_region;
					}
					return array($ipLocation,$ipRegion);
			}
			
			private function addCityCount($city){
				global $LauncherDB;
				$query = "SELECT * FROM ipCity WHERE cityName = '$city'";
				$STH = $LauncherDB->query($query);  
				$STH->setFetchMode(PDO::FETCH_OBJ);
				$row = $STH->fetchObject();
				
				if(!isset($row->cityName)){
					$query = "INSERT INTO `ipCity`(`cityName`) VALUES ('$city')";
				} else {
					$query = "UPDATE `ipCity` SET `cityCount`= cityCount+1 WHERE cityName = '$city'";	
				}
				$LauncherDB->query($query);
			}
			
			public function getIP($ip,$log=false){
				global $LauncherDB;
				if($ip){
					if(!isset($_COOKIE['ipAdded'])){
						$STH = $LauncherDB->query("SELECT * FROM `ipDatabase` WHERE ip = '$ip'"); 
						$STH->setFetchMode(PDO::FETCH_OBJ);
						$row = $STH->fetchObject();
						if (!isset($row->ip)) {
							$date="[".date("d m Y H:i")."] ";
							$ipLocation = $this->ipLocation($ip)[0];
							$ipRegion = $this->ipLocation($ip)[1];
							$LauncherDB->query("INSERT INTO `ipDatabase`(`ipLocation`, `ipRegion`, `ip`) VALUES ('$ipLocation','$ipRegion','$ip')");  
							$this->addCityCount($ipRegion);
							if($log === true){
								echo 'Adding '.$ip.' - '.$ipLocation.'('.$ipRegion.') '.'to IP database';
							}
							setcookie("ipAdded", $ip, time()+36000);
						} else {
							if($log === true){
								echo 'Cookie was not found but Ip - '.$userIP.' is already added in the Database, thanks for helping us to build server statistics';
							}
						}
					} else {
						if($log === true){
							echo 'Cookie was set for ip - '.$_COOKIE['ipAdded'];
						}
					}
				}
			}
		}