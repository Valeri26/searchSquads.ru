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
	{
	$query = "select
id, first_name ,second_name,third_name,
date_format(birth_date,(select description from dateformats where id=birthDateFormat_id)) as bdate,
(select name from warriorranks where id=rank_id) as rank,
militaryUnit_name, 
(select concat(name,',',address) from warriormilitarycommissariats where id=militarycommissariats_id) as wmc,
(select concat(name,' ',year(begin_date),'-',year(end_date)) from wars where war_id=war_id) as war,
concat((select name from leavingreasons where id=leavingreason_id),';',
date_format(leavingreason_date,(select description from dateformats where id=leavingReasonDateFormat_id)),';',
leavingReason_description) as lr
from warriors where id = ?;";

	$row = executeQuery($query, $db)->fetch_assoc();
	
	echo "<warrior>";
	echo "<id>" . $row["id"] . "</id>";
	echo "<first_name>" . $row["first_name"] . "</first_name>";
	echo "<second_name>" . $row["second_name"] . "</second_name>";
	echo "<third_name>" . $row["third_name"] . "</third_name>";
	echo "<bdate>" . $row["bdate"] . "</bdate>";
	echo "<rank>" . $row["rank"] . "</rank>";
	echo "<militaryUnit_name>" . $row["militaryUnit_name"] . "</militaryUnit_name>";
	echo "<wmc>" . $row["wmc"] . "</wmc>";
	echo "<war>" . $row["war"] . "</war>";
	echo "<lr>" . $row["lr"] . "</lr>";
		
		
	}
	{
	$query = "select
(select concat(name,',',location) from searchsquads where id=searchsquad_id) as searchsquad,
date_format(findingdate,(select description from dateformats where id=findingDateFormat_id)) as fdate,
location, findingdescription, locationdescription
from findings where warrior_id = ?;";

	$row = executeQuery($query, $db)->fetch_assoc();
	
	echo "<finding> ";
	echo "<searchsquad>" . $row["searchsquad"] . "</searchsquad>";
	echo "<fdate>" . $row["fdate"] . "</fdate>";	
	echo "<location>" . $row["location"] . "</location>";
	echo "<findingdescription>" . $row["findingdescription"] . "</findingdescription>";	
	echo "<locationdescription>" . $row["locationdescription"] . "</locationdescription>";
	echo "</finding>";		
	}	
	{
	$query = "select name, address from warriorlinks where warrior_id = ?;";

	$row = executeQuery($query, $db)->fetch_assoc();
	
	echo "<links> ";
	echo "<name>" . $row["name"] . "</name>";
	echo "<address>" . $row["address"] . "</address>";	
	echo "</links>";	
		
	}	
	{
	$query = "select location, burialdate,(select concat (name,',',location) from burialmilitarycommissariats where id=commissariat_id) as bmc
	from burials where warrior_id = ?;";

	$row = executeQuery($query, $db)->fetch_assoc();
	
	echo "<burial> ";
	echo "<location>" . $row["location"] . "</location>";
	echo "<burialdate>" . $row["burialdate"] . "</burialdate>";
	echo "<bmc>" . $row["bmc"] . "</bmc>";		
	echo "</burial>";		
	}
	{
	$query = "select first_name, second_name, third_name, address from relatives 
where relative_id in (select relative_id from warrior2relative where warrior_id = ?);";
	
	echo "<relatives> ";
	$result = executeQuery($query, $db);
	while ($row = $result->fetch_assoc()) {
		echo "<relative> ";
		echo "<first_name>" . $row["first_name"] . "</first_name>";
		echo "<second_name>" . $row["second_name"] . "</second_name>";
		echo "<third_name>" . $row["third_name"] . "</third_name>";
		echo "<address>" . $row["address"] . "</address>";		
		echo "</relative>";
		}
	echo "</relatives>";
	
	echo "</warrior>";			
	}
	
	
	$db->close();
	echo '</response>';
	
	function executeQuery($query, $db) {
		$warriorId = $_GET["warriorId"];
		
		$stmt = $db->prepare($query);
		$stmt->bind_param("i",$warriorId);
		
		$stmt->execute();
		
		$result = $stmt->get_result();		
		
		$stmt->close();
		
		return $result;
	}
?>