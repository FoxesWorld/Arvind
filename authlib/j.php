<?php
/*
=====================================================
 Have you joined or not? - joinServer | AuthLib
-----------------------------------------------------
 https://arcjetsystems.ru/
-----------------------------------------------------
 Copyright (c) 2016-2020  FoxesWorld
-----------------------------------------------------
 Данный код защищен авторскими правами
-----------------------------------------------------
 Файл: j.php
-----------------------------------------------------
 Версия: 0.0.6 Stable Alpha
-----------------------------------------------------
 Назначение: Проверка присоединения к серверу
=====================================================
*/
	define('INCLUDE_CHECK',true);
	if (($_SERVER['REQUEST_METHOD'] == 'POST' ) && (stripos($_SERVER["CONTENT_TYPE"], "application/json") === 0)) {
		$json = json_decode(file_get_contents('php://input'));
	}
    
	@$md5 = $json->selectedProfile; @$sessionid = @$json->accessToken; @$serverid = $json->serverId;
	$bad = array('error' => "Bad login",'errorMessage' => "Bad login");

	try {
		if (!preg_match("/^[a-zA-Z0-9_-]+$/", $md5) || !preg_match("/^[a-zA-Z0-9:_-]+$/", $sessionid) || !preg_match("/^[a-zA-Z0-9_-]+$/", $serverid)){
			exit(json_encode($bad));
		}
		include("../database.php");
		$db = new db($config['db_user'],$config['db_pass'],$config['db_database']);
		$stmt = $db->prepare("SELECT md5,user FROM usersession WHERE md5= :md5 And session= :sessionid");
		$stmt->bindValue(':md5', $md5);
		$stmt->bindValue(':sessionid', $sessionid);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$realmd5  = $row['md5'];
		$realUser = $row['user'];

		$ok = array('id' => $realmd5, 'name' => $realUser);
		if($realmd5 == $md5)
		{
			$stmt = $db->prepare("UPDATE usersession SET server= :serverid WHERE session = :sessionid And md5 = :md5");
			$stmt->bindValue(':md5', $md5);
			$stmt->bindValue(':sessionid', $sessionid);
			$stmt->bindValue(':serverid', $serverid);
			$stmt->execute();
				if($stmt->rowCount() == 1) {
					echo json_encode($ok);
				} else {
					exit(json_encode($bad));
				}
		}
		else exit(json_encode($bad));
	} catch(PDOException $pe) {
		$query = strval($e->queryString);
		die(display_error($e->getMessage(), $pe, $query));
	}
