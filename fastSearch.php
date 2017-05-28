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
	
	$searchRow = $_GET["searchRow"];
	
	$query = "select first_name,second_name,third_name,date_format(birth_date,(select description from dateformats where id=birthDateFormat_id)) as bdate,
	(select name from warriorranks where id=rank_id) as rank from warriors where concat(first_name,' ',second_name,' ', third_name)=?;";
	
	$stmt = $db->prepare($query);
	$stmt->bind_param("s",$searchRow);
	
	$stmt->execute();
	
	$result = $stmt->get_result();
	while ($row = $result->fetch_assoc()) {
			echo "<warrior>";
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