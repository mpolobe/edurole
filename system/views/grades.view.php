<?php
class grades {

	public $core;
	public $view;

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
		include $this->core->conf['conf']['classPath'] . "grades.inc.php";
	}

	function statementGrades() {
		include $this->core->conf['conf']['formPath'] . "searchstatement.form.php";
	}

	function transcriptGrades() {
		include $this->core->conf['conf']['formPath'] . "searchtranscript.form.php";
	}

	function searchGrades() {
		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";

		$select = new optionBuilder($this->core);

		$study = $select->showStudies(null);
		$program = $select->showPrograms(null, null, null);
		$courses = $select->showCourses(null);

		include $this->core->conf['conf']['formPath'] . "searchgrades.form.php";
	}

	public function showGrades() {
		$academicyear = $this->core->cleanGet['year'];
		$course = $this->core->cleanGet['course'];
		$programme = $this->core->cleanGet['programme'];
		$semester = $this->core->cleanGet['semester'];

		$sql = "SELECT AcademicYear, Semester, ProgramNo, CourseNo, user, b1.FirstName, b1.Surname, Grade, CAMarks, ExamMarks, TotalMarks, b2.ID, b2.FirstName, b2.Surname, `nkrumah-grades`.ID  FROM `nkrumah-grades`
			LEFT JOIN `basic-information` as b1 ON `nkrumah-grades`.`user` = `b1`.ID
			LEFT JOIN `basic-information` as b2 ON `nkrumah-grades`.`StudentNo` = `b2`.ID
			WHERE AcademicYear = '$academicyear'
			AND Semester = '$semester'
			AND CourseNo = '$course'
			ORDER BY `nkrumah-grades`.`AcademicYear`, `nkrumah-grades`.`CourseNo`  ASC";

		$this->manageGrades($sql);
	}


	public function studentGrades($item) {
		if(empty($item)){
			$item = $this->core->userID;
		}

		$sql = "SELECT AcademicYear, Semester, ProgramNo, CourseNo, user, b1.FirstName, b1.Surname, Grade, CAMarks, ExamMarks, TotalMarks, b2.ID, b2.FirstName, b2.Surname  FROM `nkrumah-grades`
			LEFT JOIN `basic-information` as b1 ON `nkrumah-grades`.`user` = `b1`.ID
			LEFT JOIN `basic-information` as b2 ON `nkrumah-grades`.`StudentNo` = `b2`.ID
			WHERE b2.ID = '$item'
			ORDER BY `nkrumah-grades`.`AcademicYear`, `nkrumah-grades`.`CourseNo`  ASC";

		$this->manageGrades($sql);

	}

	public function manageGrades($sql) {
		$user = $this->core->userID;

		if($this->core->role == 1000 && empty($sql)){
			$sql = "SELECT AcademicYear, Semester, ProgramNo, CourseNo, user, FirstName, Surname, count(StudentNo), userdate  FROM `nkrumah-grades`
			LEFT JOIN `basic-information` ON `nkrumah-grades`.user = `basic-information`.ID
			GROUP BY `nkrumah-grades`.`AcademicYear`, `nkrumah-grades`.`CourseNo`  ASC";
			$noedit=false;
		}else if(empty($sql)){
			$sql = "SELECT AcademicYear, Semester, ProgramNo, CourseNo, user, FirstName, Surname, count(StudentNo), userdate  FROM `nkrumah-grades`
			LEFT JOIN `basic-information` ON `nkrumah-grades`.user = `basic-information`.ID
			WHERE `nkrumah-grades`.user = '$user'  
			GROUP BY `nkrumah-grades`.`AcademicYear`, `nkrumah-grades`.`CourseNo`  ASC";
			$noedit=true;
		} else {
			$noedit=true;
		}

		$run = $this->core->database->doSelectQuery($sql);


		if($this->core->role == 107 || $this->core->role == 1000){
			echo '<div class="toolbar">
				<a href="' . $this->core->conf['conf']['path'] . '/grades/search">Search</a>
				<a href="' . $this->core->conf['conf']['path'] . '/grades/selectcourse">Add Grades</a>
				<a href="' . $this->core->conf['conf']['path'] . '/grades/transcript">Get Result Transcript</a>
				<a href="' . $this->core->conf['conf']['path'] . '/grades/statement">Get Result Statement</a>
				<a href="' . $this->core->conf['conf']['path'] . '/grades/print">Print Overview</a>
				</div>'; 
		}else if($this->core->role > 100){
			echo '<div class="toolbar">
				<a href="' . $this->core->conf['conf']['path'] . '/grades/selectcourse">Add Grades</a>
				</div>'; 
		}

		$init = TRUE;

		echo	'<div style="border:solid 0px #ccc; padding-left: 10px; margin-bottom: 4px;"><table width="756">' .
			'<tr>' .
			'<td width="150px"><b>Course batch:</b></td>' .
			'<td width="150px" style="text-align: right;"><b>Grades:</b></td>' .
			'<td width="150px"><b>Year</b></td>' .
			'<td width="150px"><b>Semester</b></td>' .
			'<td width="150px"><b>Submitted by</b></td>' .
			'<td width="100px"><b>Management</b></td>' .
			'</tr>' ;

		while ($fetch = $run->fetch_row()) {

			$validator = $fetch[6];
			$firstname = $fetch[5];
			$lastname = $fetch[6];
			$studentno = $fetch[8];
			$count = $fetch[7];
			$coursename = $fetch[3];
			$programme = $fetch['2'];
			$semester = $fetch[1];
			$year = $fetch[0];
			$date = $fetch[8];
			$uid = $fetch[2];

			if($count>1){
				$type = "grades";
			} else {
				$type = "grade";
			}

			echo '<tr>' .
			'<td><a href="' . $this->core->conf['conf']['path'] . '/grades/show/?course=' . urlencode($coursename) . '&programme=' . urlencode($programme) . '&year=' . urlencode($year) . '&semester=' . urlencode($semester) . '"><b>' . $coursename . ' </b> </a></td>' .
			'<td style="text-align: right;"><b>'.$count.'</b> '. $type .'</td>' .
			'<td><a href="' . $this->core->conf['conf']['path'] . '/grades/year/' . $semester. '">' . $year . '</a></td>' .
			'<td><a href="' . $this->core->conf['conf']['path'] . '/grades/year/' . $semester. '">' . $semester . '</a></td>' .
			'<td><a href="' . $this->core->conf['conf']['path'] . '/information/show/' . $uid . '">' . $firstname . ' ' . $lastname . '</a></td>';
			if($this->core->role == 107 || $this->core->role == 1000){
				echo'<td><a href="' . $this->core->conf['conf']['path'] . '/grades/edit/' . urlencode($fetch[2]) . '"> <img src="'.$this->core->fullTemplatePath.'/images/edi.png"> edit</a>
				<a href="' . $this->core->conf['conf']['path'] . '/grades/delete/' . urlencode($fetch[2]) . '" onclick="return confirm(\'Are you sure?\')"> <img src="'.$this->core->fullTemplatePath.'/images/del.png"> delete </a></td>';
			}else{
				echo'<td>no privileges</td>';
			}


		}

		echo '</tr>' .
		'</table>' . 
		'</div>';
	}

	private function enterGrades($programselected, $courseselected) {
		if (isset($programselected) && isset($courseselected)) {
			$sql = "SELECT * FROM `basic-information` as bi, `nkrumah-student-program-link` as cc WHERE `ProgrammeID` = '" . $programselected . "' AND cc.StudentID = bi.ID  ORDER BY Surname";
		}
		echo $sql;

		$run = $this->core->database->doSelectQuery($sql);
		$validator = mt_rand(100000, 9999999999999999);

		echo '<p><b>Enter grades </b></p><p>
		<form id="login" name="login" method="post" action="/grades/submit">
		<input type="hidden" name="id" value="grades-submit">
		<input type="hidden" name="validator" value="' . $validator . '">
		<input type="hidden" name="course" value="' . $courseselected . '">
		<table width="768" height="" border="0" cellpadding="0" cellspacing="0">
		<tr class="tableheader">
		<td width="20px"></td>
		<td><b>Student name</b></td>
		<td width="120px"><b>Student number</b></td>
		<td width="80px"><b>Course mark</b></td>
		<td width="80px"><b>Exam mark</b></td>
		<td width="80px"><b>Total mark</b></td>
		</tr>';

		while ($fetch = $run->fetch_row()) {
			echo '<tr>'.
			'<td><img src="'.$this->core->fullTemplatePath.'/images/bullet_user.png"></td>'.
			'<td><b><a href="' . $this->core->conf['conf']['path'] . '/information/show/' . $fetch[4] . '">' . $fetch[0] . ' ' . $fetch[2] . '</a></b></td>' .
			'<td>' . $fetch[4] . '</td>' .
			'<td><input type="textbox" name="c' . $fetch[4] . '" size="5" class="submit"></td>' .
			'<td><input type="textbox" name="g' . $fetch[4] . '" size="5" class="submit"></td>' .
			'<td><input type="textbox" name="t' . $fetch[4] . '" size="5" class="submit"></td>' .
			'</tr>';
		}

		echo '</table>
		<br><hr><br><input type="submit" value="Submit grades to board of studies" />
		</form></p>';
	}


	public function selectcourseGrades() {
		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";

		$programselected = $this->core->cleanPost['program'];
		$courseselected = $this->core->cleanPost['course'];
		$select = new optionBuilder($this->core);
		$program = $select->showPrograms(null, null, $programselected);
		$courses = $select->showCourses($programselected, null);

		if (!isset($courseselected)) {

			echo '<p><form id="login" name="login" method="POST" action="'.$this->core->conf['conf']['path'].'/grades/selectcourse">

			<div class="label">Show all students from: </div>
			<select name="program" id="program" class="submit" width="250" style="width: 250px">
				' . $program . '
			</select>';

			if (isset($programselected)) {
				echo '<br />
				<div class="label">And submit grades for: </div>
				<select name="course" id="course" class="submit" width="250" style="width: 250px">
					' . $courses . '
				</select>';
			}

			echo '<br />
			<div class="label"> </div>
			<input type="button" value="Reset" class="submit" name="reset" onclick="history.back(-1)" /> <input type="submit" value="Next step" class="submit" />
			</form></p>';
		}

		if (isset($programselected) && isset($courseselected)) {
			$this->enterGrades($programselected, $courseselected);
		}
	}


	public function submitGrades() {
		$salt = sha1(md5(date('YmdH') . $this->core->username . $this->core->userid . $this->core->role . $this->core->cleanPost['course']));
		$sql = "START TRANSACTION;";
		doInsertQuery($sql);

		$output = '<table width="768" height="" border="0" cellpadding="3" cellspacing="0">
		<tr class="tableheader">
		<td width="400"><b>Student number</b></td>
		<td><b>Grade submitted</b></td>
		</tr>';

		$courseid = $this->core->cleanPost['course'];
		$validatorid = $this->core->cleanPost['validator'];

		$sql = 'INSERT INTO `gradebook` (`ID`, `GlobalHash`, `Owner`, `Status`, `DateTime`, `Course`, `ValidatorID`) VALUES (NULL, "' . $salt . '", "' . $this->core->userid . '",  "1", NULL, "' . $courseid . '", "' . $validatorid . '");';

		if (doInsertQuery($sql)) {

			foreach ($_POST as $student => $grade) {

				if ($grade != "" && $grade != "grades-submit" && $student != "course" && $student != "validator") {
					$coursemark = ltrim($student, 'c');
					$exammark = ltrim($student, 'e');
					$totalmark = ltrim($student, 't');

					$date = date('YmdH');
					$hash = sha1("$totalmark$student$coursemark$exammark$date$salt");

					$output = $output . "<tr><td><b>" . $student . "</b></td><td><b>" . $grade . "</td></tr>";

					$sql = "INSERT INTO `edurole`.`nkrumah-grades` (`ID`, `user`, `userdate`, `usertime`, `StudentNo`, `AcademicYear`, `Semester`, `ProgramNo`, `CourseNo`, `CAMarks`, `ExamMarks`, `TotalMarks`, `Grade`, `Points`) 
					VALUES (NULL, '$this->core->userid', CURRENT_DATE(), CURRENT_TIME(), '$student', '2011', 'YEAR I', 'PROG', '$courseid', '10', '10', '10', 'B+', '$hash/$batch');";

					$this->core->database->doInsertQuery($sql);

				}
			}

			$sql = "COMMIT;";

			$this->core->logEvent("Grades submitted $courseid - $hash", "4");

			if ($this->core->database->doInsertQuery($sql)) {
				echo $output . "</table><p><b>All grades have been submitted for approval.</b></p>";
			} else {
				throwerror("ERROR UNKNOWN");
			}

		} else {
			throwerror("Please continue home");
		}
	}

}

?>
