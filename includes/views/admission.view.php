<?php	
class admission{

    public $core;
	public $view;
	
	public function configView(){
		$this->view->header		= TRUE;
		$this->view->footer		= TRUE;
		$this->view->javascript = array(3);
		$this->view->css 		= array(1,4);
		
		return $this->view;
	}
        
    public function buildView($core){
		$this->core = $core;
		        
		$action	= $this->core->cleanGet['action'];
		$uid	= $this->core->cleanGet['uid'];

		if(empty($action) && $this->core->role < 100 || $action == "profile"  && isset($uid)){	
		
			$this->admissionProfile();
		
		} elseif($action == "promote" && $this->core->role >= 103 && isset($uid)) {
		
			$this->promote();
		
		} elseif($action == "reject" && $this->core->role >= 103  && isset($uid)) {
		
			$this->reject();
		
		} elseif($action == "continue" && $this->core->role >= 103  && isset($uid)) {
		
			$this->continued();
		
		} elseif($action == "delete" && $this->core->role >= 103  && isset($uid)) {
		
			$this->delete();
		
		} elseif($action == "complete" && $this->core->role >= 103 && isset($uid)) {
		
			$this->complete();
		
		} elseif(empty($action) && $this->core->role >= 103){
		
			$this->admissionFlow();
		
		}
	
	}
	
	function admissionFlow(){
		echo breadcrumb::generate(get_class());

		echo'<div class="contentpadfull">
		<p class="title2">Admission management</p> ';
	
		$this->admissionManager();
		$this->admissionManagerDenied();
	}
	
	function admissionProfile(){
	
		$this->core->role = $_SESSION['access'];
		$id  = $_SESSION['userid'];
		$uid  = $this->core->cleanGet['uid'];

		echo breadcrumb::generate(get_class());

		echo'<div class="contentpadfull">
		<p class="title2">Personal admission progress</p><div class="student">';
		
		if($this->core->role < 100){
			$sql = "SELECT * FROM `basic-information` as bi, `roles` as rl, `access` as ac WHERE ac.`ID` = '".$id."' AND ac.`ID` = bi.`ID` AND ac.`RoleID` = rl.`ID`";
		}else{
			$sql = "SELECT * FROM `basic-information` as bi, `roles` as rl, `access` as ac WHERE ac.`ID` = '".$uid."' AND ac.`ID` = bi.`ID` AND ac.`RoleID` = rl.`ID`";
		}
	
		$this->showInfoProfile($sql);
	
	}
	
	function complete(){
		
		$uid  = $this->core->cleanGet['uid'];
	
		$sql = "UPDATE `access` SET `RoleID` = 10 WHERE `access`.`ID` = '".$uid."'";
		$run = $this->database->doInsertQuery($sql);
	
		$sql = "UPDATE `basic-information` SET `Status` = 'Enrolled' WHERE `basic-information`.`ID` = '".$uid."'";
		$run = $this->database->doInsertQuery($sql);
	
		// ADD RUN MAIL FUNCTION TO INFORM STUDENT ENROLLMENT WAS SUCCESFUL
	
		$this->admissionFlow();
	}
	
	function promote(){
		
		$uid  = $this->core->cleanGet['uid'];
	
		$sql = "UPDATE `edurole`.`access` SET `RoleID` = `RoleID`+1 WHERE `access`.`ID` = '".$uid."'";
		$run = $this->database->doInsertQuery($sql);
	
		$this->admissionFlow();
	}
	
	function reject(){
		
		$uid  = $this->core->cleanGet['uid'];
	
		$sql = "UPDATE `edurole`.`basic-information` SET `Status` = 'Rejected' WHERE `basic-information`.`ID` = '".$uid."';";
		$run = $this->database->doInsertQuery($sql);
	
		$this->admissionFlow();
	}
	
