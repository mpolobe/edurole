<?php
class roles {

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

	private function viewMenu(){
		echo '<div class="toolbar"><a href="' . $this->core->conf['conf']['path'] . '/roles/manage">Manage Roles</a>
		<a href="' . $this->core->conf['conf']['path'] . '/roles/add">Add Roles</a>
		<a href="' . $this->core->conf['conf']['path'] . '/roles/groups">Manage Permission Groups</a>
		<a href="' . $this->core->conf['conf']['path'] . '/roles/add/group">Add Permission Group</a></div>';
	}

	function editRoles($item) {
		$sql = "SELECT * FROM `roles` WHERE `roles`.ID = $item";
		$run = $this->core->database->doSelectQuery($sql);

		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
		$optionBuilder = new optionBuilder($this->core);

		while ($fetch = $run->fetch_assoc()) {
			$name = $fetch['RoleName'];
			$description = $fetch['RoleLevel'];
			$step = $fetch['RoleGroup'];

			$staff = $optionBuilder->showUsers(99, $user);

			include $this->core->conf['conf']['formPath'] . "editrole.form.php";
		}
	}

	function groupeditRoles($item) {
		$sql = "SELECT * FROM `permissions` WHERE ID = $item";
		$run = $this->core->database->doSelectQuery($sql);

		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
		$optionBuilder = new optionBuilder($this->core);

		while ($fetch = $run->fetch_assoc()) {
			$name = $fetch['RoleName'];
			$description = $fetch['RoleLevel'];
			$roles = $fetch['RoleHigh'];

			include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
			$optionBuilder = new optionBuilder($this->core);
			$roles = $optionBuilder->showRoles();
			include $this->core->conf['conf']['formPath'] . "addrolegroup.form.php";
		}
	}

	function addRoles() {
		$item = $this->core->item;
		if($item == "group"){
			include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
			$optionBuilder = new optionBuilder($this->core);
			$roles = $optionBuilder->showRoles();
			include $this->core->conf['conf']['formPath'] . "addrolegroup.form.php";
		}else{
			include $this->core->conf['conf']['formPath'] . "addrole.form.php";
		}
	}

	function deleteRoles($item) {
		$sql = 'DELETE FROM `roles` WHERE `ID` = "' . $item . '"';
		$run = $this->core->database->doSelectQuery($sql);

		$this->core->redirect("roles", "manage", NULL);
	}

	function groupdeleteRoles($item) {
		$sql = 'DELETE FROM `permissions` WHERE `ID` = "' . $item . '"';
		$run = $this->core->database->doSelectQuery($sql);

		$this->core->redirect("roles", "groups", NULL);
	}

	function saveRoles() {
		$name = $this->core->cleanPost['name'];
		$level = $this->core->cleanPost['level'];
		$group = $this->core->cleanPost['group'];
		$item = $this->core->cleanPost['item'];
		$owner = $this->core->cleanPost['hod'];

		if (!empty($item)) {
			$sql = "UPDATE `roles` SET 
			`RoleName` = '$name', 
			`RoleLevel` = '$level', 
			`RoleManager` = '$owner', 
			`RoleGroup` = '$level'  
			WHERE `ID` = $item;";
		} else {
			$sql = "INSERT INTO `roles` (`ID`, `RoleName`,  `RoleLevel`, `RoleGroup`, `RoleManager`) VALUES (NULL, '$name', '$level', '$group', '$owner');";
		}

		
	
		$run = $this->core->database->doInsertQuery($sql);

		$this->manageRoles();
	}


	function groupsaveRoles() {
		$name = $this->core->cleanPost['name'];
		$level = $this->core->cleanPost['level'];
		$high = $this->core->cleanPost['high'];
		$low = $this->core->cleanPost['low'];

		if (!empty($item)) {
			$sql = "UPDATE `roles` SET `RoleName` = '$name', `RoleLevel` = '$level', 'RoleGroup' = '$level'  WHERE `ID` = $item;";
		} else {
			$sql = "INSERT INTO `permissions` (`ID`, `RequiredRoleMin`, `RequiredRoleMax`, `PermissionDescription`) VALUES ('$level', '$low', '$high', '$name');";
		}
	
		$run = $this->core->database->doInsertQuery($sql);

		$this->core->redirect("roles", "groups", NULL);
	}



	public function permittedRoles($item) {
			$uid = $this->core->userID;

			foreach(array_keys($_POST['selected']) as $id){
				$functions["$id"]["selected"] = $_POST['selected'][$id];
				$functions["$id"]["menu"] = $_POST['menu'][$id];
			}

			$sql = "";

			foreach($functions as $setting){
		
				$sql = "INSERT INTO `functions-permissions` (`ID`, `FunctionID`, `RoleID`, `Admin`, `Added`) VALUES (NULL, '". $setting["selected"] ."', '$item', '$uid', NOW());";
				$this->core->database->doInsertQuery($sql);
			}


			$this->permissionsRoles($item);

	}


