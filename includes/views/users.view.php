<?php
class users {

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

		$uid = $this->core->cleanGet['uid'];
		$action = $this->core->cleanGet['action'];

		if($action=="add" && $this->core->role >= 100){

			echo'<div class="breadcrumb"><a href=".">home</a> > <a href="?id=users">user management</a> > add user</div>';

			echo'<div class="contentpadfull">
			<p class="title2">Add user account</p> <p><b>Please provide the needed information to create a new user account</b>';

			include"includes/forms/adduser.form.php";

		} elseif($action=="save" && $this->core->role >= 100){

			echo'<div class="breadcrumb"><a href=".">home</a> > <a href="?id=users">user management</a> > add user</div>';

			echo'<div class="contentpadfull">
			<p class="title2">Add user account</p> <p>';

			include"includes/classes/adduser.inc.php";
			$this->addUser();

		} elseif($action=="delete" && isset($uid) && $access >= 100){

			$this->deleteUser($uid);
			$this->showUserList();

			echo'<script>
				alert("The account has been deleted");
			</script>';

		} else if($this->core->role >= 100 & $action=="students"){

			$this->showStudentList();

		} else if($action == "saveedit"){

			$this->saveEdit();

		} else if($access >= 100){
	
			$this->showUserList();
		}	

	}

	function saveEdit(){

		$username = $cleanPost["username"];
		$firstname = $cleanPost["firstname"];
		$middlename = $cleanPost["middlename"];
		$surname = $cleanPost["surname"];
		$sex = $cleanPost["sex"];
		$id = $cleanPost["studentid"];
		$day = $cleanPost["day"];
		$month = $cleanPost["month"];
		$year = $cleanPost["year"];
		$pob = $cleanPost["pob"];
		$nationality = $cleanPost["nationality"];
		$streetname = $cleanPost["streetname"];
		$postalcode = $cleanPost["postalcode"];
		$town = $cleanPost["town"];
		$country = $cleanPost["country"];
		$homephone = $cleanPost["homephone"];
		$celphone = $cleanPost["celphone"];
		$dissability = $cleanPost["dissability"];
		$mstatus = $cleanPost["mstatus"];
		$email = $cleanPost["email"];
		$dissabilitytype = $cleanPost["dissabilitytype"];
		$status = $cleanPost["status"];
		$roleid = $cleanPost["role"];

		$sql = "UPDATE `basic-information` SET  `Sex` = '$sex', `Nationality` = '$nationality ', `StreetName` = '$streetname ', `PostalCode` = '$postalcode', `Town` = '$town', `Country` = '$country', `HomePhone` = '$homephone', `MobilePhone` = '$celphone', `Disability` = '$dissability', `DissabilityType` = '$dissabilitytype', `PrivateEmail` = '$email', `MaritalStatus` = '$mstatus', `Status` = '$status' WHERE `ID` = '$id' ";
		$run = $this->database->doInsertQuery($sql);

		$sql = "UPDATE `access` SET  `RoleID` =  '$roleid' WHERE `access`.`ID` = '$id';";
		$run = $this->database->doInsertQuery($sql);

		showUserList();

		echo'<script>
			alert("The account has been updated");
		</script>';
	}

	function showUserList(){

		echo'<div class="breadcrumb"><a href=".">home</a> > user management</div>';

		echo'<div class="contentpadfull">
		<p class="title2">User management</p> <p><b>Overview of all users with privileges higher than student</b>  |  <b><a href="?id=users&action=add">Create user account</a></b></p>';

		echo'<table width="768" height="" border="0" cellpadding="3" cellspacing="0">
		<tr class="tableheader">
		<td></td>
		<td><b> Student Name</b></td>
		<td><b> Access role</b></td>
		<td><b> </b></td>
		<td><b> Status</b></td>		
		<td><b> Options</b></td>
		</tr>';

		$sql = "SELECT * FROM `basic-information`, `access`, `roles` WHERE `access`.`ID` = `basic-information`.`ID` AND `access`.`RoleID` = `roles`.`ID` AND `access`.`RoleID` >= 100 ORDER BY `basic-information`.Surname";
		$run = $this->core->database->doSelectQuery($sql);

		while ($row = $run->fetch_row()) {
		
			$firstname 	= $row[0]; 
			$middlename 	= $row[1];
			$surname 	= $row[2];
			$sex 		= $row[3];
			$uid 		= $row[4];
			$nrc 		= $row[5];
			$role 		= $row[26];			
			$status 	= $row[20];
	
		  	echo'<tr>
			<td><img src="templates/default/images/bullet_user.png"></td>
			<td><a href="?id=view-information&uid='.$uid.'"><b>'.$firstname.' '.$middlename.' '.$surname.'</b></a></td>
			<td><i>'.$role.'</i></td>
				<td>'.$uid.'</td>
				<td>'.$status.'</td>
				<td><a href="?id=view-information&action=edit&uid='.$uid.'"><img src="templates/default/images/edi.png"> edit</a>  <a href="?id=users&action=delete&uid='.$uid.'" onclick="return confirm(\'Are you sure?\')"><img src="templates/default/images/del.png"> delete</a></td>
			  	</tr>'; 
				
		}
		
		echo'</table>';

	}
		
	function showStudentList(){
			
		echo'<div class="breadcrumb"><a href=".">home</a> > student management</div>';

		echo'<div class="contentpadfull">
		<p class="title2">User management</p> <p><b>Overview of all students currently enrolled</b> </b></p>';

		echo'<table width="768" height="" border="0" cellpadding="3" cellspacing="0">
		<tr class="tableheader">
		<td></td>
		<td><b> Student Name</b></td>
		<td><b> Student ID</b></td>
		<td><b> Status</b></td>		
		<td><b> Options</b></td>
		</tr>';
	
		$sql ="SELECT * FROM `basic-information`, `access`, `roles` WHERE `access`.`ID` = `basic-information`.`ID` AND `access`.`RoleID` = `roles`.`ID` AND `access`.`RoleID` = 10 ORDER BY Surname";
		$run = $this->core->database->doSelectQuery($sql);
	
		while ($row = $run->fetch_row()) {
	
			$firstname 	= $row[0]; 
			$middlename 	= $row[1];
			$surname 	= $row[2];
			$sex 		= $row[3];
			$uid 		= $row[4];
			$nrc 		= $row[5];
			$status 	= $row[20];

			echo'<tr>
			<td><img src="templates/default/images/bullet_user.png"></td>
			<td><a href="?id=view-information&uid='.$uid.'"><b>'.$firstname.' '.$middlename.' '.$surname.'</b></a></td>

			<td>'.$uid.'</td>
			<td>'.$status.'</td>
			<td><a href="?id=view-information&action=edit&uid='.$uid.'"><img src="templates/default/images/edi.png"> edit</a>  <a href="?id=users&action=delete&uid='.$uid.'" onclick="return confirm(\'Are you sure?\')"><img src="templates/default/images/del.png"> delete</a></td>
		  	</tr>'; 

		}

		echo'</table>';

	}
	
	function deleteUser($id){
	
		$sql = 'START TRANSACTION; 
			DELETE FROM `basic-information`  WHERE `ID` = "'.$id.'"; 
			DELETE FROM `access`  WHERE `ID` = "'.$id.'"; 
			COMMIT;';
	
		$run = $this->database->mysqli->doInsertQuery($sql);
	
	}

}
?>
