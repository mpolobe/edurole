<?php
class discount {

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
		echo'<div class="toolbar">
			<a href="' . $this->core->conf['conf']['path'] . '/discount/add">Add Discount</a>
			<a href="' . $this->core->conf['conf']['path'] . '/discount/print">Print Discount</a>
		</div>';
	}

	function addDiscount() {
		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
		
		$select = new optionBuilder($this->core);
		$owner = $select->showUsers("100", null);

		include $this->core->conf['conf']['formPath'] . "adddiscount.form.php";
	}

	function deleteDiscount($item) {
		$sql = 'DELETE FROM `discount` WHERE `StudentID` = "' . $item . '"';
		$run = $this->core->database->doInsertQuery($sql);

		$this->core->redirect("discount", "manage");
	}

	function saveDiscount($item) {
		$studentid = $this->core->cleanPost['uid'];
		$staffid = $this->core->cleanPost['sid'];
		$percentage = $this->core->cleanPost['percent'];

		if (!empty($item)) {
			$sql = "UPDATE `discount` SET `StudentID` = '$uid', `StaffID` = '$sid', `Percentage` = '$percentage' WHERE `StudentID` = $item;";
			$run = $this->core->database->doInsertQuery($sql);
		} else {
			$sql = "INSERT INTO `discount` (`StudentID`, `Percentage`, `StaffID`) VALUES ('$studentid', '$percentage', '$staffid');";
			$run = $this->core->database->doInsertQuery($sql);
		}
	
		$this->core->redirect("discount", "manage");
	}

	function editDiscount($item) {
		$sql = "SELECT *
			FROM `discount` 
			WHERE `discount`.StudentID = $item";

		$run = $this->core->database->doSelectQuery($sql);

		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
		
		$select = new optionBuilder($this->core);

		if ($fetch = $run->fetch_row()) {

			$name = $fetch[1];
			$description = $fetch[2];

			$select = new optionBuilder($this->core);
			$owner = $select->showUsers("100", null);


			include $this->core->conf['conf']['formPath'] . "editdiscount.form.php";
		}
	}


	public function printDiscount(){
		$this->manageDiscount(TRUE);
	}

	public function manageDiscount($print = FALSE) {
		
		$this->viewMenu();
		$sql = "SELECT `Percentage`, `Students`.`Status`, `StudentID`, `StaffID`, `Students`.`FirstName`, `Students`.`Surname`, `Staff`.`FirstName` as `StaffFirstName`, `Staff`.`Surname` as `StaffSurname` FROM `discount`
			LEFT JOIN `basic-information` AS `Students` ON `Students`.ID = `discount`.StudentID
			LEFT JOIN `basic-information` AS `Staff` ON `Staff`.ID = `discount`.StaffID";
		
		$run = $this->core->database->doSelectQuery($sql);

		echo '<table class="table table-bordered table-striped table-hover">'.
		'<thead>'.
			'<tr><td><b>Student</b></td>' .
			'<td><b>Status</b></td>' .
			'<td><b>Student ID</b></td>' .
			'<td><b>Percentage</b></td>' .
			'<td><b>Requested by</b></td>' .
			'<td><b>Options</b></td>' .
			'</tr>'.
		'</thead>
		<tbody>';

		$i = 0;
		while ($row = $run->fetch_assoc()) {

			
			$delete = '<a href="' . $this->core->conf['conf']['path'] . '/discount/delete/' . $row['StudentID'] . '" onclick="return confirm(\'Are you sure?\')"> <img src="'.$this->core->fullTemplatePath.'/images/del.png"> delete </a>';
		
			echo '<tr>'.
			'<td><b><a href="' . $this->core->conf['conf']['path'] . '/information/show/' . $row['StudentID'] . '"> ' . $row['FirstName'] . ' ' . $row['Surname'] . '</a></b></td>' .
			'<td>' .  $row['Status'] . '</td>' .
			'<td>' .  $row['StudentID'] . '</td>' .
			'<td><b>' .  $row['Percentage'] . '%</b></td>' .
			'<td> ' . $row['StaffFirstName'] . ' ' . $row['StaffSurname'] . '</td>' .
			'<td>'.
			'<a href="' . $this->core->conf['conf']['path'] . '/discount/edit/' . $row['StudentID'] . '"> <img src="'.$this->core->fullTemplatePath.'/images/edi.png"> edit</a>'.
			$delete .
			'</td>'.
			'</tr>';

			$current = $delivery;
		}

		echo '</tbody>
		</table>';
	}
}
?>