	public function functionsRoles($item) {
		$this->viewMenu();

		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
		$select = new optionBuilder($this->core);
		$rolesList = $select->showPermissions();

		$sql = "SELECT * FROM `functions` ORDER BY `Class`, `Function`";
		$run = $this->core->database->doSelectQuery($sql);

		$i = 0;

		echo'<form id="save" name="permissions" method="POST" action="'.$this->core->conf['conf']['path'].'/roles/permitted/'.$item.'">
		<div class="easymencontainer"><input type="submit" class="submit" value="ADD SELECTED FUNCTIONS" />';

		$modules = 0;
		$actions = 0;
		$cc = 0;

		$current = NULL;

		$classes = array();
		$functions = array();

		$dir = 'system/views';
		if ($handle = opendir($dir)) {

			$files = scandir($dir);

			foreach ($files as $file) {

				if ($file != "." && $file != ".." && $file != "settings.view.php" && $file != "menu.inc.php" && $file != "error.view.php") {

					include_once $file;
					$name = explode(".",$file);
					$classes[] = ucwords($name[0]);

					$functionlist = get_class_methods($name[0]);

					$functionarray = array();
					foreach($functionlist as $function){
						if($function == "configView" || $function == "buildView" || $function == "viewMenu"){
							continue;
						}

						$functionarray[] = str_replace(ucwords($name[0]), "", $function);
					}

					$functions[$name[0]] = $functionarray;
					$cc++;

				}
			}

			$functionlist = get_class_methods("settings");
			foreach($functionlist as $function){
				if($function == "configView" || $function == "buildView" || $function == "viewMenu"){
					continue;
				}

				$functionarray[] = str_replace(ucwords("settings"), "", $function);
			}

			$classes[] = "Settings";
			$functions["settings"] = $functionarray;

		}

		while ($fetch = $run->fetch_row()) {

			$roles = $select->buildSelect($rolesList, $fetch[4]);

			if($current != $fetch[1]){
				$class=ucwords($fetch[1]);

				if(!in_array($class, $classes)){
					continue;
				} else {
					$functionlist = $functions[$fetch[1]];
				}

				echo '</div><div class="easymencontainer">';

                                echo'<div style="clear:both"><div class="label"  style="width:70px;"><h3>' . $class . '</h3></div><br>
                                <div class="label"  style="width:510px;"><i>Title</i></div>
                                <div class="label" style="width:30px;"><i>ADD</i></div>
                                </div>';

				$modules++;
			} else {
				$class="";
			}

			$menu = $fetch[9];
			if(empty($menu)){
				$menu = "";
			}

			$function = $fetch[2];
			$exists = FALSE;

			foreach($functionlist as $functionfromdb){
				if($function == $functionfromdb){
					$exists = TRUE;
				}
			}

			if ($exists){
				echo'  <div style="clear:both"><div class="label"  style="width:70px;"><b>' . ucwords($fetch[2]) . '</b></div>
                                <div class="label" style="width:320px;">' . $fetch[7] . ' &nbsp; </div>
                                <div class="label" style="width:130px;"><input type="checkbox" name="selected[' . $fetch[0] . ']" value="' . $fetch[0] . '"></div>
                                </div>';
			} else{
				continue;
			}

			$current = $fetch[1];
			$actions++;
		}

		echo'</div><div class="easymencontainer"><input type="submit" class="submit" value="ADD SELECTED FUNCTIONS" />
		<div class="label"  style="width:170px;">Installed modules: <b>' . $modules . '</b> 
		<br>Total number of functions: <b>' . $actions . '</b></div>
		</form></div>';

	}


