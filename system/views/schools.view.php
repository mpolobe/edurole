<?php
class schools {

	public $core;
	public $view;
	public $item = NULL;

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

	public function editSchools($item) {
		$sql = "SELECT * FROM `schools` WHERE ID = $item";
		$run = $this->core->database->doSelectQuery($sql);

		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
		$select = new optionBuilder($this->core);
		$dean = $select->showUsers("100", null);
		
		while ($fetch = $run->fetch_row()) {
			include $this->core->conf['conf']['formPath'] . "editschool.form.php";
		}
	}

	public function addSchools() {
		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
		$select = new optionBuilder($this->core);
		$dean = $select->showUsers("100", null);
	
		include $this->core->conf['conf']['formPath'] . "addschool.form.php";
	}

	public function deleteSchools($item) {
		$sql = 'DELETE FROM `schools`  WHERE `ID` = "' . $item . '"';
		$run = $this->core->database->doInsertQuery($sql);

		$this->core->redirect("schools", "manage", NULL);
	}

	public function saveSchools() {
		$item = $this->core->item;
		$name = $this->core->cleanPost['name'];
		$dean = $this->core->cleanPost['dean'];
		$description = $this->core->cleanPost['description'];

		if (!empty($item)) {
			$sql = "UPDATE `schools` SET `Description` = '$description', `Name` = '$name', `Dean` = '$dean' WHERE `ID` = $item;";
		} else {
			$sql = "INSERT INTO `schools` (`ID`, `ParentID`, `Established`, `Name`, `Description`, `Dean`) VALUES (NULL, '0', CURRENT_DATE(), '$name', '$description', '$dean');";
		}

		$run = $this->core->database->doInsertQuery($sql);
		
		$this->core->redirect("schools", "manage", NULL);
	}

	public function manageSchools($item = null) {
		$sql = "SELECT * FROM `schools` 
			LEFT JOIN `access` ON Dean = `access`.ID
			LEFT JOIN `basic-information` ON `access`.ID = `basic-information`.ID 
			ORDER BY Name";

		$run = $this->core->database->doSelectQuery($sql);

		echo'<div class="toolbar"><a href="' . $this->core->conf['conf']['path'] . '/schools/add">Add school</a></div>
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

			echo'<tr ' . $bgc . '>
                <td><b><a href="' . $this->core->conf['conf']['path'] . '/schools/show/' . $fetch[0] . '"> ' . $fetch[3] . '</a></b></td>
				<td><a href="' . $this->core->conf['conf']['path'] . '/information/show/' . $fetch[14] . '">' . $fetch[10] . ' ' . $fetch[12] . '</a></td>
				<td>
				<a href="' . $this->core->conf['conf']['path'] . '/schools/edit/' . $fetch[0] . '"> <img src="'.$this->core->fullTemplatePath.'/images/edi.png"> edit</a>
                <a href="' . $this->core->conf['conf']['path'] . '/schools/delete/' . $fetch[0] . '" onclick="return confirm(\'Are you sure?\')"> <img src="'.$this->core->fullTemplatePath.'/images/del.png"> delete </a>
                </td>
                </tr>';
		}

		echo '</table>';
	}

	public function showSchools($item) {

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
                	     <a href="' . $this->core->conf['conf']['path'] . '/information/show/' . $fetch[14] . '">' . $fetch[10] . ' ' . $fetch[12] . '</a></td>
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
