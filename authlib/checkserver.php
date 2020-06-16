<?php
	define('INCLUDE_CHECK',true);
	include("../database.php");
	include_once("../scripts/loger.php");
	@$user     = $_GET['user'];
    @$serverid = $_GET['serverId'];
    $logger->WriteLine($log_date." ".$user." ".$serverid);
	$error_status = array(
	'error' => 'Bad params for join to server.',
	'errorMessage' => 'Bad params for join to server.');
	$error_status = json_encode($error_status);	
	
	try {
		if (!preg_match("/^[a-zA-Z0-9_-]+$/", $user) || !preg_match("/^[a-zA-Z0-9_-]+$/", $serverid)){
			die($error_status);
		}

		$stmt = $db->prepare("Select user From usersession Where user= :user And server= :serverid");
		$stmt->bindValue(':user', $user);
		$stmt->bindValue(':serverid', $serverid);
		$stmt->execute();   
       	$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$realUser = $row['user'];
		if($user == $realUser)
		{
			echo "YES";
		} else {
			//echo "NO"; 
			die($error_status);
		}
	} catch(PDOException $pe) {
		die("bad".$logger->WriteLine($log_date.$pe));  //вывод ошибок MySQL в m.log
	}
