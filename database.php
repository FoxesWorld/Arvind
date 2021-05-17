<?php
/*
=====================================================
 Database class
-----------------------------------------------------
 https://Foxesworld.ru/
-----------------------------------------------------
 Copyright (c) 2016-2020  FoxesWorld
-----------------------------------------------------
 Данный код защищен авторскими правами
-----------------------------------------------------
 Файл: database.php
-----------------------------------------------------
 Версия: 0.0.10.4 Release Candidate
-----------------------------------------------------
 Назначение: Класс для работы с БД
=====================================================
*/
if(!defined('INCLUDE_CHECK')) {
	require ($_SERVER['DOCUMENT_ROOT'].'/index.php');
	exit();
}	
	define('CONFIG', true);
	require ('config.php');
	include_once("scripts/functions.inc.php");		
	if (extension_loaded('openssl')) {
		include_once("scripts/security/security_openssl.php");
	} else if(extension_loaded('mcrypt')){
		include_once("scripts/security/security_mcrypt.php");
	} else {
		exit("Отсутствуют расширения mcrypt и openssl!");
	}

	


class db {

  const CHARSET = 'utf8';
 
  static private $db;
  protected static $instance = null;

  public function __construct($db_user, $db_pass, $db_name, $db_location = 'localhost', $show_error=1){
    if (self::$instance === null){
      try {
        self::$db = new PDO(
          'mysql:host='.$db_location.';dbname='.$db_name,
          $db_user,
          $db_pass,
          $options = [
              PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
              PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
              PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES ".self::CHARSET
          ]
        );
      } catch (PDOException $e) {
		  $query = strval($e->queryString);
		  $message = $e->getMessage();
		  display_error($message, $query, '1');
      }
    }
    return self::$instance;
  }
 
  /**
   * @param $stmt
   * @return PDOStatement
   */
  public static function query($stmt)  {
    return self::$db->query($stmt);
  }
 
  /**
   * @param $stmt
   * @return PDOStatement
   */
  public static function prepare($stmt)  {
    return self::$db->prepare($stmt);
  }
 
  /**
   * @param $query
   * @return boolean
   */
  static public function exec($query) {
    return self::$db->exec($query);
  }
 
  /**
   * @return string
   */
  static public function lastInsertId() {
    return self::$db->lastInsertId();
  }
 
  /**
   * @param $query
   * @param array $args
   * @return PDOStatement
   * @throws Exception
   */
  public static function run($query, $args = [])  {
    try{
      if (!$args) {
        return self::query($query);
      }
      $stmt = self::prepare($query);
      $stmt->execute($args);
      return $stmt;
    } catch (PDOException $e) {
          $query = strval($e->queryString);
		  $message = $e->getMessage();
		  display_error($message, $query, '1');
    }
  }

  /**
   * @param $query
   * @param array $args
   * @return mixed
   */
  public static function getRow($query, $args = [])  {
    return self::run($query, $args)->fetch();
  }

  /**
   * @param $query
   * @param array $args
   * @return array
   */
  public static function getRows($query, $args = [])  {
    return self::run($query, $args)->fetchAll();
  }

  /**
   * @param $query
   * @param array $args
   * @return mixed
   */
  public static function getValue($query, $args = [])  {
    $result = self::getRow($query, $args);
    if (!empty($result)) {
      $result = array_shift($result);
    }
    return $result;
  }

  /**
   * @param $query
   * @param array $args
   * @return array
   */
  public static function getColumn($query, $args = [])  {
    return self::run($query, $args)->fetchAll(PDO::FETCH_COLUMN);
  }

  public static function sql($query, $args = [])
  {
    self::run($query, $args);
  }
}