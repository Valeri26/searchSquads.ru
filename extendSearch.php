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
	
	
	
	$where = "";
	
	if (isset($_GET["first_name"])) {
		$where = $where .  "first_name='" . $_GET["first_name"] ."'"; 
	}
	if (isset($_GET["second_name"])) {
		if ( strlen($where) > 3 ) { $where = $where . " AND "; }
		$where = $where .  "second_name='" . $_GET["second_name"] . "'";
	}                                    
	if (isset($_GET["third_name"])) { 
		if ( strlen($where) > 3 ) { $where = $where . " AND "; }	
		$where = $where .  "third_name='" . $_GET["third_name"] . "'";
	}                                    
	if (isset($_GET["bdate"])) {        
		if ( strlen($where) > 3 ) { $where = $where . " AND "; }
		$where = $where .  "YEAR(bdate)=" . $_GET["bdate"];
	}                                    
	if (isset($_GET["rankId"])) {        
		if ( strlen($where) > 3 ) { $where = $where . " AND "; }
		$where = $where .  "rankId=" . $_GET["rankId"];
	}                                    
	if (isset($_GET["mun"])) {           
		if ( strlen($where) > 3 ) { $where = $where . " AND "; }
		$where = $where .  "mun='" . $_GET["mun"] . "'";
	}                                   
	if (isset($_GET["wmc"])) {          
		if ( strlen($where) > 3 ) { $where = $where . " AND "; }
		$where = $where .  "wmc='" . $_GET["wmc"] . "'";
	}                                    
	if (isset($_GET["finding"])) {      
		if ( strlen($where) > 3 ) { $where = $where . " AND "; }
		$where = $where .  "finding='" . $_GET["finding"] . "'";
	}
	
	$query = " SELECT * FROM (
	select
	id, first_name ,second_name,third_name,
	date_format(birth_date,(select description from dateformats where id=birthDateFormat_id)) as bdate,
	(select name from warriorranks where id=rank_id) as rank,
	militaryUnit_name, 
	(select concat(name,',',address) from warriormilitarycommissariats where id=militarycommissariats_id) as wmc,
	(select concat(name,' ',year(begin_date),'-',year(end_date)) from wars where war_id=war_id) as war,
	concat((select name from leavingreasons where id=leavingreason_id),';',
	date_format(leavingreason_date,(select description from dateformats where id=leavingReasonDateFormat_id)),';',
	leavingReason_description) as lr,
	(select location from findings where warrior_id=id) findingLocation
	from warriors) T ";

	if (strlen($where) > 3) {
		$query = $query . " WHERE " . $where;
	}
	$query = $query . " ORDER BY first_name LIMIT 20;";
	
	$stmt = $db->prepare($query);	
	
	$stmt->execute();
	
	$result = $stmt->get_result();
	while ($row = $result->fetch_assoc()) {
		echo "<warrior>";
		echo "<id>" . $row["id"] . "</id>";
		echo "<first_name>" . $row["first_name"] . "</first_name>";
		echo "<second_name>" . $row["second_name"] . "</second_name>";
		echo "<third_name>" . $row["third_name"] . "</third_name>";
		echo "<bdate>" . $row["bdate"] . "</bdate>";
		echo "<rank>" . $row["rank"] . "</rank>";			
		echo "</warrior>";		
	}
	
	$stmt->close();
	$db->close();
	echo '</response>';
?>