<?php
/*
=====================================================
 startUpSound Class
-----------------------------------------------------
 https://Foxesworld.ru/
-----------------------------------------------------
 Copyright (c) 2016-2021 FoxesWorld
-----------------------------------------------------
 This code is reserved
-----------------------------------------------------
 File: startUpSound.class.php
-----------------------------------------------------
 Version: 0.1.11 Alpha
-----------------------------------------------------
 Usage: Sound generation
=====================================================
*/

/*		 USAGE
		
		$startSound = new startUpSound($config['debugStartUpSound']);
		$sounds = $startSound->generateAudio();
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
		private static $musPerEvent = true;
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
		private static $maxDuration = 0;	//Maximum duration
		private static $soundRangeDebug;	//Debug info of the range
		
		//Initialisation
		function __construct($debug = false) {
			startUpSound::$debug = $debug;
			$this->eventNow(static::$debug);
			$this->generateMusic(static::$debug);
			$this->generateSound(static::$debug);
			$this->maxDuration(static::$debug);
		}
		
		//Function for getting the result of program work
		public function generateAudio() {
			if(static::$debug === false) {
				echo $this->outputJson();
			}
		}
		
		/**
		* @param boolean $debug
		* @return String eventNow, String musRange, String soundRange
		*/
		private function eventNow($debug = false) {
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
						$soundRange = "33";
						$musRange ="1";
						//$eventName = "newYear";
						switch($dayToday){ //WW2 Victory
							case 9:
							break;
						}
					break;
					
					case 6:
						switch($dayToday){ //Markus Pearson birthday
							case 1:
							break;
							
							case 22:
								$eventName = "twistOfTheSun";
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
					if(strpos($musRange, '/')){
						startUpSound::$musRange = explode('/',$musRange);
					} else {
						startUpSound::$musRange = $musRange;
					}
				}
				
				if(isset($soundRange)){
					if(strpos($soundRange, '/')){
						startUpSound::$soundRange = explode('/',$soundRange);
					} else {
						startUpSound::$soundRange = $soundRange;
					}
					
				}

				if($debug === true) {
					echo '<div style="border: 1px solid black; padding: 5px; border-radius: 10px; width: fit-content; margin: 15px;">'.
						'<h1 style="font-size: large;margin: 0;"><b>Event Name:</b></h1>'.
						$eventName.'</div>';
				}
		}
		
		/**
		* @param boolean $debug
		* @return String {Random mus with parameters}
		*/
		private function generateMusic($debug = false) {
			global $config;
			$minRange = 1;
			$maxRange = 1;
			
			$this->easter($config['easterMusRarity'], static::$debug, 'music');
			if($config['enableMusic'] === true) {

					if(static::$musPerEvent === true) {
						$currentMusFolder = static::$AbsolutesoundPath.'/'.static::$eventNow.'/'.static::$musMountPoint.static::$easter;
					} else {
						$currentMusFolder = static::$AbsolutesoundPath.'/'.static::$musMountPoint.static::$easter;
					}

					startUpSound::$musFilesNum = countFilesNum($currentMusFolder, '.mp3'); //Count of music
					$maxRange = startUpSound::$musFilesNum;										
				
				if(isset(static::$musRange)) {
					$RandMusFile = $this->genRange('mus', static::$musRange);
				} else {
					$RandMusFile = 'mus'.rand($minRange,$maxRange).'.mp3'; //Getting random musFile
				}
				//MusDirs****************************************								
				startUpSound::$selectedMusic = str_replace(static::$AbsolutesoundPath,"",$currentMusFolder).'/'.$RandMusFile; 	//Local musPath
				startUpSound::$musFileAbsolute = $currentMusFolder.'/'.$RandMusFile; //Absolute musFilePath
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
							"<b>eventName:</b>".			static::$eventNow.static::$soundRangeDebug.'</div>';
						
						echo $output;
				}
		}
		
		/**
		* @param boolean $debug
		* @return String {Random sound with parameters}
		*/
		private function generateSound($debug = false) {
			global $config;
			$minRange = 1;
			$maxRange = 1;

			$this->easter($config['easterMusRarity'], static::$debug, 'sound');
			if($config['enableVoice'] === true) {

				$currentSoundFolder = static::$AbsolutesoundPath.'/'.static::$eventNow.static::$easter;			//Folder of Sounds
				startUpSound::$soundFilesNum = countFilesNum($currentSoundFolder, '.mp3');						//Count of Sounds
				$maxRange = static::$soundFilesNum;

				if(isset(static::$soundRange)) {
					$RandSoundFile = $this->genRange('voice', static::$soundRange);
				} else {
					$RandSoundFile = 'voice'.rand($minRange,$maxRange).'.mp3'; //Getting random sound file
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
						"<b>eventName:</b>".static::$eventNow.static::$soundRangeDebug.'
					</div>';
						echo $output;
					}
		}

		/**
		* @param boolean $debug, Integer chance
		* @return String easter
		*/
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
		
		/**
		* @param boolean $debug
		* @return Integer maxDuration
		*/
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
		
		/**
		* @param String type {voice||mus}, Integer range
		* @return random {voice||mus}
		*/
		private function genRange($type, $range){
					if(isset($range) && is_array($range)){
							$minRange = $range[0];
							$maxRange = $range[1];
							startUpSound::$soundRangeDebug = '<div style="border: 1px solid black; padding: 5px; border-radius: 10px; width: fit-content; margin: 15 0px;">'.
							'<h1 style="font-size: large;margin: 0;">'.$type.'Range</h1>'.
							 "<b>minRange:</b>".$minRange.'<br>'.
							 "<b>maxRange:</b>".$maxRange.'</div>';
					} elseif(isset($range) && !is_array($range)){
							$minRange = $range;
							$maxRange = $range;
							startUpSound::$soundRangeDebug = '<div style="border: 1px solid black; padding: 5px; border-radius: 10px; width: fit-content; margin: 15 0px;">'.
							'<h1 style="font-size: large;margin: 0;">'.$type.'Range</h1>'.
							 "<b>".$type."ToPlay:</b>".$minRange.'</div>';
					}
				$RandSoundFile = $type.rand($minRange,$maxRange).'.mp3';
				return $RandSoundFile;
		}
		
		/**
		* @param NO
		* @return jsonAnswer
		*/
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