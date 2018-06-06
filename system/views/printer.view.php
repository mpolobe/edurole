<?php
class printer {

	public $core;
	public $view;
	public $limit;
	public $offset;
	public $pager = FALSE;

	public function configView() {
		$this->view->header = TRUE;
		$this->view->footer = TRUE;
		$this->view->menu = TRUE;
		$this->view->javascript = array('jquery.form-repeater');
		$this->view->css = array();

		return $this->view;
	}

	private function viewMenu(){
		echo '<div class="toolbar">'.
		'<a href="' . $this->core->conf['conf']['path'] . '/sms/manage">Manage SMS</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/sms/new">Send SMS</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/sms/balance">Balance</a>'.
		'</div>';
	}

	public function buildView($core) {
		$this->core = $core;

		$this->limit = 50;
		$this->offset = 0;

		include $this->core->conf['conf']['classPath'] . "users.inc.php";


		if(empty($this->core->item)){
			if(isset($this->core->cleanGet['uid'])){
				$this->core->item = trim($this->core->cleanGet['uid']);
			}
		}
		if(isset($this->core->cleanGet['offset'])){
			$this->offset = $this->core->cleanGet['offset'];
		}
		if(isset($this->core->cleanGet['limit'])){
			$this->limit = $this->core->cleanGet['limit'];
			$this->pager = TRUE;
		}
	} 


	public function searchPrinter($item) {
		$listType = "list";

		if(isset($this->core->cleanGet['studies'])){
			$studies = $this->core->cleanGet['studies'];
		}
		if(isset($this->core->cleanGet['programmes'])){
			$programmes = $this->core->cleanGet['programmes'];
		}
		if(isset($this->core->cleanGet['search'])){
			$search = $this->core->cleanGet['search'];
		}
		if(isset($this->core->cleanGet['q'])){
			$q = $this->core->cleanGet['q'];
		}

		if(isset($this->core->cleanGet['card'])){
			$card = $this->core->cleanGet['card'];
		}

		if(isset($this->core->cleanGet['studentfirstname'])){
			$firstName = $this->core->cleanGet['studentfirstname'];
		}
		if(isset($this->core->cleanGet['studentlastname'])){
			$lastName = $this->core->cleanGet['studentlastname'];
		}
		if(isset($this->core->cleanGet['listtype'])){
			$listType = $this->core->cleanGet['listtype'];
		}
		if(isset($this->core->cleanGet['year'])){
			$year = $this->core->cleanGet['year'];
		}
		if(isset($this->core->cleanGet['mode'])){
			$mode = $this->core->cleanGet['mode'];
		}
		if(isset($this->core->cleanGet['examcenter'])){
			$center = $this->core->cleanGet['examcenter'];
		}

		if (isset($lastName) || isset($firstName)) {
			$this->bynamePrinter($firstName, $lastName, $listType);
		}elseif (isset($center)){
			$this->bycenterPrinter($center);
		} else if ($this->core->action == "search" && isset($q) && $search == "study" || $this->core->action == "students" && isset($q) && $search == "study") {
			$this->bystudyPrinter($q, $listType);
		} else if ($this->core->action == "search" && isset($q) && $search == "programme" || $this->core->action == "students" && isset($q) && $search == "programme") {
			$this->byprogramPrinter($q, $listType, $year, $mode);
		} else if ($this->core->action == "search" && isset($q) && $search == "courses" || $this->core->action == "students" && isset($q) && $search == "courses") {
			$this->bycoursePrinter($q, $listType);
		} else if ($this->core->action == "search" && isset($item)) {
			$this->showPrinter($item);
		} else if ($this->core->action == "search" && isset($card)) {
			$this->showcardPrinter($card);
		} else if ($this->core->action == "search" && isset($year) && isset($mode)) {
			$this->byintakePrinter($year, $mode);
		}else{
			include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";

			$select = new optionBuilder($this->core);

			$study = $select->showStudies(null);
			$program = $select->showPrograms(null, null, null);
			$courses = $select->showCourses(null);

			if ($this->core->role >= 100) {
				include $this->core->conf['conf']['formPath'] . "searchform.form.php";
			} else {
				$this->core->throwError($this->core->translate("You do not have the authority to do system wide searches"));
			}
		}
	}

	private function byintakePrinter($year, $mode) {
		if (is_numeric($year)) {
			$sql = "SELECT * FROM `basic-information` WHERE `ID` LIKE '" . $year . "%' AND `StudyType` LIKE '" . $mode . "' AND `Status` = 'New' ORDER BY `Town` DESC";
		} else if($year == "all") {
			$sql = "SELECT * FROM `basic-information` WHERE `StudyType` LIKE '" . $mode . "'";
		}

		$this->showInfoList($sql);
	}

	
	public function showPrinter($item) {
		if(empty($item)){
			$this->searchPrinter();
		} else {
			$sql = "SELECT * FROM `basic-information` WHERE `ID` LIKE '" . $item . "'";
			$this->showInfoProfile($sql, FALSE);
		}
	}


