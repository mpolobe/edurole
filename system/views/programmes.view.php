<?php
class programmes {

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

	public function editProgrammes($item) {
		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
		$sql = "SELECT  `programmes`.ID, ProgramName, ProgramCoordinator, ProgramType, `basic-information`.ID, FirstName, Surname 
			FROM `programmes`
			LEFT JOIN `basic-information`
			ON ProgramCoordinator = `basic-information`.ID
			WHERE `programmes`.ID = $item";

		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_row()) {
			$select = new optionBuilder($this->core);
			$users = $select->showUsers("100", $fetch[4]);
			$notselectedcourses = $select->showCourses(NULL);
			$selectedcourses = $select->showCourses($fetch[0]);

			include $this->core->conf['conf']['formPath'] . "editprogramme.form.php";
		}

	}

	public function changeProgrammes($item) {

		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";

		$select = new optionBuilder($this->core);

		$study = 		$select->showStudies(null);
		$major = 		$select->showPrograms(null);
		$minor = 		$select->showPrograms(null);
		
		
		include $this->core->conf['conf']['formPath'] . "changeprogramme.form.php";
		

		$study = $this->core->cleanPost['study'];
		$major = $this->core->cleanPost['major'];
		$minor = $this->core->cleanPost['minor'];


		if(isset($study) && isset($major) && isset($minor)){

			$sql = "UPDATE `student-program-link` SET `Major` = '$major', `Minor` = '$minor' WHERE `StudentID` = '$item'";
			$run = $this->core->database->doInsertQuery($sql);

			$sql = "UPDATE `student-study-link` SET `StudyID` = '$study' WHERE `StudentID` = '$item'";
			$run = $this->core->database->doInsertQuery($sql);

			echo '<span class="successpopup">Information updated. Go <a href="' . $this->core->conf['conf']['path'] . '/information/show/'.$item.'">back to profile.</a></span>';

		}


	}

	public function addProgrammes() {
		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
		
		$select = new optionBuilder($this->core);
		$users = $select->showUsers("100", null);
		
		include $this->core->conf['conf']['formPath'] . "addprogramme.form.php";
	}

	public function deleteProgrammes($item) {
		$sql = 'DELETE FROM `programmes`  WHERE `ID` = "' . $item . '"';
		$run = $this->core->database->doInsertQuery($sql);

		$this->core->redirect("programmes", "manage", NULL);
	}

	public function saveProgrammes() {
		$item = $this->core->cleanPost['item'];
		$name = $this->core->cleanPost['name'];
		$type = $this->core->cleanPost['programtype'];
		$coordinator = $this->core->cleanPost['coordinator'];
		$description = $this->core->cleanPost['description'];

		$selected = $this->core->cleanPost['selected'];
		$nselected = $this->core->cleanPost['nselected'];

		if (!empty($nselected)) {
			foreach ($nselected as $nsel) {
				$sql = "INSERT INTO `program-course-link` (`ID`, `ProgramID`, `CourseID`, `Manditory`, `Year`) VALUES (NULL, '$item', '$nsel', '0', '1');";
				$run = $this->core->database->doInsertQuery($sql);
			}
		} elseif (!empty($selected)) {
			foreach ($selected as $sel) {
				$sql = "DELETE FROM `program-course-link` WHERE `ProgramID` = $item AND `CourseID` = $sel";
				$run = $this->core->database->doInsertQuery($sql);
			}
		} elseif (!empty($item)) {
			$sql = "UPDATE `edurole`.`programmes` SET `ProgramType` = '$type', `ProgramName` = '$name', `ProgramCoordinator` = '$coordinator' WHERE `programmes`.`ID` = $item;";
			$run = $this->core->database->doInsertQuery($sql);
		} else {
			$sql = "INSERT INTO `programmes` (`ID`, `ProgramType`, `ProgramName`, `ProgramCoordinator`) VALUES (NULL, '$type', '$name', '$coordinator');";
			$run = $this->core->database->doInsertQuery($sql);
		}

		$this->core->redirect("programmes", "manage", NULL);
	}

	public function manageProgrammes($item = NULL) {
		if(isset($item)){
			$sql = "SELECT * FROM `programmes`, `program-course-link`, `studies`, WHERE `programmes`.ParentID = `studies`.ID AND `studies`.ID = $item ORDER BY `studies`.Name";
		}else{
			$sql = "(SELECT `programmes`.ID, ProgramName, ProgramCoordinator, ProgramType FROM `programmes`) UNION ALL (SELECT `programmes`.ID, ProgramName, ProgramCoordinator, ProgramType FROM `programmes`, `program-course-link` WHERE ProgramID = programmes.ID) ORDER BY ID ";
		}

		$run = $this->core->database->doSelectQuery($sql);
		echo '<div class="toolbar"><a href="' . $this->core->conf['conf']['path'] . '/programmes/add">Add programme</a></div>' .
              '<table width="768" height="" border="0" cellpadding="3" cellspacing="0">' .
              '<tr class="tableheader">' .
              '<td><b>Programme name</b></td>' .
		'<td><b>Number of Courses</b></td>' .
		'<td><b>Programme Type</b></td>' .
		'<td><b>Management tools</b></td>' .
		'</tr>';

		$count = 0;
		$first = 1;
		$i = 0;
		$rest = NULL;
		$temp = NULL;

		while ($fetch = $run->fetch_row()) {

			if ($first == 1) {
				$first = 2;
			}

			if ($temp == $fetch[0] && $first != 2) {
				$count++;
				$temp = $fetch[0];

			} else {

				if ($i == 0) {
					$bgc = 'class="zebra"';
					$i++;
				} else {
					$bgc = '';
					$i--;
				}
				if ($count == 0) {
					$count = "-";
				}
				if (isset($out)) {
					echo $out . $count . $rest;
				}
				if ($fetch[3] == "1") {
					$type = "Minor";
				} else if ($fetch[3] == "2") {
					$type = "Major";
				} else if ($fetch[3] == "3") {
					$type = "Major & Minor";
				} else if ($fetch[3] == "4") {
					$type = "Compulsory";
				} else if ($fetch[3] == "5") {
					$type = "Diploma";
				} else {
					$type = "Unknown";
				}

				$out = '<tr ' . $bgc . '>
						<td><b><a href="' . $this->core->conf['conf']['path'] . '/programmes/show/' . $fetch[0] . '"> ' . $fetch[1] . '</a></b></td>
						<td>';

				$rest = ' </td><td> ' . $type . ' </td>
						<td>
						<a href="' . $this->core->conf['conf']['path'] . '/programmes/edit/' . $fetch[0] . '"> <img src="'. $this->core->fullTemplatePath .'/images/edi.png"> edit</a>
						<a href="' . $this->core->conf['conf']['path'] . '/programmes/delete/' . $fetch[0] . '" onclick="return confirm(\'Are you sure?\')"> <img src="'. $this->core->fullTemplatePath .'/images/del.png"> delete </a>
						</td>
						</tr>';

				$first = 3;
				$count = 0;
				$temp = $fetch[0];

			}
		}
		if ($count == 0) {
			$count = "-";
		}

		echo $out . $count . $rest;

		echo '</table>';
			$temp = $fetch[0];
	}


	public function showProgrammes($item) {
		$sql = "SELECT * FROM `programmes` 
			LEFT JOIN `basic-information` ON  ProgramCoordinator = `basic-information`.ID 
			WHERE `programmes`.ID = '$item'";

		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_row()) {

			echo '<table width="768" border="0" cellpadding="5" cellspacing="0">
				  <tr>
					<td width="205" height="28" bgcolor="#EEEEEE"><strong>Information</strong></td>
					<td bgcolor="#EEEEEE"></td>
					<td  bgcolor="#EEEEEE"></td>
				  </tr>
					<tr>
					<td width="150"><b>Name of Programme</b></td>
					<td><b>' . $fetch[2] . '</b></td>
					<td></td>
					</tr>
					<tr>
					<td width="150"><b>Programme Coordinator</b></td>
					<td><a href="' . $this->core->conf['conf']['path'] . 'information/show/' . $fetch[8] . '">' . $fetch[4] . ' ' . $fetch[6] . '</b></td>
					<td></td>
					</tr>
					<tr><td>Programme Type</td>
					<td>';

			if ($fetch[1] == "0") {
				echo 'No type selected';
			}
			if ($fetch[1] == "1") {
				echo 'Minor';
			}
			if ($fetch[1] == "2") {
				echo 'Major';
			}
			if ($fetch[1] == "3") {
				echo 'Available as both';
			}

			echo '</select></td>
                <td></td>
                </tr><tr><td>Courses</td>
                <td>';

			$sql = "SELECT * FROM `courses`, `program-course-link` 
				WHERE `program-course-link`.CourseID = `courses`.ID 
				AND `program-course-link`.ProgramID = '$fetch[0]'";

			$run = $this->core->database->doSelectQuery($sql);

			$i = 1;

			while ($fetchs = $run->fetch_assoc()) {

				echo '<li><a href="' . $this->core->conf['conf']['path'] . '/courses/show/' . $fetchs['ID'] . '">' . $fetchs['Name'] . ' - ' . $fetchs['CourseDescription'] . '</a></li>';
				$i++;

			}

			if ($i == 1) {
				echo 'No courses have been added to the program yet. Please <a href="' . $this->core->conf['conf']['path'] . '/programmes/edit/' . $fetch[0] . '">add some.</a>';
			}

			echo '</td>
                <td></td>
                </tr></table>
                </p>';
		}
	}
}

?>