	function permissionsRoles($item) {
		$this->viewMenu();

		if($item == 'delete'){
			$function = $this->core->subitem;
			$item = $this->core->subsubitem;

			$sql = "DELETE FROM `functions-permissions`  WHERE `FunctionID` = '$function' AND  `RoleID` = '$item'";
			$this->core->database->doSelectQuery($sql);
		}

		echo '<div class="toolbar">
			<a href="' . $this->core->conf['conf']['path'] . '/roles/functions/'.$item.'">Add rights to selected role</a>
		</div>';

		$sql = "SELECT * FROM `functions-permissions`
			LEFT JOIN `functions` ON `functions-permissions`.FunctionID = `functions`.ID
			LEFT JOIN `basic-information` ON `functions-permissions`.Admin = `basic-information`.ID
			WHERE `functions-permissions`.RoleID = '$item'";
			
		$run = $this->core->database->doSelectQuery($sql);

		echo'<table class="table table-bordered table-striped table-hover">
			<thead>
				<tr>
					<td><b>Function</b></td>
					<td><b>Added by</b></td>
					<td><b>Management tools</b></td>
				</tr>
			</thead>';

		$i = 0;
		while ($fetch = $run->fetch_assoc()) {

			$class = $fetch['Class'];
			$function = $fetch['Function'];
			$title = $fetch['FunctionTitle'];

			if($title == ""){
				$title = "No Name";
			}


			echo '<tr>
        		<td><a href="' . $this->core->conf['conf']['path'] . '/roles/permissions/' . $fetch['FunctionID'] . '"><b>'.$class.' / '.$function.'</b></a> (' . $title . ')</b></td>
                    <td>' . $fetch['FirstName'] . ' ' . $fetch['Surname'] . '</td>
                    <td>
                    <a href="' . $this->core->conf['conf']['path'] . '/roles/permissions/delete/' . $fetch['FunctionID'] . '/'.$item.'" onclick="return confirm(\'Are you sure?\')"> <img src="' . $this->core->fullTemplatePath . '/images/delete.gif"> Delete </a>
                    </td>
                    </tr>';
		}

		echo '</table>';

	}

	function manageRoles() {
		$this->viewMenu();

		$sql = "SELECT `roles`.ID, `roles`.RoleName, `roles`.RoleLevel, count(`access`.ID), FirstName, Surname
			FROM `roles`
			LEFT JOIN `access` ON `access`.RoleID = `roles`.ID 
			LEFT JOIN `basic-information` ON `roles`.`RoleManager` = `basic-information`.ID 
			GROUP BY `roles`.ID";

		$run = $this->core->database->doSelectQuery($sql);

		echo'<table class="table table-bordered table-striped table-hover">
			<thead>
				<tr>
					<td><b>Role Name</b></td>
					<td><b>Number of users</b></td>
					<td><b>Head of Department</b></td>
					<td><b>Management tools</b></td>
				</tr>
			</thead>';

		$i = 0;
		while ($fetch = $run->fetch_row()) {

			echo '<tr>
        		<td>'.$fetch[0].' - <b><a href="' . $this->core->conf['conf']['path'] . '/roles/permissions/' . $fetch[0] . '">' . $fetch[1] . '</a></b></td>
                    <td>' . $fetch[3] . '</td>
			<td>' . $fetch[4] . ' ' . $fetch[5] . '</td>
                    <td>
                    <a href="' . $this->core->conf['conf']['path'] . '/roles/edit/' . $fetch[0] . '"> <img src="' . $this->core->fullTemplatePath . '/images/edi.png"> edit</a>
                    <a href="' . $this->core->conf['conf']['path'] . '/roles/delete/' . $fetch[0] . '" onclick="return confirm(\'Are you sure?\')"> <img src="' . $this->core->fullTemplatePath . '/images/delete.gif"> delete </a>
                    </td>
                    </tr>';
		}

		echo '</table>';
	}

	function groupsRoles() {
		$this->viewMenu();

		$sql = "SELECT `permissions`.ID, PermissionDescription, RequiredRoleMin, RequiredRoleMax, count(`roles`.ID)
			FROM `permissions`
			LEFT JOIN `roles` ON `permissions`.RequiredRoleMax <= `roles`.ID AND `permissions`.RequiredRoleMin >= `roles`.ID 
			GROUP BY `permissions`.ID";

		$run = $this->core->database->doSelectQuery($sql);

		echo'<table class="table table-bordered table-striped table-hover">
			<thead>
				<tr>
					<td><b>Role Name</b></td>
					<td><b>Access between</b></td>
					<td><b>Management tools</b></td>
				</tr>
			</thead>';

		$i = 0;
		while ($fetch = $run->fetch_row()) {

			echo '<tr>
        		<td>'.$fetch[0].' - <b>' . $fetch[1] . '</a></b></td>
                    <td>role ' . $fetch[2] . ' to '.$fetch[3].'</td>
                    <td>
                    <a href="' . $this->core->conf['conf']['path'] . '/roles/groupedit/' . $fetch[0] . '"> <img src="' . $this->core->fullTemplatePath . '/images/edi.png"> edit</a>
                    <a href="' . $this->core->conf['conf']['path'] . '/roles/groupdelete/' . $fetch[0] . '" onclick="return confirm(\'Are you sure?\')"> <img src="' . $this->core->fullTemplatePath . '/images/delete.gif"> delete </a>
                    </td>
                    </tr>';
		}

		echo '</table>';
	}

}
?>
