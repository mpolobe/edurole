<?php
class courses {

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
		
		if ($this->core->action == "list") {
			$this->listCourses();
		} elseif ($this->core->action == "view") {
			$this->showCourse($this->core->item);
		} elseif ($this->core->action == "edit" && isset($this->core->item) && $this->core->role > 103) {
			$this->editCourse($this->core->item);
		} elseif ($this->core->action == "add" && $this->core->role > 103) {
			$this->addCourse();
		} elseif ($this->core->action == "save" && $this->core->role > 103) {
			$this->saveCourse();
		} elseif ($this->core->action == "delete" && isset($this->core->item) && $this->core->role > 103) {
			$this->deleteCourse($this->core->item);
		} else {
			$this->listCourses();
		}
	}

	function editCourse($item) {
		$function = __FUNCTION__;
		$title = 'Edit course';
		$description = 'Remember to save changes after you are done';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);

		$sql = "SELECT * FROM `courses` WHERE `courses`.ID = $item";
		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_row()) {
			include $this->core->conf['conf']['classPath'] . "editcourse.form.php";
		}
	}

	function addCourse() {
		$function = __FUNCTION__;
		$title = 'Add course';
		$description = 'Please enter the required fields';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);

		include $this->core->conf['conf']['formPath'] . "addcourse.form.php";

	}

	function deleteCourse($item) {
		$sql = 'DELETE FROM `schools`  WHERE `ID` = "' . $item . '"';
		$run = $this->core->database->doSelectQuery($sql);

		$sql = "SELECT * FROM `programmes`,`studies` WHERE `programmes`.ParentID = `studies`.ID AND `studies`.ID = $this->item ORDER BY `studies`.Name";
		$this->listCourses($sql);
		$this->core->showAlert("The course has been deleted");
	}

	function saveCourse() {
		$name = $this->core->cleanPost['name'];
		$dean = $this->core->cleanPost['dean'];
		$description = $this->core->cleanPost['description'];

		if (isset($this->item)) {
			$sql = "UPDATE `courses` SET `Description` = '$description', `Name` = '$name', `Dean` = '$dean' WHERE `ID` = $this->item;";
		} else {
			$sql = "INSERT INTO `courses` (`ID`, `ParentID`, `Established`, `Name`, `Description`, `Dean`) VALUES (NULL, '0', CURRENT_DATE(), '$name', '$description', '$dean');";
		}

		$run = $this->core->database->doInsertQuery($sql);

		$sql = "SELECT * FROM `courses`, `programmes`,`access`,`basic-information` WHERE Dean = `access`.ID AND `access`.ID = `basic-information`.ID ORDER BY Name";
		$this->listSchools($sql);
	}

	function listCourses($item) {
		$function = __FUNCTION__;
		$title = 'Overview of courses';
		$description = 'Overview of all courses currently on offer';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);

		$sql = "SELECT * FROM `programmes`,`studies` WHERE `programmes`.ParentID = `studies`.ID AND `studies`.ID = $this->item ORDER BY `studies`.Name";
		$run = $this->core->database->doSelectQuery($sql);

		echo '<p><a href="' . $this->core->conf['conf']['path'] . 'courses/add">Add course</a></p>
            <p>
            <table width="768" height="" border="0" cellpadding="3" cellspacing="0">
            <tr class="tableheader"><td width="400"><b>Course Name</b></td>' .
			'<td><b>Course coordinator</b></td>' .
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
                    <td><b><a href="' . $this->core->conf['conf']['path'] . 'courses/view/' . $fetch[0] . '"> ' . $fetch[2] . '</a></b></td>
                    <td>
                    <a href="' . $this->core->conf['conf']['path'] . 'information/view/' . $fetch[3] . '">' . $fetch[4] . ' ' . $fetch[6] . '</a>
                    </td>
                    <td>
                    <a href="' . $this->core->conf['conf']['path'] . 'courses/edit/' . $fetch[0] . '"> <img src="' . $this->core->fullTemplatePath . '/images/edit.png"> edit</a>
                    <a href="' . $this->core->conf['conf']['path'] . 'courses/delete/' . $fetch[0] . '" onclick="return confirm(\'Are you sure?\')"> <img src="' . $this->core->fullTemplatePath . '/images/delete.gif"> delete </a>
                    </td>
                    </tr>';
		}

		echo '</table></p>';
	}

	function showCourse($item) {
		$function = __FUNCTION__;
		$title = 'View course information';
		$description = 'Overview of all courses currently on offer';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);

		$sql = "SELECT * FROM `courses`, `basic-information` WHERE `courses`.ID = $item AND `courses`.CourseCoordinator = `basic-information`.ID";
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
                    <td>' . $fetch[2] . '</td>
                    <td></td>
                  </tr>
                  <tr>
                    <td><strong>Course coordinator</strong></td>
                    <td><a href="' . $this->core->conf['conf']['path'] . 'information/view/' . $fetch[3] . '">' . $fetch[4] . ' ' . $fetch[6] . '</a></td>
                    <td></td>
                  </tr>
                  <tr>
                    <td><strong>Course enrollment</strong></td>
                    <td>COUNT</td>
                    <td></td>
                  </tr>';

		}

		echo '</table></p>';
	}
}

?>
