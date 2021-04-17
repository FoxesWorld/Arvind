<?php
/*
=====================================================
 load your data!
-----------------------------------------------------
 https://Foxesworld.ru/
-----------------------------------------------------
 Copyright (c) 2016-2021  FoxesWorld
-----------------------------------------------------
 Данный код защищен авторскими правами
-----------------------------------------------------
 Файл: loadFiles.php
-----------------------------------------------------
 Версия: 0.0.1.0 Alpha
-----------------------------------------------------
 Назначение: Отдача файлов + Хеш + Размер
=====================================================
*/

	$serverInfo = serversListArray("SELECT * FROM `servers` WHERE Server_name = '$client'");
	$version = $serverInfo[0]['version'];

	/*
	function loadFiles($version, $client, $json, $checkFiles){
		if(!file_exists($config['clientsDir']."assets") ||
			!file_exists($config['clientsDir']."versions/".$version) ||
			!file_exists($config['clientsDir']."versions/".$version."/libraries") ||
			!file_exists($config['clientsDir']."versions/".$version."/".$version.".jar") ||
			!file_exists($config['clientsDir']."versions/".$version."/natives/") ||
			!file_exists($config['clientsDir']."clients/".$client."/mods/") ||
			!file_exists($config['clientsDir']."clients/".$client."/servers.dat")) {
				return Security::encrypt("client<$> $client", $config['key1']);
		}
		if($json === true) {
			
		} else {
			
		}
	} */
/* Проверка структуры клиента и отдача файлов + Хеш */
		/* Basic client structure (Alpha) */
			checkWriteRights();
			if(!file_exists($config['clientsDir']."assets") ||
			!file_exists($config['clientsDir']."versions/".$version) ||
			!file_exists($config['clientsDir']."versions/".$version."/libraries") ||
			!file_exists($config['clientsDir']."versions/".$version."/".$version.".jar") ||
			!file_exists($config['clientsDir']."versions/".$version."/natives/") ||
			!file_exists($config['clientsDir']."clients/".$client."/mods/") ||
			!file_exists($config['clientsDir']."clients/".$client."/servers.dat")) {
				die(Security::encrypt("client<$> $client", $config['key1']));
			}
		/* If a file or folder is abscent dies with an error */
	
		/* Argument for coremods (Old) */
			if(file_exists($config['clientsDir'].$client."/coremods/")) {
				$arg = checkfiles($config['clientsDir'].$client."/coremods/");
			}
			
				
        $md5user  = strtoint(xorencode(str_replace('-', '', uuidConvert($realUser)), $config['protectionKey']));
        $md5ServersDat	  = @md5_file($config['clientsDir'].$client."/servers.dat");
        $sizeServersDat  = @filesize($config['clientsDir'].$client."/servers.dat");
        $sizeass  = @filesize($config['clientsDir']."assets.zip");
		$usrsessions = $config['md5launcherjar']."<:>".$md5user."<:>".$md5ServersDat."<>".$sizeServersDat."<:><br>".$realUser.'<:>'.strtoint(xorencode($sessid, $config['protectionKey'])).'<br>'.$acesstoken.'<br>';

        if($config['temp'] === true) {
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