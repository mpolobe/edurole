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
		'<td><b>Description</b></td>' .
		'<td><b>Period</b></td>' .
		'<td><b>Total</b></td>' .
		'<td><b>Management tools</b></td>' .
		'</tr>';
	}

	function addFeepackages() {
		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
		
		$select = new optionBuilder($this->core);
		$schools = $select->showSchools();
		$owner = $select->showUsers("100", null);
		$periods = $select->showPeriods();

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
		$period = $this->core->cleanPost['period'];

		if (!empty($item)) {
			$sql = "UPDATE `fee-package` SET `Name` = '$name', `Description` = '$description', `Owner` = '$owner', `PeriodID` = '$period' WHERE `ID` = $item;";
			$run = $this->core->database->doInsertQuery($sql);
		} else {
			$sql = "INSERT INTO `fee-package` (`ID`, `Name`, `Description`, `Date`, `Owner`, `PeriodID`) VALUES (NULL, '$name', '$description', NOW(), '$owner', '$period');";
			$run = $this->core->database->doInsertQuery($sql);
		}
	
		$this->core->redirect("feepackages", "manage");
	}

	function editFeepackages($item) {
		$sql = "SELECT `fee-package`.ID, `fee-package`.Name, `fee-package`.Description, `fee-package`.Owner , `fee-package`.PeriodID
			FROM `fee-package` WHERE `fee-package`.ID = $item";

		$run = $this->core->database->doSelectQuery($sql);

		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
		
		$select = new optionBuilder($this->core);

		if ($fetch = $run->fetch_row()) {

			$name = $fetch[1];
			$owner = $select->showUsers(NULL, $fetch[3]);
			$periods = $select->showPeriods(NULL, $fetch[3]);
			$description = $fetch[2];

			include $this->core->conf['conf']['formPath'] . "editfeepackage.form.php";
		}
	}

	public function manageFeepackages($item=null) {
		
		$this->viewMenu();

		$sql = "SELECT `fee-package`.ID, `fee-package`.Name as Name, SUM(`fees`.Amount) Total, `fee-package`.Description, `periods`.Name as Period, `periods`.Delivery as Delivery
			FROM `fee-package` 
			LEFT JOIN `fees` ON `fees`.PackageID = `fee-package`.ID 
			LEFT JOIN `periods` ON `fee-package`.PeriodID = `periods`.ID
			GROUP BY `fee-package`.ID
			ORDER BY `periods`.Delivery";
		
		$run = $this->core->database->doSelectQuery($sql);

		//echo $run->num_rows;

		$i = 0;
		while ($row = $run->fetch_assoc()) {

			$delivery = $row['Delivery'];
			if($current != $delivery){
				echo '<tr class="heading">
					<td colspan="5">'.$delivery.'</td>
				</tr>';
			}

			if ($i == 0) {
				$bgc = 'class="zebra"';
				$i++;
			} else {
				$bgc = '';
				$i--;
			}

			$total = $row['Total'] > 0 ? $row['Total'] : 0;
			
			$delete = '<a href="' . $this->core->conf['conf']['path'] . '/feepackages/delete/' . $row['ID'] . '" onclick="return confirm(\'Are you sure?\')"> <img src="'.$this->core->fullTemplatePath.'/images/del.png"> delete </a>';
			if($row[0] == 1){ $delete = ""; $bgc = 'style="background-color: #EEE;"'; }

			echo '<tr ' . $bgc . '>'.
			'<td><b><a href="' . $this->core->conf['conf']['path'] . '/fees/show/' . $row['ID'] . '"> ' . $row['Name'] . '</a></b></td>' .
			'<td>' .  $row['Description'] . '</td>' .
			'<td>' .  $row['Period'] . '</td>' .
			'<td>K' .  $total . '</td>' .
			'<td>'.
			'<a href="' . $this->core->conf['conf']['path'] . '/feepackages/edit/' . $row['ID'] . '"> <img src="'.$this->core->fullTemplatePath.'/images/edi.png"> edit</a>'.
			$delete .
			'</td>'.
			'</tr>';

			$current = $delivery;
		}

		echo '</table>
		</p>';
	}
}
?>
