<?php
class information {

	public $core;
	public $view;

	public function configView() {
		$this->view->header = TRUE;
		$this->view->footer = TRUE;
		$this->view->menu = FALSE;
		$this->view->javascript = array(3);
		$this->view->css = array(4);

		return $this->view;
	}

	public function buildView($core) {

		$this->core = $core;

		$action = $this->core->cleanGet['action'];
		$listtype = $this->core->cleanGet['listtype'];
		$uid = $this->core->cleanGet['uid'];
		$studentfirstname = $this->core->cleanGet['studentfirstname'];
		$studentlastname = $this->core->cleanGet['studentlastname'];
		$study = $this->core->cleanGet['studies'];
		$program = $this->core->cleanGet['program'];

		if (isset($studentlastname) || isset($studentfirstname)) {

			if ($studentfirstname == "") {
				$studentfirstname = "%";
			}
			if ($studentlastname == "") {
				$studentlastname = "%";
			}

			$sql = "SELECT * FROM `basic-information` WHERE `Surname` LIKE '" . $studentlastname . "' AND `Firstname` LIKE '" . $studentfirstname . "'";
			$run = $this->core->database->doSelectQuery($sql);

			$pagename = "Search results";

			$function = __FUNCTION__;
			$title = 'Student Information';
			$description = 'Showing results for: ' . $studentfirstname . ' ' . $studentlastname;

			echo component::generateBreadcrumb(get_class(), $function);
			echo component::generateTitle($title, $description);

			if ($listtype == "profiles") {
				$this->showInfoProfile($run);
			} elseif ($listtype == "list") {
				$this->showInfoList($run);
			}


		} else if (isset($study) || isset($program)) {

			if ($study != "" && is_numeric($study)) {
				$sql = "SELECT * FROM `basic-information`, `student-study-link` WHERE `student-study-link`.StudentID = `basic-information`.ID AND StudyID = '" . $study . "'";
			}
			if ($program != "" && is_numeric($program)) {
				$sql = "SELECT * FROM `basic-information`, `student-program-link` WHERE `student-program-link`.StudentID = `basic-information`.GovernmentID AND Major = '" . $program . "' OR `student-program-link`.StudentID = `basic-information`.ID AND Minor = '" . $program . "'";
			}

			$run = $this->core->database->doSelectQuery($sql);

			$function = __FUNCTION__;
			$title = '"Search results';
			$description = 'Showing results for: ' . $studentfirstname . ' ' . $studentlastname;

			echo component::generateBreadcrumb(get_class(), $function);
			echo component::generateTitle($title, $description);

			if ($listtype == "profiles") {
				$this->showInfoProfile($run);
			} elseif ($listtype == "list") {
				$this->showInfoList($run);
			}

		} elseif ($action == "edit" && !isset($uid)) {

			$this->editUser($this->userid);

		} elseif ($action == "personal") {

			$function = __FUNCTION__;
			$title = 'Personal information';
			$description = 'Showing results for: ' . $studentfirstname . ' ' . $studentlastname;

			echo component::generateBreadcrumb(get_class(), $function);
			echo component::generateTitle($title, $description);

			$uid = $_SESSION['userid'];
			$sql = "SELECT * FROM  `basic-information` as bi, `access` as ac WHERE ac.`ID` = '" . $uid . "' AND ac.`ID` = bi.`ID`";
			$run = $this->core->database->doSelectQuery($sql);

			$this->showInfoProfile($run);

		} elseif ($action == "edit" && isset($uid)) {

			$this->editUser($uid);

		} elseif (isset($uid) && is_numeric($uid)) {

			$function = __FUNCTION__;
			$title = 'Personal information';
			$description = 'Showing results for: ' . $studentfirstname . ' ' . $studentlastname;

			echo component::generateBreadcrumb(get_class(), $function);
			echo component::generateTitle($title, $description);

			$sql = "SELECT * FROM `basic-information` WHERE `ID` = '" . $uid . "'";
			$run = $this->core->database->doSelectQuery($sql);
			$this->showInfoProfile($run);

		}
	}

