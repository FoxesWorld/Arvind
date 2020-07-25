<?php 
  if(!defined('INCLUDE_CHECK')) {
		die("Hacking Attempt!");
  }
  
Error_Reporting(E_ALL);
Ini_Set('display_errors', true);
header('Content-Type: text/html; charset=utf-8');
	$secureKey = "531d611814a3c9113a8b1062c1ae5875";
	$db_user 			= 'root';
	$db_pass			= 'P$Ak$O2sJZSu$aAKOBqkokf@Vs5%YCj'; 
	$db_host			= 'localhost'; 
$recievedKey = GetKey();
$operationType = GetOperType();
if($recievedKey === $secureKey){
	$user_login = $_GET['Login'];
	$serverName = getServerName($_GET['serverName']);
	if($user_login && $serverName & $operationType){
		if($operationType === 'SHOW'){
			//echo showColumns($db_host, $serverName, $db_user, $db_pass);
			die(showCases($db_host, $serverName, $db_user, $db_pass, $user_login));
		} elseif ($operationType === 'EDIT'){
			$num = getCaseEditNum();
			$caseName = getCaseEditName();
			editCaseNum($db_host, $serverName, $db_user, $db_pass, $user_login, $num, $caseName);
		}
	} else {
		die("Insufficent information!");
	}
} else {
	die ("Key is incorrect!");
}