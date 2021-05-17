<?php
/*
=====================================================
 startUpSound Class
-----------------------------------------------------
 https://Foxesworld.ru/
-----------------------------------------------------
 Copyright (c) 2016-2021 FoxesWorld
-----------------------------------------------------
 Данный код защищен авторскими правами
-----------------------------------------------------
 Файл: startUpSound.class.php
-----------------------------------------------------
 Версия: 0.1.7 Alpha
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
		private static $debug = false;
		
		/* Mus */
		private static $selectedMusic; 		//Selected mus File
		private static $musFileAbsolute;	//Absolute musFilePath
		private static $durationMus = 0;	//Duration of a musFile
		private static $musMd5;				//musFile md5
		private static $musRange;			//Range of muic files
		
		/* Sound */
		private static $selectedSound; 		//Selected sound File
		private static $soundFileAbsolute;	//Absolute soundFilePath
		private static $durationSound = 0;	//Duration of a soundFile
		private static $soundMd5;			//soundFile md5
		private static $soundRange;			//Range of sound files
		
		/* Both */
		private static $maxDuration = 0;

		function __construct($debug = false) {
			startUpSound::$debug = $debug;
			$this->eventNow(static::$debug);
			$this->generateMusic(static::$debug);
			$this->generateSound(static::$debug);
			$this->maxDuration(static::$debug);
		}
		
		function generateAudio() {
			if(static::$debug === false) {
				echo $this->outputJson();
			}
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
						//$eventName = "newYear";
						//$soundRange = "1/1";
						switch($dayToday){ //День победы
							case 9:
							break;
						}
					break;
					
					case 6:
						switch($dayToday){ //День рождения Нотча
							case 1:
							break;
						}
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
							case($dayToday < 31 && $dayToday != 22 && $dayToday != 31):
								$eventName = "winterHolidays";
							break;
							
							case 22:
								$eventName = "twistOfTheSun";
							break;
							
							case 31:
								$eventName = "newYear";
								$musRange ="1/8";
								
							break;
							
						}
					break;
				}
				if($eventName) {
					startUpSound::$eventNow = $eventName;
				} else {
					$eventName = "common";
				}

				if(isset($musRange)){
					startUpSound::$musRange = explode('/',$musRange);
				}
				
				if(isset($soundRange)){
					startUpSound::$soundRange = explode('/',$soundRange);
				}

				if($debug === true) {
					echo '<div style="border: 1px solid black; padding: 5px; border-radius: 10px; width: fit-content; margin: 15px;">'.
						'<h1 style="font-size: large;margin: 0;"><b>Event Name:</b></h1>'.
						$eventName.'</div>';
				}
		}
		
		private function generateMusic($debug = false) {
			global $config;
			
			$this->easter($config['easterMusRarity'], static::$debug, 'music');
			if($config['enableMusic'] === true) {
					if(isset(static::$musRange)){
						$minRange = static::$musRange[0];
						$maxRange = static::$musRange[1];
						$musRangeOutput = '<div style="border: 1px solid black; padding: 5px; border-radius: 10px; width: fit-content; margin: 15 0px;">'.
						'<h1 style="font-size: large;margin: 0;">musRange</h1>'.
						 "<b>minRange:</b>".$minRange.'<br>'.
						 "<b>maxRange:</b>".$maxRange.'</div>';
					}
				$currentMusFolder = static::$AbsolutesoundPath.'/'.static::$musMountPoint.static::$easter;  							//Folder of music
				startUpSound::$musFilesNum = countFilesNum($currentMusFolder, '.mp3');													//Count of music
				//Getting random musFile
				if(isset($minRange) && isset($maxRange)) {
					$RandMusFile = 'mus'.rand($minRange,$maxRange).'.mp3';
				} else {
					$RandMusFile = 'mus'.rand(1,static::$musFilesNum).'.mp3';																
				}


				//MusDirs****************************************								
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
						$output =
						'<div style="border: 1px solid black; padding: 5px; border-radius: 10px; width: fit-content; margin: 15px;">'.
						'<h1 style="font-size: large;margin: 0;">Mus Gen</h1>'.
							"<b>selectedFile:</b>".			static::$selectedMusic.'<br>'.
							"<b>musFileAbsolutePath:</b>".	static::$musFileAbsolute.'<br>'.
							"<b>musFileDuration:</b>".		static::$durationMus.'<br>'.
							"<b>filesInDir:</b>".			static::$musFilesNum.'<br>'.
							"<b>selectedMusFileHash:</b>".	static::$musMd5.'<br>'.
							"<b>eventName:</b>".			static::$eventNow.$musRangeOutput.'</div>';
						
						echo $output;
				}
		}
		
		private function generateSound($debug = false) {
			global $config;

			$this->easter($config['easterMusRarity'], static::$debug, 'sound');
			if($config['enableVoice'] === true) {
					if(isset(static::$soundRange)){
						$minRange = static::$soundRange[0];
						$maxRange = static::$soundRange[1];
						$soundRangeOutput = '<div style="border: 1px solid black; padding: 5px; border-radius: 10px; width: fit-content; margin: 15 0px;">'.
						'<h1 style="font-size: large;margin: 0;">soundRange</h1>'.
						 "<b>minRange:</b>".$minRange.'<br>'.
						 "<b>maxRange:</b>".$maxRange.'</div>';
					}
				$currentSoundFolder = static::$AbsolutesoundPath.'/'.static::$eventNow.static::$easter;			//Folder of Sounds
				startUpSound::$soundFilesNum = countFilesNum($currentSoundFolder, '.mp3');						//Count of Sounds
				if(isset(static::$soundRange)) {
					$RandSoundFile = 'voice'.rand($minRange,$maxRange).'.mp3';
				} else {
					$RandSoundFile = 'voice'.rand(1,static::$soundFilesNum).'.mp3';
				}

				//SoundDirs**************************************
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
					$output =
					'<div style="border: 1px solid black; padding: 5px; border-radius: 10px; width: fit-content; margin: 15px;">'.
						'<h1 style="font-size: large;margin: 0;">Sound Gen</h1>'.
						"<b>selectedFile:</b>".static::$selectedSound.'<br>'.
						"<b>soundFileAbsolutePath:</b>".static::$soundFileAbsolute.'<br>'.
						"<b>soundFileDuration:</b>".static::$durationSound.'<br>'.
						"<b>soundsInDir:</b>".static::$soundFilesNum.'<br>'.
						"<b>selectedSoundFileHash:</b>".static::$soundMd5.'<br>'.
						"<b>eventName:</b>".static::$eventNow.$soundRangeOutput.'
					</div>';
		
						echo $output;
					}
		}
		
		private function easter($chance, $debug = false, $of) {
			global $config;
			$minRange = 1;
			$maxRange = 1000;
			$easterChance = mt_rand($minRange, $maxRange);
				if ($easterChance <= $chance){
					startUpSound::$easter = "/easter";
					$status = 'true';
				} else {
					startUpSound::$easter = "";
					$status = 'false';
				}
			if($debug === true) {
					echo 	'<div style="border: 1px solid black; padding: 5px; border-radius: 10px; width: fit-content; margin: 15px;">'.
							'<h1 style="font-size: large;margin: 0;">Easter '.$of.'</h1>
							<b>This rand: </b>'.$easterChance.' <= '.$config['easterMusRarity'].'<br>'.
							'<b>Easter status: </b>'.$status.
							'</div>';
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
				echo '
				<div style="border: 1px solid black; padding: 5px; border-radius: 10px; width: fit-content; margin: 15px;">
				<h1 style="font-size: large; margin: 0;">Music duration</h1>
							<b>Sound duration:</b>'.static::$durationSound.'
							<br><b>Mus duration:</b> '.static::$durationMus.'
							<br> <b>Max duration:</b>'.static::$maxDuration.'
				</div>';
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

			return json_encode($outputArray, JSON_UNESCAPED_SLASHES);
		}	
	}