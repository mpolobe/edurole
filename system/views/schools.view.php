<?php
class schools {

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

		if (empty($this->core->action) && $this->core->role > 102 || $this->core->action == "management" && $this->core->role > 102) {
			$this->listSchools();
		} elseif ($this->core->action == "view" && isset($this->core->item)) {
			$this->showSchool($this->core->item);
		} elseif ($this->core->action == "edit" && isset($this->core->item) && $this->core->role > 104) {
			$this->editSchool($this->core->item);
		} elseif ($this->core->action == "add" && $this->core->role > 104) {
			$this->addSchool();
		} elseif ($this->core->action == "save" && $this->core->role > 104) {
			$this->saveSchool();
		} elseif ($this->core->action == "delete" && isset($this->core->item) && $this->core->role > 104) {
			$this->deleteSchool($this->core->item);
		}
	}

	function editSchool($item) {
		$function = __FUNCTION__;
		$title = 'Edit School';
		$description = 'Remember to save any changes you make';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);
		
		$sql = "SELECT * FROM `schools` WHERE ID = $item";
		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_row()) {
			include $this->core->conf['conf']['formPath'] . "editschool.form.php";
		}
	}

	function addSchool() {
		$function = __FUNCTION__;
		$title = 'Add School';
		$description = 'Use the following form to create new schools';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);

		include $this->core->conf['conf']['formPath'] . "addschool.form.php";
	}

	function deleteSchool($item) {
		$sql = 'DELETE FROM `schools`  WHERE `ID` = "' . $item . '"';
		$run = $this->core->database->doInsertQuery($sql);

		$this->listSchools();
		$this->core->showAlert("The school has been deleted");
	}

	function saveSchool() {
		$item = $this->core->cleanPost['item'];
		$name = $this->core->cleanPost['name'];
		$dean = $this->core->cleanPost['dean'];
		$description = $this->core->cleanPost['description'];

		if (isset($item)) {
			$sql = "UPDATE `schools` SET `Description` = '$description', `Name` = '$name', `Dean` = '$dean' WHERE `ID` = $item;";
		} else {
			$sql = "INSERT INTO `schools` (`ID`, `ParentID`, `Established`, `Name`, `Description`, `Dean`) VALUES (NULL, '0', CURRENT_DATE(), '$name', '$description', '$dean');";
		}

		$run = $this->core->database->doInsertQuery($sql);
		
		$this->listSchools();
	}

	function listSchools($item) {
		$function = __FUNCTION__;
		$title = 'Overview of schools';
		$description = 'The following schools currently exist in the system';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);

		$sql = "SELECT * FROM `schools`,`access`,`basic-information` WHERE Dean = `access`.ID AND `access`.ID = `basic-information`.ID ORDER BY Name";
		$run = $this->core->database->doSelectQuery($sql);

		echo '<p><b>Overview of all schools</b>  | <a href="' . $this->core->conf['conf']['path'] . 'schools/add">Add school</a></p><p>
            <table width="768" height="" border="0" cellpadding="3" cellspacing="0">
            <tr class="tableheader">
            <td width="350px"><b>School</b></td>
            <td width="180px"><b>Dean</b></td>
            <td width="170px"><b>Management tools</b></td>
            </tr>';

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
                    <td><b><a href="' . $this->core->conf['conf']['path'] . '/schools/view/' . $fetch[0] . '"> ' . $fetch[3] . '</a></b></td>' .
				'<td><a href="' . $this->core->conf['conf']['path'] . '/information/view/' . $fetch[14] . '">' . $fetch[10] . ' ' . $fetch[12] . '</a></td>' .
				'<td>
				<a href="' . $this->core->conf['conf']['path'] . '/schools/edit/' . $fetch[0] . '"> <img src="'.$this->core->fullTemplatePath.'/images/edi.png"> edit</a>
                    <a href="' . $this->core->conf['conf']['path'] . '/schools/delete/' . $fetch[0] . '" onclick="return confirm(\'Are you sure?\')"> <img src="'.$this->core->fullTemplatePath.'/images/del.png"> delete </a>
                    </td>
                    </tr>';

		}

		echo '</table>
            </p>';
	}

	function showSchool($item) {

		$function = __FUNCTION__;
		$title = 'School information';
		$description = 'The following attributes are saved in the school profile';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);
			
		$sql = "SELECT * FROM `schools`,`access`,`basic-information` WHERE Dean = `access`.ID AND `access`.ID = `basic-information`.ID AND `schools`.ID = $item";

		$run = $this->core->database->doSelectQuery($sql);

		$i = 0;
		while ($fetch = $run->fetch_row()) {

			if ($i == 0) {
				$bgc = 'class="zebra"';
				$i++;
			} else {
				$bgc = '';
				$i--;
			}

			echo '<table width="768" border="0" cellpadding="5" cellspacing="0">
                  <tr>
                    <td width="205" height="28" bgcolor="#EEEEEE"><strong>Information</strong></td>
                    <td width="200" bgcolor="#EEEEEE"></td>
                    <td  bgcolor="#EEEEEE"></td>
                  </tr>
                  <tr>
                    <td><strong>School name </strong></td>
                    <td>'. $fetch[3] .'</td>
                    <td></td>
                  </tr>
                  <tr>
                    <td><strong>Dean/Rector of school</strong></td>
                    <td>
                     <a href="' . $this->core->conf['conf']['path'] . 'information/view/' . $fetch[14] . '">' . $fetch[10] . ' ' . $fetch[12] . '</a></td>
                    <td></td>
                  </tr>
                  <tr>
                    <td><strong>Optional description</strong></td>
                    <td>
                            <textarea rows="4" cols="37" name="description">' . $fetch[4] . '</textarea>
                      </td>
                    <td></td>
                  </tr>
                </table>';

		}

	}
}

?>
