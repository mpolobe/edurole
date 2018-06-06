<?php
class users {

	public $core;
	public $view;
	public $item = NULL;

	public function configView() {
		$this->view->header = TRUE;
		$this->view->footer = TRUE;
		$this->view->menu = TRUE;
		$this->view->javascript = array();
		$this->view->css = array();
		
		return $this->view;
	}

	public function buildView($core) {
		$this->core = $core;
		$this->core->limit = 2000;
	}
	
	private function generatePassword($length, $charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1') {
		$str = '';
		$count = strlen($charset);
		while ($length--) {
			$str .= $charset[mt_rand(0, $count - 1)];
		}
		return $str;
	}

	function addUsers() {
		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";

		$select = new optionBuilder($this->core);
		$roles = $select->showRoles();

		include $this->core->conf['conf']['formPath'] . "adduser.form.php";
	}

	function saveUsers() {

		$password = $this->generatePassword(6);

		if ($this->core->cleanPost["otherdissability"]) {
			$dissabilitytype = $this->core->cleanPost["otherdissability"];
		}


		// ADDUSER QUERY NEEDS PREPARED STATEMENT

		// Fields user account
		$username = $this->core->cleanPost["username"];
		$firstname = $this->core->cleanPost["firstname"];
		$middlename = $this->core->cleanPost["middlename"];
		$surname = $this->core->cleanPost["surname"];
		$sex = $this->core->cleanPost["sex"];
		$id = $this->core->cleanPost["studentid"];
		$day = $this->core->cleanPost["day"];
		$month = $this->core->cleanPost["month"];
		$year = $this->core->cleanPost["year"];
		$pob = $this->core->cleanPost["pob"];
		$nationality = $this->core->cleanPost["nationality"];
		$streetname = $this->core->cleanPost["streetname"];
		$postalcode = $this->core->cleanPost["postalcode"];
		$town = $this->core->cleanPost["town"];
		$country = $this->core->cleanPost["country"];
		$homephone = $this->core->cleanPost["homephone"];
		$celphone = $this->core->cleanPost["celphone"];
		$dissability = $this->core->cleanPost["dissability"];
		$mstatus = $this->core->cleanPost["mstatus"];
		$email = $this->core->cleanPost["email"];
		$dissabilitytype = $this->core->cleanPost["dissabilitytype"];
		$status = $this->core->cleanPost["status"];
		$roleid = $this->core->cleanPost["role"];
		$studytype = $this->core->cleanPost["studytype"];

		$sql = "INSERT INTO `basic-information` (`FirstName`, `MiddleName`, `Surname`, `Sex`, `ID`, `GovernmentID`, `DateOfBirth`, `PlaceOfBirth`, `Nationality`, `StreetName`, `PostalCode`, `Town`, `Country`, `HomePhone`, `MobilePhone`, `Disability`, `DissabilityType`, `PrivateEmail`, `MaritalStatus`, `StudyType`, `Status`) 
				VALUES ('$firstname', '$middlename', '$surname', '$sex', NULL, '$id', '$year-$month-$day', '$pob', '$nationality', '$streetname', '$postalcode', '$town', '$country', '$homephone', '$celphone', '$dissability', '$dissabilitytype', '$email', '$mstatus', '$studytype', 'Employed');";
		
		//echo $sql;
	//	die();
		
		if ($this->core->database->doInsertQuery($sql)) {

			$sql = "SELECT * FROM `basic-information` WHERE `GovernmentID` = '$id'";
			$run = $this->core->database->doSelectQuery($sql);

			while ($fetch = $run->fetch_row()) {

				$passenc = $this->hashPassword($username, $password);

				$sql = "INSERT INTO `access` (`ID`, `Username`, `RoleID`, `Password`) 
						VALUES ('$fetch[4]', '$username', '$roleid', '$passenc');";
				$this->core->database->doInsertQuery($sql);

				echo '<div class="successpopup">The requested user account has been created.<br/> WRITE THE FOLLOWING INFORMATION DOWN OR REMEMBER IT!</div>';
				echo '<div class="successpopup">Username:  <b>' . $username . '</b><br>Password:  <b>' . $password . '</b></div>';

			}

		} else {

			$this->core->throwError('An error occurred with the information you have entered. Please return to the form and verify your information. <a a href="javascript:" onclick="history.go(-1); return false">Go back</a>');

		}

	}

	private function hashPassword($username, $password){
		$passwordHashed = hash('sha512', $password . $this->core->conf['conf']['hash'] . $username);
		return $passwordHashed;
	}

