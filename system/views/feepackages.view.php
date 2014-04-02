<?php
class feepackages {

	public $core;
	public $view;
	public $item = NULL;

	public function configView() {
		$this->view->header = TRUE;
		$this->view->footer = TRUE;
		$this->view->menu = TRUE;
		$this->view->javascript = array('jquery.ui.datepicker');
		$this->view->css = array();

		return $this->view;
	}

	public function buildView($core) {
		$this->core = $core;
	}

	public function viewMenu(){
		echo'<div class="toolbar"><a href="' . $this->core->conf['conf']['path'] . '/feepackages/add">Add Fee Package</a></div>'. 
		'<table width="768" height="" border="0" cellpadding="3" cellspacing="0"><tr class="tableheader"><td><b>Fee Package</b></td>' .
		'<td><b>Total Ammount</b></td>' .
		'<td><b>Management tools</b></td>' .
		'</tr>';
	}

	function addFeepackages() {
		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
		
		$select = new optionBuilder($this->core);
		$schools = $select->showSchools();
		$owner = $select->showUsers("100", null);

		include $this->core->conf['conf']['formPath'] . "addfeepackage.form.php";
	}

	function deleteFeepackages($item) {
		$sql = 'DELETE FROM `fee-package` WHERE `ID` = "' . $item . '"';
		$run = $this->core->database->doInsertQuery($sql);

		$this->core->redirect("feepackages", "manage");
	}

	function saveFeepackages($item) {
		$name = $this->core->cleanPost['name'];
		$description = $this->core->cleanPost['description'];
		$owner = $this->core->cleanPost['owner'];

		if (!empty($item)) {
			$sql = "UPDATE `fee-package` SET `Name` = '$name', `Description` = '$description', `Owner` = '$owner' WHERE `ID` = $item;";
			$run = $this->core->database->doInsertQuery($sql);
		} else {
			$sql = "INSERT INTO `fee-package` (`ID`, `Name`, `Description`, `Date`, `Owner`) VALUES (NULL, '$name', '$description', NOW(), '$owner');";
			$run = $this->core->database->doInsertQuery($sql);
		}

		$this->core->redirect("feepackages", "manage");
	}

	function editFeepackages($item) {
		$sql = "SELECT `fee-package`.ID, `fee-package`.Name, `fee-package`.Description, `fee-package`.Owner FROM `fee-package` LEFT JOIN `fees` ON `fees`.PackageID = `fee-package`.ID GROUP BY `fees`.PackageID";

		$run = $this->core->database->doSelectQuery($sql);

		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
		
		$select = new optionBuilder($this->core);

		if ($fetch = $run->fetch_row()) {

			$name = $fetch[1];
			$owner = $select->showUsers(NULL, $fetch[3]);
			$description = $fetch[2];

			include $this->core->conf['conf']['formPath'] . "editfeepackage.form.php";
		}
	}

	public function manageFeepackages($item=null) {
		
		$this->viewMenu();

		$sql = "SELECT `fee-package`.ID, `fee-package`.Name, SUM(`fees`.Amount) FROM `fee-package` LEFT JOIN `fees` ON `fees`.PackageID = `fee-package`.ID GROUP BY `fees`.PackageID";
		
		$run = $this->core->database->doSelectQuery($sql);

		$i = 0;
		while ($row = $run->fetch_row()) {

			if ($i == 0) {
				$bgc = 'class="zebra"';
				$i++;
			} else {
				$bgc = '';
				$i--;
			}

			$total = $row[2] > 0 ? $row[2] : 0;

			echo '<tr ' . $bgc . '>'.
			'<td><b><a href="' . $this->core->conf['conf']['path'] . '/fees/show/' . $row[0] . '"> ' . $row[1] . '</a></b></td>' .
			'<td>' .  $total . '</td>' .
			'<td>'.
			'<a href="' . $this->core->conf['conf']['path'] . '/feepackages/edit/' . $row[0] . '"> <img src="'.$this->core->fullTemplatePath.'/images/edi.png"> edit</a>'.
			'<a href="' . $this->core->conf['conf']['path'] . '/feepackages/delete/' . $row[0] . '" onclick="return confirm(\'Are you sure?\')"> <img src="'.$this->core->fullTemplatePath.'/images/del.png"> delete </a>'.
			'</td>'.
			'</tr>';
		}

		echo '</table>
		</p>';
	}
}
?>
