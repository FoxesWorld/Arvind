<?php
    header('Content-Type: text/html; charset=utf-8');
	define('INCLUDE_CHECK',true);
	define('ROOT_DIR', $_SERVER['DOCUMENT_ROOT']);
	define('ENGINE_DIR', ROOT_DIR.'/engine');
	
	if(isset ($_POST['action'])) {
		include("connect.php");
		include_once("loger.php");
		include_once("authlib/uuid.php");
		$x  = $_POST['action'];
		$x = str_replace(" ", "+", $x);
		$yd = Security::decrypt($x, $key2);
		if($yd==null) {
		die('Access Error!');
		exit;
		}
		@list($action, $client, $login, $postPass, $launchermd5, $ctoken) = explode(':', $yd);
	} else {
		exit;
	}

	try {
	if (!preg_match("/^[a-zA-Z0-9_-]+$/", $login) || !preg_match("/^[a-zA-Z0-9_-]+$/", $postPass) || !preg_match("/^[a-zA-Z0-9_-]+$/", $action)) {
		exit(Security::encrypt("errorLogin<$>", $key1));
    }
	if(!file_exists($uploaddirs)) die ("Путь к скинам не является папкой! Укажите правильный путь.");
	if(!file_exists($uploaddirp)) die ("Путь к плащам не является папкой! Укажите правильный путь.");
	

    if($ctoken == "null") {
			if($crypt === 'hash_md5' || $crypt === 'hash_foxy') {
				$stmt = $db->prepare("SELECT $db_columnUser,$db_columnPass FROM $db_table WHERE BINARY $db_columnUser= :login");
				$stmt->bindValue(':login', $login);
				$stmt->execute();
				$stmt->bindColumn($db_columnPass, $realPass);
				$stmt->bindColumn($db_columnUser, $realUser);
				$stmt->fetch();
				if($crypt === 'hash_smf')
				$salt = $realUser;
			}
			$checkPass = hash_name($crypt, $realPass, $postPass, @$salt);

			if($useantibrut) {
				$ip  = getenv('REMOTE_ADDR');	
				$time = time();
				$bantime = $time+(10);
				$stmt = $db->prepare("Select sip,time From sip Where sip='$ip' And time>'$time'");
				$stmt->execute();
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				$real = $row['sip'];
				if($ip == $real) {
					$stmt = $db->prepare("DELETE FROM sip WHERE time < '$time';");
					$stmt->execute();
					exit(Security::encrypt("temp<$>", $key1));
				}
				
				if ($login != $realUser) {
					$stmt = $db->prepare("INSERT INTO sip (sip, time)VALUES ('$ip', '$bantime')");
					$stmt->execute();
					exit(Security::encrypt("errorLogin<$>", $key1));
				}
				if(!strcmp($realPass,$checkPass) == 0 || !$realPass) {
					$stmt = $db->prepare("INSERT INTO sip (sip, time)VALUES ('$ip', '$bantime')");
					$stmt->execute();
					exit(Security::encrypt("errorLogin<$>", $key1));
				}

			} else {
				if($checkPass !=  $realPass)  die(Security::encrypt('errorLogin<$>', $key1));
			}
	}

    if($ctoken == "null") {
       	$acesstoken = token();
    } else {
       	$acesstoken = $postPass;
    }
	
		$sessid = token();
        $stmt = $db->prepare("SELECT user, token FROM usersession WHERE user= :login");
		$stmt->bindValue(':login', $login);
		$stmt->execute();
		$rU = $stmt->fetch(PDO::FETCH_ASSOC);
		if($rU['user'] != null) {
            $realUser = $rU['user'];
		}

        if($ctoken != "null") {

			if($rU['token'] != $acesstoken || $login != $realUser) {
	        	exit(Security::encrypt("errorLogin<$>", $key1));
			}
	    }
		if($login == $rU['user']) {
            if($ctoken == "null") {
				$stmt = $db->prepare("UPDATE usersession SET session = '$sessid', token = :token WHERE user= :login");
				$stmt->bindValue(':token', $acesstoken);
            }
            else {
            	$stmt = $db->prepare("UPDATE usersession SET session = '$sessid' WHERE user= :login");
            }
			$stmt->bindValue(':login', $login);
			$stmt->execute();
		}
		else if($ctoken == "null" || $login != $rU['user']) {
			$stmt = $db->prepare("INSERT INTO usersession (user, session, md5, token) VALUES (:login, '$sessid', :md5, '$acesstoken')");
			$stmt->bindValue(':login', $realUser);
			$stmt->bindValue(':md5', str_replace('-', '', uuidConvert($realUser)));
			$stmt->execute();
		}
	
	if($useban) {
	    $time = time();
	    $tipe = '2';
		$stmt = $db->prepare("Select name From $banlist Where name= :login And type<'$tipe' And temptime>'$time'");
		$stmt->bindValue(':login', $login);
		$stmt->execute();
	    if($stmt->rowCount()) {
			$stmt = $db->prepare("Select name,temptime From $banlist Where name= :login And type<'$tipe' And temptime>'$time'");
			$stmt->bindValue(':login', $login);
			$stmt->execute();
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			exit(Security::encrypt('Временный бан до '.date('d.m.Yг. H:i', $row['temptime'])." по времени сервера", $key1));
	    }
			$stmt = $db->prepare("Select name From $banlist Where name= :login And type<'$tipe' And temptime='0'");
			$stmt->bindValue(':login', $login);
			$stmt->execute();
		if($stmt->rowCount()) {
	      exit(Security::encrypt("Вечный бан", $key1));
	    }
	}
    
	if($action == 'auth') {

/*	if($checklauncher) {
	if($launchermd5 != null) {
    if($launchermd5 == @$md5launcherexe) {
		    $check = "1";
		    }
		    if($launchermd5 == @$md5launcherjar) {
		       $check = "1";
		    }
		}
		if(!@$check == "1") {
			exit(Security::encrypt("badlauncher<$>_$masterversion", $key1));
		}
	} */

        if($assetsfolder){
			$z = "/"; 
			} else { 
			$z = ".zip"; 
		}

		if(
		!file_exists("clients/assets".$z)||
		!file_exists("clients/".$client."/bin/")||
		!file_exists("clients/".$client."/mods/")||
		!file_exists("clients/".$client."/coremods/")||
		!file_exists("clients/".$client."/natives/")||
		!file_exists("clients/".$client."/config.zip")
		)
		die(Security::encrypt("client<$> $client", $key1));

        $md5user  = strtoint(xorencode(str_replace('-', '', uuidConvert($realUser)), $protectionKey));
        $md5zip	  = @md5_file("clients/".$client."/config.zip");
        $md5ass	  = @md5_file("clients/assets.zip");
        $sizezip  = @filesize("clients/".$client."/config.zip");
        $sizeass  = @filesize("clients/assets.zip");
		$usrsessions = "$masterversion<:>$md5user<:>".$md5zip."<>".$sizezip."<:>".$md5ass."<>".$sizeass."<br>".$realUser.'<:>'.strtoint(xorencode($sessid, $protectionKey)).'<br>'.$acesstoken.'<br>';

        function hashc($assetsfolder,$client) {
        	if($assetsfolder) {
	        	$hash_md5    = str_replace("\\", "/",checkfiles('clients/'.$client.'/bin/').checkfiles('clients/'.$client.'/mods/').checkfiles('clients/'.$client.'/coremods/').checkfiles('clients/'.$client.'/natives/').checkfiles('clients/assets')).'<::>assets/indexes<:b:>assets/objects<:b:>assets/virtual<:b:>'.$client.'/bin<:b:>'.$client.'/mods<:b:>'.$client.'/coremods<:b:>'.$client.'/natives<:b:>';
			} else {
		        $hash_md5    = str_replace("\\", "/",checkfiles('clients/'.$client.'/bin/').checkfiles('clients/'.$client.'/mods/').checkfiles('clients/'.$client.'/coremods/').checkfiles('clients/'.$client.'/natives/')).'<::>'.$client.'/bin<:b:>'.$client.'/mods<:b:>'.$client.'/coremods<:b:>'.$client.'/natives<:b:>';
		    }
		    return $hash_md5;
        }

        if($temp) {
	        $filecashe = 'temp/'.$client;
			if (file_exists($filecashe)) {
				 $fp = fopen($filecashe, "r");
				 $hash_md5 = fgets($fp);
				 fclose($fp);
			} else {
				$hash_md5 = hashc($assetsfolder,$client);
				$fp = fopen($filecashe, "w");
				fwrite($fp, $hash_md5);
				fclose($fp);
			}
	    } else {
	    	$hash_md5 = hashc($assetsfolder,$client);
	    }
        echo Security::encrypt($usrsessions.$hash_md5, $key1);
	} elseif($action == 'radio'){
		die("Let's listen some!");
	}
	
	
	} catch(PDOException $pe) {
		die(Security::encrypt("errorsql<$>", $key1).$logger->WriteLine($log_date.$pe));  //вывод ошибок MySQL в m.log
	}
	
	
	
	
	
	
	//===================================== Вспомогательные функции ==================================//

	function xorencode($str, $key) {
		while(strlen($key) < strlen($str)) {
			$key .= $key;
		}
		return $str ^ $key;
	}

	function strtoint($text) {
		$res = "";
		for ($i = 0; $i < strlen($text); $i++) $res .= ord($text{$i}) . "-";
		$res = substr($res, 0, -1);
		return $res;
	}

	function strlen_8bit($binary_string) {
        if (function_exists('mb_strlen')) {
            return mb_strlen($binary_string, '8bit');
        }
        return strlen($binary_string);
    }
	
    function substr_8bit($binary_string, $start, $length) {
        if (function_exists('mb_substr')) {
            return mb_substr($binary_string, $start, $length, '8bit');
        }
        return substr($binary_string, $start, $length);
    }
	
	function pass_get_info($hash) {
        $return = true;
        if (substr_8bit($hash, 0, 4) == '$2y$' && strlen_8bit($hash) == 60) {
            $return = false;
        }
        return $return;
    }
	
	function pass_verify($password, $hash) {
        $ret = crypt($password, $hash);
		
        if (!is_string($ret) || strlen_8bit($ret) != strlen_8bit($hash) || strlen_8bit($ret) <= 13) {
            return false;
        }
        $status = 0;
        for ($i = 0; $i < strlen_8bit($ret); $i++) {
            $status |= (ord($ret[$i]) ^ ord($hash[$i]));
        }
        return $status === 0;
    }
	
	function hash_name($ncrypt, $realPass, $postPass, $salt) {
		$cryptPass = false;

		if ($ncrypt === 'hash_md5' or $ncrypt === 'hash_launcher') {
				$cryptPass = md5($postPass);
		}

		if ($ncrypt === 'hash_foxy') {
			if(pass_get_info($realPass)) {
				$cryptPass = md5(md5($postPass));
			} else {
				if(pass_verify($postPass, $realPass)) {
					$cryptPass = $realPass;
				} else {
					$cryptPass = "0";
				}
			}
		}
		return $cryptPass;
	}

	    function checkfiles($path) {
        $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);
        $massive = "";
		    foreach($objects as $name => $object) {
			    $basename = basename($name);
			    $isdir = is_dir($name);
			    if ($basename!="." and $basename!=".." and !is_dir($name)){
			     	$str = str_replace('clients/', "", str_replace($basename, "", $name));
			     	$massive = $massive.$str.$basename.':>'.md5_file($name).':>'.filesize($name).'<:>';
			    }
		    }
		    return $massive;
        }

        function token() {
        $chars="0123456789abcdef";
        $max=64;
        $size=StrLen($chars)-1;
        $password=null;
        while($max--)
        $password.=$chars[rand(0,$size)];
          return $password;
        }

    function imagestype($binary) {
	    if (
	        !preg_match(
	            '/\A(?:(\xff\xd8\xff)|(GIF8[79]a)|(\x89PNG\x0d\x0a)|(BM)|(\x49\x49(?:\x2a\x00|\x00\x4a))|(FORM.{4}ILBM))/',
	            $binary, $hits
	        )
	    ) {
	        return 'application/octet-stream';
	    }
	    static $type = array (
	        1 => 'image/jpeg',
	        2 => 'image/gif',
	        3 => 'image/png',
	        4 => 'image/x-windows-bmp',
	        5 => 'image/tiff',
	        6 => 'image/x-ilbm',
	    );
	    return $type[count($hits) - 1];
    }