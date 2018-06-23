<?php
class information {

	public $core;
	public $view;
	public $limit;
	public $offset;
	public $pager = FALSE;

	public function configView() {
		$this->view->header = TRUE;
		$this->view->footer = TRUE;
		$this->view->menu = TRUE;
		$this->view->javascript = array('jquery.form-repeater');
		$this->view->css = array();

		return $this->view;
	}

	private function viewMenu(){
		echo '<div class="toolbar">'.
		'<a href="' . $this->core->conf['conf']['path'] . '/sms/manage">Manage SMS</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/sms/new">Send SMS</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/sms/balance">Balance</a>'.
		'</div>';
	}

	public function buildView($core) {
		$this->core = $core;

		$this->limit = 50;
		$this->offset = 0;

		include $this->core->conf['conf']['classPath'] . "users.inc.php";


		if(empty($this->core->item)){
			if(isset($this->core->cleanGet['uid'])){
				$this->core->item = trim($this->core->cleanGet['uid']);
			}
		}
		if(isset($this->core->cleanGet['offset'])){
			$this->offset = $this->core->cleanGet['offset'];
		}
		if(isset($this->core->cleanGet['limit'])){
			$this->limit = $this->core->cleanGet['limit'];
			$this->pager = TRUE;
		}
	} 

	public function studentsInformation($item) {
		$this->searchInformation($item);
	}

	public function saveInformation($item){
		$users = new users($this->core);
		$users->saveEdit($this->core->item, TRUE);

		$this->core->throwSuccess($this->core->translate("The user account has been updated"));
		$this->editInformation($item);
	}

	public function personalInformation($item){
		$userid = $this->core->userID;

		$sql = "SELECT * FROM  `basic-information` as bi, `access` as ac WHERE ac.`ID` = '" . $userid . "' AND ac.`ID` = bi.`ID`";

		$this->showInfoProfile($sql, TRUE);
	}

	public function searchInformation($item) {
		$listType = "list";

		if(isset($this->core->cleanGet['studies'])){
			$studies = $this->core->cleanGet['studies'];
		}
		if(isset($this->core->cleanGet['programmes'])){
			$programmes = $this->core->cleanGet['programmes'];
		}
		if(isset($this->core->cleanGet['search'])){
			$search = $this->core->cleanGet['search'];
		}
		if(isset($this->core->cleanGet['q'])){
			$q = $this->core->cleanGet['q'];
		}

		if(isset($this->core->cleanGet['card'])){
			$card = $this->core->cleanGet['card'];
		}

		if(isset($this->core->cleanGet['group'])){
			$group = $this->core->cleanGet['group'];
		}

		if(isset($this->core->cleanGet['studentfirstname'])){
			$firstName = $this->core->cleanGet['studentfirstname'];
		}
		if(isset($this->core->cleanGet['studentlastname'])){
			$lastName = $this->core->cleanGet['studentlastname'];
		}
		if(isset($this->core->cleanGet['listtype'])){
			$listType = $this->core->cleanGet['listtype'];
		}
		if(isset($this->core->cleanGet['year'])){
			$year = $this->core->cleanGet['year'];
		}
		if(isset($this->core->cleanGet['mode'])){
			$mode = $this->core->cleanGet['mode'];
		}
		if(isset($this->core->cleanGet['examcenter'])){
			$center = $this->core->cleanGet['examcenter'];
		}
		if(isset($this->core->cleanGet['role'])){
			$role = $this->core->cleanGet['role'];
		}

		if (isset($lastName) || isset($firstName)) {
			$this->bynameInformation($firstName, $lastName, $listType);
		}elseif (isset($center)){
			$this->bycenterInformation($center);
		} else if ($this->core->action == "search" && isset($q) && $search == "study" || $this->core->action == "students" && isset($q) && $search == "study") {
			$this->bystudyInformation($q, $listType);
		} else if ($this->core->action == "search" && isset($q) && $search == "programme" || $this->core->action == "students" && isset($q) && $search == "programme") {
			$this->byprogramInformation($q, $listType, $year, $mode);
		} else if ($this->core->action == "search" && isset($q) && $search == "course" || $this->core->action == "students" && isset($q) && $search == "course") {
			$this->bycourseInformation($q, $listType, $mode);
		} else if ($this->core->action == "search" && isset($role)) {
			$this->showroleInformation($role);
		} else if ($this->core->action == "search" && isset($card)) {
			$this->showcardInformation($card);
		} else if ($this->core->action == "search" && isset($year) && isset($mode)) {
			$this->byintakeInformation($year, $mode, $group);
		} else if ($this->core->action == "search" && isset($item)) {
			$this->showInformation($item);
		}else{
			include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";

			$select = new optionBuilder($this->core);

			$study = $select->showStudies(null);
			$program = $select->showPrograms(null, null, null);
			$courses = $select->showCourses(null);
			$centres = $select->showCentres(null);
			$roles   = $select->showRoles(null);

			if ($this->core->role >= 100) {
				include $this->core->conf['conf']['formPath'] . "searchform.form.php";
			} else {
				$this->core->throwError($this->core->translate("You do not have the authority to do system wide searches"));
			}
		}
	}

