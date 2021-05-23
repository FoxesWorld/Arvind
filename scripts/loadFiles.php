<?php
/*
=====================================================
 Let's load your data! | loadFiles
-----------------------------------------------------
 https://Foxesworld.ru/
-----------------------------------------------------
 Copyright (c) 2016-2021  FoxesWorld
-----------------------------------------------------
 Данный код защищен авторскими правами
-----------------------------------------------------
 Файл: loadFiles.php
-----------------------------------------------------
 Версия: 0.0.1.3 Alpha
-----------------------------------------------------
 Назначение: Files out + crypt + size
=====================================================
*/
	if(!defined('INCLUDE_CHECK')) {
		die ("Hacking Attempt!");
	}

	$serverInfo = serversListArray("SELECT * FROM `servers` WHERE Server_name = '$client'");
	$version = $serverInfo[0]['version'];

		/* Basic client structure (Alpha) */
		$clientStructureCheck = array($config['clientsDir']."assets",
		$config['clientsDir']."versions/".$version, 
		$config['clientsDir']."versions/".$version."/libraries",
		$config['clientsDir']."versions/".$version."/".$version.".jar",
		$config['clientsDir']."versions/".$version."/natives/");
		
		foreach($clientStructureCheck as $key) {
			if(!file_exists($key)) {
				die(Security::encrypt("client<$> $client", $config['key1']));
			}
		}

        $md5user  = strtoint(xorencode(str_replace('-', '', uuidConvert($realUser)), $config['protectionKey']));
        $md5ServersDat	  = @md5_file($config['clientsDir']."clients/".$client."/servers.dat");
        $sizeServersDat  = @filesize($config['clientsDir']."clients/".$client."/servers.dat");
		$usrsessions = $config['md5launcherjar']."<:>".$md5user."<:>".$md5ServersDat."<>".$sizeServersDat."<:><br>".$realUser.'<:>'.strtoint(xorencode($sessid, $config['protectionKey'])).'<br>'.$acesstoken.'<br>';

        if($config['temp'] === true) {
		checkWriteRights();
        $filecache = $config['clientsDir']."clients/".$client.'/'.$client;
                if (file_exists($filecache)) {
                     $fp = fopen($filecache, "r");
                     $hash_md5 = fgets($fp);
                     fclose($fp);
                } else {
                    $hash_md5 = hashcVersion($client);
					try {
						$fp = fopen($filecache, "w");
						fwrite($fp, $hash_md5);
						fclose($fp);
					} catch(Exception $e){
						die($e);
					}
				}
	} else {
            $hash_md5 = hashcVersion($client);
	}
            echo Security::encrypt($usrsessions.$hash_md5, $config['key1']);