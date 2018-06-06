<?php
class export {

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


	public function searchExport($item) {
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

		if (isset($lastName) || isset($firstName)) {
			$this->bynameExport($firstName, $lastName, $listType);
		}elseif (isset($center)){
			$this->bycenterExport($center);
		} else if ($this->core->action == "search" && isset($q) && $search == "study" || $this->core->action == "students" && isset($q) && $search == "study") {
			$this->bystudyExport($q, $listType);
		} else if ($this->core->action == "search" && isset($q) && $search == "programme" || $this->core->action == "students" && isset($q) && $search == "programme") {
			$this->byprogramExport($q, $listType, $year, $mode);
		} else if ($this->core->action == "search" && isset($q) && $search == "course" || $this->core->action == "students" && isset($q) && $search == "course") {
			$this->bycourseExport($q, $listType, $mode);
		} else if ($this->core->action == "search" && isset($item)) {
			$this->showExport($item);
		} else if ($this->core->action == "search" && isset($card)) {
			$this->showcardExport($card);
		} else if ($this->core->action == "search" && isset($year) && isset($mode)) {
			$this->byintakeExport($year, $mode, $group);
		}else{
			include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";

			$select = new optionBuilder($this->core);

			$study = $select->showStudies(null);
			$program = $select->showPrograms(null, null, null);
			$courses = $select->showCourses(null);

			if ($this->core->role >= 100) {
				include $this->core->conf['conf']['formPath'] . "searchform.form.php";
			} else {
				$this->core->throwError($this->core->translate("You do not have the authority to do system wide searches"));
			}
		}
	}

	private function bycenterExport($item, $listType = "list") {
	
		if(empty($item)){
			$this->searchInformation();
		} else {
			$year = $this->core->cleanGet['year'];
			$stype = $this->core->cleanGet['mode'];

			$sql = "SELECT * FROM `basic-information`, `student-data-other` 
			WHERE `basic-information`.`ID` = `student-data-other`.StudentID 
			AND `student-data-other`.ExamCentre = '$item' 
			AND `student-data-other`.YearOfStudy LIKE '$year' 
			AND `basic-information`.StudyType = '$stype' 
			GROUP BY `basic-information`.`ID`";


			$this->showInfoList($sql);
		}
	}

	private function byintakeExport($year, $mode) {
		if (is_numeric($year)) {
			$sql = "SELECT * FROM `basic-information` WHERE `ID` LIKE '" . $year . "%' AND `StudyType` LIKE '" . $mode . "' ORDER BY `ID` ASC";
		} else if($year == "all") {
			$sql = "SELECT * FROM `basic-information` WHERE `StudyType` LIKE '" . $mode . "'";
		}

		$this->showInfoList($sql);
	}

	
	public function showExport($item) {
		if(empty($item)){
			$this->searchExport();
		} else {
			$sql = "SELECT * FROM `basic-information` WHERE `ID` LIKE '" . $item . "'";
			$this->showInfoProfile($sql, FALSE);
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

	private function bycourseExport($course, $listType, $studytype){

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
			$firstname = $row[0];
			$middlename = $row[1];
			$surname = $row[2];
			$uid = $row[4];
			$streetname = $row[9];
			$postalcode = $row[10];
			$town = $row[11];
			$country = $row[12];


			echo '<div style=""><div style="-moz-transform: rotate(-90deg); -webkit-transform: rotate(-90deg);">
				<table width="400" height="63" border="0" cellpadding="0" cellspacing="0">
				<tr><td><b>' . $firstname . ' ' . $middlename . ' ' . $surname . '</b></td></tr>
				<tr><td width=""><b>' . $streetname . '</b></td></tr>';

				if($postalcode != "" && $postalcode != " "){ echo'<tr><td><b>PO. BOX ' . $postalcode . '</b></td></tr>';	}

				echo'<tr><td><b>' . $town . '</b></td></tr>
				<tr><td><b>' . $country . '</b></td></tr>
			</table>
			</div></div>';
		
		}

		if ($results != TRUE) {
			$this->core->throwError('Your search did not return any results');
		}
	}

	private function showInfoList($sql) {


		$run = $this->core->database->doSelectQuery($sql);

		$count = $this->offset+1;

		echo'<table class="table table-bordered table-striped table-hover">
		<tr>
				<td>#</td>
				<td width="">Student Number</td>
				<td>Student Name</td>
				<td>NRC</td>
				<td>Phone number</td>
				<td>Mode of Delivery</td>
				<td>Status</td>
				<td>Registered</td>
			      </tr>';

		while ($row = $run->fetch_row()) {
			$results = TRUE;
			$firstname = $row[0];
			$middlename = $row[1];
			$surname = $row[2];
			$uid = $row[4];
			$nrc = $row[5];
			$streetname = $row[9];
			$postalcode = $row[10];
			$town = $row[11];
			$country = $row[12];
			$phone = $row[14];

			$mode = $row[19];
			$status = $row[20];

			if($status == "New"){
				$status = "NEVER REPORTED";
			}

			$registered = 'NO';
			$sqx = "SELECT COUNT(DISTINCT CourseID)  FROM `course-electives` WHERE `StudentID` = '$uid' AND `Approved` = '1'";
			$runx = $this->core->database->doSelectQuery($sqx);
			while ($fetch = $runx->fetch_row()) {
				$countd =$fetch['0'];
				if($countd>0){
					$registered = 'YES ('.$countd.' courses)';
				}else{
					$registered = 'NO';
				}
			}
			
			echo '<tr>
				<td>'.$count.'</td>
				<td width="">' . $uid . '</td>
				<td><b>' . $firstname . ' ' . $middlename . ' ' . $surname . '</b></td>
				<td>' . $nrc . '</td>
				<td>' . $phone . '</td>
				<td>' . $mode . '</td>
				<td>' . $status . '</td>
				<td>' . $registered . '</td>
			      </tr>';
			

			$count++;
			$results = TRUE;
		}

		echo'</table>';

		if($this->core->pager == FALSE){
			if ($results != TRUE) {
				$this->core->throwError('Your search did not return any results');
			}

		}


	echo'<script type="text/javascript">
			window.print();
		</script>';


	}

	public function editInformation($item) {
		if(empty($item) || $this->core->role <= 10){ $item = $this->core->userID;  }

		$sql = "SELECT * FROM  `basic-information` as bi 
		LEFT JOIN `access` as ac ON ac.`ID` = '" . $item . "' 
		WHERE bi.`ID` = '" . $item . "'";

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
			$status = $row[20];
			$role = $row[23];

			include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";

			$select = new optionBuilder($this->core);
			$select = $select->showRoles($role);

			$selectstudy = new optionBuilder($this->core);
			$selectstudy = $selectstudy->showStudies(NULL);
		}

		include $this->core->conf['conf']['formPath'] . "edituser.form.php";

	}
}

?>
