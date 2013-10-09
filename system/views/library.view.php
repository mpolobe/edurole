<?php
class library {

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
		$access = $_SESSION['access'];
		include $this->core->conf['conf']['classPath'] . "files.inc.php";

		if ($this->core->action == "manage" || !isset($this->core->action) && $access > 100) {

			$this->manageBooks();

		} elseif ($this->core->action == "overview" || !isset($this->core->action) || !isset($this->core->action) && $access >= 10) {
			$function = __FUNCTION__;
			$title = 'Overview of personal assignments';
			$description = 'View your books on loan from the library';

			echo $this->core->breadcrumb->generate(get_class(), $function);
			echo component::generateTitle($title, $description);

			$this->viewLoans();
		}
	}

	function viewLoans() {

		global $connection;


		$sql = "SELECT * FROM `assignments`, `courses`, `basic-information` WHERE  `courses`.ID = CourseID AND `assignments`.CreatorID = `basic-information`.ID ORDER BY DateCreated";

		if (!$pep = mysql_query($sql, $connection)) {
			die('Error: ' . mysql_error());
		}

		$init = TRUE;

		while ($fetch = mysql_fetch_row($pep)) {

			$grade = $fetch[3];
			$firstname = $fetch[16];
			$lastname = $fetch[18];
			$studentno = $fetch[8];
			$assignmentid = $fetch[0];
			$assignmentname = $fetch[2];
			$assignmentfile = $fetch[9];
			$batchdescription = $fetch[3];
			$uid = $fetch[20];
			$date = $fetch[11];


			echo '<div style="border:solid 1px #ccc; padding-left: 10px; margin-bottom: 4px;">
				<table width="756">' .
				'<tr>' .
				'<td width="200px"><b>Book name:</b></td>' .
				'<td width="150px"><b>Written by</b></td>' .
				'<td width="200px"><b>Return book before</b></td>' .
				'<td width="100px"><b></td>' .
				'</tr>';
			echo '<tr>' .
				'<td><a href="' . $this->core->conf['conf']['path'] . 'courses/view/' . $assignmentid . '"><b>' . $assignmentname . '</b></a></td>' .
				'<td>' . $firstname . ' ' . $lastname . '</a>' .
				'<td>' . $date . '</td>' .
				'<td>
				<b><a href="' . $this->core->conf['conf']['path'] . 'studies/edit/' . $fetch[0] . '"> Submit result </a></b>
				</td>' .
				'</tr></table></div>';


		}

		echo '</table></div></p>';

	}

	function manageBooks() {

		$function = __FUNCTION__;
		$title = 'Overview of all assignments';
		$description = 'Manage assignments for students to view';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);

		$sql = "SELECT * FROM `assignments`, `courses`, `basic-information` WHERE  `courses`.ID = CourseID AND `assignments`.CreatorID = `basic-information`.ID ORDER BY DateCreated";
		$run = $this->core->database->doSelectQuery($sql);

		$init = TRUE;

		while ($fetch = $run->fetch_row()) {

			$grade = $fetch[3];
			$firstname = $fetch[16];
			$lastname = $fetch[18];
			$studentno = $fetch[8];
			$assignmentid = $fetch[0];
			$assignmentname = $fetch[2];
			$assignmentfile = $fetch[9];
			$batchdescription = $fetch[3];
			$uid = $fetch[20];
			$date = $fetch[11];


			echo '<div style="border:solid 1px #ccc; padding-left: 10px; margin-bottom: 4px;">
				<table width="756">' .
				'<tr>' .
				'<td width="200px"><b>Assignment name:</b></td>' .
				'<td width="150px"><b>Assigned by</b></td>' .
				'<td width="200px"><b>Deadline for submission</b></td>' .
				'<td width="100px"><b></td>' .
				'</tr>';
			echo '<tr>' .
				'<td><a href="' . $this->core->conf['conf']['path'] . 'courses/view/' . $assignmentid . '"><b>' . $assignmentname . '</b></a></td>' .
				'<td><a href="' . $this->core->conf['conf']['path'] . 'information/view/' . $uid . '">' . $firstname . ' ' . $lastname . '</a></td>' .
				'<td>' . $date . '</td>' .
				'<td>
				<b><a href="' . $this->core->conf['conf']['path'] . 'studies/edit/' . $fetch[0] . '"> Submit result </a></b>
				</td>' .
				'</tr></table></div>';


		}

		echo '</table></div></p>';

	}
}

?>