<?php
class audit{

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
		echo '<div class="toolbar">'.
		'<a href="' . $this->core->conf['conf']['path'] . '/audit/grades">Grades Audit Trail</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/audit/transactions">Transactions Audit Trail</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/audit/registration">Registration Audit Trail</a>'.
		'</div>';
	}

	public function transactionsAudit($item) {

		$this->viewMenu();

		echo'<table id="results" class="table table-bordered table-striped table-hover">' .
			'<thead><tr>' .
			'<td><b>Time</b></td>' .
			'<td><b>Owner</b></td>' .
			'<td><b>Action</b></td>' .
			'<td><b>Student</b></td>' .
			'<td><b>Details</b></td>' .
			'</tr></thead><tbody>';
		

		if(empty($item)){
			$sql = "SELECT * FROM `audit`, `basic-information` WHERE `AuditType` = 'payments' AND `basic-information`.ID = `UserID`";
		} 

		$run = $this->core->database->doSelectQuery($sql);
		$i = 0; $c = 1;
		while ($row = $run->fetch_assoc()) {

			$action = $row["Action"];
			$reference =$row["ReferenceID"];
			$data = $row["AuditData"];
	
			echo'<tr >' . 
				'<td>'.$c.' -  <i>' . $row["DateTime"] . ' </i></td>' .
				'<td><b><a href="' . $this->core->conf['conf']['path'] . '/information/show/' . $row["UserID"] . '">' . $row["FirstName"] . ' ' . $row["Surname"] . '</a></b></td>' .
				'<td><b>' . $row["Action"] . ' </b></td>' .
				'<td><a href="' . $this->core->conf['conf']['path'] . '/information/show/' . $reference . '">' . $reference . '</a></td>' .
				'<td>' . $data . ' </td>' .
				'</tr>';

			$c++;
		}

		echo '</tbody></table></p>';
	
	}

	public function registrationAudit($item) {

		$this->viewMenu();

		echo'<table id="results" class="table table-bordered table-striped table-hover">' .
			'<thead><tr>' .
			'<td><b>Time</b></td>' .
			'<td><b>Owner</b></td>' .
			'<td><b>Action</b></td>' .
			'<td><b>Student</b></td>' .
			'<td><b>Details</b></td>' .
			'</tr></thead><tbody>';
		

		if(empty($item)){
			$sql = "SELECT * FROM `audit`, `basic-information` WHERE `AuditType` = 'admission' AND `basic-information`.ID = `UserID`";
		} 

		$run = $this->core->database->doSelectQuery($sql);
		$i = 0; $c = 1;
		while ($row = $run->fetch_assoc()) {

			$action = $row["Action"];
			$reference =$row["ReferenceID"];
			$data = $row["AuditData"];
	
			echo'<tr >' . 
				'<td>'.$c.' -  <i>' . $row["DateTime"] . ' </i></td>' .
				'<td><b><a href="' . $this->core->conf['conf']['path'] . '/information/' . $row["UserID"] . '">' . $row["FirstName"] . ' ' . $row["Surname"] . '</a></b></td>' .
				'<td><b>' . $row["Action"] . ' </b></td>' .
				'<td>' . $reference . ' </td>' .
				'<td>' . $data . ' </td>' .
				'</tr>';

			$c++;
		}

		echo '</tbody></table></p>';
	
	}



	public function gradesAudit($item) {
		$this->viewMenu();

		echo'<table id="results" class="table table-bordered table-striped table-hover">' .
			'<thead><tr>' .
			'<td><b>Time</b></td>' .
			'<td><b>Owner</b></td>' .
			'<td><b>Action</b></td>' .
			'<td><b>Student</b></td>' .
			'<td><b>Details</b></td>' .
			'</tr></thead><tbody>';
		

		if(empty($item)){
			$sql = "SELECT * FROM `audit`, `basic-information` WHERE `AuditType` = 'Grades' AND `basic-information`.ID = `UserID`";
		} 

		$run = $this->core->database->doSelectQuery($sql);

		$i = 0; $c = 1;
		while ($row = $run->fetch_assoc()) {

			$action = $row["Action"];
			$reference =$row["ReferenceID"];
			$data = $row["AuditData"];

			if($action == "Deleted grade"){
				$sqlx = "SELECT * FROM `grades-removed` WHERE `ID` = '$reference'";
				$runx = $this->core->database->doSelectQuery($sqlx);
				while ($rowx = $runx->fetch_assoc()) {
					$reference = $rowx["StudentNo"];
					$data =  $rowx["Grade"] . ", " . $rowx["CourseNo"] . ", " . $rowx["Semester"]. ", " . $rowx["Year"];
				}
			}

			echo'<tr >' . 
				'<td>'.$c.' -  <i>' . $row["DateTime"] . ' </i></td>' .
				'<td><b><a href="' . $this->core->conf['conf']['path'] . '/information/' . $row["UserID"] . '">' . $row["FirstName"] . ' ' . $row["Surname"] . '</a></b></td>' .
				'<td><b>' . $row["Action"] . ' </b></td>' .
				'<td>' . $reference . ' </td>' .
				'<td>' . $data . ' </td>' .
				'</tr>';


			$c++;
		}

		echo '</tbody></table></p>';
	}
}
?>

