<?php
class courses {

	public $core;
	public $view;
	public $item = NULL;

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
		$this->item = $this->core->cleanGet['item'];

		if ($this->core->action == "list") {
			$sql = "SELECT * FROM `programmes`,`studies` WHERE `programmes`.ParentID = `studies`.ID AND `studies`.ID = $item ORDER BY `studies`.Name";
			$this->listCourses($sql);
		} elseif ($this->core->action == "view") {
			$sql = "SELECT * FROM `courses`, `basic-information` WHERE `courses`.ID = $item AND `courses`.CourseCoordinator = `basic-information`.ID";
			$this->showCourse($sql);
		} elseif ($this->core->action == "edit" && isset($item) && $this->core->role > 3) {
			$sql = "SELECT * FROM `courses` WHERE `courses`.ID = $item";
			$this->editCourse($sql);
		} elseif ($this->core->action == "add" && $this->core->role > 3) {
			$this->addCourse();
		} elseif ($this->core->action == "save" && $this->core->role > 3) {
			$this->saveCourse();
		} elseif ($this->core->action == "delete" && isset($item) && $this->core->role > 3) {
			$this->deleteCourse($item);
		} else {
			$sql = "SELECT * FROM `courses`, `basic-information` WHERE `courses`.CourseCoordinator = `basic-information`.ID ORDER BY `courses`.Name";
			$this->listCourses($sql);
		}
	}

	function editCourse($sql) {
		$function = __FUNCTION__;
		$title = 'Edit course';
		$description = 'Remember to save changes after you are done';

		echo component::generateBreadcrumb(get_class(), $function);
		echo component::generateTitle($title, $description);

		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_row()) {
			include $this->core->classpath . "editcourse.form.php";
		}
	}

	function addCourse() {
		$function = __FUNCTION__;
		$title = 'Add course';
		$description = 'Please enter the required fields';

		echo component::generateBreadcrumb(get_class(), $function);
		echo component::generateTitle($title, $description);

		include $this->core->formPath . "addcourse.form.php";

	}

	function deleteCourse($id) {
		$sql = 'DELETE FROM `schools`  WHERE `ID` = "' . $id . '"';
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

		$run = $this->database->doInsertQuery($sql);

		$sql = "SELECT * FROM `courses`, `programmes`,`access`,`basic-information` WHERE Dean = `access`.ID AND `access`.ID = `basic-information`.ID ORDER BY Name";
		$this->listSchools($sql);
	}

	function listCourses($sql) {
		$function = __FUNCTION__;
		$title = 'Overview of courses';
		$description = 'Overview of all courses currently on offer';

		echo component::generateBreadcrumb(get_class(), $function);
		echo component::generateTitle($title, $description);

		$run = $this->core->database->doSelectQuery($sql);

		echo '<p><a href="?id=courses&action=add">Add course</a></p>
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
                    <td><b><a href="?id=courses&action=view&item=' . $fetch[0] . '"> ' . $fetch[2] . '</a></b></td>
                    <td>
                    <a href="?id=view-information&uid=' . $fetch[3] . '">' . $fetch[4] . ' ' . $fetch[6] . '</a>
                    </td>
                    <td>
                    <a href="?id=courses&action=edit&item=' . $fetch[0] . '"> <img src="templates/default/images/edi.png"> edit</a>
                    <a href="?id=courses&action=delete&item=' . $fetch[0] . '" onclick="return confirm(\'Are you sure?\')"> <img src="templates/default/images/del.png"> delete </a>
                    </td>
                    </tr>';
		}

		echo '</table>
            </p>';
	}

	function showCourse($sql) {
		$function = __FUNCTION__;
		$title = 'View course information';
		$description = 'Overview of all courses currently on offer';

		echo component::generateBreadcrumb(get_class(), $function);
		echo component::generateTitle($title, $description);

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
                    <td><a href="?id=view-information&uid=' . $fetch[3] . '">' . $fetch[4] . ' ' . $fetch[6] . '</a></td>
                    <td></td>
                  </tr>
                  <tr>
                    <td><strong>Course enrollment</strong></td>
                    <td>COUNT</td>
                    <td></td>
                  </tr>';

		}

		echo '</table>
            </p>';

	}

}

?>
