<?php
header('Content-Type: text/html; charset=utf-8');
  if(!defined('INCLUDE_CHECK')) {
		die("Hacking Attempt!");
  } 
	
	function getBalance($user_login){
		include ('usercache.php');
		$query = "SELECT * FROM balance WHERE username = '$user_login'";
		$STH = $userdataDB->query($query);  
		$STH->setFetchMode(PDO::FETCH_OBJ);
		$empty = $STH->rowCount() === 0; 
		$row = $STH->fetch();
		$balance = $row -> balance;
		$STH = null;
		
		if($empty){
			$balance = "0";
		}
	return $balance;
	}
	
		/* CASES */
			/*  GET VaRS */
			function getCaseEditNum(){
				if(isset($_GET['editNum'])){
					$editNum = $_GET['editNum'];
				} else {
					die("При выполнении данной операции необходимо указать количество кейсов");
				}
			return $editNum;
			}
			
			function getCaseEditName(){
				if(isset($_GET['editName'])){
					$editName = $_GET['editName'];
				} else {
					die("При выполнении данной операции необходимо указать имя кейса");
				}
			return $editName;
			}
			
			function getServerName($serverName){
				$serverName = "server_".$serverName;
				return $serverName;
			}
			
			function GetOperType(){
				if(isset($_GET['operationType'])){
					$GetOperType = $_GET['operationType'];
					if($GetOperType === 'SHOW' || $GetOperType === 'EDIT'){
						$GetOperType = $_GET['operationType'];
					} else {
						die("Unknown operation!");
					}
					
				} else {
					die("Может ключик то и верный, а тип операции ты не указапл =(");
				}
			return $GetOperType;
			}
			
			function GetKey(){
				if(isset($_GET['secureKey'])){
					$recievedKey = $_GET['secureKey'];
				} else {
					die("Захотел без ключика к нам прийти? Похвально, хитрец! А лис не перехитришь. =)");
				}
				return $recievedKey;
			}
			/*  GET VaRS */
			
	function casesDB($db_host, $serverName, $db_user, $db_pass){
		try { 
			$casesDB = new PDO("mysql:host=$db_host;dbname=$serverName;charset=UTF8", $db_user, $db_pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
		} catch(PDOException $e) { 
			die($e->getMessage());
		}
		return $casesDB;
	}
	
	function addUser($user_login, $casesDB){
		try{
			$queryADD = "INSERT INTO `cases`(`username`) VALUES ('$user_login')";
			$STH = $casesDB->query($queryADD);
			echo "Adding $user_login to SQL";
		} catch (PDOException $pe) {
			die($pe);
		}
	}
	
	function showCases($db_host, $serverName, $db_user, $db_pass, $user_login){
		try{
			$casesDB = casesDB($db_host, $serverName, $db_user, $db_pass);
			$query = "SELECT * FROM cases WHERE username = '$user_login'";
			$STH = $casesDB->query($query);  
			$STH->setFetchMode(PDO::FETCH_OBJ);
			$empty = $STH->rowCount() === 0; 

			if($empty){
				addUser($user_login, $casesDB);
			} else {
				$row = $STH->fetch();
				$wool = $row -> wool;//чтобы содержание скрипта зависело от наполнения бд
				$cobble = $row -> cobble;
				$cobol = $row -> cobol;
				$return = "$wool:$cobble:$cobol";
			$STH = null;
			}
		} catch (PDOException $pe) {
		}
		return $return;
	}
	
	function editCaseNum($db_host, $serverName, $db_user, $db_pass, $user_login, $num, $caseName){
		try{
			$casesDB = casesDB($db_host, $serverName, $db_user, $db_pass);
			$query = "UPDATE cases SET $caseName = $num";
			$STH = $casesDB->query($query);
			$STH = null;
			die('succes');
		} catch (PDOException $pe){
			die($pe);
		}
	}

	function showColumns($db_host, $serverName, $db_user, $db_pass){
		$casesDB = casesDB($db_host, $serverName, $db_user, $db_pass);
		$query = "SHOW columns FROM cases;";
		$STH = $casesDB->query($query);
		$row = $STH->fetch();
		echo var_dump($row[2]);
		$STH = null;
	}
		/* CASES */