	function showInfoProfile($run) {

		while ($row = $run->fetch_row()) {

			$results = TRUE;
			$firstname = $row[0];
			$middlename = $row[1];
			$surname = $row[2];
			$sex = $row[3];
			$uid = $row[4];
			$nrc = $row[5];
			$dob = $row[6];
			$pob = $row[7];
			$nationality = $row[8];

			$streetname = $row[9];
			$postalcode = $row[10];
			$town = $row[11];
			$country = $row[12];
			$homephone = $row[13];
			$mobilephone = $row[14];

			$disability = $row[15];
			$disabilitytype = $row[16];
			$email = $row[17];
			$maritalstatus = $row[18];
			$studytype = $row[22];
			$studentstatus = $row[23];
			$approved = $row[23];

			$role = $row[23];

			$picid = substr($uid, 4);
			$picid = ltrim($picid, '0');

			echo '<div class="student">
		<div class="studentname"> ' . $firstname . ' ' . $middlename . ' ' . $surname . ' </div>';

			echo '<div class="profilepic">';

			if (file_exists("datastore/student-pictures/picture-$picid.jpg")) {
				echo '<img width="100%" src="datastore/student-pictures/picture-' . $picid . '.jpg">';
			} else {
				echo '<div class="none">No image available</div>';
			}

			if ($this->core->role > 103) {
				echo '<div style="margin-top: 1px; border-top: solid 1px #ccc; padding:10px;"><b><a href="?id=view-information&action=edit&uid=' . $uid . '">Edit user information</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px;"><b><a href="?id=view-information&action=edithousing&uid=' . $uid . '">Edit housing information</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px;"><b><a href="?id=grades&action=view-grades&uid=' . $uid . '">Show users grades</a></b></div>';
			}

			echo '</div>
			<table width="400" height="63" border="0" cellpadding="0" cellspacing="0">
			  <tr>
			<td>Student Number</td>
			<td><b>' . $uid . '</b></td>
			  </tr>
			  <tr>
			<td width="200">Gender/Sex</td>
			<td><u>' . $sex . '</u></td>
	 		 </tr>
	
	 		 <tr>
			<td>NRC</td>
			<td>' . $nrc . '</td>
	 		 </tr>
	 		 <tr>
			<td>Date of Birth</td>
			<td>' . $dob . '</td>
	 		 </tr>
			  <tr>
			<td>Nationality</td>
			<td>' . $nationality . '</td>
			  </tr>
			  <tr>
			<td>Marital Status</td>
			<td>' . $maritalstatus . '</td>
	 		 </tr>';

			$sql = "SELECT * FROM `roles` WHERE `ID` LIKE '$role'";
			$run = $this->core->database->doSelectQuery($sql);

			while ($row = $run->fetch_row()) {

				echo '<tr>
			<td>Access Level</td>
			<td>' . $row[1] . '</td>
			</tr>';

			}

			echo '</table>';

			$sql = "SELECT * FROM `student-program-link` as sp, `programmes` as pr WHERE sp.`StudentID` = '" . $nrc . "' AND sp.`Major` = pr.`ID` OR sp.`StudentID` = '" . $nrc . "' AND sp.`Minor` = pr.`ID` ";
			$run = $this->core->database->doSelectQuery($sql);

			while ($row = $run->fetch_row()) {

				$name = $row[7];

				if (!isset($major)) {
					$major = $name;
					echo '<p><div class="segment"><strong>Student course information</strong></div></p>
				<table width="500" height="" border="0" cellpadding="0" cellspacing="0">
				<tr>
				<td width="200">Major</td>
				<td width=""><b>' . $major . '</b></td>
				</tr>';
				} else {
					$minor = $name;
					echo '<tr>
				<td>Minor</td>
				<td width=""><b>' . $minor . '</b></td>
			  	</tr>
				</table>';
					unset($major);
				}

			}

			if (!isset($minor)) {
				$minor = $name;
				echo '<tr>
			<td>Minor</td>
			<td width=""><b>' . $minor . '</b></td>
		  	</tr>
			</table>';
			}

			echo '<p><div class="segment"><strong>Contact information</strong></div></p>
	<table width="400" height="" border="0" cellpadding="0" cellspacing="0">
	  <tr>
		<td width="200">Streetname</td>
		<td width="">' . $streetname . '</td>
	  </tr>';

			if ($postalcode != "") {
				echo '<tr>
		<td>Postal code</td>
		<td>' . $postalcode . '</td>
	  </tr>';
			}

			if ($town != "") {
				echo '<tr>
		<td>Town</td>
		<td>' . $town . '</td>
	  </tr>';
			}

			if ($country != "") {
				echo '<tr>
		<td>Country</td>
		<td>' . $country . '</td>
	  </tr>';
			}

			echo '<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	</tr>';

			if ($homephone != "" && $homephone != "0") {
				echo '<tr>
		<td>Home Phone</td>
		<td>' . $homephone . '</td>
	  </tr>';
			}

			if ($mobilephone != "" && $mobilephone != "0") {
				echo '<tr>
		<td>Mobile Phone</td>
		<td>' . $mobilephone . '</td>
	  </tr>';
			}

			if ($email != "") {
				echo '<tr>
		<td>Private Email</td>
		<td><a href="mailto:' . $email . '">' . $email . '</td>
	  </tr>
	</table>';
			}


			$sql = "SELECT * FROM `emergency-contact` WHERE `StudentID` = '" . $nrc . "'";
			$run = $this->core->database->doSelectQuery($sql);

			while ($fetch = $run->fetch_row()) {

				$fullname = $fetch[2];
				$relationship = $fetch[3];
				$phonenumber = $fetch[4];
				$street = $fetch[5];
				$town = $fetch[6];
				$postalcode = $fetch[7];

				echo '<p><div class="segment"><strong>Student emergency information</strong></div></p>
			<table width="500" height="" border="0" cellpadding="0" cellspacing="0">
			  <tr>
				<td width="200">Full Name</td>
				<td width="">' . $fullname . '</td>
			  </tr>
			  <tr>
				<td>Relationship</td>
				<td>' . $relationship . '</td>
			  </tr>';
				if ($phonenumber != "" && $phonenumber != "0") {
					echo '<tr>
				<td>Phonenumber</td>
				<td>' . $phonenumber . '</td>
			  </tr>';
				}
				echo '<tr>
				<td>Street</td>
				<td>' . $street . '</td>
			  </tr>
			  <tr>
				<td>Town</td>
				<td>' . $town . '</td>
			  </tr>
			  <tr>
				<td>Postalcode</td>
				<td>' . $postalcode . '</td>
			  </tr>
			</table>';

			}

			$sql = "SELECT * FROM `education-background` WHERE `StudentID` = '" . $nrc . "'";
			$run = $this->core->database->doSelectQuery($sql);
			$n = 0;

			while ($row = $run->fetch_row()) {

				$name = $row[2];
				$type = $row[3];
				$institution = $row[4];
				$filename = $row[5];

				if ($n == 0) {
					echo '<p><div class="segment"><strong>Student education history</strong></div></p>';
					$n++;
				} else {
					echo '<div style="border-bottom: 1px solid #ccc; width:500px; margin-top: 15px; margin-bottom: 15px;" > </div>';
				}

				echo '<table width="500" height="" border="0" cellpadding="0" cellspacing="0">
			  <tr>
				<td width="200">Name of institution</td>
				<td width="">' . $institution . '</td>
			  </tr>
			  <tr>
				<td>Level of certificate</td>
				<td>' . $type . '</td>
			  </tr>
			  <tr>
				<td>Name of certificate</td>
				<td>' . $name . '</td>
			  </tr>';

				if ($filename != "") {
					echo '<tr>
				<td>Image of certificate</td>
				<td><a href="?id=download&file=education-history/' . $filename . '"><b>View file</b></a></td>
			  </tr>';
				}

				echo '</table>';

			}


			echo "</div>";

		}

		if ($results != TRUE) {
			$this->core->throwError('Your search did not return any results');
		}

	}

