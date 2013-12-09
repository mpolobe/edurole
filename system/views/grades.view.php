<?php
class grades {

	public $core;
	public $view;

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
		include $this->core->conf['conf']['classPath'] . "grades.inc.php";

		if ($this->core->action == "view-grades" && $this->core->role >= 100) {
			$this->viewGrades();
		}else 	if ($this->core->action == "search" && $this->core->role >= 100) {
			$this->getGrades();
		}else if ($this->core->action == "view" && $this->core->role >= 100 && isset($this->core->cleanGet['course'])) {
			$this->view();
		}else if ($this->core->action == "view" && $this->core->role >= 100 && isset($this->core->cleanGet['student'])) {
			$this->viewStudentGrades($this->core->cleanGet['student']);
		} elseif ($this->core->action == "management" && $this->core->role >= 100) {
			$this->manager();
		} elseif ($this->core->action == "selectcourse" && $this->core->role >= 100) {
			$this->selectCourse();
		} elseif ($this->core->action == "entergrades" && $this->core->role >= 100) {
			$this->enterGrades(NULL, NULL);
		} elseif ($this->core->action == "submit" && $this->core->role >= 100) {
			$this->gradesSubmit();
		} elseif ($this->core->action == "transcript" && $this->core->role >= 105){
			$this->getTranscript();
		} elseif ($this->core->action == "statement" && $this->core->role >= 100){
			$this->getStatement();
		} else {
			if ($this->core->role >= 101) {
				$this->manager();
			}
			if ($this->core->role == 10) {
				$this->viewStudentGrades($this->core->username);
			}
		}

	}

	function getStatement() {

		$function = __FUNCTION__;
		$title = 'Get statement of results by student number';
		$description = 'Enter the student ID';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);

		if ($this->core->role >= 100) {
			include $this->core->conf['conf']['formPath'] . "searchstatement.form.php";
		} else {
			$this->core->throwError("You do not have the authority to get statements");
		}
	}

	function getTranscript() {

		$function = __FUNCTION__;
		$title = 'Get transcript by student number';
		$description = 'Enter the student ID';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);

		if ($this->core->role > 104) {
			include $this->core->conf['conf']['formPath'] . "searchtranscript.form.php";
		} else {
			$this->core->throwError("You do not have the authority to get transcripts");
		}
	}

	function getGrades() {

		$function = __FUNCTION__;
		$title = 'Search grades';
		$description = 'Search by programme or course';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);

		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";

		$select = new optionBuilder($this->core);

		$study = $select->showStudies(null);
		$program = $select->showPrograms(null, null, null);
		$courses = $select->showCourses(null);

		if ($this->core->role > 104) {
			include $this->core->conf['conf']['formPath'] . "searchgrades.form.php";
		} else {
			$this->core->throwError("You do not have the authority to search grades");
		}
	}


	function viewGrades() {
	
		$function = __FUNCTION__;
		$title = 'Gradebook';
		$description = 'Overview of submitted grades';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);
		
		include $this->core->conf['conf']['viewPath'] . "statement.view.php";
		$view = new statement();
		$view->buildView($this->core);
	}

	function gradeBook() {
		$function = __FUNCTION__;
		$title = 'Grading center';
		$description = 'Overview of personally submitted grades';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);

		$sql = "SELECT * FROM `gradebook`, `courses` WHERE `gradebook`.`CourseID` = `courses`.`ID` AND `gradebook`.`OwnerID` = '" . $this->core->userID . "' ORDER BY `gradebook`.`DateTime`";
		$run = $this->core->database->doSelectQuery($sql);

		echo '<p><b>Overview of all batches of grades submitted</b></p>';

		$init = TRUE;

		while ($fetch = $run->fetch_row()) {

			$validator = $fetch[6];
			$firstname = $fetch[11];
			$lastname = $fetch[13];
			$studentno = $fetch[8];
			$courseid = $fetch[7];
			$coursename = $fetch[9];
			$batchname = $fetch[8];
			$date = $fetch[4];
			$uid = $fetch[10];

			echo '<div style="border:solid 1px #ccc; padding-left: 10px; margin-bottom: 4px;"><table width="756">' .
				'<tr>' .
				'<td><b>Results batch:</b></td>' .
				'<td><b>Batchnumber</b></td>' .
				'<td><b>Date and Time</b></td>' .
				'</tr>' .
				'<tr>' .
				'<td><a href="' . $this->core->conf['conf']['path'] . '/courses/view/' . $courseid . '"><b>' . $coursename . '</b> </a></td>' .
				'<td><a href="' . $this->core->conf['conf']['path'] . '/information/view/' . $batchname . '">(' . $validator . ')</a></td>' .
				'<td>' . $date . '</td>' .
				'<td></td>' .
				'<td>' .
				'</td>' .
				'</tr> ' .
				'</table></div>';
		}
	}

	function view() {
		$function = __FUNCTION__;
		$title = 'Grade management';
		$description = 'Overview of all batches of grades submitted';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);

		$sql = " SELECT * FROM `nkrumah-grades`
		LEFT JOIN `basic-information` ON `nkrumah-grades`.user = `basic-information`.ID
		LEFT JOIN `courses` ON `courses`.Name = `nkrumah-grades`.CourseNo
		ORDER BY `nkrumah-grades`.ID";

		$academicyear = $this->core->cleanGet['year'];
		$course = $this->core->cleanGet['course'];
		$programme = $this->core->cleanGet['programme'];
		$semester = $this->core->cleanGet['semester'];

		$sql = "SELECT AcademicYear, Semester, ProgramNo, CourseNo, user, b1.FirstName, b1.Surname, Grade, CAMarks, ExamMarks, TotalMarks, b2.ID, b2.FirstName, b2.Surname  FROM `nkrumah-grades`
			LEFT JOIN `basic-information` as b1 ON `nkrumah-grades`.`user` = `b1`.ID
			LEFT JOIN `basic-information` as b2 ON `nkrumah-grades`.`StudentNo` = `b2`.ID
			WHERE AcademicYear = '$academicyear'
			AND Semester = '$semester'
			AND CourseNo = '$course'
			ORDER BY `nkrumah-grades`.`AcademicYear`, `nkrumah-grades`.`CourseNo`  ASC";

		$run = $this->core->database->doSelectQuery($sql);


		if($this->core->role == 107 || $this->core->role == 1000){
			echo '<div class="toolbar">
				<a href="' . $this->core->conf['conf']['path'] . '/grades/search">Search</a>
				<a href="' . $this->core->conf['conf']['path'] . '/grades/selectcourse">Add Grades</a>
				<a href="' . $this->core->conf['conf']['path'] . '/grades/transcript">Get Result Transcript</a>
				<a href="' . $this->core->conf['conf']['path'] . '/grades/statement">Get Result Statement</a>
				<a href="' . $this->core->conf['conf']['path'] . '/grades/print">Print Overview</a>
				</div>'; 
		}else{
			echo '<div class="toolbar">
				<a href="' . $this->core->conf['conf']['path'] . '/grades/selectcourse">Add Grades</a>
				</div>'; 
		}


		$init = TRUE;

			echo	'<div style="border:solid 1px #ccc; padding-left: 10px; margin-bottom: 4px;"><table width="756">' .
				'<tr>' .
				'<td width="150px"><b>Student names:</b></td>' .
				'<td width="100px"><b>Course:</b></td>' .
				'<td width="100px"><b>Year / Semester</b></td>' .
				'<td width="40px"><b>Course Mark</b></td>' .
				'<td width="40px"><b>Exam Mark</b></td>' .
				'<td width="40px"><b>Total Mark</b></td>' .
				'<td width="50px"><b>End Grade</b></td>' .
				'</tr>';

		while ($fetch = $run->fetch_row()) {

			$validator = $fetch[6];
			$firstname = $fetch[5];
			$lastname = $fetch[6];
			$studentno = $fetch[8];
			$courseid = $fetch[7];
			$coursename = $fetch[3];
			$programme = $fetch['2'];
			$semester = $fetch[1];
			$year = $fetch[0];
			$date = $fetch[4];
			$uid = $fetch[2];

			$grade = $fetch[7];
			$camark = $fetch[8];
			$exammark = $fetch[9];
			$totalmark = $fetch[10];

			$studentno = $fetch[11];
			$studentfirstname = $fetch[12];
			$studentlastname = $fetch[13];

				echo'<tr>' .
				'<td><a href="' . $this->core->conf['conf']['path'] . '/grades/view/?student='.$studentno.'"><b>'. $studentfirstname  .' '.$studentlastname .'</b> </a></td>' .
				'<td><a href="' . $this->core->conf['conf']['path'] . '/courses/view/' . $courseid . '"><b>'. $course .'</b> </a></td>' .
				'<td><a href="' . $this->core->conf['conf']['path'] . '/grades/year/' . $semester. '">'. $year .' / '. $semester .'</a></td>' .
				'<td>'. $camark .'</td>' .
				'<td>'. $exammark .'</td>' .
				'<td>'. $totalmark .'</td>' .
				'<td><b>'. $grade .'</b></td>' .
				'<td>'.
				'<a href="' . $this->core->conf['conf']['path'] . '/grades/edit/' . $fetch[0] . '"> <img src="'.$this->core->fullTemplatePath.'/images/edi.png"> edit</a>
				<a href="' . $this->core->conf['conf']['path'] . '/studies/delete/' . $fetch[0] . '" onclick="return confirm(\'Are you sure?\')"> <img src="'.$this->core->fullTemplatePath.'/images/del.png"> delete </a>
				</td>' .
				'</tr>';

		}

				echo '</table>' . 
				'</div>';
	}


	function viewStudentGrades() {
		$function = __FUNCTION__;
		$title = 'Student results management';
		$description = 'Overview of all grades';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);

		$sql = " SELECT * FROM `nkrumah-grades`
		LEFT JOIN `basic-information` ON `nkrumah-grades`.user = `basic-information`.ID
		LEFT JOIN `courses` ON `courses`.Name = `nkrumah-grades`.CourseNo
		ORDER BY `nkrumah-grades`.ID";

		$studentno = $this->core->cleanGet['student'];

		$sql = "SELECT AcademicYear, Semester, ProgramNo, CourseNo, user, b1.FirstName, b1.Surname, Grade, CAMarks, ExamMarks, TotalMarks, b2.ID, b2.FirstName, b2.Surname  FROM `nkrumah-grades`
			LEFT JOIN `basic-information` as b1 ON `nkrumah-grades`.`user` = `b1`.ID
			LEFT JOIN `basic-information` as b2 ON `nkrumah-grades`.`StudentNo` = `b2`.ID
			WHERE b2.ID = '$studentno'
			ORDER BY `nkrumah-grades`.`AcademicYear`, `nkrumah-grades`.`CourseNo`  ASC";

		$run = $this->core->database->doSelectQuery($sql);

		if($this->core->role == 107 || $this->core->role == 1000){
			echo '<div class="toolbar">
				<a href="' . $this->core->conf['conf']['path'] . '/grades/search">Search</a>
				<a href="' . $this->core->conf['conf']['path'] . '/grades/selectcourse">Add Grades</a>
				<a href="' . $this->core->conf['conf']['path'] . '/grades/transcript">Get Result Transcript</a>
				<a href="' . $this->core->conf['conf']['path'] . '/grades/statement">Get Result Statement</a>
				<a href="' . $this->core->conf['conf']['path'] . '/grades/print">Print Overview</a>
				</div>'; 
		}else{
			echo '<div class="toolbar">
				<a href="' . $this->core->conf['conf']['path'] . '/grades/selectcourse">Add Grades</a>
				</div>'; 
		}


		$init = TRUE;


			echo	'<div style="border:solid 1px #ccc; padding-left: 10px; margin-bottom: 4px;"><table width="756">' .
				'<tr>' .
				'<td width="150px"><b>Student name:</b></td>' .
				'<td width="100px"><b>Course:</b></td>' .
				'<td width="100px"><b>Year / Semester</b></td>' .
				'<td width="40px"><b>Course Mark</b></td>' .
				'<td width="40px"><b>Exam Mark</b></td>' .
				'<td width="40px"><b>Total Mark</b></td>' .
				'<td width="50px"><b>End Grade</b></td>' .
				'</tr>';

		while ($fetch = $run->fetch_row()) {

			$validator = $fetch[6];
			$firstname = $fetch[5];
			$lastname = $fetch[6];
			$studentno = $fetch[8];
			$courseid = $fetch[7];
			$coursename = $fetch[3];
			$programme = $fetch['2'];
			$semester = $fetch[1];
			$year = $fetch[0];
			$date = $fetch[4];
			$uid = $fetch[2];

			$grade = $fetch[7];
			$camark = $fetch[8];
			$exammark = $fetch[9];
			$totalmark = $fetch[10];

			$studentno = $fetch[11];
			$studentfirstname = $fetch[12];
			$studentlastname = $fetch[13];

				echo '<tr>' .
				'<td><a href="' . $this->core->conf['conf']['path'] . '/information/view/'.$studentno.'"><b>'. $studentfirstname  .' '.$studentlastname .'</b> </a></td>' .
				'<td><a href="' . $this->core->conf['conf']['path'] . '/courses/view/' . $courseid . '"><b>'. $coursename .'</b> </a></td>' .
				'<td><a href="' . $this->core->conf['conf']['path'] . '/grades/year/' . $semester. '">'. $year .' / '. $semester .'</a></td>' .
				'<td>'. $camark .'</td>' .
				'<td>'. $exammark .'</td>' .
				'<td>'. $totalmark .'</td>' .
				'<td><b>'. $grade .'</b></td>' .
				'<td>'.
				'<a href="' . $this->core->conf['conf']['path'] . '/grades/edit/' . $fetch[0] . '"> <img src="'.$this->core->fullTemplatePath.'/images/edi.png"> edit</a>
				<a href="' . $this->core->conf['conf']['path'] . '/studies/delete/' . $fetch[0] . '" onclick="return confirm(\'Are you sure?\')"> <img src="'.$this->core->fullTemplatePath.'/images/del.png"> delete </a>
				</td>' .
				'</tr>';


		}

				echo'</table>' . 
				'</div>';
	}

	function manager() {
		$function = __FUNCTION__;
		$title = 'Grade management';
		$description = 'Overview of all batches of grades submitted';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);


		$sql = "SELECT AcademicYear, Semester, ProgramNo, CourseNo, user, FirstName, Surname, count(StudentNo), userdate  FROM `nkrumah-grades`
			LEFT JOIN `basic-information` ON `nkrumah-grades`.user = `basic-information`.ID
			GROUP BY `nkrumah-grades`.`AcademicYear`, `nkrumah-grades`.`CourseNo`  ASC";

		$run = $this->core->database->doSelectQuery($sql);


		if($this->core->role == 107 || $this->core->role == 1000){
			echo '<div class="toolbar">
				<a href="' . $this->core->conf['conf']['path'] . '/grades/search">Search</a>
				<a href="' . $this->core->conf['conf']['path'] . '/grades/selectcourse">Add Grades</a>
				<a href="' . $this->core->conf['conf']['path'] . '/grades/transcript">Get Result Transcript</a>
				<a href="' . $this->core->conf['conf']['path'] . '/grades/statement">Get Result Statement</a>
				<a href="' . $this->core->conf['conf']['path'] . '/grades/print">Print Overview</a>
				</div>'; 
		}else{
			echo '<div class="toolbar">
				<a href="' . $this->core->conf['conf']['path'] . '/grades/selectcourse">Add Grades</a>
				</div>'; 
		}

		$init = TRUE;

		echo	'<div style="border:solid 1px #ccc; padding-left: 10px; margin-bottom: 4px;"><table width="756">' .
			'<tr>' .
			'<td width="150px"><b>Course batch:</b></td>' .
			'<td width="150px"><b>Grades:</b></td>' .
			'<td width="150px"><b>Year / Semester</b></td>' .
			'<td width="150px"><b>Submitted by</b></td>' .
			'<td width="150px"><b>Date and Time</b></td>' .
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

			echo '<tr>' .
			'<td><a href="' . $this->core->conf['conf']['path'] . '/grades/view/?course=' . urlencode($coursename) . '&programme=' . urlencode($programme) . '&year=' . urlencode($year) . '&semester=' . urlencode($semester) . '"><b>' . $coursename . ' </b> </a></td>' .
			'<td><b>'.$count.'</b> Grades</td>' .
			'<td><a href="' . $this->core->conf['conf']['path'] . '/grades/year/' . $semester. '">' . $year . ' / ' . $semester . '</a></td>' .
			'<td><a href="' . $this->core->conf['conf']['path'] . '/information/view/' . $uid . '">' . $firstname . ' ' . $lastname . '</a></td>' .
			'<td>' . $date . '</td>';

			if($this->core->role == 107 || $this->core->role == 1000){
				echo'<td><a href="' . $this->core->conf['conf']['path'] . '/grades/edit/' . $fetch[0] . '"> <img src="'.$this->core->fullTemplatePath.'/images/edi.png"> edit</a>
				<a href="' . $this->core->conf['conf']['path'] . '/studies/delete/' . $fetch[0] . '" onclick="return confirm(\'Are you sure?\')"> <img src="'.$this->core->fullTemplatePath.'/images/del.png"> delete </a></td>';
			}else{
				echo'<td>no privileges</td>';
			}


		}

		echo '</tr>' .
		'</table>' . 
		'</div>';
	}

	function enterGrades($programselected, $courseselected) {
		if (isset($programselected) && isset($courseselected)) {
			$sql = "SELECT * FROM `basic-information` as bi, `student-program-link` as cc WHERE `Major` = '" . $programselected . "' AND cc.StudentID = bi.ID OR `Minor` = '" . $programselected . "' AND cc.`StudentID` = bi.`GovernmentID`  ORDER BY Surname";
		}

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
			'<td><b><a href="' . $this->core->conf['conf']['path'] . '/information/view/' . $fetch[4] . '">' . $fetch[0] . ' ' . $fetch[2] . '</a></b></td>' .
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


	function selectCourse() {
		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";

		$function = __FUNCTION__;
		$title = 'Submit grades';
		$description = 'Select the programme you wish to list the students from and course you are entering the grades for';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);

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


	function gradesSubmit() {
		$function = __FUNCTION__;
		$title = 'Grades submitted';
		$description = 'Overview of personally submitted grades';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);

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