	function continued(){
		
		$uid  = $this->core->cleanGet['uid'];
	
		$sql = "UPDATE `edurole`.`basic-information` SET `Status` = 'Requesting' WHERE `basic-information`.`ID` = '".$uid."';";
		$run = $this->database->doInsertQuery($sql);
	
		$this->admissionFlow();
	}
	
	
	function delete(){
		
		$uid  = $this->core->cleanGet['uid'];
	
		$sql = "UPDATE `edurole`.`basic-information` SET `Status` = 'Failed' WHERE `basic-information`.`ID` = '".$uid."';";
		$run = $this->database->doInsertQuery($sql);
	
		$this->admissionFlow();
	}
	
	function admissionManager(){
	
		$id  = $this->userid;
	
		echo'<p class="title1">Currently active admission requests</p> ';
	
		$sql ="SELECT * FROM `basic-information`, `access`, `student-study-link`, `study` WHERE `access`.`ID` = `basic-information`.`ID` AND  `access`.`RoleID` < 10 AND  `access`.`ID` = `student-study-link`.`StudentID` AND `student-study-link`.`StudyID` = `study`.ID AND `basic-information`.Status = 'Requesting'";
		$run = $this->core->database->doSelectQuery($sql);
	
		echo'<table width="768" height="" border="0" cellpadding="5" cellspacing="0">
		<tr>
		<td bgcolor="#EEEEEE"></td>
		<td bgcolor="#EEEEEE" width="200px"><b> Student Name</b></td>
		<td bgcolor="#EEEEEE"><b> <b>National ID</b></td>
		<td bgcolor="#EEEEEE"><b> Admission Phase</b></td>		
		<td bgcolor="#EEEEEE"><b> Study</b></td>		
		<td bgcolor="#EEEEEE" width="90px"><b> Options</b></td>
		<td bgcolor="#EEEEEE" width="60px"></td>
		</tr>';
	
		while ($fetch = $run->fetch_row()) {
	
			$status  	= $fetch[7]; 	
			$study 	= $fetch[36];
			$firstname 	= $fetch[0]; 
			$middlename 	= $fetch[1];
			$surname 	= $fetch[2];
			$sex 		= $fetch[3];
			$uid 		= $fetch[4];
			$nrc 		= $fetch[5];
			$role 		= $fetch[25];
			$status 	= $fetch[23];
	
			if($status==6){
				$next = '<a href="?id=admission&action=complete&uid='.$uid.'"><img src="templates/default/images/ex2.gif"> <b>Complete</b> </a>';
			} else {
				$next = '<a href="?id=admission&action=promote&uid='.$uid.'"><img src="templates/default/images/ex2.gif"> Approve step </a>';
			}
	
			echo'<tr>
				<td><img src="templates/default/images/bullet_user.png"></td>
				<td><a href="?id=view-information&uid='.$uid.'"><b>'.$firstname.' '.$middlename.' '.$surname.'</b></a></td>
		
				<td>'.$nrc.'</td>
				<td><a href="?id=admission&action=profile&uid='.$uid.'">Step '.$status.'</a></td>
				<td><b>'.$study.'</b></td>
				<td>'.$next.' </td>
				<td> <a href="?id=admission&action=reject&uid='.$uid.'"><img src="templates/default/images/del.png"> Deny</a></td>
				</tr>'; 
		}
	
		echo'</table>';
	
	}
	