	function showInfoList($run) {

		echo '<br/> <table width="768" height="" border="0" cellpadding="5" cellspacing="0">
	<tr>
	<td bgcolor="#EEEEEE"></td>
	<td bgcolor="#EEEEEE"><b> Student Name</b></td>
	<td bgcolor="#EEEEEE"><b> Student ID</b></td>
	<td bgcolor="#EEEEEE"><b> National ID</b></td>
	<td bgcolor="#EEEEEE"><b> Date of Birth</b></td>		
	<td bgcolor="#EEEEEE"><b> Status</b></td>
	</tr>';

		while ($row = $run->fetch_row()) {

			$results = TRUE;
			$firstname = $row[0];
			$middlename = $row[1];
			$surname = $row[2];
			$sex = $row[3];
			$uid = $row[4];
			$nrc = $row[5];
			$dob = $row[6];
			$studentstatus = $row[20];

			echo '<tr>
		<td><img src="templates/default/images/bullet_user.png"></td>
		<td><a href="?id=view-information&uid=' . $uid . '"><b>' . $firstname . ' ' . $middlename . ' ' . $surname . '</b></a></td>
		<td><i>' . $uid . '</i></td>
		<td>' . $nrc . '</td>
		<td>' . $dob . '</td>
		<td>' . $studentstatus . '</td>
	  	</tr>';

		}

		echo '</table>';

		if ($results != TRUE) {
			$this->core->throwError('Your search did not return any results');
		}

	}

	function editUser($id) {
		$sql = "SELECT * FROM  `basic-information` as bi, `access` as ac WHERE ac.`ID` = '" . $id . "' AND ac.`ID` = bi.`ID`";
		$run = $this->core->database->doSelectQuery($sql);

		$function = __FUNCTION__;
		$title = 'Edit personal information';
		$description = 'Editing student information';

		echo component::generateBreadcrumb(get_class(), $function);
		echo component::generateTitle($title, $description);

		while ($row = $run->fetch_row()) {

			$ID = $row[4];
			$firstname = $row[0];
			$middlename = $row[1];
			$surname = $row[2];
			$gender = $row[3];
			$dob = $row[6];
			$nationality = $row[8];
			$street = $row[9];
			$postal = $row[10];
			$town = $row[11];
			$country = $row[12];
			$homephone = $row[13];
			$celphone = $row[14];
			$disability = $row[15];
			$email = $row[17];
			$relation = $row[18];
			$status = $row[20];
			$role = $row[23];

		}

		include $this->core->formPath . "edituser.form.php";
		include $this->core->classPath . "showoptions.inc.php";

		$select = new optionBuilder($this->core);
		$select = $select->showRoles($role);

	}


}

?>
