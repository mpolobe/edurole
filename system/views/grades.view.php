<?php
class grades {

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

		$this->core->action = $this->core->cleanGet['action'];
		$this->item = $this->core->cleanGet['item'];

		include $this->core->classpath . "grades.inc.php";

		if ($this->core->action == "view-grades") {
			$this->gradebook();
		} elseif ($this->core->action == "management") {
			$this->manager();
		} elseif ($this->core->action == "selectcourse") {
			$this->selectCourse();
		} elseif ($this->core->action == "entergrades") {
			$this->enterGrades(NULL,NULL);
		} elseif ($this->core->action == "submit") {
			$this->gradesSubmit();
		} else {
			if ($this->core->role >= 104) {
				$this->manager();
			} else {
				$this->viewOwn();
			}
		}

	}

	function gradebook() {
		$function = __FUNCTION__;
		$title = 'Gradebook';
		$description = 'Please enter a name for the new file to create it in the current working directory';

		echo component::generateBreadcrumb(get_class(), $function);
		echo component::generateTitle($title, $description);

		$sql = "SELECT * FROM `grades`, `courses` WHERE `grades`.StudentID = \"" . $_SESSION['username'] . "\" AND `courses`.ID = CourseID ORDER BY Name";

		$run = $this->core->database->doSelectQuery($sql);

		echo '<p><b>Overview of all grades submitted</b></p><p>';

		$init = TRUE;

		$prevbatch = NULL;
		while ($fetch = $run->fetch_row()) {

			$grade = $fetch[3];
			$studentno = $fetch[8];
			$courseid = $fetch[9];
			$coursename = $fetch[11];
			$batchname = $fetch[8];
			$date = $fetch[4];

			if ($prevbatch != $batchname) {
				echo '<table width="700">' .
					'<tr>' .
					'<td><b>Course</b></td>' .
					'<td><b>Date</b></td>' .
					'<td><b>Grade</b></td>' .
					'</tr>';
			}

			echo '<tr>' .
				'<td><a href="?id=courses&action=view&item=' . $courseid . '"><b>' . $coursename . '</b></a></td>' .
				'<td>' . $date . '</td>' .
				'<td><b>' . $grade . '</b></td>' .
				'</tr>';

			if ($prevbatch != $batchname && $init != TRUE) {
				echo '</table><br />';
			}

			$init = FALSE;
			$prevbatch = $batchname;
		}

		echo '</table></p>';

	}

	function viewOwn() {
		$function = __FUNCTION__;
		$title = 'Grading center';
		$description = 'Overview of personally submitted grades';

		echo component::generateBreadcrumb(get_class(), $function);
		echo component::generateTitle($title, $description);

		$sql = "SELECT * FROM `gradebook`, `courses` WHERE `courses`.ID = `gradebook`.Course AND `gradebook`.Owner = '".$this->core->userid."' ORDER BY `gradebook`.DateTime";

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
				'<td><a href="?id=courses&action=view&item=' . $courseid . '"><b>' . $coursename . '</b> </a></td>' .
				'<td><a href="?id=view-information&uid=' . $batchname . '">(' . $validator . ')</a></td>' .
				'<td>' . $date . '</td>' .
				'<td></td>' .
				'<td>
				</td>' .
				'</tr>';
			echo '</table></div>';
		}
	}

	function manager() {
		$function = __FUNCTION__;
		$title = 'Grade management';
		$description = 'Overview of personally submitted grades';

		echo component::generateBreadcrumb(get_class(), $function);
		echo component::generateTitle($title, $description);

		$sql = "SELECT * FROM `gradebook`, `courses`, `basic-information` WHERE  `courses`.ID = `gradebook`.Course AND `gradebook`.Owner = `basic-information`.ID ORDER BY `gradebook`.DateTime";

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
				'<td width="200px"><b>Results batch:</b></td>' .
				'<td width="150px"><b>Batchnumber</b></td>' .
				'<td width="150px"><b>Submitted by</b></td>' .
				'<td width="150px"><b>Date and Time</b></td>' .
				'<td width="100px"><b>Management</b></td>' .
				'</tr>';
			echo '<tr>' .
				'<td><a href="?id=courses&action=view&item=' . $courseid . '"><b>' . $coursename . '</b> </a></td>' .
				'<td><a href="?id=view-information&uid=' . $batchname . '">(' . $validator . ')</a></td>' .
				'<td><a href="?id=view-information&uid=' . $uid . '">' . $firstname . ' ' . $lastname . '</a></td>' .
				'<td>' . $date . '</td>' .
				'<td>
				<a href="?id=studies&action=edit&item=' . $fetch[0] . '"> <img src="templates/default/images/edi.png"> edit</a>
				<a href="?id=studies&action=delete&item=' . $fetch[0] . '" onclick="return confirm(\'Are you sure?\')"> <img src="templates/default/images/del.png"> delete </a>
				</td>' .
				'</tr>';
			echo'</table></div>';
		}
	}

	function enterGrades($programselected, $courseselected) {
		if (isset($programselected) && isset($courseselected)) {
			$sql = "SELECT * FROM `basic-information` as bi, `student-program-link` as cc WHERE `Major` = '" . $programselected . "' AND cc.StudentID = bi.ID OR `Minor` = '" . $programselected . "' AND cc.`StudentID` = bi.`GovernmentID`  ORDER BY Surname";
		}

		$run = $this->core->database->doSelectQuery($sql);
		$validator = mt_rand(100000, 9999999999999999);

		echo '<p><b>Enter grades </b></p><p>
		<form id="login" name="login" method="post" action="?id=grades&action=submit">
		<input type="hidden" name="id" value="grades-submit">
		<input type="hidden" name="validator" value="' . $validator . '">
		<input type="hidden" name="course" value="' . $courseselected . '">
		<table width="768" height="" border="0" cellpadding="0" cellspacing="0">
		<tr class="tableheader">
		<td width="20px"></td>
		<td><b>Student name</b></td>
		<td><b>Student number</b></td>
		<td><b>Grade field</b></td>
		</tr>';

		while ($fetch = $run->fetch_row()) {
			echo '<tr>
			<td><img src="templates/default/images/bullet_user.png"></td>
			<td><b><a href="?id=view-information&uid=' . $fetch[4] . '">' . $fetch[0] . ' ' . $fetch[2] . '</a></b></td>' .
			'<td>' . $fetch[4] . '</td>' .
			'<td><input type="textbox" name="g' . $fetch[4] . '" size="5" class="submit"></td>' .
			'</tr>';
		}

		echo '</table>
		<br><hr><br><input type="submit" value="Submit grades to board of studies" />
		</form></p>';
	}


	function selectCourse() {
		include $this->core->classPath . "showoptions.inc.php";

		$function = __FUNCTION__;
		$title = 'Submit grades';
		$description = 'Overview of personally submitted grades';

		echo component::generateBreadcrumb(get_class(), $function);
		echo component::generateTitle($title, $description);

		$programselected = $this->core->cleanPost['program'];
		$courseselected = $this->core->cleanPost['course'];
		$select = new optionBuilder($this->core);
		$program = $select->showPrograms(null, null, $programselected);
		$courses = $select->showCourses($programselected, null);

		if (!isset($courseselected)) {

			echo '<p><b>Select the programme you wish to list the students from and course you are entering the grades for:</b></p>
			<p><form id="login" name="login" method="POST" action="?id=grades&action=selectcourse">

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

		echo component::generateBreadcrumb(get_class(), $function);
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
					$student = ltrim($student, 'g');
					$date = date('YmdH');
					$hash = sha1("$grade$student$grade$date$salt");

					$output = $output . "<tr><td><b>" . $student . "</b></td><td><b>" . $grade . "</td></tr>";
					$sql = "INSERT INTO grades (`ID`, `StudentID`, `CourseID`,  `Grade`, `Datestamp`, `GradeHash`, `CreatorID`, `MarkType`, `BatchID`) VALUES (NULL, '$student', '$courseid', '$grade', CURRENT_TIMESTAMP, '$hash', '$this->core->userid', '1', '$batch');";
					$this->core->database->doInsertQuery($sql);

				}
			}

			$sql = "COMMIT;";

			if ($this->database->doInsertQuery($sql)) {
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