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
		$description = $this->core->cleanPost['description'];
		$item = $this->core->cleanPost['item'];

		if (isset($item)) {
			$sql = "UPDATE `courses` SET `CourseDescription` = '$description', `Name` = '$name', `CourseCoordinatorInternal` = '$internal',  `CourseCoordinatorDistance` = '$distance'  WHERE `ID` = $item;";
		} else {
			$sql = "INSERT INTO `courses` (`ID`, `Name`,  `CourseCoordinatorInternal`, `CourseCoordinatorDistance`, `CourseDescription`) VALUES (NULL, '$name', '$internal', '$distance', '$description');";
		}

		$run = $this->core->database->doInsertQuery($sql);

		$this->core->redirect("courses", "manage", NULL);
	}

	function manageCourses() {
		$sql = "SELECT * FROM `courses` 
			LEFT JOIN `basic-information` as `bi` ON `courses`.CourseCoordinatorInternal = `bi`.ID  
			LEFT JOIN `basic-information` as `bd` ON `courses`.CourseCoordinatorDistance = `bd`.ID 
			ORDER BY `courses`.Name";

		$run = $this->core->database->doSelectQuery($sql);

		echo '<div class="toolbar"><a href="' . $this->core->conf['conf']['path'] . '/courses/add">Add course</a></div>
            <table width="768" height="" border="0" cellpadding="3" cellspacing="0">
            <tr class="tableheader"><td width="400"><b>Course Name</b></td>' .
			'<td><b>Internal Coordinator</b></td>' .
			'<td><b>Distance Coordinator</b></td>' .
			'<td><b>Management tools</b></td>' .
			'</tr>';

		$i = 0;
		while ($fetch = $run->fetch_row()) {
			if ($i == 0) {
				$bgc = 'class="zebra"';
				$i++;
			} else {
				$bgc = '';
				$i--;
			}

			echo '<tr ' . $bgc . '>
                    <td><b><a href="' . $this->core->conf['conf']['path'] . '/courses/show/' . $fetch[0] . '"> ' . $fetch[1] . ' - ' . $fetch[4] . '</a></b></td>
                    <td>
                    <a href="' . $this->core->conf['conf']['path'] . '/information/show/' . $fetch[9] . '">' . $fetch[5] . ' ' . $fetch[7] . '</a>
                    </td>
                    <td>
                    <a href="' . $this->core->conf['conf']['path'] . '/information/show/' . $fetch[10] . '">' . $fetch[26] . ' ' . $fetch[28] . '</a>
                    </td>
                    <td>
                    <a href="' . $this->core->conf['conf']['path'] . '/courses/edit/' . $fetch[0] . '"> <img src="' . $this->core->fullTemplatePath . '/images/edi.png"> edit</a>
                    <a href="' . $this->core->conf['conf']['path'] . '/courses/delete/' . $fetch[0] . '" onclick="return confirm(\'Are you sure?\')"> <img src="' . $this->core->fullTemplatePath . '/images/delete.gif"> delete </a>
                    </td>
                    </tr>';
		}

		echo '</table>';
	}

	function showCourses($item) {
		$sql = "SELECT * FROM `courses` 
			LEFT JOIN `basic-information` ON `courses`.CourseCoordinatorInternal = `basic-information`.ID 
			WHERE `courses`.ID = $item";

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
                    <td><strong>Course enrollment</strong></td>
                    <td>COUNT</td>
                    <td></td>
                  </tr>';

		}

		echo '</table>';
	}
}
?>
