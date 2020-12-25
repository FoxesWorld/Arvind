<?php
/*
=====================================================
 Where are you from? | geoIP 
-----------------------------------------------------
 https://Foxesworld.ru/
-----------------------------------------------------
 Copyright (c) 2016-2020  FoxesWorld
-----------------------------------------------------
 Данный код защищен авторскими правами
-----------------------------------------------------
 Файл: geoIP.class.php
-----------------------------------------------------
 Версия: 0.0.4 Alpha
-----------------------------------------------------
 Назначение: Определение и запись места входа в БазуДанных
=====================================================
*/
if(!defined('INCLUDE_CHECK')) {
	require ($_SERVER['DOCUMENT_ROOT'].'/index.php');
	exit();
}	

//===================================================
class geoPlugin {
	
    //the geoPlugin server
    var $host = 'http://www.geoplugin.net/php.gp?ip={IP}&base_currency={CURRENCY}';
 
    var $currency = 'USD';
    var $ip = null;
    var $city = null;
    var $region = null;
    var $areaCode = null;
    var $dmaCode = null;
    var $countryCode = null;
    var $countryName = null;
    var $continentCode = null;
    var $latitute = null;
    var $longitude = null;
    var $currencyCode = null;
    var $currencySymbol = null;
    var $currencyConverter = null;
 
    function __construct($ip = null) {
        global $_SERVER;
 
        if (is_null($ip)) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
 
        $host = str_replace( '{IP}', $ip, $this->host );
        $host = str_replace( '{CURRENCY}', $this->currency, $host );
        $data = array();
        $response = $this->fetch($host);
        $data = unserialize($response);
 
        $this->ip = $ip;
        $this->city = $data['geoplugin_city'];
        $this->region = $data['geoplugin_region'];
        $this->areaCode = $data['geoplugin_areaCode'];
        $this->dmaCode = $data['geoplugin_dmaCode'];
        $this->countryCode = $data['geoplugin_countryCode'];
        $this->countryName = $data['geoplugin_countryName'];
        $this->continentCode = $data['geoplugin_continentCode'];
        $this->latitude = $data['geoplugin_latitude'];
        $this->longitude = $data['geoplugin_longitude'];
        $this->currencyCode = $data['geoplugin_currencyCode'];
        $this->currencySymbol = $data['geoplugin_currencySymbol'];
        $this->currencyConverter = $data['geoplugin_currencyConverter'];
		geoPlugin::getIP($this->ip,$this->countryName,$this->city,false);
    }
 
    private function fetch($host) {
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $host);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_USERAGENT, 'geoPlugin PHP Class v1.0');
            $response = curl_exec($ch);
            curl_close ($ch);
        } elseif(ini_get('allow_url_fopen')) {
            $response = file_get_contents($host, 'r');
        } else {
            trigger_error ('geoPlugin class Error: Cannot retrieve data. Either compile PHP with cURL support or enable allow_url_fopen in php.ini ', E_USER_ERROR);
            return;
        }
 
        return $response;
    }
 
    private function convert($amount, $float=2, $symbol=true) {
        if ( !is_numeric($this->currencyConverter) || $this->currencyConverter == 0 ) {
            trigger_error('geoPlugin class Notice: currencyConverter has no value.', E_USER_NOTICE);
            return $amount;
        }
        if ( !is_numeric($amount) ) {
            trigger_error ('geoPlugin class Warning: The amount passed to geoPlugin::convert is not numeric.', E_USER_WARNING);
            return $amount;
        }
        if ( $symbol === true ) {
            return $this->currencySymbol . round( ($amount * $this->currencyConverter), $float );
        } else {
            return round( ($amount * $this->currencyConverter), $float );
        }
    }
 
    private function nearby($radius=10, $limit=null) {
 
        if ( !is_numeric($this->latitude) || !is_numeric($this->longitude) ) {
            trigger_error ('geoPlugin class Warning: Incorrect latitude or longitude values.', E_USER_NOTICE);
            return array( array() );
        }
        $host = "http://www.geoplugin.net/extras/nearby.gp?lat=" . $this->latitude . "&long=" . $this->longitude . "&radius={$radius}";
 
        if ( is_numeric($limit) )
            $host .= "&limit={$limit}";
 
        return unserialize( $this->fetch($host) );
    }
	
	private static function getIP($ip,$ipLocation,$ipRegion,$log=false){
		global $config;
		if($ip){
			$db = new db($config['db_user'],$config['db_pass'],$config['dbname_launcher']);
			if(!isset($_COOKIE['ipAdded'])){
				$query = "SELECT * FROM `ipDatabase` WHERE ip = '$ip'";
				$data = $db->getValue($query);
				if (!isset($data) || $data === false) {
					//$date="[".date("d m Y H:i")."] ";
						if(!$ipLocation){
							$ipLocation = 'Nowhere';
						}
						if(!$ipRegion){
							$ipRegion = 'Land Of Fools';
						}					
					$db->run("INSERT INTO `ipDatabase`(`ipLocation`, `ipRegion`, `ip`) VALUES ('$ipLocation','$ipRegion','$ip')");  
					geoPlugin::addCityCount($ipRegion);
					if($log === true){
						echo 'Adding '.$ip.' - '.$ipLocation.'('.$ipRegion.') '.'to IP database';
					}
					setcookie("ipAdded", $ip, time()+36000);
				} else {
					if($log === true){
						echo 'Cookie was not found but Ip - '.$ip.' is already added in the Database, so get another one cookie! 
							  Thanks for helping us to build server statistics :3';
						setcookie("ipAdded", $ip, time()+36000);
					}
				}
			} else {
				if($log === true){
					echo 'Cookie was set for ip - '.$_COOKIE['ipAdded'];
				}
			}
		} else {
			echo "That can't happen!";
		}
	}

	private static function addCityCount($city){
		global $config;
		$db = new db($config['db_user'],$config['db_pass'],$config['dbname_launcher']);
		$query = "SELECT * FROM ipCity WHERE cityName = '$city'";
		$data = $db->getValue($query);  
		
		if(!isset($data) || $data === false){
			$query = "INSERT INTO `ipCity`(`cityName`) VALUES ('$city')";
		} else {
			$query = "UPDATE `ipCity` SET `cityCount`= cityCount+1 WHERE cityName = '$city'";	
		}
		$db->run($query);
	}
}