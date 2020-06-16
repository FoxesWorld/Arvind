<?php
/*
=====================================================
 Functions
-----------------------------------------------------
 https://arcjetsystems.ru/
-----------------------------------------------------
 Copyright (c) 2016-2020  FoxesWorld
-----------------------------------------------------
 Данный код защищен авторскими правами
-----------------------------------------------------
 Файл: functions.inc.php
-----------------------------------------------------
 Версия: 0.0.2 Alpha
-----------------------------------------------------
 Назначение: Различные функции
=====================================================
*/
if(!defined('INCLUDE_CHECK')) {
		die("Hacking Attempt!");
	} else {
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

			function checkfiles($path) {
					$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);
					$massive = "";
						foreach($objects as $name => $object) {
							$basename = basename($name);
							$isdir = is_dir($name);
							if ($basename!="." and $basename!=".." and !is_dir($name)){
								$str = str_replace('files/clients/', "", str_replace($basename, "", $name));
								$massive = $massive.$str.$basename.':>'.md5_file($name).':>'.filesize($name).'<:>';
							}
						}
						return $massive;
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

			function imagestype($binary) {
					if (
						!preg_match(
							'/\A(?:(\xff\xd8\xff)|(GIF8[79]a)|(\x89PNG\x0d\x0a)|(BM)|(\x49\x49(?:\x2a\x00|\x00\x4a))|(FORM.{4}ILBM))/',
							$binary, $hits
						)
					) {
						return 'application/octet-stream';
					}
					static $type = array (
						1 => 'image/jpeg',
						2 => 'image/gif',
						3 => 'image/png',
						4 => 'image/x-windows-bmp',
						5 => 'image/tiff',
						6 => 'image/x-ilbm',
					);
					return $type[count($hits) - 1];
				}
				
		    function parse_online($host, $port){
				$socket = @fsockopen($host, $port);
					if ($socket !== false) {
					@fwrite($socket, "\xFE");
					$data = "";
					$data = @fread($socket, 256);
					@fclose($socket);
			 
				if ($data == false or substr($data, 0, 1) != "\xFF") return;{
				  $info= substr( $data, 3 );
				  $info = iconv( 'UTF-16BE', 'UTF-8', $info );

					 if( $info[ 1 ] === "\xA7" && $info[ 2 ] === "\x31" ) {
					 $info = explode( "\x00", $info );
					 $playersOnline=IntVal( $info[4] );
					 $playersMax = IntVal( $info[5] );
						} else {
					 $info = Explode( "\xA7", $info );
					 $playersOnline=IntVal( $info[1] );
					 $playersMax = IntVal( $info[2] );
						}
						return ("$playersOnline\n$playersMax");
						}} else {
						return("offline");
					}
				}
}