	public function showcardInformation($item) {
		if(empty($item)){
			$this->searchInformation();
		} else {
			$sql = "SELECT * FROM `basic-information` as `bi`, `accesscards` WHERE `CardID` LIKE '" . $item . "' AND UserID = `bi`.ID";
			$this->showInfoProfile($sql, FALSE);
		}
	}


	public function showroleInformation($item) {
		if(empty($item)){
			$this->searchInformation();
		} else {
		
			$sql = "SELECT * FROM `basic-information` as `bi`, `access`, `roles` WHERE `roles`.`ID` = '" . $item . "' AND `access`.RoleID = `roles`.ID AND `access`.ID = `bi`.ID";
			$this->showInfoList($sql);
		}
	}


	public function showInformation($item) {
		if(empty($item)){
			$this->searchInformation();
		} else {
			$sql = "SELECT * FROM `basic-information` WHERE `ID` LIKE '" . $item . "'";
			$this->showInfoProfile($sql, FALSE);
		}
	}


	public function bycenterInformation($item, $listType = "list") {
		if(empty($item)){
			$this->searchInformation();
		} else {
			$year = $this->core->cleanGet['year'];
			$stype = $this->core->cleanGet['mode'];

			$sql = "SELECT * FROM `basic-information`, `student-data-other` WHERE `basic-information`.`ID` = `student-data-other`.StudentID AND `student-data-other`.ExamCentre = '$item' AND `student-data-other`.YearOfStudy = '$year' AND `basic-information`.StudyType = '$stype' GROUP BY `basic-information`.`ID`";

			if ($listType == "profiles") {
				$this->showInfoProfile($sql, FALSE);
			} elseif ($listType == "list") {
				$this->showInfoList($sql);
			}
		}
	}

	private function bynameInformation($firstName, $lastName, $listType) {
		if (empty($firstName)) {
			$firstName = "%";
		}
		if (empty($lastName)) {
			$lastName = "%";
		}

		$sql = "SELECT * FROM `basic-information` WHERE `Surname` LIKE '" . $lastName . "' AND `Firstname` LIKE '" . $firstName . "'";

		if ($listType == "profiles") {
			$this->showInfoProfile($sql, FALSE);
		} elseif ($listType == "list") {
			$this->showInfoList($sql);
		}
	}

	private function byintakeInformation($year, $mode, $group) {

		if($mode == "Masters"){
			$mode = "Distance";
			$year = "1". $year;
		}

		if (is_numeric($year)) {
			$sql = "SELECT * FROM `basic-information` WHERE `ID` LIKE '" . $year . "%' AND `StudyType` LIKE '" . $mode . "'";
		} else if($year == "all") {
			$sql = "SELECT * FROM `basic-information` WHERE `StudyType` LIKE '" . $mode . "'";
		}

		if(!empty($group)){
			$sql = "SELECT * FROM `basic-information`, `groups` 
				WHERE `basic-information`.`ID` LIKE '" . $year . "%' 
				AND `StudyType` LIKE '" . $mode . "' 
				AND `basic-information`.`ID` = `groups`.`StudentID` 
				AND `Group` = $group";
		}

		$this->showInfoList($sql);
	}

	private function bystudyInformation($study, $listType) {
		if ($study != "" && is_numeric($study)) {
			$sql = "SELECT * FROM `basic-information`, `student-study-link` WHERE `student-study-link`.StudentID = `basic-information`.ID AND StudyID = '" . $study . "'";
		}

		if ($listType == "profiles") {
			$this->showInfoProfile($sql, FALSE);
		} elseif ($listType == "list") {
			
			$this->showInfoList($sql);
		}
	}

