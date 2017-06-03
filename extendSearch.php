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
		$where += "first_name=" . $_GET["first_name"];
	}
	
	$first_name = $_GET["first_name"];
	$second_name = $_GET["second_name"];
	$third_name = $_GET["third_name"];
	$bdate = $_GET["bdate"];
	$rankId = $_GET["rankId"];
	$mun = $_GET["mun"];
	$wmc = $_GET["wmc"];
	$finding = $_GET["finding"];
	
	// $query = "select id, first_name ,second_name,third_name,date_format(birth_date,(select description from dateformats where id=birthDateFormat_id)) as bdate,
	// (select name from warriorranks where id=rank_id) as rank from warriors where concat(first_name,' ',second_name,' ', third_name)=?;";
	
	// $stmt = $db->prepare($query);
	// $stmt->bind_param("s",$searchRow);
	
	// $stmt->execute();
	
	// $result = $stmt->get_result();
	// while ($row = $result->fetch_assoc()) {
	echo "<warrior>";
	echo "<id>" . $first_name . "</id>";
	echo "<first_name>" . $first_name . "</first_name>";
	echo "<second_name>" . $second_name . "</second_name>";
	echo "<third_name>" . $third_name . "</third_name>";
	echo "<bdate>" . $bdate . "</bdate>";
	echo "<rank>" . $rankId . "</rank>";			
	echo "</warrior>";		
	// }
	
	// $stmt->close();
	$db->close();
	echo '</response>';
?>