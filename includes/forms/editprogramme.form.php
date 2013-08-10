<?php
$item = $this->core->cleanGet['item']; if(!isset($item)){ $item = $this->core->cleanPost['item']; }
$dean = $fetch[2];
function showUserSelect($role, $dean){
	global $connection;

	if(empty($role)){	$role=2;	}
	$sql ="SELECT * FROM `basic-information`, `access`, `roles` WHERE `access`.`ID` = `basic-information`.`ID` AND `access`.`RoleID` = `roles`.`ID` AND `access`.`RoleID` >= $role";
	$run = doSelectQuery($sql);

	while($fetch = mysql_fetch_row($run)){
	
		$firstname 	= $fetch[0]; 
		$surname 	= $fetch[2];
		$uid 		= $fetch[4];
		if($uid == $dean){ $sel = 'selected="selected"'; } else { $sel=""; } 

	  	$out = $out . '<option value="'.$uid.'"  '.$sel.'>'.$firstname.' '.$surname.'</option>'; 
		
	}

	return($out);
}

$select = showUserSelect("4", $dean);

echo'<form id="editprogramme" name="editprogramme" method="post" action="?id=programmes&action=save">
	<p>Please enter the following information</p>
	<table width="768" border="0" cellpadding="5" cellspacing="0">
              <tr>
                <td width="205" height="28" bgcolor="#EEEEEE"><strong>Information</strong></td>
                <td  colspan="2" width="200"  bgcolor="#EEEEEE"><strong>Currently selected</strong></td>
                <td bgcolor="#EEEEEE"><strong>Description</strong></td>
              </tr>
              <tr>
		<td width="150"><b>Name of Programme</b></td>
                <td colspan="2">
                  <input type="text" name="name" value="'. $fetch[1] .'" /></td>
                <td></td>
              </tr>
              <tr>
		<td width="150"><b>Programme Coordinator</b></td>
                <td colspan="2">
                  <select name="coordinator" id="coordinator">
			'.  $select .'
                  </select></td>
                <td>Functional course coordinator</td>
              </tr>
		<tr><td>Programme Type</td>
		<td colspan="2"><select name="programtype">

		<option value="0" '; if($fetch[3]=="0"){ echo'selected=""'; } echo'>-choose-</option>
		<option value="1" '; if($fetch[3]=="1"){ echo'selected=""'; } echo'>Minor</option>
		<option value="2" '; if($fetch[3]=="2"){ echo'selected=""'; } echo'>Major</option>
		<option value="3" '; if($fetch[3]=="3"){ echo'selected=""'; } echo'>Available as both</option>

		</select></td>
		<td></td>
		</tr>

              <tr>
		<td width="150"></td>
                <td colspan="2">
                  	  <input type="hidden" name="item" value="'. $item .'" />
	 		 <input type="submit" class="submit" name="submit" id="submit" value="Save changes to programme" /></td>
                <td></td>
              </tr>';

            echo'</table>

	 </form><br /><br /> <p class="title2">Manage courses in programme</p><p>Please enter the following information</p>';

	echo'	<table width="700" border="0" cellpadding="5" cellspacing="0">
              <tr>
                <td width="205" height="28" bgcolor="#EEEEEE"><strong>Information</strong></td>
                <td  width="130" bgcolor="#EEEEEE"><strong>Currently selected</strong></td>
                <td  width="130" bgcolor="#EEEEEE">Currently not selected</td>
                <td bgcolor="#EEEEEE"><strong>Description</strong></td>
              </tr>

 	<tr >
	<td>Select which courses should be part of this programme</td>
	<td width="100"> <form id="selected" name="selectedfr" method="post" action="?id=programmes&action=savecourses">
	<input type="hidden" name="item" value="'. $item .'" />
	<select name="selected[]" multiple="multiple" size="10" style="width: 130px">'; 


	$sql="SELECT * FROM `courses`, `programmes`, `program-course-link` WHERE `program-course-link`.CourseID = `courses`.ID AND `program-course-link`.ProgramID = `programmes`.ID AND `program-course-link`.ProgramID = $item";

	if (!$srd= mysql_query($sql,$connection)) {
		die('Error: ' . mysql_error());
	}
	
	$i=1;

	while($fetchw = mysql_fetch_row($srd)){

		echo '<option value="'.$fetchw[0].'">'.$fetchw[2].'</option>';
		$i++;

	}

	if($i==1){
		echo'No courses have been added to the program yet. Please <a href="?id=programmes&action=edit&item='.$fetch[0].'">add some.</a>';
	}

	echo'</select>   <input type="submit" class="submit" name="submit" id="submit" value="Remove Selected" style="width: 130px" /></form></td>
	<td  width="100">
	<form id="nselected" name="nselectedfr" method="post" action="?id=programmes&action=savecourses">
	<input type="hidden" name="item" value="'. $item .'" />
	<select name="nselected[]" multiple="multiple" size="10" style="width: 130px">';

	$sql="SELECT * FROM `courses` ORDER BY `courses`.Name";
	
	if (!$srd= mysql_query($sql,$connection)) {
		die('Error: ' . mysql_error());
	}
	
	$i=1;

	while($fetchw = mysql_fetch_row($srd)){

		echo '<option value="'.$fetchw[0].'">'.$fetchw[2].'</option>';
		$i++;

	}

	if($i==1){
		echo'No courses have been added to the program yet. Please <a href="?id=programmes&action=edit&item='.$fetch[0].'">add some.</a>';
	}

	echo'</select>  <input type="submit" class="submit" name="submit" id="submit" value="Add Selected" style="width: 130px" /></form>
	</td>
	<td></td>
	</tr></table>';

?>
