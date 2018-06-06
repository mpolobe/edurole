<?php
class admission {

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
	}

	private function viewMenu(){
		$today = date("Y-m-d");

		if(isset($_GET['date'])){
			$today = $_GET['date'];
		}

		echo '<div class="toolbar">'.
		'<a href="' . $this->core->conf['conf']['path'] . '/admission/active">Active Registration Requests</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/admission/rejected">Rejected Requests</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/admission/print">Print Error List</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/admission/printall">Print Complete List</a>'.
		'</div>';
	}




	public function importAdmission(){
	

		$filename = "/data/website/edurole/datastore/import.csv";
		if(!file_exists($filename)){
			echo'<div class="errorpopup"> NO FILE FOUND CREATE IMPORT.CSV</div>';
		}

		$file = file_get_contents($filename);
		$document = explode("\n", $file);

			echo'<div class="successpopup"> FOUND IMPORT.CSV</div>';
		$i=0;
		foreach($document as $line){
			$i++;
			if($i == 1){ continue; }

			$csv = explode(",", $line);

			$lastname = trim($csv[0]);
			$middlename = trim($csv[1]);
			$firstname = trim($csv[2]);
			$sex = trim($csv[3]);
			$nrc = trim($csv[4]);
			$studentid = trim($csv[5]);
			$phone = trim($csv[6]);
			$mode = trim($csv[7]);

			if(empty($firstname) || empty($lastname) || empty($phone) || empty($mode) || empty($nrc) || empty($studentid)){
				echo'<div class="errorpopup">Missing field</div>';
			}

			$sqd = "INSERT INTO `basic-information` (`FirstName`, `MiddleName`, `Surname`, `Sex`, `ID`, `GovernmentID`, `DateOfBirth`, `PlaceOfBirth`, `Nationality`, `StreetName`, `PostalCode`, `Town`, `Country`, `HomePhone`, `MobilePhone`, `Disability`, `DissabilityType`, `PrivateEmail`, `MaritalStatus`, `StudyType`, `Status`) 
				VALUES ('$firstname', '$middlename', '$lastname', '$sex', '$studentid', '$nrc', '', '', '', '', '', '', '', '', '$phone', '', NULL, '', '', '$mode', 'New');";

			$this->core->database->doInsertQuery($sqd);
			echo'<div class="successpopup"> IMPORTED '.$studentid.'</div>';

			$this->core->audit(__CLASS__, $nrc, $studentid, "Manually added student $studentid - $nrc");

		}





	}



	public function activateAdmission($item){
	
		$sql = "UPDATE `basic-information` SET  `Status` =  'Approved' WHERE  `basic-information`.`ID` = '$item';";
		$run = $this->core->database->doInsertQuery($sql);

		$this->core->audit(__CLASS__, $item, $item, "Activated student $item");
	

		$this->core->redirect("information", "show", $item);

	}


	public function manualAdmission(){
		$firstname = $this->core->cleanGet['firstname'];
		$lastname = $_GET['lastname'];
		$phone = $_GET['phone'];
		$mode = $_GET['mode'];
		$nrc = $_GET['nrc'];
		$studentid = $_GET['studentid'];

		$userid = $this->core->userID;
		$url = $_SERVER['REQUEST_URI'];

		if(empty($firstname) || empty($lastname) || empty($phone) || empty($mode) || empty($nrc) || empty($studentid)){
			echo'<form>
			<div class="successpopup">Please ensure you enter all details. <br> PLEASE NOTE THE REGISTRAR RECEIVES A DIRECT NOTIFICATION OF ADDED STUDENTS</div>';

			echo'<div class="heading">Directly admit student</div> 
			<div class="label">First Name</div><input name="firstname"  class="submit"> <br>
			<div class="label">Surname</div><input name="lastname"  class="submit"> <br>
			<div class="label">NRC number</div><input name="nrc"  class="submit"> <br>
			<div class="label">Student number</div><input name="studentid"  class="submit"> <br>
			<div class="label">Phone number</div><input name="phone"  class="submit"> <br>
			<div class="label">Mode</div><select name="mode"  class="submit">
							<option value="Fulltime">Fulltime Student</option>
							<option value="Distance">Distance Education</option>
							</select><br>
			<br><input value="Directly Admit Student" type="submit" class="btn btn-primary">
			</form>';
		 }


		$sql = "INSERT INTO `basic-information` (`FirstName`, `MiddleName`, `Surname`, `Sex`, `ID`, `GovernmentID`, `DateOfBirth`, `PlaceOfBirth`, `Nationality`, `StreetName`, `PostalCode`, `Town`, `Country`, `HomePhone`, `MobilePhone`, `Disability`, `DissabilityType`, `PrivateEmail`, `MaritalStatus`, `StudyType`, `Status`) VALUES ('$firstname', '', '$lastname', '', '$studentid', '$nrc', '', '', '', '', '', '', '', '', '$phone', '', NULL, '', '', '$mode', 'New');";
		$run = $this->core->database->doInsertQuery($sql);

		$this->core->audit(__CLASS__, $nrc, $studentid, "Manually added student $studentid - $nrc");
	}

	public function manageAdmission() {
		$this->viewMenu();
		
		$sql = 'SELECT StudyType, COUNT(ID)  FROM `basic-information` WHERE `Status` = \'Requesting\' GROUP BY `basic-information`.StudyType ORDER BY StudyType';

		$run = $this->core->database->doSelectQuery($sql);
		$i=0;

		while ($fetch = $run->fetch_row()) {
			if($i == 0){
				$part = $fetch[1];
			}elseif ($i == 1){
				$int = $fetch[1];
			}else{
				$dis = $fetch[1];
			}

			$i++;
		}

		echo '<div class="toolbar">'.
		'<a href="' . $this->core->conf['conf']['path'] . '/admission/internal">Fulltime students ('.$int.')</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/admission/parttime">Part-time students ('.$part.')</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/admission/distance">Distance students ('.$dis.')</a>'.
		'</div>';
	}

	public function internalAdmission() {
		$this->manageAdmission();
		$this->activeAdmission("Fulltime");
		$this->rejectedAdmission();
	}

	public function distanceAdmission() {
		$this->manageAdmission();
		$this->activeAdmission("Distance");
		$this->rejectedAdmission();
	}

	public function parttimeAdmission() {
		$this->manageAdmission();
		$this->activeAdmission("Partime");
		$this->rejectedAdmission();
	}

	public function printAdmission() {
		$sql = "SELECT `basic-information`.ID, `basic-information`.FirstName,  `basic-information`.MiddleName, `basic-information`.Surname, `ProA`.ProgramName, `ProB`.ProgramName, `student-data-other`.ExamCentre, `student-data-other`.YearOfStudy, `basic-information`.Status,`student-data-other`.StudentID, `ProA`.ID, `ProB`.ID, `student-program-link`.`Major`, `student-program-link`.`Minor`, `student-program-link`.`StudentID`, `basic-information`.MobilePhone FROM `basic-information`
			LEFT JOIN `student-study-link` ON `basic-information`.`ID` = `student-study-link`.`StudentID`
			LEFT JOIN `student-program-link` ON `basic-information`.`ID` = `student-program-link`.`StudentID`
			LEFT JOIN `programmes` AS `ProA` ON `ProA`.`ID` = `student-program-link`.`Major`
			LEFT JOIN `programmes` AS `ProB` ON `ProB`.`ID` = `student-program-link`.`Minor`
			LEFT JOIN `student-data-other` ON `student-data-other`.`StudentID` = `basic-information`.`ID`
			WHERE `basic-information`.`Status` = 'Requesting' GROUP BY `basic-information`.ID";

		$run = $this->core->database->doSelectQuery($sql);

		$header = '<table id="active" class="table table-bordered table-striped table-hover">
			<thead>
				<tr>
					<th bgcolor="#EEEEEE"><b>Student No</b></th>
					<th bgcolor="#EEEEEE" width="300px" data-sort"string"><b>Student Name</b></th>
					<th bgcolor="#EEEEEE"><b>Major</b></th>
					<th bgcolor="#EEEEEE"><b>Minor</b></th>
					<th bgcolor="#EEEEEE"><b>Exam Center</b></th>
					<th bgcolor="#EEEEEE"><b>Year</b></th>
					<th bgcolor="#EEEEEE"><b>Celphone</b></th>
					<th bgcolor="#EEEEEE"><b>Picture</b></th>
				</tr>
			</thead>
			<tbody>';

		$footer =  '</tbody></table>';

		$i = 1;
		$j = 1;

		$output = "";

		echo '<h1>Incomplete Information</h1>' . $header;

		while ($fetch = $run->fetch_row()) {

			$uid = $fetch[0];
			$firstname = $fetch[1];
			$middlename = $fetch[2];
			$surname = $fetch[3];
			$status = $fetch[8]; 
			$phone = $fetch[15]; 

			if (file_exists("datastore/identities/pictures/$uid.png")) {
				$picture = "YES";
			} else {
				$picture = "<div style=\"color: red;  font-weight: bold;\">REPORT</div>";	
				$report = "PIC";
			}

			if(empty($fetch[4])){ $major = "<div style=\"color: red; font-weight: bold;\">REPORT</div>";   $report = "YES"; } else { $major = $fetch[4]; }
			if(empty($fetch[5])){ $minor = "<div style=\"color: red;  font-weight: bold;\">REPORT</div>";  $report = "YES"; } else { $minor = $fetch[5]; }
			if(empty($fetch[7])){ $year = "<div style=\"color: red;  font-weight: bold;\">REPORT</div>";   $report = "YES"; } else { $year = "YEAR " . $fetch[7];  }
			if(empty($fetch[6])){ $exam = "<div style=\"color: red;  font-weight: bold;\">REPORT</div>";   $report = "YES"; } else { $exam = $fetch[6];  }
			

			if ($major == "Civic Education" AND $minor == "Civic Education"){
				$major = "<div style=\"color: red; font-weight: bold;\">REPORT</div>"; 
				$minor = "<div style=\"color: red; font-weight: bold;\">REPORT</div>"; 
			}

			if($report == "YES"){
				$output = $output . '<tr>
				<td>'.$uid.'</td>
				<td>'.$i.' - <b>' . $firstname . ' ' . $middlename . ' ' . $surname . '</b></td>
				<td>' . $major . '</td>
				<td>' . $minor . '</td>
				<td>' . $exam . '</td>
				<td><b>' . $year . '</b></td>
				<td><b>' . $phone . '</b></td>
				<td>'.$picture.'</td>
				</tr>';
				$i++;
			} elseif ($report == "PIC") {
				$picoutput =  $picoutput . '<tr>
				<td>'.$uid.'</td>
				<td>'.$j.' - <b>' . $firstname . ' ' . $middlename . ' ' . $surname . '</b></td>
				<td>' . $major . '</td>
				<td>' . $minor . '</td>
				<td>' . $exam . '</td>
				<td><b>' . $year . '</b></td>
				<td><b>' . $phone . '</b></td>
				<td>'.$picture.'</td>
				</tr>';
				$j++;	
			}

			$report = FALSE;
		}

		echo $output . $footer;
		

		echo '<h1>Missing Student Pictures</h1>'. $header . $picoutput . $footer;
	}

	public function printallAdmission() {
		echo '<p class="title1">Currently active admission requests</p> ';

		$sql = "SELECT * FROM `basic-information`
			LEFT JOIN `access` ON `basic-information`.`ID` = `access`.`ID`
			LEFT JOIN `student-study-link` ON `basic-information`.`ID` = `student-study-link`.`StudentID`
			LEFT JOIN `study` ON `student-study-link`.`StudyID` = `study`.`ID`
			LEFT JOIN `student-program-link` ON `basic-information`.`ID` = `student-program-link`.`StudentID`
			LEFT JOIN `programmes` AS `ProA` ON `ProA`.`ID` = `student-program-link`.`Major`
			LEFT JOIN `programmes` AS `ProB` ON `ProB`.`ID` = `student-program-link`.`Minor`
			LEFT JOIN `student-data-other` ON `student-data-other`.`StudentID` = `basic-information`.`ID`
			WHERE `basic-information`.`Status` = 'Requesting' GROUP BY `basic-information`.ID";

		$run = $this->core->database->doSelectQuery($sql);

		echo '<table id="active" class="table table-bordered table-striped table-hover">
			<thead>
				<tr>
					<th bgcolor="#EEEEEE"><b>Student No</b></th>
					<th bgcolor="#EEEEEE" width="300px" data-sort"string"><b>Student Name</b></th>
					<th bgcolor="#EEEEEE"><b>Exam Center</b></th>
					<th bgcolor="#EEEEEE"><b>Major</b></th>
					<th bgcolor="#EEEEEE"><b>Minor</b></th>
					<th bgcolor="#EEEEEE"><b>Year</b></th>
					<th bgcolor="#EEEEEE"><b>Picture</b></th>
				</tr>
			</thead>
			<tbody>';

		$i = 1;

		while ($fetch = $run->fetch_row()) {

			$status = $fetch[7];
			$study = $fetch[36];
			$firstname = $fetch[0];
			$middlename = $fetch[1];
			$surname = $fetch[2];
			$sex = $fetch[3];
			$uid = $fetch[4];
			$nrc = $fetch[5];
			$role = $fetch[25];
			$status = $fetch[23]; 

			if (file_exists("datastore/identities/pictures/$uid.png")) {
				$picture = "YES"; 
			} else {
				$picture = "<div style=\"color: red;  font-weight: bold;\">REPORT</div>";	
				$report = TRUE;
			}

			if(empty($fetch[49])){ $major = "<div style=\"color: red; font-weight: bold;\">REPORT</div>";   $report = TRUE; } else { $major = $fetch[49]; }
			if(empty($fetch[53])){ $minor = "<div style=\"color: red;  font-weight: bold;\">REPORT</div>";  $report = TRUE; } else { $minor = $fetch[53]; }
			if(empty($fetch[57])){ $year = "<div style=\"color: red;  font-weight: bold;\">REPORT</div>";   $report = TRUE; } else { $year = "YEAR " . $fetch[57];  }
			if(empty($fetch[58])){ $exam = "<div style=\"color: red;  font-weight: bold;\">REPORT</div>";   $report = TRUE; } else { $exam = $fetch[58];  }
			
			echo '<tr>
			<td>'.$uid.'</td>
			<td>'.$i.' - <b>' . $firstname . ' ' . $middlename . ' ' . $surname . '</b></td>
			<td>' . $exam . '</td>
			<td>' . $major . '</td>
			<td>' . $minor . '</td>
			<td><b>' . $year . '</b></td>
			<td>'.$picture.'</td>
			</tr>';
			$i++;
			

			
			
			$report = FALSE;
		}

		echo '</tbody>
		</table>';

	}




	public function profileAdmission($item) {
		if ($this->core->role < 100) {
			$sql = "SELECT * FROM `basic-information` as bi, `roles` as rl, `access` as ac WHERE ac.`ID` = '" . $this->core->username . "' AND ac.`ID` = bi.`ID` AND ac.`RoleID` = rl.`ID`";
		} else {
			$sql = "SELECT * FROM `basic-information` as bi, `roles` as rl, `access` as ac WHERE ac.`ID` = '" . $this->core->item . "' AND ac.`ID` = bi.`ID` AND ac.`RoleID` = rl.`ID`";
		}

		$this->showInfoProfile($sql);
	}

	public function completeAdmission($item) {
		$sql = "UPDATE `access` SET `RoleID` = 10 WHERE `access`.`ID` = '" . $item . "'";
		$run = $this->core->database->doInsertQuery($sql);

		$sql = "UPDATE `basic-information` SET `Status` = 'Enrolled' WHERE `basic-information`.`ID` = '" . $item . "'";
		$run = $this->core->database->doInsertQuery($sql);

		$sql = "SELECT * FROM `basic-information` WHERE `GovernmentID` = '" . $item . "'";
		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_assoc()) {
			$recipient = $fetch["PrivateEmail"];
			$mailer = serviceBuilder("mailer");
			$mailer->newMail("registrationSuccessful", $recipient);
		}

		$this->core->redirect("admission", "manage", NULL);
	}

	public function promoteAdmission($item) {
		$sql = "UPDATE `edurole`.`access` SET `RoleID` = `RoleID`+1 WHERE `access`.`ID` = '" . $item . "' AND `RoleID` = '". $this->core->route[3]."'";
		$run = $this->core->database->doInsertQuery($sql);

		$this->core->redirect("admission", "manage", NULL);
	}

	public function rejectAdmission($item) {
		$sql = "UPDATE `edurole`.`basic-information` SET `Status` = 'Rejected' WHERE `basic-information`.`ID` = '" . $item . "';";
		$run = $this->core->database->doInsertQuery($sql);

		$this->core->redirect("admission", "manage", NULL);
	}

	public function continueAdmission($item) {
		$sql = "UPDATE `edurole`.`basic-information` SET `Status` = 'Requesting' WHERE `basic-information`.`ID` = '" . $item . "';";
		$run = $this->core->database->doInsertQuery($sql);

		$this->core->redirect("admission", "manage", NULL);
	}


	public function deleteAdmission($item) {
		$sql = "UPDATE `edurole`.`basic-information` SET `Status` = 'Failed' WHERE `basic-information`.`ID` = '" . $item . "';";
		$run = $this->core->database->doInsertQuery($sql);

		$this->core->redirect("admission", "manage", NULL);
	}

	public function activeAdmission($mode) {

		if(empty($mode)){ $mode = "%"; }

		echo '<p class="title1">Currently active admission requests</p> ';

		$sql = "SELECT `basic-information`.ID, `basic-information`.Firstname, `basic-information`.MiddleName, `basic-information`.Surname, `basic-information`.GovernmentID,  `basic-information`.StudyType,  `basic-information`.Status
			FROM `basic-information`
			LEFT JOIN `student-study-link` ON `basic-information`.`ID` = `student-study-link`.`StudentID`
			LEFT JOIN `study` ON `student-study-link`.`StudyID` = `study`.`ID`
			WHERE `basic-information`.`Status` = 'Requesting' AND `basic-information`.StudyType LIKE '$mode' GROUP BY `basic-information`.ID";

		$run = $this->core->database->doSelectQuery($sql);

		$sql = "SELECT Name, Value FROM `settings` WHERE `Name` LIKE 'AdmissionLevel%' ORDER BY Name ASC";
		$go = $this->core->database->doSelectQuery($sql);

		$i=1;
		while ($fetch = $go->fetch_row()) {
			$name = substr($fetch[1],0,50).'...';
			$statusName[$i] = $name;
			$i++;
		}

		echo '<table id="active" class="table table-bordered table-striped table-hover">
			<thead>
				<tr>
					<th bgcolor="#EEEEEE" width="300px" data-sort"string"><b> Applicant Name</b></th>
					<th bgcolor="#EEEEEE"><b>National Reg.</b></th>
					<th bgcolor="#EEEEEE"><b>Student ID</b></th>
					<th bgcolor="#EEEEEE" width="180px"><b> Options</b></th>
				</tr>
			</thead>
			<tbody>';

		$i = 1;

		while ($fetch = $run->fetch_row()) {

			$status = $fetch[6];
			$study = $fetch[7];
			$firstname = $fetch[1];
			$middlename = $fetch[2];
			$surname = $fetch[3];
			$uid = $fetch[0];
			$nrc = $fetch[4];

			if ($status == 6) {
				$next = '<a href="' . $this->core->conf['conf']['path'] . '/admission/complete/' . $uid . '/' . $status . '"><img src="' . $this->core->fullTemplatePath . '/images/exleft.gif"> <b>Complete</b> </a>';
			} else {
				$next = '<a href="' . $this->core->conf['conf']['path'] . '/admission/promote/' . $uid . '/' . $status . '"><img src="' . $this->core->fullTemplatePath . '/images/exleft.gif">Approve</a>';
			}

			echo '<tr>
				<td>'.$i.' - <a href="' . $this->core->conf['conf']['path'] . '/information/show/' . $uid . '"><b>' . $firstname . ' ' . $middlename . ' ' . $surname . '</b></a> </td>
				<td>' . $nrc . '</td>
				<td><b>' . $uid . '</b></td>
				<td>' . $next . ' / <a href="' . $this->core->conf['conf']['path'] . '/admission/reject/' . $uid . '"><img src="' . $this->core->fullTemplatePath . '/images/delete.gif"> Deny</a></td>
				</tr>';
			$i++;
		}

		echo '</tbody>
		</table>';

	}

	public function rejectedAdmission() {

		echo '<p class="title1">Currently denied admission requests</p> ';

		$sql = "SELECT * FROM `basic-information`, `access`, `student-study-link`, `study` 
			WHERE `access`.`ID` = `basic-information`.`ID` 
			AND  `access`.`RoleID` < 10 
			AND  `access`.`ID` = `student-study-link`.`StudentID` 
			AND `student-study-link`.`StudyID` = `study`.ID 
			AND `basic-information`.Status = 'Rejected'";

		$run = $this->core->database->doSelectQuery($sql);

		$sql = "SELECT Name, Value FROM `settings` WHERE `Name` LIKE 'AdmissionLevel%' ORDER BY Name ASC";
		$go = $this->core->database->doSelectQuery($sql);

		$i=1;

		while ($fetch = $go->fetch_row()) {
			$name = substr($fetch[1],0,50).'...';
			$statusName[$i] = $name;
			$i++;
		}

		echo '<table id="active" class="table table-bordered table-striped table-hover">
			<thead>
				<tr>
					<th bgcolor="#EEEEEE" width="300px" data-sort"string"><b> Applicant Name</b></th>
					<th bgcolor="#EEEEEE"><b> <b>National ID</b></th>
					<th bgcolor="#EEEEEE"><b> Study</b></th>
					<th bgcolor="#EEEEEE" width="90px"><b> Options</b></th>
				</tr>
			</thead>
			<tbody>';

		while ($fetch = $run->fetch_row()) {

			$status = $fetch[7];
			$study = $fetch[36];
			$firstname = $fetch[0];
			$middlename = $fetch[1];
			$surname = $fetch[2];
			$sex = $fetch[3];
			$uid = $fetch[4];
			$nrc = $fetch[5];
			$role = $fetch[25];
			$status = $fetch[23];

			echo '<tr>
				<td><a href="' . $this->core->conf['conf']['path'] . '/information/show/' . $uid . '"><b>' . $firstname . ' ' . $middlename . ' ' . $surname . '</b></a> <br> <a href="' . $this->core->conf['conf']['path'] . '/admission/profile/' . $uid . '">' . $status .' - '.$statusName[$status].'  </a> </td>
				<td>' . $nrc . '</td>
				<td><b>' . $study . '</b></td>
				<td><a href="' . $this->core->conf['conf']['path'] . '/admission/continue/' . $uid . '"><img src="' . $this->core->fullTemplatePath . '/images/edit.gif"> Continue </a><br/><a href="' . $this->core->conf['conf']['path'] . '/admission/delete/' . $uid . '"><img src="' . $this->core->fullTemplatePath . '/images/delete.gif"> Delete</a></td>
				</tr>';
		}

		echo '</tbody>
		</table>';



	}

	private function showInfoProfile($sql) {

		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_row()) {

			$id = $_SESSION['userid'];
			$firstname = $fetch[0];
			$middlename = $fetch[1];
			$surname = $fetch[2];
			$sex = $fetch[3];
			$studentid = $fetch[4];
			$nrc = $fetch[5];
			$studytype = $fetch[22];
			$role = $fetch[21];
			$status = $fetch[20];

			echo '<div class="studentname">' . $firstname . ' ' . $middlename . ' ' . $surname . ' </div>
			<table width="768" border="0" cellpadding="0" cellspacing="0">
			 <tr>
			<td>Student number</td>
			<td>' . $studentid . '</td>
			 </tr>
			</table>';

			$i = 1;

			echo '<p><b>To successfully complete your admission process you will need to complete ALL steps.</b></p>
			<table width="768" border="0" cellpadding="3" cellspacing="0">';

			$sql = "SELECT `Name`, `Value` FROM `settings` WHERE `Name` LIKE 'AdmissionLevel%'";
			$run = $this->core->database->doSelectQuery($sql);

			while ($fetch = $run->fetch_row()) {

				if ($i == $role && $status == "Rejected") {
					$background = 'style="background-color: #F2BFBF"';
					$step = '<image src="' . $this->core->fullTemplatePath . '/images/error.png"> <b>FAILED TO MEET REQUIREMENTS</b>';
				} elseif ($i <= $role) {
					$background = 'style="background-color: #DFF2BF"';
					$step = '<image src="' . $this->core->fullTemplatePath . '/images/check.png"> completed';
				} elseif ($i > $role) {
					$background = 'style="background-color: #fff"';
					$step = '<image src="' . $this->core->fullTemplatePath . '/images/tviload.gif"> not yet completed';
				}

				echo '<tr ' . $background . '>
					<td width="100"><b>Step ' . $i . '</b></td>
					<td width="300"><em>' . $fetch[1] . '</em></td>
					<td>' . $step . '</td>
					</tr>';

				$i++;
			}

			echo '</table>';

		}


	}
}

?>