	function admissionManagerDenied(){

		$id  = $this->userid;
	
		echo'<p class="title1">Currently denied admission requests</p> ';
	
		$sql ="SELECT * FROM `basic-information`, `access`, `student-study-link`, `study` WHERE `access`.`ID` = `basic-information`.`ID` AND  `access`.`RoleID` < 10 AND  `access`.`ID` = `student-study-link`.`StudentID` AND `student-study-link`.`StudyID` = `study`.ID AND `basic-information`.Status = 'Rejected'";
		$run = $this->core->database->doSelectQuery($sql);
		
		echo'<table width="768" height="" border="0" cellpadding="5" cellspacing="0">
		<tr>
		<td bgcolor="#EEEEEE"></td>
		<td bgcolor="#EEEEEE" width="200px"><b> Student Name</b></td>
		<td bgcolor="#EEEEEE"><b> <b>National ID</b></td>
		<td bgcolor="#EEEEEE"><b> Admission Phase</b></td>		
		<td bgcolor="#EEEEEE"><b> Study</b></td>		
		<td bgcolor="#EEEEEE" width="90px"><b> Options</b></td>
		<td bgcolor="#EEEEEE" width="60px"></td>
		</tr>';
	
		while ($fetch = $run->fetch_row()) {
	
			$status  	= $fetch[7]; 	
			$study 	= $fetch[36];
			$firstname 	= $fetch[0]; 
			$middlename 	= $fetch[1];
			$surname 	= $fetch[2];
			$sex 		= $fetch[3];
			$uid 		= $fetch[4];
			$nrc 		= $fetch[5];
			$role 		= $fetch[25];
			$status 	= $fetch[23];
	
			echo'<tr>
				<td><img src="templates/default/images/bullet_user.png"></td>
				<td><a href="?id=view-information&uid='.$uid.'"><b>'.$firstname.' '.$middlename.' '.$surname.'</b></a></td>
		
				<td>'.$nrc.'</td>
				<td><a href="?id=admission&action=profile&uid='.$uid.'">Step '.$status.'</a></td>
				<td><b>'.$study.'</b></td>
				<td><a href="?id=admission&action=continue&uid='.$uid.'"><img src="templates/default/images/edi.png"> Continue </a></td><td>  <a href="?id=admission&action=reject&uid='.$uid.'"><img src="templates/default/images/del.png"> Delete</a></td>
				</tr>'; 
			
		}
	
		echo'</table>';
	
	}
	
	
	function admissionProgress($role, $status){
	
			$i=1;
			echo'<p><b>To successfully complete your admission process you will need to complete ALL steps.</b></p>
			<table width="768" border="0" cellpadding="3" cellspacing="0">';
			
			$sql = "SELECT `Name`, `Value` FROM `settings` WHERE `Name` LIKE 'AdmissionLevel%'";
			$run = $this->core->database->doSelectQuery($sql);
	
			while ($fetch = $run->fetch_row()) {
	
			
				if($i == $role && $status=="Rejected"){ 
					$background = 'style="background-color: #F2BFBF"';
					$step = '<image src="templates/default/images/error.png"> <b>FAILED TO MEET REQUIREMENTS</b>';	
				} elseif($i <= $role){
					$background = 'style="background-color: #DFF2BF"'; 
					$step = '<image src="templates/default/images/check.png"> completed';
				} elseif ($i > $role) {
					$background = 'style="background-color: #fff"'; 
					$step = '<image src="templates/default/images/tviload.gif"> not yet completed';
				}
	
				echo'<tr '.$background.'>
				<td width="100"><b>Step '.$i.'</b></td>
				<td width="300"><em>'.$fetch[1].'</em></td>
				<td>'.$step.'</td>
				</tr>'; 
	
				$i++;
			}
	
			echo'</table>';
	
	}
	
	function showInfoProfile($sql){
	
		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_row()) {
	
			$id  = $_SESSION['userid'];
			$firstname = $fetch[0]; 	$middlename = $fetch[1];
			$surname = $fetch[2]; 		$sex = $fetch[3];
			$studentid = $fetch[4]; 	$nrc = $fetch[5];
			$studytype = $fetch[22];	$role = $fetch[21];	
			$status = $fetch[20];
	
			echo'<div class="studentname">'.$firstname.' '.$middlename.' '.$surname.' </div>
			<table width="768" border="0" cellpadding="0" cellspacing="0">
			 <tr>
			<td>Student number</td>
			<td>'.$studentid.'</td>
			 </tr>';
		
		}
		
		echo'</table></div>';
		
		$this->admissionProgress($role, $status);
	
	}
}
?>