	function manageUsers() {
		$this->core->pager = FALSE;

		if($this->core->pager == FALSE){
			echo '<div class="toolbar">
			<a href="' . $this->core->conf['conf']['path'] . '/users/add">Add new user account</a>
			</div>'; 

			echo '<table width="768" height="" border="0" cellpadding="3" cellspacing="0">
			<tr class="tableheader">
			<td style="width: 200px"><b> Staff Name</b></td>
			<td><b> Access role</b></td>
			<td><b> Username </b></td>
			<td><b> Status</b></td>		
			<td><b> Options</b></td>
			</tr>
			</table>';
		}

		$sql = "SELECT * FROM `basic-information`
			LEFT JOIN `access` ON `access`.ID = `basic-information`.ID
			LEFT JOIN `roles` ON `access`.RoleID = `roles`.ID
			WHERE `access`.RoleID > 10
			ORDER BY `basic-information`.Surname";

	
		$run = $this->core->database->doSelectQuery($sql);

		$sqlcount = "SELECT count(*)  FROM `basic-information`, `access`, `roles` 
			WHERE `access`.`ID` = `basic-information`.`ID` 
			AND `access`.`RoleID` = `roles`.`ID` 
			AND `access`.`RoleID` > 10 ORDER BY `basic-information`.Surname";

		$runcount = $this->core->database->doSelectQuery($sqlcount);

		while ($row = $runcount->fetch_row()) {
			$total = $row[0];
		}

		while ($row = $run->fetch_row()) {

			$firstname = $row[0];
			$middlename = $row[1];
			$surname = $row[2];
			$username = $row[22];
			
			if($surname == "Demo"){
				$style = ' color: #000; ';
			} else {
				$style ="";
			}

			if(empty($firstname) && empty($lastname)){
				$firstname = $username;
			}

			$sex = $row[3];
			$uid = $row[4];
			$nrc = $row[5];
			$role = $row[26];
			$status = $row[20];


			echo '<div class="resultrow">
				<div style="width: 20px; float:left;"><img src="' . $this->core->fullTemplatePath . '/images/user.png"></div>
				<div style="width: 190px; float:left;"><a href="' . $this->core->conf['conf']['path'] . '/information/show/' . $uid . '" style="'.$style.'"><b>' . $firstname . ' ' . $middlename . ' ' . $surname . '</b></a></div>
				<div style="width: 175px; float:left;"><i>' . $role . '</i></div>
				<div style="width: 150px; float:left;"><b>' .$username .'</b></div>
				<div style="width: 115px; float:left; height: 15px;">' . $status . '</div>
				<div style="width: 100px; float:left;"><a href="' . $this->core->conf['conf']['path'] . '/information/edit/' . $uid . '"><img src="' . $this->core->fullTemplatePath . '/images/edi.png"> edit</a>  <a href="' . $this->core->conf['conf']['path'] . '/users/delete/' . $uid . '" onclick="return confirm(\'Are you sure?\')"><img src="' . $this->core->fullTemplatePath . '/images/delete.gif"> delete</a></div>
			</div>';
		}


	}

	function studentsUsers() {

		if($this->core->pager == FALSE){

			echo '<table width="768" height="" border="0" cellpadding="3" cellspacing="0">
			<tr class="tableheader">
			<td></td>
			<td><b> Student Name</b></td>
			<td><b> Student ID</b></td>
			<td><b> Status</b></td>		
			<td><b> Options</b></td>
			</tr></table>';
		}

		$sql = "SELECT * FROM `basic-information` 
			LEFT JOIN `access` ON `access`.`ID` = `basic-information`.`ID` 
			LEFT JOIN `roles` ON `roles`.`ID` = `access`.`RoleID` 
			WHERE `basic-information`.`Status` = 'Distance' 
			OR `basic-information`.`StudyType` = 'Fulltime' 
			ORDER BY `Surname`";

		$sql = $sql . " LIMIT ". $this->core->limit ." OFFSET ". $this->core->offset;

		$run = $this->core->database->doSelectQuery($sql);

		$sqlcount = "SELECT count(*) FROM `basic-information` 
			WHERE `basic-information`.`Status` = 'Distance' 
			OR `basic-information`.`StudyType` = 'Fulltime'";

		$runcount = $this->core->database->doSelectQuery($sqlcount);

		while ($row = $runcount->fetch_row()) {
			$total = $row[0];
		}
		

		while ($row = $run->fetch_row()) {

			$firstname = $row[0];
			$middlename = $row[1];
			$surname = $row[2];
			$sex = $row[3];
			$uid = $row[4];
			$nrc = $row[5];
			$status = $row[20];

		echo '<div class="resultrow">
				<div style="width: 20px; float:left;"><img src="' . $this->core->fullTemplatePath . '/images/bullet_user.png"></div>
				<div style="width: 275px; float:left;"> <a href="' . $this->core->conf['conf']['path'] . '/information/show/' . $uid . '"><b>' . $firstname . ' ' . $middlename . ' ' . $surname . '</b></a></div>
				<div style="width: 190px; float:left;">' . $uid . '</div>
				<div style="width: 140px; float:left;">' . $status . '</div>
				<div style="width: 100px; float:left;"><a href="' . $this->core->conf['conf']['path'] . '/information/edit/' . $uid . '"><img src="' . $this->core->fullTemplatePath . '/images/edi.png"> edit</a>  <a href="' . $this->core->conf['conf']['path'] . '/users/delete/' . $uid . '" onclick="return confirm(\'Are you sure?\')"><img src="' . $this->core->fullTemplatePath . '/images/delete.gif"> delete</a></div>
		  	</div>';
		}

		if($this->core->pager == FALSE){
			echo'<div id="results">&zwnj;</div>';
			include $this->core->conf['conf']['libPath'] . "edurole/autoload.js";
		}

	}

	function deleteUsers($item) {
		$sql = 'UPDATE `basic-information` SET `Status` = "Removed" WHERE `ID` = "' . $item . '";';
		$run = $this->core->database->doInsertQuery($sql);

		$sql = 'DELETE FROM `access`  WHERE `ID` = "' . $item . '";';
		$run = $this->core->database->doInsertQuery($sql);

		$this->core->logEvent("Removed user $item", "4");

		$this->showUserList();
		$this->core->showAlert("The account has been deleted");
	}
}

?>
