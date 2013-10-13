<?php
class users {

	public $core;
	public $view;
	public $item = NULL;

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

		if ($this->core->action == "add" && $this->core->role >= 100) {
			$this->addUser();
		} elseif ($this->core->action == "save" && $this->core->role >= 100) {
			$this->saveUser();
		} elseif ($this->core->action == "delete" && isset($this->core->item) && $core->role >= 100) {
			$this->deleteUser($this->core->item);
		} else if ($this->core->role >= 100 & $this->core->action == "students") {
			$this->showStudentList();
		} else if ($this->core->action == "saveedit") {
			$this->saveEdit();
		} else if ($core->role >= 100) {
			$this->showUserList();
		}
	}

	function saveUser() {
		$function = __FUNCTION__;
		$title = 'Add user account';
		$description = 'The account information has been saved';
		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);

		include $this->core->conf['conf']['classPath'] . "adduser.inc.php";
		$this->addUser();
	}

	function addUser() {
		$function = __FUNCTION__;
		$title = 'Add user account';
		$description = 'Please provide the needed information to create a new user account';
		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);

		include $this->core->conf['conf']['formPath'] . "adduser.form.php";
	}

	function showUserList() {

		$function = __FUNCTION__;
		$title = 'User management';
		$description = 'Overview of all users with privileges higher than student';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);

		echo '<table width="768" height="" border="0" cellpadding="3" cellspacing="0">
		<tr class="tableheader">
		<td></td>
		<td><b> Student Name</b></td>
		<td><b> Access role</b></td>
		<td><b> </b></td>
		<td><b> Status</b></td>		
		<td><b> Options</b></td>
		</tr>';

		$sql = "SELECT * FROM `basic-information`, `access`, `roles` WHERE `access`.`ID` = `basic-information`.`ID` AND `access`.`RoleID` = `roles`.`ID` AND `access`.`RoleID` >= 100 ORDER BY `basic-information`.Surname";
		$run = $this->core->database->doSelectQuery($sql);

		while ($row = $run->fetch_row()) {

			$firstname = $row[0];
			$middlename = $row[1];
			$surname = $row[2];
			$sex = $row[3];
			$uid = $row[4];
			$nrc = $row[5];
			$role = $row[26];
			$status = $row[20];

			echo '<tr>
			<td><img src=' . $this->core->fullTemplatePath . '/images/user.png"></td>
			<td><a href="' . $this->core->conf['conf']['path'] . '/information/view/' . $uid . '"><b>' . $firstname . ' ' . $middlename . ' ' . $surname . '</b></a></td>
			<td><i>' . $role . '</i></td>
			<td>' . $uid . '</td>
			<td>' . $status . '</td>
			<td><a href="' . $this->core->conf['conf']['path'] . '/information/edit/' . $uid . '"><img src="' . $this->core->fullTemplatePath . '/images/edi.png"> edit</a>  <a href="' . $this->core->conf['conf']['path'] . '/users/delete/' . $uid . '" onclick="return confirm(\'Are you sure?\')"><img src="' . $this->core->fullTemplatePath . '/images/delete.gif"> delete</a></td>
			</tr>';
		}

		echo '</table>';
	}

	function showStudentList() {
		$function = __FUNCTION__;
		$title = 'User management';
		$description = 'Overview of all students currently enrolled';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);

		echo '<table width="768" height="" border="0" cellpadding="3" cellspacing="0">
		<tr class="tableheader">
		<td></td>
		<td><b> Student Name</b></td>
		<td><b> Student ID</b></td>
		<td><b> Status</b></td>		
		<td><b> Options</b></td>
		</tr>';

		$sql = "SELECT * FROM `basic-information`, `access`, `roles` WHERE `access`.`ID` = `basic-information`.`ID` AND `access`.`RoleID` = `roles`.`ID` AND `access`.`RoleID` = 10 ORDER BY Surname";
		$run = $this->core->database->doSelectQuery($sql);

		while ($row = $run->fetch_row()) {

			$firstname = $row[0];
			$middlename = $row[1];
			$surname = $row[2];
			$sex = $row[3];
			$uid = $row[4];
			$nrc = $row[5];
			$status = $row[20];

			echo '<tr>
			<td><img src="' . $this->core->fullTemplatePath . '/images/bullet_user.png"></td>
			<td><a href="' . $this->core->conf['conf']['path'] . '/information/view/' . $uid . '"><b>' . $firstname . ' ' . $middlename . ' ' . $surname . '</b></a></td>

			<td>' . $uid . '</td>
			<td>' . $status . '</td>
			<td><a href="' . $this->core->conf['conf']['path'] . '/information/edit/' . $uid . '"><img src="' . $this->core->fullTemplatePath . '/images/edi.png"> edit</a>  <a href="' . $this->core->conf['conf']['path'] . '/users/delete/' . $uid . '" onclick="return confirm(\'Are you sure?\')"><img src="' . $this->core->fullTemplatePath . '/images/delete.gif"> delete</a></td>
		  	</tr>';

		}

		echo '</table>';
	}

	function deleteUser($id) {
		$sql = 'START TRANSACTION; 
			DELETE FROM `basic-information`  WHERE `ID` = "' . $id . '";
			DELETE FROM `access`  WHERE `ID` = "' . $id . '";
			COMMIT;';

		$run = $this->core->database->doInsertQuery($sql);

		$this->showUserList();
		$this->core->showAlert("The account has been deleted");
	}
}

?>
