

	<link rel="stylesheet" type="text/css" href="styles.css">
	
	<center>
	<table id="tblExtendSearch">
	<tr> 
		<td> <label for="first_name">Фамилия:</label> </td>
		<td> <input type="text" name="first_name"  id="first_name"> </td>
	</tr>
	
	<tr> 
		<td> <label for="textfield2">Имя:</label> </td>
		<td> <input type="text" name="textfield2" id="second_name"> </td>
	</tr>
	
	<tr>
		<td>  <label for="third_name">Отчество:</label> </td>
		<td>  <input type="text" name="third_name" id="third_name"> </td>
	</tr>
	
	<tr>
		<td> <label for="bdate">Год рождения:</label> </td>
		<td>  <input type="text" name="bdate" id="bdate"> </td>
	</tr>
	
	<tr>
		<td>  <label for="rankId">Звание:</label> </td>
		<td> 
		<select name="rankId" id="rankId">
		<option selected value> -- Выберите звание -- </option>
		<?php
		$query="select id, name from warriorranks;";
		$res=$db->query($query);
		
		while($row = $res->fetch_assoc()) {
			$rankId = $row["id"];
			$rankName = $row["name"];
			echo "<option value='$rankId'>$rankName</option>";
		}	
		?>
        </select> </td>
	</tr>
	
	<tr>
		<td>  <label for="mun">Воинская часть:</label> </td>
		<td> <input type="text" name="mun" id="mun"> </td>
	</tr>
	
	<tr>
		<td>  <label for="wmc">Военкомат призыва:</label></td>
		<td> <input type="text" name="wmc" id="wmc"> </td>
	</tr>
	
	<tr>
		<td>  <label for="finding">Место обнаружения:</label></td>
		<td>  <input type="text" name="finding" id="finding"></td>
	</tr>
	</table>
	
	<input type="button" name="btnExtendSearch" id="btnExtendSearch" value="Найти" onclick="ExtendSearch()";>
	
	</center>
	
	

	
	