	private function byprogramInformation($program, $listType, $year, $mode) {
		if ($program != "" && is_numeric($program)) {


			$sql = "SELECT * FROM `basic-information` as bi, `student-program-link` as sp, `programmes` as p, `programmes-link` as pl 
				WHERE sp.`StudentID` = `bi`.ID 
				AND sp.`ProgrammeID` = pl.`ID`
				AND p.`ID` = pl.`Major`
				AND p.`ID` = '$program'
				AND bi.`ID` LIKE '" . $year . "%' 
				AND bi.`StudyType` LIKE '" . $mode . "'
				OR  sp.`StudentID` = `bi`.ID 
				AND sp.`ProgrammeID` = pl.`ID`
				AND p.`ID` = pl.`Minor`
				AND p.`ID` = '$program'
				AND bi.`ID` LIKE '" . $year . "%' 
				AND bi.`StudyType` LIKE '" . $mode . "'";
		}

		if ($listType == "profiles") {
			$this->showInfoProfile($sql, FALSE);
		} elseif ($listType == "list") {
			$this->showInfoList($sql);
		}
	}

	private function bycourseInformation($course, $listType, $studytype){

		if ($course != "" && is_numeric($course)) {

			if(empty($studytype)){ $studytype = "%"; }

			$sql = "SELECT * FROM `basic-information`, `course-electives`  
				WHERE `basic-information`.ID = `course-electives`.StudentID
				AND `course-electives`.CourseID = '$course'
				AND `course-electives`.Approved IN (1)
				AND `StudyType` LIKE '$studytype'
				GROUP BY `StudentID`";

			if(empty($_GET['offset'])){
				$sqlx = "SELECT * FROM `courses`
					WHERE `courses`.ID = '$course'";
	
				$runx = $this->core->database->doSelectQuery($sqlx);
	
				while ($row = $runx->fetch_assoc()) {
					echo '<div class="heading"><h2>'.$row['Name'].' - '.$row['CourseDescription'].'</h2></div>';
				}
			}

			$this->showInfoList($sql);
		} else {
			$this->core->throwError($this->core->translate("You have not selected a course"));
		}
	
	
	}

