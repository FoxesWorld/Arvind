<?php

			function eventNow(){
				global $config;
					//Vars definition
					$soundsPath = FILES_DIR."eventSounds";
					$pathJSON = "/launcher/files/eventSounds";
					$musFilesNum = countFilesNum($soundsPath.'/mus', '.mp3');					//Count of ordinary Music
					$easterMusFilesNum = countFilesNum($soundsPath.'/mus/easterMusic', '.mp3'); //Count of Easter Music
					$eventName;
					$easter;
					
					//Date explosion
					$dateExploded = explode ('.',CURRENT_DATE);
					$dayToday = $dateExploded[0];
					$monthToday = $dateExploded[1];
					$yearToday = $dateExploded[2];
					
				//Checking each of 12 months
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
				}

				//Generating a Mus File
				if($config['enableMusic'] === true) {
					$easterMusFile = easterEgg($config['easterMusRarity']);
					if(isset($musRange)){
						$musRange = explode('/',$musRange);
						$minRange = $musRange[0];
						$maxRange = $musRange[1];
					}

					if(isset($minRange) && isset($maxRange)) {
						$RandMusic = rand($minRange,$maxRange);
					} else {
						$RandMusic = rand(1,$musFilesNum);
					}
					$selectedMusic = "mus".$RandMusic.".mp3";
					$selectedSound;
					if($easterMusFile == "YES"){
						$selectedMusic = "easterMusic/mus".rand(1, $easterMusFilesNum).".mp3";
					}

					$musMd5 = md5_file($soundsPath.'/mus/'.$selectedMusic);
				}
				//************************
				
				//Generating a sound file
			if($config['enableVoice'] === true) {
				 if(isset($eventName)){ //If we have an event today
					$status = 'Event';
					$eventSoundsNum = countFilesNum($soundsPath.'/'.$eventName, '.mp3'); //Num of event Sounds	
					$selectedSound = $eventName.rand(1,$eventSoundsNum).'.mp3';
					$thisSoundMd5 = md5_file($soundsPath.'/'.$eventName.'/'.$selectedSound);
				} else {
					$status = 'noEvent';
					$eventName = 'common';
					$easterSoundFile = easterEgg($config['easterMusRarity']);
					if($easterSoundFile == "YES"){
						$easterFilesNum = countFilesNum($soundsPath.'/'.$eventName.'/easterVoices', '.mp3'); //Num of easter sounds
						$selectedSound = "easterVoices/voice".rand(1,$easterFilesNum).".mp3";
						$thisSoundMd5 = md5_file($soundsPath.'/'.$eventName.'/easterVoices/'.$selectedSound);
					} else {
						$commonFilesNum = countFilesNum($soundsPath.'/'.$eventName, '.mp3'); //Num of commonFiles
						$selectedSound = "voice".rand(1,$commonFilesNum).".mp3";
						$thisSoundMd5 = md5_file($soundsPath.'/'.$eventName.'/'.$selectedSound);
					}

				}
			} else {
				$status = 'soundOff';
			}
			//************************
				
				$outputArray = array(
					"Status" => $status,
					//"filesDir" => $pathJSON.'/',
					"selectedMusic" => $selectedMusic,
					"selectedSound" => $selectedSound,
					"soundMd5" => $thisSoundMd5,
					"MusicMd5" => $musMd5,
					"eventName" => $eventName);

				return json_encode($outputArray);
			} 
			
			function easterEgg($chance){
				$minRange = 1;
				$maxRange = 1000;
				if (mt_rand($minRange, $maxRange) <= $chance){
					$returnText = 'YES';
				} else {
					$returnText= 'NO';
				}
				return $returnText;
			}
?>