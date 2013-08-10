<?php
class studies {

    public $core;
	public $view;
	
	public function configView(){
		$this->view->header		= TRUE;
		$this->view->footer		= TRUE;
		$this->view->menu		= FALSE;
		$this->view->javascript = array(3);
		$this->view->css 		= array(4);
		
		return $this->view;
	}
        
    public function buildView($core){

        $this->core = $core;

		$action = $this->core->cleanGet['action'];
		$item   = $this->core->cleanGet['item'];
	
		if(empty($action) && $this->core->role > 100){

			$sql="SELECT `study`.ID, `study`.Name, `schools`.name, `schools`.id FROM `study`,`schools` WHERE `study`.ParentID = `schools`.ID ORDER BY `study`.Name";
			$this->listStudies($sql);

		} elseif($action=="list" && $this->core->role > 100){

			$sql="SELECT * FROM `study`,`schools` WHERE `study`.ParentID = `schools`.ID AND `study`.ID = $item ORDER BY `study`.Name";
			$this->listStudies($sql);

		} elseif($action=="view" && $this->core->role > 100){

			$sql="SELECT * FROM `study`,`schools` WHERE `study`.ParentID = `schools`.ID AND `study`.ParentID = `schools`.ID AND `study`.ID = $item";
			$this->showStudy($sql);

		} elseif($action=="edit" && isset($item) && $this->core->role > 100) {

			$sql="SELECT * FROM `study`,`schools` WHERE `study`.ParentID = `schools`.ID AND `study`.ParentID = `schools`.ID AND `study`.ID = $item";
			$this->editStudy($sql);

		} elseif($action=="add" && $this->core->role > 100) {

			$this->addStudy();

		} elseif($action=="save" && $this->core->role > 100) {

			$this->saveStudy();
		
			$sql="SELECT `study`.ID, `study`.Name, `schools`.name, `schools`.id FROM `study`,`schools` WHERE `study`.ParentID = `schools`.ID ORDER BY `study`.Name";
			$this->listStudies($sql);

		} elseif($action=="delete" && isset($item)){

			$this->deleteStudy($item);
		
			$sql="SELECT * FROM `study`,`schools` WHERE `study`.ParentID = `schools`.ID ORDER BY `study`.Name";
			$this->listStudies($sql);
		
			echo'<script>
				alert("The study has been deleted");
			</script>';
		}
		
	}

	function editStudy($sql){
		
		echo'<div class="breadcrumb"><a href=".">home</a> > <a href="?id=schools">schools</a> > <a href="?id=studies">studies</a> > study information</div>
		<div class="contentpadfull">
		<p class="title2">Edit study</p>';
		
		$run = $this->database->doInsertQuery($sql);
		
		while ($fetch = $run->fetch_row()) {
			include"includes/forms/editstudy.form.php";
		}
	}
		
	function addStudy(){
		
		echo'<div class="breadcrumb"><a href=".">home</a> > <a href="?id=schools">schools</a> > <a href="?id=studies">studies</a> > add study</div>
		<div class="contentpadfull">
		<p class="title2">Add study</p>';
		
		include"includes/forms/addstudy.form.php";
	}
		
	function deleteStudy($id){
		$sql = 'DELETE FROM `study`  WHERE `ID` = "'.$id.'"';
		$run = $this->database->doInsertQuery($sql);
	}
		
	function saveStudy(){
		
		$fullname = $this->core->cleanPost['fullname'];
		$shortname = $this->core->cleanPost['shortname'];
		$school = $this->core->cleanPost['school'];
		$delivery = $this->core->cleanPost['delivery'];
		$type = $this->core->cleanPost['studytype'];
		$active = $this->core->cleanPost['active'];
		$duration = $this->core->cleanPost['duration'];
		$startintake = $this->core->cleanPost['startintake'];
		$endintake = $this->core->cleanPost['endintake'];
		$maxintake = $this->core->cleanPost['maxintake'];
		$intensity = $this->core->cleanPost['intensity'];
		$description = $this->core->cleanPost['description'];
		$item = $this->core->cleanPost['item'];
		
		if(isset($item)){
			$sql = "UPDATE `edurole`.`study` SET `ParentID` = '$school', `IntakeStart` = '$startintake', `IntakeEnd` = '$endintake', `Delivery` = '$delivery', `IntakeMax` = '$maxintake', `Name` = '$fullname', `ShortName` = '$shortname', `Active` = '$active', `StudyType` = '$type', `TimeBlocks` = '$duration', `StudyIntensity` = '$intensity' WHERE `ID` = $item;";
		}else {
			$sql = "INSERT INTO `study` (`ID`, `ParentID`, `IntakeStart`, `IntakeEnd`, `Delivery`, `IntakeMax`, `Name`, `ShortName`, `Active`, `StudyType`, `TimeBlocks`, `StudyIntensity`) VALUES (NULL, '$school', '$startintake', '$endintake', '$delivery', '$maxintake', '$fullname', '$shortname', '$active', '$type', '$duration', '$intensity');"; 
		}
		
		$run = $this->database->doInsertQuery($sql);

	}
		
