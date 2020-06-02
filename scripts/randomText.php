<?php 
	//if(defined("INCLUDE_CHECK")){
	$request = $_POST['request'];
	if($request === "textGenerate"){
		echo $request;
	}
	} else {
		die("Hacking Attempt!");
	}

?>