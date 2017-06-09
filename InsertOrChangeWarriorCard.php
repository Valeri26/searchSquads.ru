<?php

	$host='localhost';
	$user='root';
	$password='';
	$dbname='search_squad_results';
	
	//session_start();
	header('Content-Type: text/xml');
	echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
	echo '<response>';
	
	$db=mysqli_connect($host,$user,$password,$dbname);
	mysqli_set_charset($db, "utf8");
	if ($db->connect_errno) {
		echo '</response>';
		exit;
	}
	
	
	
	
	
	$query = "";
	$stmt = $db->prepare($query);	
	$stmt->execute();
	
	$result = $stmt->get_result();	
	
	$stmt->close();
	$db->close();
	echo '</response>';
?>