<?php
class programmes {

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

		if ($this->core->action == "list" && isset($this->core->item) && $this->core->role > 100) {
			$this->listProgrammes($this->core->item);
		} elseif ($this->core->action == "view") {
			$this->showProgram($this->core->item);
		} elseif ($this->core->action == "edit" && isset($this->core->item) && $this->core->role > 100) {
			$this->editProgram($this->core->item);
		} elseif ($this->core->action == "add" && $this->core->role > 100) {
			$this->addProgram();
		} elseif ($this->core->action == "save" && $this->core->role > 100) {
			$this->saveProgram();
			$this->listProgrammes();
		} elseif ($this->core->action == "delete" && isset($this->core->item) && $this->core->role > 100) {
			$this->deleteProgram($this->core->item);
		} else {
			$this->listProgrammes();
		}
	}

	function editProgram($item) {
		$function = __FUNCTION__;
		$title = 'Programme management';
		$description = 'Edit Programme';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);
		
		$sql = "SELECT `programmes`.ID, ProgramName, ProgramCoordinator, ProgramType, `basic-information`.ID, FirstName, Surname FROM `programmes`, `basic-information` WHERE `programmes`.ID = $item AND ProgramCoordinator = `basic-information`.ID";
		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_row()) {
			include $this->core->conf['conf']['formPath'] . "editprogramme.form.php";
		}

	}

	function addProgram() {
		$function = __FUNCTION__;
		$title = 'Add new programme';
		$description = 'Add a new programme to the system';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);

		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
		
		$select = new optionBuilder($this->core);
		$users = $select->showUsers("100", null);
		
		include $this->core->conf['conf']['formPath'] . "addprogramme.form.php";
	}

	function deleteProgram($item) {
		$sql = 'DELETE FROM `programmes`  WHERE `ID` = "' . $item . '"';
		$run = $this->database->doInsertQuery($sql);

		$this->listProgrammes();
		$this->core->showAlert("The programme has been deleted");
	}

	function saveProgram() {
		$item = $this->core->cleanPost['item'];
		$name = $this->core->cleanPost['name'];
		$type = $this->core->cleanPost['programtype'];
		$coordinator = $this->core->cleanPost['coordinator'];
		$description = $this->core->cleanPost['description'];
		$selected = $this->core->cleanPost['selected'];
		$nselected = $this->core->cleanPost['nselected'];

		if (isset($nselected)) {
			foreach ($nselected as $nsel) {
				$sql = "INSERT INTO `program-course-link` (`ID`, `ProgramID`, `CourseID`, `Manditory`, `Year`) VALUES (NULL, '$item', '$nsel', '', '');";
				$run = $this->database->doInsertQuery($sql);
			}
		} elseif (isset($selected)) {
			foreach ($selected as $sel) {
				$sql = "DELETE FROM `program-course-link` WHERE `ProgramID` = $item AND `CourseID` = $sel";
				$run = $this->database->doInsertQuery($sql);
			}
		} elseif (isset($item)) {
			$sql = "UPDATE `edurole`.`programmes` SET `ProgramType` = '$type', `ProgramName` = '$name', `ProgramCoordinator` = '$coordinator' WHERE `programmes`.`ID` = $item;";
			$run = $this->database->doInsertQuery($sql);
		} else {
			$sql = "INSERT INTO `programmes` (`ID`, `ProgramType`, `ProgramName`, `ProgramCoordinator`) VALUES (NULL, '$type', '$name', '$coordinator');";
			$run = $this->database->doInsertQuery($sql);
		}
	}

	function listProgrammes($item) {
		$function = __FUNCTION__;
		$title = 'Programme management';
		$description = 'Overview of programmes';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);

		if(isset($item)){
			$sql = "SELECT * FROM `programmes`, `program-course-link`, `studies`, WHERE `programmes`.ParentID = `studies`.ID AND `studies`.ID = $item ORDER BY `studies`.Name";
		}else{
			$sql = "(SELECT `programmes`.ID, ProgramName, ProgramCoordinator, ProgramType FROM `programmes`) UNION ALL (SELECT `programmes`.ID, ProgramName, ProgramCoordinator, ProgramType FROM `programmes`, `program-course-link` WHERE ProgramID = programmes.ID) ORDER BY ID ";
		}

		$run = $this->core->database->doSelectQuery($sql);
		echo '<div class="toolbar"><a href="' . $this->core->conf['conf']['path'] . '/programmes/add">Add programme</a></div>
                <table width="768" height="" border="0" cellpadding="3" cellspacing="0">
                <tr class="tableheader">
                <td><b>Programme name</b></td>' .
			'<td><b>Number of Courses</b></td>' .
			'<td><b>Programme Type</b></td>' .
			'<td><b>Management tools</b></td>' .
			'</tr>';

		$count = 0;
		$first = 1;
		$i = 0;
		$rest = NULL;

		while ($fetch = $run->fetch_row()) {

			if ($first == 1) {
				$temp = $fetch[0];
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
				} else {
					$type = "Unknown";
				}

				$out = '<tr ' . $bgc . '>
						<td><b><a href="' . $this->core->conf['conf']['path'] . '/programmes/view/' . $fetch[0] . '"> ' . $fetch[1] . '</a></b></td>
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
	}


	function showProgram($item) {
		$function = __FUNCTION__;
		$title = 'View program';
		$description = 'Overview of program';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);

		$sql = "SELECT `programmes`.ID, ProgramName, ProgramCoordinator, ProgramType, `basic-information`.ID, FirstName, Surname FROM `programmes`, `basic-information` WHERE `programmes`.ID = $item AND ProgramCoordinator = `basic-information`.ID";
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
					<td><b>' . $fetch[1] . '</b></td>
					<td></td>
					</tr>
					<tr>
					<td width="150"><b>Programme Coordinator</b></td>
					<td><a href="' . $this->core->conf['conf']['path'] . 'information/view/' . $fetch[4] . '">' . $fetch[5] . ' ' . $fetch[6] . '</b></td>
					<td></td>
					</tr>
					<tr><td>Programme Type</td>
					<td>';

			if ($fetch[3] == "0") {
				echo 'No type selected';
			}
			if ($fetch[3] == "1") {
				echo 'Minor';
			}
			if ($fetch[3] == "2") {
				echo 'Major';
			}
			if ($fetch[3] == "3") {
				echo 'Available as both';
			}

			echo '</select></td>
                <td></td>
                </tr><tr><td>Courses</td>
                <td>';

			$sql = "SELECT * FROM `courses`, `programmes`, `program-course-link` WHERE `program-course-link`.CourseID = `courses`.ID AND `program-course-link`.ProgramID = `programmes`.ID AND `program-course-link`.ProgramID = $fetch[0]";

			$run = $this->core->database->doSelectQuery($sql);

			$i = 1;

			while ($fetch = $run->fetch_row()) {

				echo '<li><a href="' . $this->core->conf['conf']['path'] . 'courses/view/' . $fetch[0] . '">' . $fetch[2] . '</a></li>';
				$i++;

			}

			if ($i == 1) {
				echo 'No courses have been added to the program yet. Please <a href="' . $this->core->conf['conf']['path'] . 'programmes/edit/' . $fetch[0] . '">add some.</a>';
			}

			echo '</td>
                <td></td>
                </tr></table>
                </p>';
		}
	}
}

?>
