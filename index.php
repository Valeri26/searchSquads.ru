<?php

$host='localhost';
$user='root';
$password='';
$dbname='search_squad_results';
$db=mysqli_connect($host,$user,$password,$dbname);
mysqli_set_charset($db, "utf8");
if ($db->connect_errno) {
	exit;
}
session_start();

 $str = "OK";
if (isset($_POST['txtLogin']) && isset($_POST['txtPassword'])) {
	 
	 
	 $login=$_POST['txtLogin'];
	 $password=$_POST['txtPassword'];
	 
	 $query="select * from users where login='$login' and password=md5('$password') LIMIT 1;";
	 $res=$db->query($query);
	 
	 if ($res->num_rows == 1) {
			 $row=$res->fetch_assoc();
			
			 $_SESSION['user_login']=$row['login'];			 
			 setcookie("user_login",$row['login'],time()+60);
			 
			
	 }
	 header("Location: index.php");
	 die();
} 
else if (isset($_POST['btnSignOut'])) {
	 signOut();
	 header("Location: index.php");
	 die();
}


	

 function islogin() {	 
	if (isset($_SESSION['user_login'])) return true;
	
	
	if (isset($_COOKIE['user_login'])) {
		$_SESSION['user_login']=$_COOKIE['user_login'];
		return true;
	}
	
	return false;
}

function signOut() {
	$_SESSION = array();
	if (isset($_COOKIE['user_login'])) {
		unset($_COOKIE['user_login']);
		setcookie("user_login",'',time()-6000);
	}
}
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>eCommerce template By Adobe Dreamweaver CC</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="eCommerceAssets/styles/eCommerceStyle.css" rel="stylesheet" type="text/css">
<!--The following script tag downloads a font from the Adobe Edge Web Fonts server for use within the web page. We recommend that you do not modify it.-->
<script>var __adobewebfontsappname__="dreamweaver"</script><script src="http://use.edgefonts.net/montserrat:n4:default;source-sans-pro:n2:default.js" type="text/javascript"></script>