	private function showInfoProfile($sql, $personal) {

		$run = $this->core->database->doSelectQuery($sql);

		while ($row = $run->fetch_row()) {
			$results = TRUE;
			$firstname = ucfirst($row[0]);
			$middlename = ucfirst($row[1]);
			$surname = ucfirst($row[2]);

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
			$mode = $row[19];
			$sstatus = $row[20];

			if(isset($row[23])){
				$role = $row[23];
			} else {
				$role = "10";
			}


			if( $sstatus=="Deregistered"){ 
				$style = "background-color: #000;"; 
				$activate = "Deregistered account";
				$links = "#";
			} else if( $sstatus=="Graduated"){ 
				$style = "background-color: #62ab3b;";
				$activate = "GRADUATE ACCOUNT";
				$links ="#";
			} else if( $sstatus=="Requesting"){ 
				$style = "background-color: #6297C3;";
				$activate = "ACTIVATE ACCOUNT";
				$links =  $this->core->conf['conf']['path'] . '/admission/activate/'.$uid;
			} else if( $sstatus=="Approved"){ 
				$style = "background-color: #6297C3;";
				$activate = "ACTIVE ACCOUNT";
				$links = "";
			}

			if($this->core->role == 1000){
				$links =  $this->core->conf['conf']['path'] . '/admission/activate/'.$uid;
			}


			if($sstatus == "Deceased"){
				$style = "background-color: #000;";
				$firstname = "&#10014; " . $firstname;
			}

			echo '<div class="student" style="">
			<div class="studentname" style="clear:both; '.$style.'"> Name: ' . $firstname . ' ' . $middlename . ' ' . $surname . ' </div>';

 

			echo '<div class="profilepic">';

			if(isset($this->core->cleanGet['payid'])){
				$payid = $this->core->cleanGet['payid'];
				$other = "?payid=".$this->core->cleanGet['payid'];
				$date = $this->core->cleanGet['date'];

				echo'<div style="background-color: #DFDFDF; font-weight: bold; font-size: 14px; border: 1px solid #0098FF; text-align: center; padding: 10px;">
				<a href="' . $this->core->conf['conf']['path'] . '/payments/modify/'.$payid.'?uid='.$uid.'&date='.$date.'">ASSIGN PAYMENT</a>
				</div>';
			}
	
	
			if($sstatus == "Employed"){	
				$num = "System number"; 	
				echo'<div style="background-color: #DFDFDF; font-weight: bold; font-size: 14px; border: 1px solid #ccc; text-align: center; padding: 3px;">Employee</div>';
			}else{
				
				echo'<a href="'.$links.'">
				<div style="'.$style.' font-weight: bold; font-size: 14px; border: 1px solid #ccc; text-align: center; padding: 3px; color: #FFF;">
				'.$activate.'</div></a>';
				$num = "Student number";
				echo'<div style="background-color: #DFDFDF; font-weight: bold; font-size: 14px; border: 1px solid #ccc; text-align: center; padding: 3px;">'.$mode.' student</div>';

				if($mode == "Distance"){
					$sql = "SELECT `Group` FROM `groups` WHERE `StudentID` LIKE '$uid'";
					$run = $this->core->database->doSelectQuery($sql);

					while ($rd = $run->fetch_assoc()) {
						$group = $rd['Group'];
					}

					echo'<div style="background-color: #b9b9b9; font-weight: bold; font-size: 14px; border: 1px solid #ccc; text-align: center; padding: 3px;">Group '.$group.'</div>';
				}
			}


			echo'<a href="'.$this->core->conf['conf']['path'].'/picture/make/'.$uid.'">';
			if (file_exists("datastore/identities/pictures/$uid.png_final.png")) {
				echo '<img width="100%" src="'.$this->core->conf['conf']['path'].'/datastore/identities/pictures/' . $uid . '.png_final.png">';
			} else 	if (file_exists("datastore/identities/pictures/$uid.png")) {
				echo '<img width="100%" src="'.$this->core->conf['conf']['path'].'/datastore/identities/pictures/' . $uid . '.png">';
			} else {
				echo '<img width="100%" src="'.$this->core->conf['conf']['path'].'/templates/default/images/noprofile.png">';
			}
			echo'</a>';


			if($sstatus=="Approved" || $sstatus=="Deregistered" || $sstatus == "Graduated" || $sstatus == "Employed"){

			if ($this->core->role == 108) {
				echo '<div style="margin-top: 1px; border-top: solid 1px #ccc; padding:10px;"><b><a href="' . $this->core->conf['conf']['path'] . '/information/edit/' . $uid . '">Edit user information</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px;"><b><a href="' . $this->core->conf['conf']['path'] . '/statement/results/' . $uid . '">Grades</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px;"><b><a href="' . $this->core->conf['conf']['path'] . '/grades/course/' . $uid . '">Course marks</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px;"><b><a href="' . $this->core->conf['conf']['path'] . '/payments/dosa/' . $uid . ''.$other.'">Show payments</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px;"><b><a href="' . $this->core->conf['conf']['path'] . '/billing/dosa/'.$uid.'">Show bills</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px;"><b><a href="' . $this->core->conf['conf']['path'] . '/sms/new/'. $mobilephone .'">Send student SMS</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px;"><b><a href="' . $this->core->conf['conf']['path'] . '/accommodation/swap/' . $uid . '">Correct boarding</a></b></div>';
		
			} elseif ($this->core->role == 102) {
				echo '<div style="margin-top: 1px; border-top: solid 1px #ccc; padding:10px;"><b><a href="' . $this->core->conf['conf']['path'] . '/information/edit/' . $uid . '">Edit user information</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px;"><b><a href="' . $this->core->conf['conf']['path'] . '/statement/results/' . $uid . '">Grades</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px;"><b><a href="' . $this->core->conf['conf']['path'] . '/payments/show/' . $uid . ''.$other.'">Show payments</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px;"><b><a href="' . $this->core->conf['conf']['path'] . '/billing/show/'.$uid.'">Show bills</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px;"><b><a href="' . $this->core->conf['conf']['path'] . '/cards/show/' . $uid . ''.$other.'">EduCard</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px;"><b><a href="' . $this->core->conf['conf']['path'] . '/sms/new/'. $mobilephone .'">Send student SMS</a></b></div>';
	
			} elseif ($this->core->role >= 100) {
				echo '<div style="margin-top: 1px; border-top: solid 1px #ccc; padding:10px;"><b><a href="' . $this->core->conf['conf']['path'] . '/information/edit/' . $uid . '">Edit user information</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px;"><b><a href="' . $this->core->conf['conf']['path'] . '/statement/results/' . $uid . '">Grades</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px;"><b><a href="' . $this->core->conf['conf']['path'] . '/grades/course/' . $uid . '">Course marks</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px;"><b><a href="' . $this->core->conf['conf']['path'] . '/payments/show/' . $uid . ''.$other.'">Show payments</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px;"><b><a href="' . $this->core->conf['conf']['path'] . '/billing/show/'.$uid.'">Show bills</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px;"><b><a href="' . $this->core->conf['conf']['path'] . '/cards/show/' . $uid . ''.$other.'">EduCard</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px;"><b><a href="' . $this->core->conf['conf']['path'] . '/sms/new/'. $mobilephone .'">Send student SMS</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px;"><b><a href="' . $this->core->conf['conf']['path'] . '/cards/print/' . $uid . '">Print card</a></b></div>';
	
			} elseif ($this->core->role <= 10 && $personal == TRUE) {
				echo '<div style="margin-top: 1px; border-top: solid 1px #ccc; padding:10px;"><b><a href="' . $this->core->conf['conf']['path'] . '/information/edit/">Edit user information</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px;"><b><a href="' . $this->core->conf['conf']['path'] . '/grades/personal/">Show grades</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px;"><b><a href="' . $this->core->conf['conf']['path'] . '/payments/personal/">Show payments</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px;"><b><a href="' . $this->core->conf['conf']['path'] . '/billing/personal">Show bills</a></b></div>';
			}
			if ($this->core->role >= 1000) {
				echo '<div style="border-top: solid 1px #ccc; padding:10px;"><b><a href="' . $this->core->conf['conf']['path'] . '/password/reset/'. $uid .'">Reset password</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px;"><b><a href="' . $this->core->conf['conf']['path'] . '/accommodation/assign?userid='. $uid .'">Assign room</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px;"><b><a href="' . $this->core->conf['conf']['path'] . '/register/course/delete?userid='. $uid .'">Delete course registration</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px;"><b><a href="' . $this->core->conf['conf']['path'] . '/programmes/change/'.$uid.'">Change Major/Minor</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px;"><b><a href="' . $this->core->conf['conf']['path'] . '/examination/results/?uid='.$uid.'">Print Exam Slip</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px;"><b><a href="' . $this->core->conf['conf']['path'] . '/accommodation/swap/' . $uid . '">Correct boarding</a></b></div>';
						echo '<div style="border-top: solid 1px #ccc; padding:10px;"><b><a href="' . $this->core->conf['conf']['path'] . '/accommodation/swap/' . $uid . '">Correct boarding</a></b></div>';
		
			}
			} else {
				echo'<a href="' . $this->core->conf['conf']['path'] . '/admission/activate/'.$uid.'"><div style="background-color: red; font-weight: bold; font-size: 14px; border: 1px solid #ccc; text-align: center; padding: 3px; color: #FFF;">ACCOUNT NOT ACTIVE</div></a>';
			}


			echo '</div>
			<div>
			<table width="400" height="63" border="0" cellpadding="0" cellspacing="0">
			  <tr>
			<td>'.$num.'</td>
			<td><b>' . $uid . '</b></td>
			  </tr>
			  <tr>
			<td width="200">Gender</td>
			<td><u>' . $sex . '</u></td>
	 		 </tr>
	
	 		 <tr>
			<td>NRC</td>
			<td>' . $nrc . '</td>
	 		 </tr>
	 		 <tr>
			<td>Date of birth</td>
			<td>' . $dob . '</td>
	 		 </tr>
			  <tr>
			<td>Nationality</td>
			<td>' . $nationality . '</td>
			  </tr>
			  <tr>
			<td>Marital status</td>
			<td>' . $maritalstatus . '</td>
	 		 </tr>
			  <tr>
			<td>Registration status</td>
			<td><b>' . $sstatus . '</b></td>
	 		 </tr>';

			$sql = "SELECT * FROM `roles` WHERE `ID` LIKE '$role'";
			$run = $this->core->database->doSelectQuery($sql);

			while ($row = $run->fetch_row()) {

				echo '<tr>
				<td>Access Level</td>
				<td>' . $row[1] . '</td>
				</tr>';

			}


	
				$sql = "SELECT * FROM `student-data-other` WHERE `StudentID` LIKE '$uid' ORDER BY `ID` DESC LIMIT 1";
				$run = $this->core->database->doSelectQuery($sql);

				while ($row = $run->fetch_row()) {
					$studygroup = $row[9];
					$studygrouptwo = $row[10];

				echo '<tr>
				<td>Year of Study</td>
				<td>Year <b>' . $row[2] . '</b></td>
				</tr>';
				echo '<tr>
				<td>Exam center</td>
				<td>' . $row[3] . '</td>
				</tr>';
				echo '<tr>
				<td>District Resource Center</td>
				<td>' . $row[7] . '</td>
				</tr>';

				if(!empty($studygroup)){
					echo '<tr>
					<td>Study Group</td>
					<td>' . $studygroup . ' / ' . $studygrouptwo . '</td>
					</tr>';
				}


			}


			echo '</table></div>';

		


			$sql = 'SELECT `study`.Name FROM `student-study-link`, `study` 
				WHERE `student-study-link`.StudentID = "'.$uid.'"
				AND `student-study-link`.StudyID = `study`.ID';

			$run = $this->core->database->doSelectQuery($sql);

			while ($row = $run->fetch_row()) {
				$studyname = $row[0];
			}


		
	
			echo '<div><br> <h2>Student course information</h2><br>
			<table width="500" height="" border="0" cellpadding="0" cellspacing="0">
			<tr>
			<td width="200">Study</td>
			<td width=""><b>' . $studyname . '</b></td>
			</tr>';

			$sql = "SELECT * FROM `student-program-link` as sp, `programmes` as p
				WHERE sp.`StudentID` = '$uid' 
				AND sp.`Major` = p.`ID` 
				ORDER BY sp.`ID` DESC LIMIT 1";

			$run = $this->core->database->doSelectQuery($sql);
	
			while ($row = $run->fetch_row()) {

				$name = $row[7];

				echo '<tr>
				<td>Major </td>
				<td width=""><b>' . $name . '</b></td>
				</tr>';

					$student = TRUE;
				
				
			}

			$sql = "SELECT * FROM `student-program-link` as sp, `programmes` as p
				WHERE sp.`StudentID` = '$uid' 
				AND sp.`Minor` = p.`ID`
				ORDER BY sp.`ID` DESC LIMIT 1";

			$run = $this->core->database->doSelectQuery($sql);
	
			while ($row = $run->fetch_row()) {

				$name = $row[7];

				echo '<tr>
				<td>Minor </td>
				<td width=""><b>' . $name . '</b></td>
				</tr>';

					$student = TRUE;
				
				
			}


			echo'<tr><td colspan="2"><br><b>COURSE PROGRESSION: </b><br><br>';

			$sqls = "SELECT *, `periods`.Year  FROM `course-electives`
			LEFT JOIN `periods` ON `course-electives`.`PeriodID` = `periods`.ID
			LEFT JOIN `courses` ON `course-electives`.`CourseID` = `courses`.ID 
			WHERE `course-electives`.StudentID  = '$uid' 
			AND `course-electives`.Approved IN (1)";

			$runo = $this->core->database->doSelectQuery($sqls);

			while ($fetchw = $runo->fetch_assoc()) {
				if($year != $fetchw['Year'] . $fetchw['Semester']){
					echo '<b>' . $fetchw['Year'].' - Sem. '.$fetchw['Semester'] . '</b><br>';
				}
				echo'<li>'.$fetchw['Name'].'  - <i>'.$fetchw['CourseDescription'].'</i> - <a href="' . $this->core->conf['conf']['path'] . '/register/course/delete/'.$fetchw['ID'].'?userid='.$uid.'">X</a></li>';
				$year = $fetchw['Year'] . $fetchw['Semester'];
			}

			if($runo->num_rows == 0){
				echo '<h2>NO COURSES SELECTED</h2>';
			}	

			echo'</p></td></tr>';


			echo'</table></div>';
	

			$housing = FALSE;

			$sql = "SELECT * 
				FROM `housing`, `rooms`, `hostel`, `basic-information`, `periods`
				WHERE `housing`.RoomID = `rooms`.ID 
				AND `rooms`.HostelID = `hostel`.ID 
				AND `basic-information`.ID = `housing`.StudentID 
				AND `basic-information`.ID = '$uid'
				AND `housing`.PeriodID = `periods`.ID";

			$run = $this->core->database->doSelectQuery($sql);

			while ($fetch = $run->fetch_assoc()) {

				$AccommodationName = $fetch['HostelName'];
				$RoomNumber = $fetch['RoomNumber'];
				$RoomType = $fetch['RoomType'];	
				$RoomID = $fetch['RoomID'];	
				$weeks = $fetch['Weeks'];

				 
				echo '<div>
				<div class="segment">Housing information</div>
				<table width="500" height="" border="0" cellpadding="0" cellspacing="0">
				<tr>
				<td width="200">Accommodation</td>
				<td width="">' . $AccommodationName . '</td>
				<tr>
				<td>Room</td>
				<td width=""><a href="' . $this->core->conf['conf']['path'] . '/accommodation/room/'. $RoomID .'">' . $RoomNumber . ' (' . $RoomType . ')</a></td>
				</tr>
				<tr>
				<td>Weeks</td>
				<td width=""><b> ' . $weeks . ' WEEKS</b></td>
				</tr>
				</table></div>';


				$housing = TRUE;
			}

			if($housing == FALSE){
		
				$sql = "SELECT * FROM `housingapplications`, `rooms`, `hostel`,`basic-information`
				WHERE `housingapplications`.RoomID = `rooms`.ID 
				AND `rooms`.HostelID = `hostel`.ID 
				AND `basic-information`.ID = `housingapplications`.StudentID 
				AND `basic-information`.ID = '$uid'";
				$run = $this->core->database->doSelectQuery($sql);

				while ($fetch = $run->fetch_assoc()) {

					$AccommodationName = $fetch['HostelName'];
					$RoomNumber = $fetch['RoomNumber'];
					$RoomType = $fetch['RoomType'];	
				
					echo '<div>
					<div class="segment">HOUSING APPLICATION</div>
					<table width="500" height="" border="0" cellpadding="0" cellspacing="0" style="color: #ccc">
					<tr>
					<td width="200">Hostel name</td>
					<td width="">' . $AccommodationName . '</td>
					</tr>
					<tr>
					<td>Room</td>
					<td width="">' . $RoomNumber . ' (' . $RoomType . ')</td>
					</tr>
					</table></div>';


				}
			}

			

			echo '<div><br>
			<h2>Student Contact Information</h2><br>
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
				</tr>';
			}
			echo'</table></div>';

			$sql = "SELECT * FROM `emergency-contact` WHERE `StudentID` = '" . $nrc . "' OR  `StudentID` = '" . $uid . "' ";
			$run = $this->core->database->doSelectQuery($sql);

	
			while ($fetch = $run->fetch_row()) {

				$fullname = $fetch[2];
				$relationship = $fetch[3];
				$phonenumber = $fetch[4];
				$street = $fetch[5];
				$town = $fetch[6];
				$postalcode = $fetch[7];

			echo '<div><br>
			<h2>Emergency information (Next of Kin)</h2><br>
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
			</table></div>';

			}

			$sql = "SELECT * FROM `education-background` WHERE `StudentID` = '" . $nrc . "' OR  `StudentID` = '" . $uid . "' ";
			$run = $this->core->database->doSelectQuery($sql);
			$n = 0;

			while ($row = $run->fetch_row()) {

				$name = $row[2];
				$type = $row[3];
				$institution = $row[4];
				$filename = $row[5];

				if ($n == 0) {
					echo '<br/><h2>Education history</h2><br>';
					$n++;
				} else {
					echo '<hr>';
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
					<td><a href="' . $this->core->conf['conf']['path'] . 'download/educationhistory/' . $filename . '"><b>View file</b></a></td>
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

	private function showInfoList($sql) {

		$sqld = $sql . " LIMIT ". $this->limit ." OFFSET ". $this->offset;

		$run = $this->core->database->doSelectQuery($sqld);

		if(!isset($this->core->cleanGet['offset'])){
			
			$_SESSION["recipients"] = $sql;
			$url = $_SERVER['QUERY_STRING'];
			$url = explode('&', $url);
			$url = $url[2]. '&'. $url[3].'&'. $url[4];

			echo '<div class="toolbar">'.
			'<a href="' . $this->core->conf['conf']['path'] . '/sms/newbulk">Send SMS to all results</a>'.
			'<a href="' . $this->core->conf['conf']['path'] . '/printer/search?'.$url.'">Print letter to all results</a>'.
			'<a href="' . $this->core->conf['conf']['path'] . '/export/search?'.$url.'">Export all results</a>'.
			'<a href="' . $this->core->conf['conf']['path'] . '/sign/search?'.$url.'">Print sign list</a>'.
			'</div>';

			echo'<table id="results" class="table table-bordered table-striped table-hover">
			<thead>
				<tr>
					<th bgcolor="#EEEEEE" width="30px"></th>
					<th bgcolor="#EEEEEE" width="40px">#</th>
					<th bgcolor="#EEEEEE" width="300px" data-sort"string"=""><b>Student Name</b></th>
					<th bgcolor="#EEEEEE"><b>Student Number</b></th>
					<th bgcolor="#EEEEEE"><b>Phone number</b></th>
					<th bgcolor="#EEEEEE"><b>Status</b></th>
					<th bgcolor="#EEEEEE"><b>Delivery</b></th>
				</tr>
			</thead>
			<tbody>';
		}

		$count = $this->offset+1;

		while ($row = $run->fetch_row()) {
			$results == TRUE;

			$id = $row[4];
			$NID = $row[5];
			$firstname = $row[0];
			$middlename = $row[1];
			$surname = $row[2];
			$celphone = $row[14];
			$status = $row[20];
			$mode = $row[19];

			echo'<tr>
				<td><img src="' . $this->core->conf['conf']['path'] . '/templates/edurole/images/user.png"></td><td>'.$count.'</td>
				<td><a href="' . $this->core->conf['conf']['path'] . '/information/show/' . $id . '"><b> '.$firstname.' '.$middlename.' '.$surname.'</b></a></td>
				<td> '.$id.'</td>
				<td> '.$celphone.'</td>
				<td> '.$status.'</td>
				<td> '.$mode.'</td>
				</tr>';

			$count++;
			$results = TRUE;
		}

		if($this->core->pager == FALSE){
			if ($results != TRUE) {
				$this->core->throwError('Your search did not return any results');
			}

			if($this->core->pager == FALSE){

				include $this->core->conf['conf']['libPath'] . "edurole/autoload.js";
			}
		}

		if(!isset($this->core->cleanGet['offset'])){
			echo'</tbody>
			</table>';
		}


	}

	public function editInformation($item) {
		if(empty($item) || $this->core->role <= 10){ $item = $this->core->userID;  }

		$sql = "SELECT * FROM  `basic-information` as bi 
		LEFT JOIN `access` as ac ON ac.`ID` = '" . $item . "' 
		LEFT JOIN `student-study-link` ON  `student-study-link`.StudentID = `bi`.ID
		WHERE bi.`ID` = '" . $item . "'
		LIMIT 1";

		$run = $this->core->database->doSelectQuery($sql);
 
		while ($row = $run->fetch_row()) {
			$id = $row[4];
			$NID = $row[5];
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
			$role = $row[23];
			$status = $row[20];
			$method = $row[19];

			$study = $row[35];

			include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";

			$select = new optionBuilder($this->core);
			$select = $select->showRoles($role);


			$selectstudy = new optionBuilder($this->core);
			$selectstudy = $selectstudy->showStudies(NULL);


			$major = new optionBuilder($this->core);
			$major = $major->showPrograms(NULL);
			$minor = $major;
		

		}

		include $this->core->conf['conf']['formPath'] . "edituser.form.php";

	}


	public function groupInformation($item) {
		if(empty($item) || $this->core->role <= 10){ $item = $this->core->userID;  }


		$sql = "SELECT `Group` FROM `groups` WHERE `StudentID` LIKE '$uid'";
		$run = $this->core->database->doSelectQuery($sql);

		while ($rd = $run->fetch_assoc()) {
			$group = $rd['Group'];


			include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";

			$select = new optionBuilder($this->core);
			$select = $select->showGroups($group);

		}

		include $this->core->conf['conf']['formPath'] . "editgroup.form.php";

	}
}

?>
