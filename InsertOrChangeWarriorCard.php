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
	
	$wid = $_GET['wid'];
	$fname = $_GET['fname'];
	$sname = $_GET['sname'];
	$tname = $_GET['tname'];
	$bdate = $_GET['bdate'];
	$rankid = $_GET['rankid'];
	$mun = $_GET['mun'];
	$wmcid = $_GET['wmcid'];
	$wmcname = $_GET['wmc_name'];
	$wmcaddr = $_GET['wmc_addr'];
	$warid = $_GET['warid'];
	
	$lrid = $_GET['lrid'];
	$lrdate = $_GET['lrdate'];
	$lrdateformat = $_GET['lrdateformat'];
	$lrdesc = $_GET['lrdesc'];
	
	$ssid = $_GET['ssid'];
	$ssname = $_GET['ss_name'];
	$ssaddr = $_GET['ss_addr'];
	
	$fdate = $_GET['findingdate'];
	$fdateformat = $_GET['findingdateformat'];
	$flocation = $_GET['findinglocation'];
	$fldesc = $_GET['fldesc'];
	$fdesc = $_GET['fdesc'];
	
	$burialdate = $_GET['burialdate'];
	$buriallocation = $_GET['buriallocation'];
	$burialcommissariat = $_GET['burialcommissariat'];
	
	$relatives = array();
	
	$i = 1;
	while(isset($_GET["relid".$i])) {
		
		$relid = $_GET['relid'.$i];
		$relFName = $_GET['relFName'.$i];
		$relSName = $_GET['relSName'.$i];
		$relTName = $_GET['relTName'.$i];
		$relAddr = $_GET['relAddr'.$i];
		
		echo "<z> $relid, $relFName, $relSName, $relTName, $relAddr </z>";
		
		array_push($relatives, [$relid, $relFName, $relSName, $relTName, $relAddr]);
		$i = $i + 1;
	}
	
	
	// Определяем Идентификатор РВК
	while ($wmcid == -1) {
		$query = " SELECT id FROM warriormilitarycommissariats WHERE name = ? LIMIT 1";
		$stmt = $db->prepare($query);
		$stmt -> bind_param("s", $wmcname);
		$stmt -> execute();
		
		$result = $stmt->get_result();
		
		while($row = $result->fetch_assoc()) {
			$wmcid = $row['id'];
		}
		
		if ($wmcid == -1) {			
			$query = "INSERT INTO warriormilitarycommissariats(name,address) VALUES(?,?);";
			$stmt = $db->prepare($query);
			$stmt -> bind_param("ss", $wmcname, $wmcaddr);
			$stmt -> execute();
		}
		
		$stmt -> close();
	}
	
	// Определяем ИД Поискового отряда
	while ($ssid == -1) {
		$query = "select id from searchsquads where name = ? limit 1;";
		$stmt = $db->prepare($query);
		$stmt -> bind_param("s", $ssname);
		$stmt -> execute();
		
		$result = $stmt->get_result();
		
		while($row = $result->fetch_assoc()) {
			$ssid = $row['id'];
		}
		
		if ($ssid == -1) {			
			$query = "INSERT INTO searchsquads(name,location) VALUES(?,?);";
			$stmt = $db->prepare($query);
			$stmt -> bind_param("ss", $ssname, $ssaddr);
			$stmt -> execute();
		}
		
		$stmt -> close();
	}
	
	
	if ($bdate == 0) $bdate = 'NULL';
	else $bdate = "'$bdate-01-01'";
	
	if ($rankid == 0) $rankid = 'NULL';
	
	if ($wmcid == 0) $wmcid = 'NULL';
	if ($warid == 0) $warid = 'NULL';
	
	if ($wid == -1 ) {
			$query = "INSERT INTO warriors(first_name, second_name, third_name, birth_date, birthDateFormat_id, rank_id, militaryUnit_name, militaryCommissariats_id, war_id, leavingReason_id, leavingReason_date, leavingReasonDateFormat_id, leavingReason_description) VALUES 
			('$fname','$sname','$tname',$bdate,1,$rankid,'$mun',$wmcid,$warid,$lrid,'$lrdate',$lrdateformat,'$lrdesc');";
			
			$stmt = $db->prepare($query);
			$stmt -> execute();
			 
			$query = "select id from warriors where first_name=? AND second_name=? AND third_name=? order by id desc limit 1;";
			$stmt = $db->prepare($query);
			$stmt -> bind_param("sss", $fname, $sname, $tname);
			$stmt -> execute();
			$result = $stmt->get_result();
			while($row = $result->fetch_assoc()) {
				$wid = $row['id'];
			}

			
			if ($wid != -1) {
				// findings
				$query = "INSERT INTO Findings VALUES ($wid, $ssid, '$fdate', $fdateformat, '$flocation', '$fdesc', '$fldesc');";			
				$stmt = $db->prepare($query);
				$stmt -> execute(); 
			
			
				// burialcommissariat
				$query = "INSERT INTO Burials(location, commissariatName, burialDate, warrior_id) VALUES ('$buriallocation', '$burialcommissariat', '$burialdate', $wid);";
				$stmt = $db->prepare($query);
				$stmt -> execute();				
			}	
	} else {
		$query = "UPDATE warriors SET first_name = '$fname', second_name='$sname', third_name='$tname', birth_date=$bdate, rank_id=$rankid, militaryUnit_name='$mun', militaryCommissariats_id=$wmcid, war_id=$warid, leavingReason_id=$lrid, leavingReason_date='$lrdate', leavingReasonDateFormat_id=$lrdateformat, leavingReason_description='$lrdesc' WHERE id = $wid;";
		$stmt = $db->prepare($query);
		$stmt -> execute();
		
		$query = "UPDATE Findings SET searchSquad_id=$ssid, findingDate='$fdate', findingDateFormat_id=$fdateformat, location='$flocation', findingDescription='$fdesc', locationDescription='$fldesc' WHERE warrior_id = $wid;";
		$stmt = $db->prepare($query);
		$stmt -> execute();

		$query = "UPDATE Burials SET location='$buriallocation', commissariatName='$burialcommissariat', burialDate='$burialdate' WHERE warrior_id=$wid;";
		$stmt = $db->prepare($query);
		$stmt -> execute();
	}
	
	
	for($i = 0; $i < count($relatives); $i++) {
		$relid 		= $relatives[$i][0];
		$relFName 	= $relatives[$i][1];
		$relSName 	= $relatives[$i][2];
		$relTName 	= $relatives[$i][3];
		$relAddr 	= $relatives[$i][4];
		
		if ($relid == -1) {
			$query = "INSERT INTO relatives(first_name, second_name, third_name, address) VALUES(?,?,?,?);";
			$stmt = $db->prepare($query);
			$stmt -> bind_param("ssss", $relFName, $relSName, $relTName, $relAddr);
			$stmt -> execute();
			$stmt -> close();
			
			$query = "select relative_id from relatives where first_name='$relFName' AND second_name='$relSName' AND third_name='$relTName' order by relative_id desc limit 1;";
			echo $query;
			$stmt = $db->prepare($query);			
			$stmt -> execute();
			$result = $stmt->get_result();
			while($row = $result->fetch_assoc()) {
				$relid = $row['relative_id'];
			}
			
			$query = "INSERT INTO warrior2relative VALUES($wid,$relid);";
			$stmt = $db->prepare($query);
			$stmt -> execute();
			
		} else {
			$query = "UPDATE relatives SET first_name = '$relFName', second_name = '$relSName', third_name = '$relTName', address = '$relAddr' WHERE relative_id = $relid;";
			$stmt = $db->prepare($query);			
			$stmt -> execute();			
		}
	}
	
	$db->close();
	echo '</response>';
?>