<script> 
	var xmlHttp = createXmlHttpRequestObject();
	
	function createXmlHttpRequestObject() {
		var xmlHttp;
		if (window.ActiveXObject) {
			try {
				xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e) {
				xmlHttp = false;
			}
		}
		else
		{
			try {
				xmlHttp = new XMLHttpRequest();
			} catch (e) {
				xmlHttp = false;
			}
		}
		if (!xmlHttp) {
		alert("Error while creation object XMLHttpRequest") }
		 else {
			return xmlHttp;
		}
	}
	

	function FastSearch() {	
		var txtFastSearch = document.getElementById("txtFastSearch");
		var searchRow = encodeURIComponent(txtFastSearch.value);
		
		if (xmlHttp.readyState == 4 || xmlHttp.readyState == 0) {
			xmlHttp.open("GET", "fastSearch.php?searchRow="+searchRow, true);
			xmlHttp.onreadystatechange = handleServerResponse;
			xmlHttp.send(null);	
		}
	}
	
	function ExtendSearch() {			
		var url = "extendSearch.php";
		var params = [];
		
		var first_name = encodeURIComponent(document.getElementById("first_name").value);	
		var second_name = encodeURIComponent(document.getElementById("second_name").value);
		var third_name = encodeURIComponent(document.getElementById("third_name").value);
		var bdate = encodeURIComponent(document.getElementById("bdate").value);
		var rankId = encodeURIComponent(document.getElementById("rankId").value);
		var mun = encodeURIComponent(document.getElementById("mun").value);
		var wmc = encodeURIComponent(document.getElementById("wmc").value);
		var finding = encodeURIComponent(document.getElementById("finding").value);

		if (first_name.length > 0) {
			params.push("first_name="+first_name);
		}
		if (second_name.length > 0) {
			params.push("second_name="+second_name);
		}
		if (third_name.length > 0) {
			params.push("third_name="+third_name);
		}
		if (bdate.length > 0) {
			params.push("bdate="+bdate);
		}
		if (rankId.length > 0) {
			params.push("rankId="+rankId);
		}
		if (mun.length > 0) {
			params.push("mun="+mun);
		}
		if (wmc.length > 0) {
			params.push("wmc="+wmc);
		}
		if (finding.length > 0) {
			params.push("finding="+finding);
		}		
		
		var sParams = params.reduce(
			 function(acc, curv, curidx) {
			 if (curidx == 0) return acc+curv;
			 return acc + "&" + curv; 
			 }, "");	
		
		 // var element = document.getElementById("mContent");
		 // element.innerHTML = url+"?"+sParams;
		// return ;
		if (xmlHttp.readyState == 4 || xmlHttp.readyState == 0) {
			xmlHttp.open("GET", url+"?"+sParams, true);
			xmlHttp.onreadystatechange = handleServerResponse;
			xmlHttp.send(null);	
		}
	}
	
	function handleServerResponse(){
		if (xmlHttp.readyState == 4)
		{
			if (xmlHttp.status == 200)
			{
				xmlResponse = xmlHttp.responseXML;	
				xmlRoot = xmlResponse.documentElement;
				
				warriors = xmlRoot.getElementsByTagName("warrior");
				
				var htmlCode = "<link rel='stylesheet' type='text/css' href='styles.css'> <center> <table id='tblFastSearch'>";
				
				htmlCode += "<tr><th>ИД</th><th>Фамилия</th><th>Имя</th><th>Отчество</th><th>Дата рождения</th><th>Звание</th></tr>";
				
				for( var i = 0; i < warriors.length; i++) {
					htmlCode += "<tr>";
					
					var children = warriors.item(i).childNodes;
					
					for( var j = 0; j < children.length; j++) {
						htmlCode += "<td>";	
						htmlCode += children.item(j).innerHTML;
						htmlCode += "</td>";
					}
					htmlCode += "</tr>";
				}
				
				htmlCode += "</table> </center>"
				
				 var element = document.getElementById("mContent");
				 element.innerHTML = htmlCode;
				 
				var tbl = document.getElementById("tblFastSearch");				
				var rows = tbl.getElementsByTagName("tr");
				for (i = 1; i < rows.length; i++) {
					
					var currentRow = rows.item(i);
					var createClickHandler = 
						function(row) 
						{
							return function() { 
													var cell = row.getElementsByTagName('td')[0];
													var id = cell.innerHTML;

													OpenWarriorCard(id);																
											};
						};

					currentRow.ondblclick = createClickHandler(currentRow);
				}
				
			}
		}
	}
	
	function OpenWarriorCard(id) {
		
		var html = 
			"<form id='dynForm' method='GET'> <input name='warriorId' type='hidden' value='" + id + "'></form>";
		
		document.body.innerHTML += html;
		
		document.getElementById('dynForm').submit();		
	}
	
	
</script>

</head>

<body>
<div id="mainWrapper">
  <header> 
    <!-- This is the header content. It contains Logo and links -->
    <div id="logo"> <!-- <img src="logoImage.png" alt="sample logo"> --> 
      <!-- Company Logo text --> 
      LOGO </div>
    <div id="headerLinks">
	
	
	
	
	<?php
	
	
	if (islogin()) 
	{
		print "<form action='' method='POST'>";
		//print "<b>" . htmlspecialchars($_SESSION['user_login']) . "&nbsp </b> ";
		print '<input type="submit" name="btnSignOut" id="button" value="Выйти">';
		print '</form>';
	}
	else 
	 {
		print "<form action='' method='POST'>";
		print '<input type="text" name="txtLogin" placeholder="Логин">';
		print '<input type="password"  name="txtPassword" placeholder="Пароль">';
		print '<input type="submit" name="btnSignIn" id="button" value="Войти">';
		print '</form>';
	 }
	?>
	
	</div>
  </header>
  <section id="offer">
   <label></label>

 
 <center>
 
 
