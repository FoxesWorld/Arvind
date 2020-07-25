<?php
  if(!defined('INCLUDE_CHECK')) {
		die("Hacking Attempt!");
  } 

		$db_user			= 'root'; 
		$db_pass			= 'P$Ak$O2sJZSu$aAKOBqkokf@Vs5%YCj'; 
		$db_host			= 'localhost'; 
		$dbname 			= 'fox_userdata';
		try { 
		$userdataDB = new PDO("mysql:host=$db_host;dbname=$dbname;charset=UTF8", $db_user, $db_pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
		} catch(PDOException $e) { 
		die($e->getMessage());
	}