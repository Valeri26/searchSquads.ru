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
			
			
			$first_name = "Андреев";
			$second_name = "Алексей";
			$third_name = "Иванович";
			$bdate = "1901";
			$rank = "рядовой";
			$militaryUnit_name = "ВЧ ХХХ";
			
			$readonly = 'readonly';
			if ($isLoggedIn) $readonly = '';
			print "<link rel='stylesheet' type='text/css' href='styles.css'>";
			print "<center><form id='frmWarriorCard'> <table id='tblWarriorCard'>";
			
			print "<input id='warriorId' type='hidden' value='$warriorId' readonly>";
				   
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
					print "<option selected> $rank </option>";
					print "<option> One </option>";
					print "<option> Two </option>";
				print "</select>";
			} else {
				print "<input id='rank' type='text' value='$rank' $readonly>";
			}
			
			print "</td></tr>";

			print "<tr>
				   <td> <label for='militaryUnit_name'>Воинская часть:</label> </td>
				   <td> <input id='militaryUnit_name' type='text' value='$militaryUnit_name' $readonly> </td>
				   </tr>";
			
			print "</table> </form> </center>";
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