	private function byprogramInformation($program, $listType, $year, $mode) {
		if ($program != "" && is_numeric($program)) {


			$sql = "SELECT * FROM `basic-information` as bi, `student-program-link` as sp, `programmes` as p, `programmes-link` as pl 
				WHERE sp.`StudentID` = `bi`.ID 
				AND sp.`ProgrammeID` = pl.`ID`
				AND p.`ID` = pl.`Major`
				AND p.`ID` = '$program'
				AND bi.`ID` LIKE '" . $year . "%' 
				AND bi.`StudyType` LIKE '" . $mode . "'
				OR  sp.`StudentID` = `bi`.ID 
				AND sp.`ProgrammeID` = pl.`ID`
				AND p.`ID` = pl.`Minor`
				AND p.`ID` = '$program'
				AND bi.`ID` LIKE '" . $year . "%' 
				AND bi.`StudyType` LIKE '" . $mode . "'";
		}

		if ($listType == "profiles") {
			$this->showInfoProfile($sql, FALSE);
		} elseif ($listType == "list") {
			$this->showInfoList($sql);
		}
	}

	private function showInfoProfile($sql, $personal) {

		$run = $this->core->database->doSelectQuery($sql);

		while ($row = $run->fetch_row()) {
			$results = TRUE;
			$firstname = $row[0];
			$middlename = $row[1];
			$surname = $row[2];
			$uid = $row[4];
			$streetname = $row[9];
			$postalcode = $row[10];
			$town = $row[11];
			$country = $row[12];


			echo '<div style=""><div style="-moz-transform: rotate(-90deg); -webkit-transform: rotate(-90deg);">
				<table width="400" height="63" border="0" cellpadding="0" cellspacing="0">
				<tr><td><b>' . $firstname . ' ' . $middlename . ' ' . $surname . '</b></td></tr>
				<tr><td width=""><b>' . $streetname . '</b></td></tr>';

				if($postalcode != "" && $postalcode != " "){ echo'<tr><td><b>PO. BOX ' . $postalcode . '</b></td></tr>';	}

				echo'<tr><td><b>' . $town . '</b></td></tr>
				<tr><td><b>' . $country . '</b></td></tr>
			</table>
			</div></div>';
		
		}

		if ($results != TRUE) {
			$this->core->throwError('Your search did not return any results');
		}
	}

	private function showInfoList($sql) {


		$run = $this->core->database->doSelectQuery($sql);

		$count = $this->offset+1;

		while ($row = $run->fetch_row()) {
			$results = TRUE;
			$firstname = $row[0];
			$middlename = $row[1];
			$surname = $row[2];
			$uid = $row[4];
			$streetname = $row[9];
			$postalcode = $row[10];
			$town = $row[11];
			$phone = $row[14];
			$co = $row[7];

			echo '<div style=""><div style="width: 400px; margin-top: 300px; position: relative; page-break-after:always; -moz-transform: rotate(-90deg); -webkit-transform: rotate(-90deg);">
				<table width="" height="" border="0" cellpadding="0" cellspacing="0">
				<tr><td><b>' . $firstname . ' ' . $middlename . ' ' . $surname . '</b></td></tr>
				<tr><td>C/O: '.$co.'</td></tr>
				<tr><td width=""><b>' . $streetname . '</b></td></tr>';

				if($postalcode != "" && $postalcode != " "){ echo'<tr><td><b>PO. BOX ' . $postalcode . '</b></td></tr>';	}

				echo'<tr><td><b>' . $town . '</b></td></tr>
				<tr><td><b>PHONE: ' . $phone . '</b></td></tr>
			</table>
			</div></div>';

//			echo'<p style="page-break-after:always;"></p>';

			$count++;
			$results = TRUE;
		}

		if($this->core->pager == FALSE){
			if ($results != TRUE) {
				$this->core->throwError('Your search did not return any results');
			}

			if($this->core->pager == FALSE){

				include $this->core->conf['conf']['libPath'] . "edurole/autoload.js";
			}
		}


	echo'<script type="text/javascript">
			window.print();
		</script>';


	}

	public function editInformation($item) {
		if(empty($item) || $this->core->role <= 10){ $item = $this->core->userID;  }

		$sql = "SELECT * FROM  `basic-information` as bi 
		LEFT JOIN `access` as ac ON ac.`ID` = '" . $item . "' 
		WHERE bi.`ID` = '" . $item . "'";

		$run = $this->core->database->doSelectQuery($sql);
 
		while ($row = $run->fetch_row()) {
			$id = $row[4];
			$NID = $row[5];
			$firstname = $row[0];
			$middlename = $row[1];
			$surname = $row[2];
			$gender = $row[3];
			$dob = $row[6];
			$nationality = $row[8];
			$street = $row[9];
			$postal = $row[10];
			$town = $row[11];
			$country = $row[12];
			$homephone = $row[13];
			$celphone = $row[14];
			$disability = $row[15];
			$email = $row[17];
			$relation = $row[18];
			$status = $row[20];
			$role = $row[23];

			include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";

			$select = new optionBuilder($this->core);
			$select = $select->showRoles($role);

			$selectstudy = new optionBuilder($this->core);
			$selectstudy = $selectstudy->showStudies(NULL);
		}

		include $this->core->conf['conf']['formPath'] . "edituser.form.php";

	}
}

?>
