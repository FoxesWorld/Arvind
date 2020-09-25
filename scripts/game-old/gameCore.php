<?php
Error_Reporting(E_ALL);
Ini_Set('display_errors', true);
define('INCLUDE_CHECK',true); 
include ('gameFunctions.php');
include ('cases.php');

	if(isset($_GET['getBalance'])){
		$user_login = $_GET['getBalance'];
		if($user_login){
			die(getBalance($user_login));
		}
	}
