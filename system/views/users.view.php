<?php
class users {

	public $core;
	public $view;
	public $item = NULL;

	public function configView() {
		$this->view->header = TRUE;
		$this->view->footer = TRUE;
		$this->view->menu = TRUE;
		$this->view->javascript = array(3);
		$this->view->css = array(4);

		return $this->view;
	}

	public function buildView($core) {
		$this->core = $core;

		if ($this->core->action == "add" && $this->core->role >= 100) {
			$this->addUser();
		} elseif ($this->core->action == "save" && $this->core->role >= 100) {
			$this->saveUser();
		} elseif ($this->core->action == "delete" && isset($this->core->item) && $this->core->role > 104) {
			$this->deleteUser($this->core->item);
		} else if ($this->core->role >= 100 & $this->core->action == "students") {
			$this->showStudentList();
		} elseif ($this->core->action == "password" && isset($core->role) && $this->core->role < 100 ) {
			$this->changePassword($this->core->username, FALSE);
		} elseif ($this->core->action == "password" && isset($core->role) && $this->core->role == 1000 && isset($this->core->item)) {
			$this->changePassword($this->core->item, TRUE);
		} elseif ($this->core->action == "password" && isset($core->role) && $this->core->role == 1000 && !isset($this->core->item)) {
			$this->changePassword($this->core->username, FALSE);
		} else if ($core->role >= 100) {
			$this->showUserList();
		}
	}

	public function changePassword($item, $admin) {
		$function = __FUNCTION__;

		$oldpass = $this->core->cleanPost["oldpass"];
		$newpass = $this->core->cleanPost["newpass"];
		$newpasscheck = $this->core->cleanPost["newpasscheck"];

		$title = 'Change your account password';
		$description = 'You are able to change your account password here.';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);

		$auth = new auth($this->core);
		