	public function listStudies($sql){
		
		echo'<div class="breadcrumb"><a href=".">home</a> > <a href="?id=schools">schools</a> > studies</div>
		<div class="contentpadfull">
		<p class="title2">Overview of studies</p>';
			

		
		echo'<p><b>Overview of all studies</b>  | <a href="?id=studies&action=add">Add study</a></p><p>'.
		'<table width="768" height="" border="0" cellpadding="3" cellspacing="0"><tr class="tableheader"><td><b>Study</b></td>'.
		'<td><b>School</b></td>'.
		'<td><b>Management tools</b></td>'.
		'</tr>';
		
		$run = $this->core->database->doSelectQuery($sql);

		while ($row = $run->fetch_row()) {

			if($i==0){ $bgc='class="zebra"'; $i++; } else { $bgc=''; $i--; }
		
			echo '<tr '.$bgc.'>
			<td><b><a href="?id=studies&action=view&item='. $row[0] .'"> '. $row[1] .'</a></b></td>'.
			'<td><a href="?id=schools&action=view&item='.$row[3] .'">'. $row[2] .'</a></td>'.
			'<td>
			<a href="?id=studies&action=edit&item='. $row[0] .'"> <img src="templates/default/images/edi.png"> edit</a>
			<a href="?id=studies&action=delete&item='. $row[0] .'" onclick="return confirm(\'Are you sure?\')"> <img src="templates/default/images/del.png"> delete </a>
			</td>
			</tr>';
		}
		
		echo'</table>
		</p>';
	}
		
		
	function showStudy($sql){
		
			echo'<div class="breadcrumb"><a href=".">home</a> > <a href="?id=schools">schools</a> > <a href="?id=studies">studies</a> > study information</div>
			<div class="contentpadfull">
			<p class="title2">Study information</p><br />';
		
			$run = $this->core->database->doSelectQuery($sql);

			while ($fetch = $run->fetch_row()) {

				if($i==0){ $bgc='class="zebra"'; $i++; } else { $bgc=''; $i--; }
		
				$method = $fetch[4];
				echo'<table width="768" border="0" cellpadding="5" cellspacing="0">
					 <tr>
						<td width="205" height="28" bgcolor="#EEEEEE"><strong>Information</strong></td>
						<td width="200" bgcolor="#EEEEEE"></td>
						<td  bgcolor="#EEEEEE"></td>
					  </tr>
					  <tr>
						<td><strong>Study name</strong></td>
						<td>'. $fetch[6] .'</td>
						<td></td>
					  </tr>
					  <tr>
						<td><strong>Part of school</strong></td>
						<td><a href="?id=schools&action=view&item='.$fetch[13].'">'.$fetch[16].'</a>
					  </td>
						<td></td>
					  </tr>
		
				<tr><td>Method of Delivery</td><td>
				<select name="delivery">
		
				<option value="0" >-choose-</option>
				<option value="1" '; if($method=="1"){ echo'selected="selected"'; } echo'>Regular programme</option>
				<option value="2" '; if($method=="2"){ echo'selected="selected"'; } echo'>Parallel programme</option>
			<option value="3" '; if($method=="3"){ echo'selected="selected"'; } echo'>Distance learning</option>
			<option value="4" '; if($method=="4"){ echo'selected="selected"'; } echo'>Various forms</option>
		
			</select></td><td></td></tr>
		
			<tr><td>Program Type</td><td>
			<select name="programtype">
		
			<option value="0" '; if($fetch[9]=="0"){ echo'selected=""'; } echo'>-choose-</option>
			<option value="1" '; if($fetch[9]=="1"){ echo'selected=""'; } echo'>Bachelor of art</option>
			<option value="2" '; if($fetch[9]=="2"){ echo'selected=""'; } echo'>Bachelor of Engineering</option>
			<option value="3" '; if($fetch[9]=="3"){ echo'selected=""'; } echo'>Bachelor of science</option>
			<option value="4" '; if($fetch[9]=="4"){ echo'selected=""'; } echo'>Diploma maths and science</option>
			<option value="5" '; if($fetch[9]=="5"){ echo'selected=""'; } echo'>Diploma other than maths and science</option>
			<option value="6" '; if($fetch[9]=="6"){ echo'selected=""'; } echo'>Doctor</option>
			<option value="7" '; if($fetch[9]=="7"){ echo'selected=""'; } echo'>Licentiate</option>
			<option value="8" '; if($fetch[9]=="8"){ echo'selected=""'; } echo'>Master of art</option>
			<option value="9" '; if($fetch[9]=="9"){ echo'selected=""'; } echo'>Master of Business Administration</option>
			<option value="10" '; if($fetch[9]=="10"){ echo'selected=""'; } echo'>Master of Engineering Science</option>
			<option value="11" '; if($fetch[9]=="11"){ echo'selected=""'; } echo'>Master of science</option>
			<option value="12" '; if($fetch[9]=="12"){ echo'selected=""'; } echo'>Master of Science Engineering </option>
			<option value="13" '; if($fetch[9]=="13"){ echo'selected=""'; } echo'>Secondary school</option>
		
		
			</select></td><td></td></tr>
		
			<tr><td>Currenty on offer</td><td><select name="active">
		
			<option value="0" '; if($fetch[8]=="0"){ echo'selected=""'; } echo'>No</option>
			<option value="1" '; if($fetch[8]=="1"){ echo'selected=""'; } echo'>Yes</option>
		
			</select></td><td></td></tr>
		
			<tr><td>Start of Intake</td>
			<td><b>'. $fetch[2] .'</b></td>
			<td></td>
			</tr>
		
			<tr><td>End of Intake</td>
			<td><b>'. $fetch[3] .'</b></td>
			<td></td>
			</tr>
		
			<tr><td>Length of study</td>
			<td><b>'. $fetch[10]/12 .' years</b></td>
			<td></td>
			</tr>';
		
		
			}
		
		echo'<td>Available programmes</td>
		 <td></td>
		<td></td>
		 </tr>
		</table>';

	}

}
?>