<input type="text"  id="txtFastSearch" placeholder="ФИО бойца" value="Андреев Алексей Иванович">
<input type="button" name="btnFastSearch" id="button" value="Найти" onclick="FastSearch();">



 </center>
 
  
  </section>
  <div id="content">
    <section class="sidebar"> 
      <!-- This adds a sidebar with 1 searchbox,2 menusets, each with 4 links -->
      <div id="menubar">
        <nav class="menu">
          <h2><!-- Title for menuset 1 -->Меню </h2>
          <hr>
          <ul>
            <!-- List of links under menuset 1 -->
            <li><a href="index.php?aboutproject" title="Link">О проекте</a></li>
            <li><a href="http://lenww2.ru/" title="Link">Мемориалы</a></li>
            <li><a href="http://rf-poisk.ru/region/47/reestr/" title="Link">Поисковые отряды/объединения</a></li>
            <li class="notimp"><!-- notimp class is applied to remove this link from the tablet and phone views --><a href="index.php"  title="Link">Расширенный поиск</a></li>
			
			<?php
				if (islogin()) {
				print "<li><a href='index.php?warriorId=-1' title='Link'>Внести данные</a></li>";
				}
			?>
			
            <li><a href="index.php?contacts" title="Link">Контакты</a></li>
          </ul>
        </nav>
      </div>
    </section>
    <section class="mainContent">

	
	<div id="mContent">
	<?php
	
		
		if(isset($_GET['aboutproject'])) {
			include('aboutproject.tpl');
		} 
		else if(isset($_GET['contacts'])) {
			include('contacts.tpl');
		}
		else if (isset($_GET['warriorId'])) {
			$warriorId = (int) $_GET['warriorId'];
			$isLoggedIn = islogin();
			
			if ($warriorId == 0 || $warriorId < -1) {				
				exit('Ошибка!');
			}
			
			if ($warriorId == -1 && !$isLoggedIn) {
				exit("У вас недостаточно прав");
			}
			
			$first_name = "";
			$second_name = "";
			$third_name = "";
			$bdate = "";
			
			$rankid = "";
			$ranks = array();
			
			$militaryUnit_name = "";
			
			$wmcid = -1;
			$wmcs = array([-1,"",""]);
			
			$warid = 0;
			$wars = array();
			
			$lrid = 0;
			$lrdate = "";
			$lrdesc = "";
			$lrs = array();			
			$relatives = array();
			
			$searchsquadid = -1;
			$searchsquads = array([-1,"",""]);
			
			$findingdate = "";
			$location = "";
			$locationdescription = "";
			$findingdescription = "";
			$location = "";
			$burialdate = "";
			$commissariat_id = "";
		
			
			
			if ($warriorId > 0)  {
				$query = "select
				id, first_name ,second_name,third_name,
				date_format(birth_date,(select description from dateformats where id=birthDateFormat_id)) as bdate,
				rank_id,
				militaryUnit_name, 
				militarycommissariats_id,
				war_id,
				leavingreason_id as lrid, 
				date_format(leavingreason_date,(select description from dateformats where id=leavingReasonDateFormat_id)) as lrdate,
				leavingReason_description as lrdesc 				
				from warriors where id = ?;";

				$stmt = $db->prepare($query);
				$stmt->bind_param("i",$warriorId);		
				$stmt->execute();
				
				$row = $stmt->get_result()->fetch_assoc();
					
					
				$first_name	= $row['first_name'];
				$second_name = $row['second_name'];
				$third_name = $row['third_name'];
				$bdate = $row['bdate'];
				$rankid = $row['rank_id'];
				$militaryUnit_name = $row['militaryUnit_name'];
				$wmcid = $row['militarycommissariats_id'];
				$warid = $row['war_id'];
				$lrid = $row['lrid'];
				$lrdate = $row['lrdate'];
				$lrdesc = $row['lrdesc'];				
				
				$stmt->close();
				
				// relatives
				$query = "select relative_id, concat(first_name,' ',second_name,' ',third_name) name, address from relatives 
				where relative_id in (select relative_id from warrior2relative where warrior_id = ?);";
				
				$stmt = $db->prepare($query);
				$stmt->bind_param("i",$warriorId);		
				$stmt->execute();	
				
				$result = $stmt->get_result();
				while ($row = $result->fetch_assoc()) {
					array_push($relatives, [$row['relative_id'], $row['name'], $row['address']]); 
				}
				
				$stmt->close();
				
				//
				$query = "select
				searchsquad_id,
				date_format(findingdate,(select description from dateformats where id=findingDateFormat_id)) as fdate,
				location, findingdescription, locationdescription
				from findings where warrior_id = ?;";
				
				$stmt = $db->prepare($query);
				$stmt->bind_param("i",$warriorId);		
				$stmt->execute();	
				
				$row = $stmt->get_result()->fetch_assoc();
				
				$searchsquadid = $row['searchsquad_id'];
				$findingdate = $row['fdate'];
				$location = $row['location'];
				$locationdescription = $row['locationdescription'];
				$findingdescription = $row['findingdescription'];
				
				$stmt->close();
				
				//
				$query = "select
				location, date_format(burialdate, '%d-%m-%Y') burialdate,
				(select concat (name,',',location) from burialmilitarycommissariats where id=commissariat_id) as bmc
				from burials where warrior_id = ?;";
				
				$stmt = $db->prepare($query);
				$stmt->bind_param("i",$warriorId);		
				$stmt->execute();	
				
				$row = $stmt->get_result()->fetch_assoc();
				
				$location = $row['location'];
				$burialdate = $row['burialdate'];
				$location = $row['location'];
				$commissariat_id = $row['bmc'];
				
				$stmt->close();
				
			}
			
			if ($warriorId == -1 || $warriorId > 0){
					// getleaving reasons				
				$query = "select id, name from leavingreasons order by id;";
				$stmt = $db->prepare($query);
				$stmt->execute();
				
				$result = $stmt->get_result();
				while ($row = $result->fetch_assoc()) {
					array_push($lrs, [$row['id'], $row['name']]);
				}
				$stmt->close();
			
				// get ranks
				$query = "select id, name from warriorranks order by id;";
				$stmt = $db->prepare($query);
				$stmt->execute();
				
				$result = $stmt->get_result();
				while ($row = $result->fetch_assoc()) {
					array_push($ranks, [$row['id'], $row['name']]);
				}
				$stmt->close();
				
				//get warrior military commissariats;
				$query = "select id,name,address from warriormilitarycommissariats order by name;";
				$stmt = $db->prepare($query);
				$stmt->execute();
				
				$result = $stmt->get_result();
				while ($row = $result->fetch_assoc()) {
					array_push($wmcs, [$row['id'], $row['name'], $row['address']]);
				}
				$stmt->close();
				
				//get wars;
				$query = "select war_id id, concat(name,' (',YEAR(begin_date),'-',YEAR(end_date),')') name from wars order by war_id;";
				$stmt = $db->prepare($query);
				$stmt->execute();
				
				$result = $stmt->get_result();
				while ($row = $result->fetch_assoc()) {
					array_push($wars, [$row['id'], $row['name']]);
				}
				$stmt->close();
				
				// get search squads
				$query = "select id,name,location from searchsquads order by name;";
				$stmt = $db->prepare($query);
				$stmt->execute();
				
				$result = $stmt->get_result();
				while ($row = $result->fetch_assoc()) {
					array_push($searchsquads, [$row['id'], $row['name'], $row['location']]);
				}
				$stmt->close();
				
				
			}
			
			
			
			$readonly = 'readonly';
			if ($isLoggedIn) $readonly = '';
			
			$js_wmcs = "";
			$js_searchsquads = "";
			
			for($i = 0; $i < count($wmcs); $i++) {
				if ($wmcs[$i][0]==-1) continue;
				if (strlen($js_wmcs) > 0) $js_wmcs = $js_wmcs . ",";
				$js_wmcs = $js_wmcs . "[" . $wmcs[$i][0] . ",'" . $wmcs[$i][1] . "','" . $wmcs[$i][2] . "']";
			}
			for($i = 0; $i < count($searchsquads); $i++) {
				if ($searchsquads[$i][0]==-1) continue;
				if (strlen($js_searchsquads) > 0) $js_searchsquads = $js_searchsquads . ",";
				$js_searchsquads = $js_searchsquads . "[" . $searchsquads[$i][0] . ",'" . $searchsquads[$i][1] . "','" . $searchsquads[$i][2] . "']";
			}
				
			print "<link rel='stylesheet' type='text/css' href='styles.css'>";			
			print "<script> 
			
				function OnRowChanged(rowIndex) {
					var maxRowIndex = 10;
					if (rowIndex == maxRowIndex) return;
					
					var tb = document.getElementById('tblRelative');
					var nrow = tb.getElementsByTagName('tr').length;					
					
					if (rowIndex + 2 == nrow) {
						var row = tb.insertRow(rowIndex + 2);
						var cell1 = row.insertCell(0);
						var cell2 = row.insertCell(1);
						var cell3 = row.insertCell(2);
						
						cell2.innerHTML = \"<input type='text' onchange='OnRowChanged(\" + (rowIndex + 1) + \");'>\";
						cell3.innerHTML = \"<input type='text' onchange='OnRowChanged(\" + (rowIndex + 1) + \");'>\";
					}
				}
				
				function OnWMCChanged() {
					var wmcs = [$js_wmcs];
					
					var wmcid = document.getElementById('selWMC').value;

					var wmc_name = document.getElementById('wmc_name');
					var wmc_addr = document.getElementById('wmc_address');	

					wmc_name.value = '';
					wmc_addr.value = '';
						
					if (wmcid == -1) {
						wmc_name.readOnly = '';
						wmc_addr.readOnly = '';
					} else {
						wmc_name.readOnly = 'readonly';
						wmc_addr.readOnly = 'readonly';	

						
						for (i = 0; i < wmcs.length; i++) {
							
							if (wmcs[i][0] == wmcid) {								
								wmc_name.value = wmcs[i][1];
								wmc_addr.value = wmcs[i][2];
								break;
							}
						}
					}					
				}
				
				function OnSearchSquadChanged() {
					var searchsquads = [$js_searchsquads];
					
					var searchsquadid = document.getElementById('selSearchSquad').value;					
							
					ss_name = document.getElementById('searchsquad_name');
					ss_addr = document.getElementById('searchsquad_address');	

					ss_name.value = '';
					ss_addr.value = '';
					
					if (searchsquadid == -1) {
						ss_name.readOnly = '';
						ss_addr.readOnly = '';
					} else {
						ss_name.readOnly = 'readonly';
						ss_addr.readOnly = 'readonly';	

						
						for (i = 0; i < searchsquads.length; i++) {
							if (searchsquads[i][0] == searchsquadid) {								
								ss_name.value = searchsquads[i][1];
								ss_addr.value = searchsquads[i][2];
								break;
							}
						}
					}		
				}
				
				</script>";
			
			print "<center><form id='frmWarriorCard'> <table id='tblWarriorCard'>";
			
			
			
			print "<input id='warriorId' type='hidden' value='$warriorId' readonly>";
			print "<tr> <td colspan=2> <div align='center' style='font-size:18px;'> Данные о бойце </div> </td> </tr>";	   
			print "<tr>
				   <td> <label for='first_name'>Фамилия:</label> </td>
				   <td> <input id='first_name' type='text' value='$first_name' $readonly> </td>
				   </tr>";
			
			print "<tr>
				   <td> <label for='second_name'>Имя:</label> </td>
				   <td> <input id='second_name' type='text' value='$second_name' $readonly> </td>
				   </tr>";
				   
			print "<tr>
				   <td> <label for='third_name'>Отчество:</label> </td>
				   <td> <input id='third_name' type='text' value='$third_name' $readonly> </td>
				   </tr>";
				   
			print "<tr>
				   <td> <label for='bdate'>Год рождения:</label> </td>
				   <td> <input id='bdate' type='text' value='$bdate' $readonly> </td>
				   </tr>";
			
			print "<tr>
				   <td> <label for='rank'>Звание:</label> </td> <td>";				   
			if ($isLoggedIn) {
				print "<select>";
					print "<option";
					if ($warriorId == -1) print " selected ";
					print " disabled> --Выберите звание-- </option>";
					for($i = 0; $i < count($ranks); $i += 1) {
						print "<option value=$ranks[$i][0]";
						if ((int)$ranks[$i][0] == $rankid) print " selected ";
						print ">". $ranks[$i][1] ."</option>";
					}
				print "</select>";
			} else {
				for($i = 0; $i < count($ranks); $i++) {
					if ($ranks[$i][0] == $rankid) 
					print "<input id='rank' type='text' value='" . $ranks[$i][1] . "' readonly>";
				}				
			}			
			print "</td></tr>";

			print "<tr>
				   <td> <label for='militaryUnit_name'>Воинская часть:</label> </td>
				   <td> <input id='militaryUnit_name' type='text' value='$militaryUnit_name' $readonly> </td>
				   </tr>";
			
			/* РВК */
			print "<tr>
				   <td> <label for='wmc'>Каким РВК призван:</label> </td> <td>";				   
			if ($isLoggedIn) {
				print "<select id='selWMC' onchange='OnWMCChanged()'>";
					print "<option";
					if ($warriorId == -1) print " selected ";
					print " disabled> --Выберите РВК-- </option>";
					print "<option value=-1> Новый РВК </option>";
					
					for($i = 0; $i < count($wmcs); $i += 1) {
						if ($wmcs[$i][0] == -1) continue;
						
						print "<option value=" . $wmcs[$i][0];
						if ((int)$wmcs[$i][0] == $wmcid) print " selected ";
						print "> " . $wmcs[$i][1] . ", " . $wmcs[$i][2] . "</option>";
					}
					
				print "</select>";
			} else {
				for($i = 0; $i < count($wmcs); $i++) {
					if ($wmcs[$i][0] == $wmcid) 
					print "<input id='wmc' type='text' value='" . $wmcs[$i][1] . ", " . $wmcs[$i][2] . "' readonly>";
				}			
			}			
			print "</td></tr>";
			
			if ($isLoggedIn) {
				for($i = 0; $i < count($wmcs); $i++) {
					if ($wmcs[$i][0] == $wmcid) {
						print "<tr>
						   <td> <label for='wmc_name'>Название РВК:</label> </td>
						   <td> <input id='wmc_name' type='text' value='" . $wmcs[$i][1] . "' readonly> </td>
						   </tr>";
						print "<tr>
						   <td> <label for='wmc_address'>Адрес РВК:</label> </td>
						   <td> <input id='wmc_address' type='text' value='" . $wmcs[$i][2] . "' readonly> </td>
						   </tr>";
					}					
				}				
			}
			/* Война */
			print "<tr class='first_tr'>
				   <td> <label for='war'>Война:</label> </td> <td>";				   
			if ($isLoggedIn) {
				print "<select>";
					print "<option";
					if ($warriorId == -1) print " selected ";
					print " disabled> --Выберите войну-- </option>";
					for($i = 0; $i < count($wars); $i += 1) {
						print "<option value=$wars[$i][0]";
						if ((int)$wars[$i][0] == $warid) print " selected ";
						print ">". $wars[$i][1] ."</option>";
					}
				print "</select>";
			} else {
				for($i = 0; $i < count($wars); $i++) {
					if ($wars[$i][0] == $warid) 
					print "<input id='war' type='text' value='" . $wars[$i][1] . "' readonly>";
				}
			}			
			print "</td></tr>";
			   
			/* Причина выбытия */
			print "<tr>
				   <td> <label for='leavingreason'>Причина выбытия:</label> </td> <td>";				   
			if ($isLoggedIn) {
				print "<select>";
					print "<option";
					if ($warriorId == -1) print " selected ";
					print " disabled> --Выберите причину-- </option>";
					for($i = 0; $i < count($lrs); $i += 1) {
						print "<option value=$lrs[$i][0]";
						if ((int)$lrs[$i][0] == $lrid) print " selected ";
						print ">". $lrs[$i][1] ."</option>";
					}
					
				print "</select>";
			} else {
				for($i = 0; $i < count($lrs); $i++) {
					if ($lrs[$i][0] == $lrid) 
					print "<input id='leavingreason' type='text' value='" . $lrs[$i][1] . "' readonly>";
				}
			}	
			print "</td></tr>";
			
			print "<tr>
				   <td> <label for='lrdate'>Дата выбытия:</label> </td>
				   <td> <input id='lrdate' type='text' value='$lrdate' $readonly> </td>
				   </tr>";
			print "<tr>
				   <td> <label for='lrdesc'>Описание выбытия:</label> </td>
				   <td> <input id='lrdesc' type='text' value='$lrdesc' $readonly> </td>
				   </tr>";	   
				   
				
			print "<tr> <td colspan=2> <p align='center' style='font-size:18px;'> Сведения о родственниках</p> </td> </tr>
				   
				   <tr>
				   <td colspan=2> 
						<center>
						<table id='tblRelative'>
						<tr> <th> ИД </th> <th> ФИО </th> <th> Адрес </th> </tr>";
					
					for($i = 0; $i < count($relatives); $i++) {		
						
						$rel_id = $relatives[$i][0];
						$rel_name = $relatives[$i][1];
						$rel_addr = $relatives[$i][2];
						
						print "<tr> <td> $rel_id </td> <td>
						<input class='inpRel' type='text' value='$rel_name' onchange='OnRowChanged($i)' $readonly> 
						</td><td> 
						<input type='text' value='$rel_addr' onchange='OnRowChanged($i)' $readonly> 
						</td></tr>";
									
					}
					
					
			if ($isLoggedIn) {
				
				$i = count($relatives);
				print "<tr> 
					<td> f </td>
					<td> <input type='text' onchange='OnRowChanged($i);'> </td> 
					<td> <input type='text' onchange='OnRowChanged($i);'> </td> 
					</tr>";
			}
						
			print "		</table> </center>
						
				   </td>
				   </tr>
				   
				   ";
				   
			   
			print "<tr> <td colspan=2> <p align='center' style='font-size:18px;'> Поисковый отряд </p> </td> </tr>";	
			print "<tr>
				   <td> <label for='searchsquad'>Поисковый отряд/объединение:</label> </td> <td>";				   
			if ($isLoggedIn) {
				print "<select id='selSearchSquad' onchange='OnSearchSquadChanged();'>";
					print "<option";
					if ($warriorId == -1) print " selected ";
					print " disabled> --Выберите отряд/объединение-- </option>";
					print "<option value=-1> Новый РВК </option>";
					
					for($i = 0; $i < count($searchsquads); $i += 1) {
						if ($searchsquads[$i][0] == -1) continue;
						
						print "<option value=". $searchsquads[$i][0];
						if ((int)$searchsquads[$i][0] == $searchsquadid) print " selected ";
						print "> " . $searchsquads[$i][1] . ", " . $searchsquads[$i][2] . "</option>";
					}
				print "</select>";
			} else {
				for($i = 0; $i < count($searchsquads); $i++) {
					if ($searchsquads[$i][0] == $searchsquadid) 
					print "<input id='wmc' type='text' value='" . $searchsquads[$i][1] . ", " . $searchsquads[$i][2] . "' readonly>";
				}	
			}			
			print "</td></tr>";	
			 
			if ($isLoggedIn) {
				for($i = 0; $i < count($searchsquads); $i++) {
					if ($searchsquads[$i][0] == $searchsquadid) {
						print "<tr>
							   <td> <label for='searchsquad_name'>Название:</label> </td>
							   <td> <input id='searchsquad_name' type='text' value='". $searchsquads[$i][1] ."' readonly> </td>
							   </tr>";
						print "<tr>
							   <td> <label for='searchsquad_address'>Адрес:</label> </td>
							   <td> <input id='searchsquad_address' type='text' value='". $searchsquads[$i][2] ."' readonly> </td>
							   </tr>";
					}					
				}				
			}
			if ($isLoggedIn) {
				
			}
			
			print "<tr> <td colspan=2> <p align='center' style='font-size:18px;'> Сведения об обнаружении </p> </td> </tr>";			
			print "<tr>
				   <td> <label for='findingdate'>Дата:</label> </td>
				   <td> <input id='findingdate' type='text' value='$findingdate' $readonly> </td>
				   </tr>";
				   
			print "<tr>
				   <td> <label for='location'>Место:</label> </td>
				   <td> <input id='location' type='text' value='$location' $readonly> </td>
				   </tr>";
				   
			print "<tr>
				   <td> <label for='locationdescription'>Описание места:</label> </td>
				   <td> <input id='locationdescription' type='text' value='$locationdescription' $readonly> </td>
				   </tr>";
				   
			print "<tr>
				   <td> <label for='findingdescription'>Вид находки, по которой установлена личность:</label> </td>
				   <td> <input id='findingdescription' type='text' value='$findingdescription' $readonly> </td>
				   </tr>";
				   
			//Сведения о захоронении
			print "<tr> <td colspan=2> <p align='center' style='font-size:18px;'> Сведения о захоронении </p> </td> </tr>";
			
			print "<tr>
				   <td> <label for='location'>Место:</label> </td>
				   <td> <input id='location' type='text' value='$location' $readonly> </td>
				   </tr>";
				   
			print "<tr>
				   <td> <label for='burialdate'>Дата:</label> </td>
				   <td> <input id='burialdate' type='text' value='$burialdate' $readonly> </td>
				   </tr>";
				   
			print "<tr>
				   <td> <label for='commissariat_id'>Наименование военкомата:</label> </td>
				   <td> <input id='commissariat_id' type='text' value='$commissariat_id' $readonly> </td>
				   </tr>";
			
			
			print "</table> </form> </center>";
			
			$btnName = 'Изменить данные';
			if ($warriorId == -1) $btnName = 'Внести данные';
			
			if ($isLoggedIn) {
				print "<br>";
				print "<input type='button' value='$btnName'>";
			}
			
			print "<br><br>";
		}
		else {
			include('extendSearch.tpl');
		}
		
	?>
	</div>
	
    </section>
  </div>
  <footer> 
    <div>
      <p>2017 - <?php print  date("Y") ?> </p>
    </div>  
  </footer>
</div>
</body>
</html>