		if (!empty($newpass)) {

			if ($newpass == $newpasscheck) {

				if (!$auth->ldapChangePass($item, $oldpass, $newpass)) {
					$ldap = false;
				}
				if ($auth->mysqlChangePass($item, $oldpass, $newpass, $admin) == false && $ldap == false) {
					$this->core->throwError("The information you have entered is incorrect.");
				}

			} else {
				echo "<h2>The entered passwords do not match</h2>";
			}

		} else {

			echo "<p>Please remember to enter all fields!</p>";
			include $this->core->conf['conf']['formPath'] . "changepass.form.php";

		}
	}
	
	function saveUser() {
		$function = __FUNCTION__;
		$title = 'Add user account';
		$description = 'The account information has been saved';
		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);

		$this->addUserSave();
	}

	public function password($length, $charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1') {
		$str = '';
		$count = strlen($charset);
		while ($length--) {
			$str .= $charset[mt_rand(0, $count - 1)];
		}
		return $str;
	}

	function addUser() {
		$function = __FUNCTION__;
		$title = 'Add user account';
		$description = 'Please provide the needed information to create a new user account';
		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);

		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";

		$select = new optionBuilder($this->core);
		$roles = $select->showRoles();

		include $this->core->conf['conf']['formPath'] . "adduser.form.php";
	}

	public function addUserSave() {

		$password = $this->password(6);

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

		$sql = "INSERT INTO `basic-information` (`FirstName`, `MiddleName`, `Surname`, `Sex`, `ID`, `GovernmentID`, `DateOfBirth`, `PlaceOfBirth`, `Nationality`, `StreetName`, `PostalCode`, `Town`, `Country`, `HomePhone`, `MobilePhone`, `Disability`, `DissabilityType`, `PrivateEmail`, `MaritalStatus`, `StudyType`, `Status`) VALUES ('$firstname', '$middlename', '$surname', '$sex', NULL, '$id', '$year-$month-$day', '$pob', '$nationality', '$streetname', '$postalcode', '$town', '$country', '$homephone', '$celphone', '$dissability', '$dissabilitytype', '$email', '$mstatus', '$studytype', 'Employed');";

		if ($this->core->database->doInsertQuery($sql)) {

			$sql = "SELECT * FROM `basic-information` WHERE `GovernmentID` = '$id'";
			$run = $this->core->database->doSelectQuery($sql);

			while ($fetch = $run->fetch_row()) {

				$passenc = $this->hashPassword($username, $password);

				$sql = "INSERT INTO `access` (`ID`, `Username`, `RoleID`, `Password`) VALUES ('$fetch[4]', '$username', '$roleid', '$passenc');";
				$this->core->database->doInsertQuery($sql);

				echo '<div class="successpopup">The requested user account has been created.<br/> WRITE THE FOLLOWING INFORMATION DOWN OR REMEMBER IT!</div>';
				echo '<div class="successpopup">Username:  <b>' . $username . '</b><br>Password:  <b>' . $password . '</b></div>';

			}

		} else {

			$this->core->throwError('An error occurred with the information you have entered. Please return to the form and verify your information. <a a href="javascript:" onclick="history.go(-1); return false">Go back</a>');

		}

	}

	public function hashPassword($username, $password){
		$passwordHashed = hash('sha512', $password . $this->core->conf['conf']['hash'] . $username);
		return $passwordHashed;
	}

	function showUserList() {

		$function = __FUNCTION__;
		$title = 'User management';
		$description = 'Overview of all users with privileges higher than student';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);

		echo '<div class="toolbar">
			<a href="' . $this->core->conf['conf']['path'] . '/users/add">Add new user account</a>
			</div>'; 

		echo '<table width="768" height="" border="0" cellpadding="3" cellspacing="0">
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

			$firstname = $row[0];
			$middlename = $row[1];
			$surname = $row[2];

			$username = $row[22];

			if(empty($firstname) && empty($lastname)){
				$firstname = $username;
			}

			$sex = $row[3];
			$uid = $row[4];
			$nrc = $row[5];
			$role = $row[26];
			$status = $row[20];

			echo '<tr>
			<td><img src="' . $this->core->fullTemplatePath . '/images/user.png"></td>
			<td><a href="' . $this->core->conf['conf']['path'] . '/information/view/' . $uid . '"><b>' . $firstname . ' ' . $middlename . ' ' . $surname . '</b></a></td>
			<td><i>' . $role . '</i></td>
			<td>' . $uid . '</td>
			<td>' . $status . '</td>
			<td><a href="' . $this->core->conf['conf']['path'] . '/information/edit/' . $uid . '"><img src="' . $this->core->fullTemplatePath . '/images/edi.png"> edit</a>  <a href="' . $this->core->conf['conf']['path'] . '/users/delete/' . $uid . '" onclick="return confirm(\'Are you sure?\')"><img src="' . $this->core->fullTemplatePath . '/images/delete.gif"> delete</a></td>
			</tr>';
		}

		echo '</table>';
	}

	function showStudentList() {
		$function = __FUNCTION__;
		$title = 'User management';
		$description = 'Overview of all students currently enrolled';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);

		echo '<table width="768" height="" border="0" cellpadding="3" cellspacing="0">
		<tr class="tableheader">
		<td></td>
		<td><b> Student Name</b></td>
		<td><b> Student ID</b></td>
		<td><b> Status</b></td>		
		<td><b> Options</b></td>
		</tr>';

		$sql = "SELECT * FROM `basic-information` 
			LEFT JOIN `access` ON `access`.`ID` = `basic-information`.`ID` 
			LEFT JOIN `roles` ON `roles`.`ID` = `access`.`RoleID` 
			WHERE `basic-information`.`Status` = 'Distance' 
			OR `basic-information`.`StudyType` = 'Fulltime' 
			ORDER BY `Surname`";

		$run = $this->core->database->doSelectQuery($sql);

		while ($row = $run->fetch_row()) {

			$firstname = $row[0];
			$middlename = $row[1];
			$surname = $row[2];
			$sex = $row[3];
			$uid = $row[4];
			$nrc = $row[5];
			$status = $row[20];

			echo '<tr>
			<td><img src="' . $this->core->fullTemplatePath . '/images/bullet_user.png"></td>
			<td><a href="' . $this->core->conf['conf']['path'] . '/information/view/' . $uid . '"><b>' . $firstname . ' ' . $middlename . ' ' . $surname . '</b></a></td>

			<td>' . $uid . '</td>
			<td>' . $status . '</td>
			<td><a href="' . $this->core->conf['conf']['path'] . '/information/edit/' . $uid . '"><img src="' . $this->core->fullTemplatePath . '/images/edi.png"> edit</a>  <a href="' . $this->core->conf['conf']['path'] . '/users/delete/' . $uid . '" onclick="return confirm(\'Are you sure?\')"><img src="' . $this->core->fullTemplatePath . '/images/delete.gif"> delete</a></td>
		  	</tr>';

		}

		echo '</table>';
	}

	function deleteUser($id) {
		$sql = 'UPDATE `basic-information` SET `Status` = "Removed" WHERE `ID` = "' . $id . '";';
		$run = $this->core->database->doInsertQuery($sql);

		$sql = 'DELETE FROM `access`  WHERE `ID` = "' . $id . '";';
		$run = $this->core->database->doInsertQuery($sql);

		$this->core->logEvent("Removed user $id", "4");

		$this->showUserList();
		$this->core->showAlert("The account has been deleted");
	}
}

?>
