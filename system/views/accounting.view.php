<?php
class accounting {

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
		'<a href="' . $this->core->conf['conf']['path'] . '/acounting/balances">Student Balances</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/acounting/incoming">Incoming Payments</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/acounting/outgoing">Outgoing Payments</a>'.
		'</div>';
	}

	public function buildView($core) {
		$this->core = $core;

		$this->limit = 50;
		$this->offset = 0;

		include $this->core->conf['conf']['classPath'] . "users.inc.php";


		if(empty($this->core->item)){
			if(isset($this->core->cleanGet['uid'])){
				$this->core->item = $this->core->cleanGet['uid'];
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


	public function balancesAccounting($item, $listType = "list") {
		$year = $this->core->cleanGet['year'];

		if(isset($this->core->cleanGet['amount'])){
			$amount = $this->core->cleanGet['amount'];
		}else{
			$amount = 0;
		}

		if(isset($this->core->cleanGet['mode'])){
			$mode = $this->core->cleanGet['mode'];
		}else{
			$mode = "%";
		}

		if($mode == "all"){
			$mode = "%";
		}

		$sql = "SELECT  `FirstName`,`Surname`,`ID`,`StudyType`,`Status`,`balances`.`StudentID`, `Amount`  FROM `basic-information`, `balances` 
			WHERE `basic-information`.`ID` = `balances`.StudentID 
			AND `balances`.`Amount` > $amount 
			AND `basic-information`.StudyType LIKE '$mode'
			ORDER BY `balances`.`Amount` DESC";


		$count = "SELECT COUNT(`balances`.StudentID) FROM `basic-information`, `balances` 
			WHERE `basic-information`.`ID` = `balances`.StudentID 
			AND `balances`.`Amount` > $amount
			AND `basic-information`.StudyType LIKE '$mode'
			ORDER BY `balances`.`Amount` DESC";

		$total = "SELECT SUM(`balances`.Amount) FROM `basic-information`, `balances` 
			WHERE `basic-information`.`ID` = `balances`.StudentID 
			AND `balances`.`Amount` > $amount
			AND `basic-information`.StudyType LIKE '$mode'
			ORDER BY `balances`.`Amount` DESC";

		$this->showInfoList($sql, $count, $total);
	}

	public function printrequestingAccounting($item){
		$year = $this->core->cleanGet['year'];

		if(isset($this->core->cleanGet['amount'])){
			$amount = $this->core->cleanGet['amount'];
		}else{
			$amount = -10000;
		}

		if(isset($this->core->cleanGet['mode'])){
			$mode = $this->core->cleanGet['mode'];
		}else{
			$mode = "%";
		}

		if($mode == "all"){
			$mode = "%";
		}

		$this->limit = 9999999;

		$sql = "SELECT `FirstName`,`Surname`,`basic-information`.ID,`StudyType`,`Status`,`balances`.`StudentID`, `Amount`, `ExamCentre` FROM `basic-information`, `balances` 
			LEFT JOIN `student-data-other` ON  `student-data-other`.StudentID = `balances`.StudentID 
			WHERE `basic-information`.`ID` = `balances`.StudentID 
			AND `balances`.`Amount` > $amount 
			AND `basic-information`.Status = 'Requesting' 
			AND `basic-information`.StudyType LIKE '$mode'
			ORDER BY `ExamCentre` ASC";

		$count = "SELECT COUNT(`balances`.StudentID) FROM `basic-information`, `balances` 
			LEFT JOIN `student-data-other` ON  `student-data-other`.StudentID = `balances`.StudentID 
			WHERE `basic-information`.`ID` = `balances`.StudentID 
			AND `balances`.`Amount` > $amount 
			AND `basic-information`.Status = 'Requesting' 
			AND `basic-information`.StudyType LIKE '$mode'
			ORDER BY `balances`.`Amount` DESC";

		$total = "SELECT SUM(`balances`.StudentID) FROM `basic-information`, `balances` 
			LEFT JOIN `student-data-other` ON  `student-data-other`.StudentID = `balances`.StudentID 
			WHERE `basic-information`.`ID` = `balances`.StudentID 
			AND `balances`.`Amount` > $amount 
			AND `basic-information`.Status = 'Requesting' 
			AND `basic-information`.StudyType LIKE '$mode'
			ORDER BY `balances`.`Amount` DESC";

		$this->showInfoList($sql, $count, $total, TRUE, TRUE);
	}


	public function requestingAccounting($item, $listType = "list") {
		$year = $this->core->cleanGet['year'];
		
		if(isset($this->core->cleanGet['amount'])){
			$amount = $this->core->cleanGet['amount'];
		}else{
			$amount = 0;
		}

		if(isset($this->core->cleanGet['mode'])){
			$mode = $this->core->cleanGet['mode'];
		}else{
			$mode = "%";
		}

		if($mode == "all"){
			$mode = "%";
		}

		echo '<div class="toolbar">'.
			'<a href="' . $this->core->conf['conf']['path'] . '/accounting/printrequesting">Print list</a>'.
			'</div>';


		$sql = "SELECT  `FirstName`,`Surname`,`ID`,`StudyType`,`Status`,`balances`.`StudentID`, `Amount`  FROM `basic-information`, `balances` 
			WHERE `basic-information`.`ID` = `balances`.StudentID 
			AND `balances`.`Amount` > $amount
			AND `basic-information`.Status = 'Requesting'
			AND `basic-information`.StudyType LIKE '$mode'
			ORDER BY `balances`.`Amount` DESC";

		$count = "SELECT COUNT(`balances`.StudentID) FROM `basic-information`, `balances` 
			WHERE `basic-information`.`ID` = `balances`.StudentID 
			AND `balances`.`Amount` > $amount	
			AND `basic-information`.Status = 'Requesting'
			AND `basic-information`.StudyType LIKE '$mode'
			ORDER BY `balances`.`Amount` DESC";

		$total = "SELECT SUM(`balances`.Amount) FROM `basic-information`, `balances` 
			WHERE `basic-information`.`ID` = `balances`.StudentID 
			AND `balances`.`Amount` > $amount	
			AND `basic-information`.Status = 'Requesting'
			AND `basic-information`.StudyType LIKE '$mode'
			ORDER BY `balances`.`Amount` DESC";

		$this->showInfoList($sql, $count, $total);

	}

	public function intakeAccounting($item, $listType = "list") {
		$year = $this->core->cleanGet['year'];

		if(isset($this->core->cleanGet['amount'])){
			$amount = $this->core->cleanGet['amount'];
		}else{
			$amount = -10000;
		}

		if(isset($this->core->cleanGet['mode'])){
			$mode = $this->core->cleanGet['mode'];
		}else{
			$mode = "%";
		}

		if($mode == "all"){
			$mode = "%";
		}

		$sql = "SELECT `FirstName`,`Surname`, `transactions`.StudentID as idd,  `StudyType`, `basic-information`.`Status`, `transactions`.Amount,   `balances`.Amount, TransactionDate , `transactions`.TransactionID 
			FROM   `balances`, `basic-information`, `$item`
			LEFT JOIN `transactions` ON `$item`.StudentID = `transactions`.StudentID
			WHERE `$item`.StudentID = `balances`.StudentID
			AND `$item`.StudentID = `basic-information`.ID
			AND `balances`.`Amount` > $amount
			AND `basic-information`.StudyType LIKE '$mode'
			ORDER BY idd DESC";


		$count = "SELECT COUNT(`balances`.StudentID), `FirstName`,`Surname`, `transactions`.StudentID as idd,  `StudyType`, `basic-information`.`Status`, `transactions`.Amount,   `balances`.Amount, TransactionDate , `transactions`.TransactionID 
			FROM   `balances`, `basic-information`, `$item`
			LEFT JOIN `transactions` ON `$item`.StudentID = `transactions`.StudentID
			WHERE `$item`.StudentID = `balances`.StudentID
			AND `$item`.StudentID = `basic-information`.ID
			AND `balances`.`Amount` > $amount
			AND `basic-information`.StudyType LIKE '$mode'
			GROUP BY `$item`.StudentID";

		$total = "SELECT SUM(`balances`.Amount), `FirstName`,`Surname`, `transactions`.StudentID as idd,  `StudyType`, `basic-information`.`Status`, `transactions`.Amount,   `balances`.Amount, TransactionDate , `transactions`.TransactionID 
			FROM   `balances`, `basic-information`, `$item`
			LEFT JOIN `transactions` ON `$item`.StudentID = `transactions`.StudentID
			WHERE `$item`.StudentID = `balances`.StudentID
			AND `$item`.StudentID = `basic-information`.ID
			AND `balances`.`Amount` > $amount
			AND `basic-information`.StudyType LIKE '$mode'
			ORDER BY idd DESC";

		$this->showInfoList($sql, $count, $total, TRUE);

	}


	private function showInfoList($sql, $count, $total, $intake, $print) {
		setlocale(LC_MONETARY, 'en_US.UTF-8');

		if($intake == TRUE){
			$sqld = $sql;
		}else{
			$sqld = $sql . " LIMIT ". $this->limit ." OFFSET ". $this->offset;
		}

		$run = $this->core->database->doSelectQuery($sqld);
		$runc = $this->core->database->doSelectQuery($count);
		$rund = $this->core->database->doSelectQuery($total);

		while ($row = $runc->fetch_row()) {
			$count = $row[0];
		}
		while ($row = $rund->fetch_row()) {
			$total = money_format('%!.0n', $row[0]);
		}

		if(isset($this->core->cleanGet['amount'])){
			$amount = $this->core->cleanGet['amount'];
		}else{
			$amount = 0;
		}

		if(!isset($this->core->cleanGet['offset'])){

			$_SESSION["recipients"] = $sql;
			
			echo '<div class="toolbar">'.
			'<a href="' . $this->core->conf['conf']['path'] . '/accounting/requesting">Recently active students</a>'.
			'<a href="' . $this->core->conf['conf']['path'] . '/sms/newbulk">Send SMS to all students</a>'.
			'</div>';

			echo '<form id="narrow" name="narrow" method="get" action=""><div class="toolbar">'.
			'<div class="toolbaritem">'.$count.' TOTAL</div>';

			if($print != TRUE){
				echo'<div class="toolbaritem">K'.$total.' TOTAL</div>';
			}

			echo'<div class="toolbaritem">Balance higher than: <input type="text" value="'.$amount.'" name="amount" style="width: 70px; margin-top: -15px;">';
			echo'<select name="mode" class="submit" style="width: 105px;  margin-top: -17px;">
				<option value="Fulltime">Fulltime</option>
				<option value="Distance">Distance</option>
				<option value="Part-time">Part-time</option>
				<option value="Dismissed">Dismissed</option>
				<option value="all" selected>ALL</option>
			</select>
			<input type="submit" value="update"  style="width: 80px; margin-top: -15px;"/></div>'.
			'</div></form>';


			if($print == TRUE){
				$center = '<th bgcolor="#EEEEEE"><b>Exam Centre</b></th>';
			}

			echo'<table id="results" class="table table-bordered  table-hover">
			<thead>
				<tr>
					<th bgcolor="#EEEEEE" width="30px"></th>
					<th bgcolor="#EEEEEE" >#</th>
					<th bgcolor="#EEEEEE" data-sort"string"=""><b>Student Name</b></th>
					<th bgcolor="#EEEEEE"><b>Student Number</b></th>
					<th bgcolor="#EEEEEE"><b>Balance</b></th>
					<th bgcolor="#EEEEEE"><b>Status</b></th>
					<th bgcolor="#EEEEEE"><b>Delivery</b></th>
					'.$center.'
				</tr>
			</thead>
			<tbody>';
		}

		$count = $this->offset+1;


		while ($row = $run->fetch_row()) {
			$results == TRUE;

			$firstname = $row[0];
			$surname = $row[1];
			$id = $row[2];
			$mode = $row[3];
			$status = $row[4];
			$center = $row[7];
			$amount = $row[6];

			if(isset($row[7]) && $print == FALSE){

				$date = $row[7];
				$payment = $row[5];
				$tid = $row[8];
				$count = "";
				$cset = 'style="background-color: #EEEEEE"';
				$extra = "BALANCE: ";
				$txt = '<td ><img src="/edurole/templates/edurole/images/user.png"></td>
				<td colspan="2"><a href="' . $this->core->conf['conf']['path'] . '/information/show/' . $id . '"><b> '.$firstname.' '.$surname.'</b></a></td>';
			}else{
				$date = NULL;
				$txt = '<td><img src="/edurole/templates/edurole/images/user.png"></td><td>'.$count.'</td>
				<td><a href="' . $this->core->conf['conf']['path'] . '/information/show/' . $id . '"><b> '.$firstname.' '.$surname.'</b></a></td>';
			}

			$amount = money_format('%!.0n', $amount);

			if($id != $oldid){
				$subcount = 1;
				echo'<tr '.$cset.'>
				'.$txt.'
				<td> '.$id.'</td>
				<td> <b>'.$extra.' '.$amount.' ZMW owed</b></td>
				<td> '.$status.'</td>
				<td> '.$mode.'</td>';

				if($print == TRUE){
					echo '<td> '.$center.'</td>';
				}
				
				echo'</tr>';
			}

			if($date != NULL){
				echo'<tr>
				<td>'.$subcount.'</td>
				<td><b>  '.$tid.'</b></td>
				<td>'.$date.'</td>
				<td> <b>'.$payment.' ZMW</b></td>
				<td> BILL MUSTER</td>
				<td></td>
				<td></td>
				</tr>';
				$subcount++;
			}

			$oldid = $id;
			$count++;
			$results = TRUE;
		}

		if($intake != TRUE){
		if($this->core->pager == FALSE){
			if ($results != TRUE) {
				$this->core->throwError('Your search did not return any results');
			}

			if($this->core->pager == FALSE){

				include $this->core->conf['conf']['libPath'] . "edurole/autoload.js";
			}
		}
		}

		if(!isset($this->core->cleanGet['offset'])){
			echo'</tbody>
			</table>';
		}


	}
}