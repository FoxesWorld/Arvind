<?php
/*
=====================================================
 startUpSound PDO
-----------------------------------------------------
 https://Foxesworld.ru/
-----------------------------------------------------
 Copyright (c) 2016-2021 FoxesWorld
-----------------------------------------------------
 Данный код защищен авторскими правами
-----------------------------------------------------
 Файл: startUpSound.class.php
-----------------------------------------------------
 Версия: 0.1.2 Alpha
-----------------------------------------------------
 Назначение: Генерация звука запуска
=====================================================
*/

require ('mp3File.class.php');

	class startUpSound {
	
		/* Base utils */
		private static $AbsolutesoundPath = FILES_DIR."eventSounds";
		private static $currentDate = CURRENT_DATE;
		private static $musMountPoint = 'mus';
		private static $eventNow = 'common';
		private static $musFilesNum = 0;
		private static $soundFilesNum = 0;
		private static $easter = "";
		
		/* Mus */
		private static $selectedMusic; 		//Selected mus File
		private static $musFileAbsolute;	//Absolute musFilePath
		private static $durationMus = 0;	//Duration of a musFile
		private static $musMd5;				//musFile md5
		
		/* Sound */
		private static $selectedSound; 		//Selected sound File
		private static $soundFileAbsolute;	//Absolute soundFilePath
		private static $durationSound = 0;	//Duration of a soundFile
		private static $soundMd5;			//soundFile md5
		
		/* Both */
		private static $maxDuration = 0;

		function __construct($debug = false) {
			global $config;
			$this->eventNow();
			$this->generateMusic($debug);
			$this->generateSound($debug);
			$this->maxDuration($debug);
		}
		
		function generateAudio() {
			echo $this->outputJson();
		}
		
		public function eventNow($debug = false) {
			$eventName = null;
			$dateExploded = explode ('.',startUpSound::$currentDate);
			$dayToday = $dateExploded[0];
			$monthToday = $dateExploded[1];
			$yearToday = $dateExploded[2];

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
					
					default:
						$eventName = "common";
					break;
				}
				if($eventName) {
					startUpSound::$eventNow = $eventName;
				}
				
				if($debug === true) {
					echo $eventName;
				}
		}
		
		private function generateMusic($debug = false) {
			global $config;
			$this->easter($config['easterMusRarity']);

			if($config['enableMusic'] === true) {
				$currentMusFolder = static::$AbsolutesoundPath.'/'.static::$musMountPoint.static::$easter;  							//Folder of music /Avalon/sites/foxLogin/www/launcher/files/eventSounds/mus/easter
				startUpSound::$musFilesNum = countFilesNum($currentMusFolder, '.mp3');													//Count of music
				$RandMusFile = 'mus'.rand(1,static::$musFilesNum).'.mp3';																//Getting random File
				
				//***********************************************								
				startUpSound::$selectedMusic = str_replace(static::$AbsolutesoundPath.'/mus',"",$currentMusFolder).'/'.$RandMusFile; 	//Local musPath
				startUpSound::$musFileAbsolute = $currentMusFolder.'/'.$RandMusFile;													//Absolute musFilePath
				//***********************************************
				
				if(file_exists(static::$musFileAbsolute)) {
					startUpSound::$musMd5 = md5_file(static::$musFileAbsolute);
					$mp3MusFile = new MP3File(static::$musFileAbsolute);
					startUpSound::$durationMus = $mp3MusFile->getDurationEstimate();
				} else {
					static::$selectedMusic = "musicOff";
				}

			} else {
				static::$selectedMusic = "musicOff";
			}
				if($debug === true) {
						$outputArray = array(
							"selectedFile" 			=> static::$selectedMusic,
							"musFileAbsolutePath" 	=> static::$musFileAbsolute,
							"musFileDuration" 		=> static::$durationMus,
							"filesInDir"			=> static::$musFilesNum,
							"selectedMusFileHash" 	=> static::$musMd5,
							"eventName" 			=> static::$eventNow);
						
						echo var_dump($outputArray);
				}
		}
		
		private function generateSound($debug = false) {
			global $config;
			$this->easter($config['easterMusRarity']);

			if($config['enableVoice'] === true) {
				$currentSoundFolder = static::$AbsolutesoundPath.'/'.static::$eventNow.static::$easter;			//Folder of Sounds	/Avalon/sites/foxLogin/www/launcher/files/eventSounds/common/easter
				startUpSound::$soundFilesNum = countFilesNum($currentSoundFolder, '.mp3');						//Count of Sounds
				$RandSoundFile = 'voice'.rand(1,static::$soundFilesNum).'.mp3';
				
				//***********************************************
				startUpSound::$selectedSound = str_replace(static::$AbsolutesoundPath,"",$currentSoundFolder).'/'.$RandSoundFile;
				startUpSound::$soundFileAbsolute = static::$AbsolutesoundPath.static::$selectedSound;
				//***********************************************
				
				if(file_exists(static::$soundFileAbsolute)) {
					startUpSound::$soundMd5 = md5_file(static::$soundFileAbsolute);
					$mp3SoundFile = new MP3File(static::$soundFileAbsolute);
					startUpSound::$durationSound = $mp3SoundFile->getDurationEstimate();
				} else {
					static::$selectedSound = 'soundOff';
				}

			} else {
				static::$selectedSound = 'soundOff';
			}
				if($debug == true) {
					$outputArray = array(
						"selectedFile" 				=> static::$selectedSound,
						"soundFileAbsolutePath" 	=> static::$soundFileAbsolute,
						"soundFileDuration" 		=> static::$durationSound,
						"soundsInDir"				=> static::$soundFilesNum,
						"selectedSoundFileHash" 	=> static::$soundMd5,
						"eventName" 				=> static::$eventNow);
		
						echo var_dump($outputArray);
					}
		}
		
		private function easter($chance) {
			$minRange = 1;
			$maxRange = 1000;
				if (mt_rand($minRange, $maxRange) <= $chance){
					startUpSound::$easter = "/easter";
				} else {
					startUpSound::$easter = "";
				}
		}
		
		private function maxDuration($debug = false) {
			$duration;
			if(static::$durationMus > static::$durationSound) {
				$duration = static::$durationMus;
			} else {
				$duration= static::$durationSound;
			}
				startUpSound::$maxDuration = $duration;
			
			if($debug === true) {
				echo static::$durationSound.' '.static::$durationMus.' '.static::$maxDuration;
			}
		}
		
		
		private function outputJson() {
			$outputArray = array(
					"maxDuration" 		=> static::$maxDuration,
					"selectedMusic" 	=> static::$selectedMusic,
					"selectedSound" 	=> static::$selectedSound,
					"soundMd5" 			=> static::$soundMd5,
					"MusicMd5" 			=> static::$musMd5,
					"eventName" 		=> static::$eventNow);

			return json_encode($outputArray);
		}	
	}