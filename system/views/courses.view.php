<?php
class courses {

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
	}

	function editCourses($item) {

		$sql = "SELECT * FROM `courses` WHERE `courses`.ID = $item";
		$run = $this->core->database->doSelectQuery($sql);

		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
		$optionBuilder = new optionBuilder($this->core);

		while ($fetch = $run->fetch_assoc()) {
			$name = $fetch['Name'];
			$description = $fetch['CourseDescription'];
			$item = $fetch['ID'];

			$icoordinator = $fetch['CourseCoordinatorInternal'];
			$dcoordinator = $fetch['CourseCoordinatorDistance'];

			$internal = $optionBuilder->showUsers(99, $icoordinator);
			$distance = $optionBuilder->showUsers(99, $dcoordinator);

			$select = new optionBuilder($this->core);
			$notselectedcourses = $select->showCourses(NULL);
			$selectedcourses = $select->showPCourses($item);


			include $this->core->conf['conf']['formPath'] . "editcourse.form.php";
		}
	}

	function addCourses() {
		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
		$optionBuilder = new optionBuilder($this->core);

		$internal = $optionBuilder->showUsers(99);
		$distance = $optionBuilder->showUsers(99);

		include $this->core->conf['conf']['formPath'] . "addcourse.form.php";
	}

	function deleteCourses($item) {
		$sql = 'DELETE FROM `courses` WHERE `ID` = "' . $item . '"';
		$run = $this->core->database->doSelectQuery($sql);

		$this->core->redirect("courses", "manage", NULL);
	}


	function saveCourses() {
		
		$name = $this->core->cleanPost['name'];
		$internal = $this->core->cleanPost['internal'];
		$distance = $this->core->cleanPost['distance'];
		$method = $this->core->cleanPost['ds'];
		$description = $this->core->cleanPost['description'];
		$item = $this->core->cleanPost['item'];

		$assessmentweight = $this->core->cleanPost['assessmentweight'];
		$examweight = $this->core->cleanPost['examweight'];
		$credit = $this->core->cleanPost['credits'];
		$year = $this->core->cleanPost['year'];
		$term = $this->core->cleanPost['term'];
	
		$selected = $this->core->cleanPost['selected'];
		$nselected = $this->core->cleanPost['nselected'];

		if (!empty($nselected)) {
			foreach ($nselected as $nsel) {
				$sql = "INSERT INTO `course-prerequisites` (`ID`, `CourseID`, `Prerequisites`, `Method`) 
					VALUES (NULL, '$item', '$nsel', '$method');";

				$run = $this->core->database->doInsertQuery($sql);
			}
		} elseif (!empty($selected)) {
			foreach ($selected as $sel) {
				$sql = "DELETE FROM `course-prerequisites` WHERE `CourseID` = $item AND `Prerequisites` = $sel";
				$run = $this->core->database->doInsertQuery($sql);
			}
		} elseif (isset($item)) {
			$sql = "UPDATE `courses` SET `CourseDescription` = '$description', `Name` = '$name', `CourseCoordinatorInternal` = '$internal',  `CourseCoordinatorDistance` = '$distance'  WHERE `ID` = $item;";
			$run = $this->core->database->doInsertQuery($sql);
			$sql = "INSERT INTO `course-prerequisites` (`ID`, `CourseID`, `Prerequisites`, `Method`) VALUES (NULL, '$item', '', '$method');";
			$run = $this->core->database->doInsertQuery($sql);
		} else {
			$sql = "INSERT INTO `courses` (`ID`, `Name`, `CourseCoordinatorInternal`, `CourseCoordinatorDistance`, `CourseDescription`, `AssessmentWeight`, `ExamWeight`, `CourseCredit`, `Year`, `Term`) 
			VALUES (NULL, '$name', '$internal', '$distance', '$description', '$assessmentweight', '$examweight', '$credit', '$year', '$term');";
			$run = $this->core->database->doInsertQuery($sql);
		}

		$this->core->redirect("courses", "manage", NULL);
	}

	function manageCourses() {

		$mode = $this->core->cleanGet['mode'];

		if(isset($mode)){
			$sql = "SELECT `courses`.*, `bi`.ID AS CID, `xi`.FirstName, `xi`.Surname,  `courses`.Name as namesd
			FROM `courses`, `basic-information` as bi,`basic-information` as xi, `course-electives`, `student-data-other`
			WHERE `course-electives`.StudentID = `bi`.ID 
			AND `courses`.ID = `course-electives`.CourseID 
          	 	AND  `xi`.ID = `courses`.CourseCoordinatorInternal 
			AND `student-data-other`.StudentID = `bi`.ID
			AND `bi`.StudyType = '$mode'
			AND  `course-electives`.Approved = '1'
			GROUP BY `courses`.ID ORDER BY namesd";

			$run = $this->core->database->doSelectQuery($sql);


			$sqlc = "SELECT * FROM (SELECT COUNT(`courses`.ID) as ct, Sex, `courses`.Name as namesd FROM `course-electives` , `basic-information`, `courses`, `student-data-other`
			WHERE `course-electives`.Approved = '1'
			AND `course-electives`.StudentID = `basic-information`.ID
			AND `student-data-other`.StudentID = `basic-information`.ID
			AND `courses`.ID = CourseID
			AND `basic-information`.StudyType = '$mode'
			GROUP BY `course-electives`.CourseID, Sex) AS tmpt ORDER BY `tmpt`.namesd";

		
		}else{

			$sql = "SELECT * FROM (SELECT `courses`.*, `bi`.ID AS CID, `bi`.FirstName, `bi`.Surname,  COUNT(`courses`.ID) as ct, `courses`.Name as namesd FROM `courses` 
			LEFT JOIN `basic-information` as `bi` ON `courses`.CourseCoordinatorInternal = `bi`.ID  
			LEFT JOIN `course-electives` ON `courses`.ID = `course-electives`.CourseID AND `course-electives`.Approved = '1'
			GROUP BY `courses`.ID) AS tmpt ORDER BY `tmpt`.namesd";

			$run = $this->core->database->doSelectQuery($sql);


			$sqlc = "SELECT * FROM (SELECT COUNT(`courses`.ID) as ct, Sex, `courses`.Name as namesd FROM `course-electives` , `basic-information`, `courses`
			WHERE `course-electives`.Approved = '1'
			AND `course-electives`.StudentID = `basic-information`.ID
			AND `courses`.ID = CourseID
			GROUP BY `course-electives`.CourseID, Sex) AS tmpt ORDER BY `tmpt`.namesd";

		}

		$runc = $this->core->database->doSelectQuery($sqlc);

		while ($fetch = $runc->fetch_assoc()) {
			$course = $fetch['namesd']; 
			$count = $fetch['ct']; 
			$sex = $fetch['Sex']; 
			$counter[$course][$sex] = $count;
		}

		echo '<form id="narrow" name="narrow" method="get" action=""><div class="toolbar">
			<div class="toolbar">
				<a href="' . $this->core->conf['conf']['path'] . '/courses/add">Add course</a>
				<div class="toolbaritem">Mode filter:
					<select name="mode" class="submit" style="width: 105px;  margin-top: -17px;">
						<option value="Fulltime">Full-time</option>
						<option value="Distance">Distance</option>
						<option value="Parttime">Part-time students</option>
						<option value="%" selected>ALL</option>
					</select>
					<input type="submit" value="update"  style="width: 80px; margin-top: -15px;"/></div>
				</div>
				</form>
			</div>';



		echo '<table width="768" height="" border="0" cellpadding="3" cellspacing="0">
           	 <tr class="tableheader">
			<td><b>#</b></td>
			<td width="400"><b>Course Name</b></td>' .
			'<td><b>Course Coordinator</b></td>' .
			'<td><b>Students</b></td>' .
			'<td><b>Management</b></td>' .
			'</tr>';

		$i = 0;
		while ($fetch = $run->fetch_assoc()) {
			if ($i == 0) {
				$bgc = 'class="zebra"';
				$i++;
			} else {
				$bgc = '';
				$i--;
			}



			$course = $fetch['Name'];
			$male = $counter[$course]['Male'];
			$female = $counter[$course]['Female'];

			if(empty($male)){ $male = 0; }
			if(empty($female)){ $female = 0; }
			
			$total = $male + $female;

			if($fetch['ct'] == 1){
				$count = "none";
			} else {
				$count = "<b>".$fetch['ct'] . " students</b>";
			}

			$a++;

			echo '<tr ' . $bgc . '>
			<td>'.$a.'</td>
                    <td><b><a href="' . $this->core->conf['conf']['path'] . '/courses/show/' . $fetch['ID'] . '"> ' . $fetch['Name'] . ' - ' . $fetch['CourseDescription'] . '</a></b></td>
                    <td>
                    <a href="' . $this->core->conf['conf']['path'] . '/information/show/' . $fetch[5] . '">' . $fetch['FirstName'] . ' ' . $fetch[Surname] . '</a>
                    </td>
                    <td>
                    <a href="' . $this->core->conf['conf']['path'] . '/information/search?search=course&q=' . $fetch['ID'] . '&mode='.$mode.'">M: '.$male.' / F: '.$female.' / T: '.$total.'</a>
                    </td>
                    <td>
                    <a href="' . $this->core->conf['conf']['path'] . '/courses/edit/' . $fetch['ID'] . '"> <img src="' . $this->core->fullTemplatePath . '/images/edi.png"> </a>
                  -    <a href="' . $this->core->conf['conf']['path'] . '/courses/delete/' . $fetch['ID'] . '" onclick="return confirm(\'Are you sure?\')"> <img src="' . $this->core->fullTemplatePath . '/images/delete.gif"> </a>
                    </td>
                    </tr>';
		}

		echo '</table>';
	}

	function showCourses($item) {
		if(isset($_GET['course'])){
			$item = $_GET['course'];
		}

		$sql = "SELECT `courses`.*, `basic-information`.*, COUNT(`courses`.ID) FROM `courses` 
			LEFT JOIN `basic-information` ON `courses`.CourseCoordinatorInternal = `basic-information`.ID 
			LEFT JOIN `course-electives` ON `courses`.ID = `course-electives`.CourseID 
			WHERE `courses`.ID = '$item'
			GROUP BY `courses`.ID";


		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_row()) {


			echo '<table width="768" border="0" cellpadding="5" cellspacing="0">
                  <tr>
                    <td width="205" height="28" bgcolor="#EEEEEE"><strong>Information</strong></td>
                    <td width="200" bgcolor="#EEEEEE"></td>
                    <td  bgcolor="#EEEEEE"></td>
                  </tr>
                  <tr>
                    <td><strong>Course name</strong></td>
                    <td> <b>' . $fetch[1] . '</b> - ' . $fetch[4] . '</td>
                    <td></td>
                  </tr>
                  <tr>
                    <td><strong>Course coordinator</strong></td>
                    <td><a href="' . $this->core->conf['conf']['path'] . '/information/show/' . $fetch[2] . '">' . $fetch[5] . ' ' . $fetch[7] . '</a></td>
                    <td></td>
                  </tr>
                  <tr>
                    <td><strong>Course enrolment</strong></td>
                    <td><a href="' . $this->core->conf['conf']['path'] . '/information/search?search=course&q='.$item.'">'.$fetch[26].' Students</a></td>
                    <td></td>
                  </tr>';

		}

		echo '</table>';
	}
}
?>
