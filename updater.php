<?php
/*
=====================================================
 Launcher-N-Hash Class
-----------------------------------------------------
 https://Foxesworld.ru/
-----------------------------------------------------
 Copyright (c) 2016-2021 FoxesWorld
-----------------------------------------------------
 Данный код защищен авторскими правами
-----------------------------------------------------
 Файл: updater.php
-----------------------------------------------------
 Версия: 0.2.4 Alpha
-----------------------------------------------------
 Назначение: Проверка хеша лаунчера и апдейтера
=====================================================
*/ 
header("Content-Type: text/plain; charset=UTF-8");
define('INCLUDE_CHECK',true);
define('CONFIG', true);
include_once ("config.php");

	if($_SERVER['QUERY_STRING'] === ''){
		die("Hacking Attempt!");
	} else {
		$updater = new updater();
	}
	
class updater {

	private static $updater_type;
	private static $updater_hash;
	private static $launcher_hash;
	private static $download;

	public function __construct($debug = false) {
		updater::$updater_type = $_GET['updater_type'] ?? null;
		updater::$updater_hash = $_GET['hash'] ?? null;
		updater::$launcher_hash = $_GET['ver'] ?? null;
		updater::$download = $_GET['download'] ?? null;
		$this->updaterCheck($debug);
		$this->launcherHash();
		$this->downloadUpdater();
		
		if($debug === true) {
			error_reporting(E_ALL);
		}
	}
	
	/**
	* @param boolean $debug
	* @return YES||NO
	* @throws Exception
	*/
	private function updaterCheck($debug) {
		global $config;
		try {
			if(isset(static::$updater_type)){
				$file = "updater";
				switch(static::$updater_type){
					case 'jar' || 'exe':
						$fileName = $file.'.'.static::$updater_type;
						$updaterHashLocal = md5_file($config['updaterRepositoryPath'].static::$updater_type);
						$updateState = static::$updater_hash == $updaterHashLocal ? "NO" : "YES";
					break;

					default:
						$updateState = "Unknown updater type!";
					break;
				}

				$answer = array('fileName' => $fileName, 'fileHash' => $updaterHashLocal, 'updateState' => $updateState);
				$answer = json_encode($answer);
				die($answer);
			}
		}  catch (Exception $e) {
			die("File not found! ".$e);
		}
		if($debug === true) {
			echo 'updaterRepositoryPath: '.$config['updaterRepositoryPath'].'extension';
		}
	}
	
	/**
	* @param boolean $debug
	* @return YES||NO
	* @throws Exception
	*/
	private function launcherHash() {
		global $config;
		
		if(isset(static::$launcher_hash)){
			try {
				$launcherRepositoryHash = md5_file($config['launcherRepositoryPath']);
				$launcherState = static::$launcher_hash == $launcherRepositoryHash  ? "NO" : "YES";
				$fileName = explode('/',$config['launcherRepositoryPath']); 
				$answer = array('fileName' =>$fileName[2], 'hash' => $launcherRepositoryHash, 'updateState' => $launcherState, 'updateNotes' => $this->readUpdateNotes('files/launcher/notes.txt')[0]);
				$answer = json_encode($answer);
				die($answer);
			}  catch (Exception $e) {
				die("File not found! ".$e);
			}
		}
	}
	
	private function readUpdateNotes($file){
		if(file_exists($file)) {
			$fd = fopen($file, 'r');
			$fileContents = array();
			while(!feof($fd))
			{
				$str = htmlentities(fgets($fd));
				$fileContents[] = $str;
			}
			return $fileContents;
		} else {
			die("File - ".$file." not found!");
		}
	}

	private function downloadUpdater(){ 
		switch (static::$download){
			case 'jar' || 'exe':
				$file = "files//updater//updater.".static::$download;
				if(file_exists($file)) {
					header('Content-Description: File Transfer');
					header('Content-Type: application/octet-stream');
					header('Content-Disposition: attachment; filename=' . basename("Foxesworld.".static::$download));
					header('Content-Transfer-Encoding: binary');
					header('Expires: 0');
					header('Cache-Control: must-revalidate');
					header('Pragma: public');
					header('Content-Length: ' . filesize($file));
					readfile($file);
					exit;
				}
			break;
				
			default:
				die ("Unknown request!");
			break;
		}
	}
}
?>