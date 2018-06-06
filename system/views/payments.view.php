 <?php
class payments {

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

	public function clearPayments($item){

		$owner = $this->core->userID;
		$student = $item;
		$period = $this->getPeriod($item);
		$balance = $this->getBalance($item);

		$sql  = "INSERT INTO `edurole`.`cleared` (`ID`, `StudentID`, `PeriodID`, `Balance`, `DateTime`, `Owner`) 
			VALUES (NULL, '$student', '$period', '$amount', NOW(), '$owner');";
					 
		$run = $this->core->database->doInsertQuery($sql);
			
		$this->core->audit(__CLASS__, $item, $userid, "Cleared balance $item - $amount - $period");
		$this->core->redirect("payments", "show", $student);
	}

	private function getPeriod($item){

		$sql = "SELECT PeriodID  FROM `course-electives` WHERE `StudentID` = '$item' AND `Approved` = 1 ORDER BY EnrolmentDate ASC LIMIT 1";
		$run = $this->core->database->doSelectQuery($sql);
		$period = FALSE;

		while ($row = $run->fetch_assoc()) {
			$period = $row["PeriodID"];
		}
		
		return $period;
	}

	private function getCleared($item){

		$sql = "SELECT StudentID  FROM `cleared` WHERE `StudentID` = '$item'";
		$run = $this->core->database->doSelectQuery($sql);
		$period = FALSE;

		while ($row = $run->fetch_assoc()) {
			$period = $row["PeriodID"];
		}
		
		return $period;
	}

	private function viewMenu(){
		$today = date("Y-m-d");

		if(isset($_GET['date'])){
			$today = $_GET['date'];
		}

		echo '<div class="toolbar">'.
		'<a href="' . $this->core->conf['conf']['path'] . '/payments/add?date='.$today.'">Add payment</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/payments/confirm?date='.$today.'">Confirm payment</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/payments/manage?date='.$today.'">Daily payments</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/payments/print?date='.$today.'">Print list</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/payments/month">Monthly totals</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/payments/daily">Daily totals</a></div>';
	}

	public function buildView($core) {
		$this->core = $core;
	}

	public function unknownPayments($item=NULL, $linked = TRUE) {
		$this->managePayments(NULL, FALSE);
	}

	public function editPayments($item) {
		echo'<div class="col-lg-12 greeter" style="">Payment Reversal</div>';
		echo'<p><b>To reverse this transaction press the following button</b></p>';
		echo '<div style="border: solid 1px #ccc; padding:10px; width: 200px; text-align: center">
		<b><a href="' . $this->core->conf['conf']['path'] . '/payments/reverse/'. $item .'">Reverse payment</a></b></div>';
		echo'<p><br></p>';
		echo'<div class="col-lg-12 greeter" style="">Payment Re-Assignment</div>';
		include $this->core->conf['conf']['formPath'] . "assignpayment.form.php";
	}
	
	public function balancePayments($item){
		
		$function = $item;
		$student = $this->core->subitem;
		
		$code = $this->core->cleanPost['code'];
		$amount = $this->core->cleanPost['amount'];
		$uid = $this->core->cleanPost['uid'];
		
		if($function == 'add'){
			
			include $this->core->conf['conf']['formPath'] . "addbalance.form.php";
			
		}else if($function == 'save'){
			
			$sql  = "INSERT INTO `balances` (`StudentID`, `Amount`, `LastUpdate`, `LastTransaction`, `Original`) 
					 VALUES ('$uid', '$amount', NOW(), 'IMPORT', '$amount', '$code');";
					 
			$run = $this->core->database->doInsertQuery($sql);
			
			echo '<div class="successpopup">Balance for student was added. <a href="'.$this->core->conf['conf']['path'].'/information/show/'.$uid.'">Back to student profile</a></div>';
			
		}
	}

	public function reversePayments($item) {

		$sql  = "UPDATE `transactions` SET  `Status` =  'REVERSED' 
			 WHERE  `transactions`.`ID` = $item;";

		$run = $this->core->database->doSelectQuery($sql);

		$sql = "SELECT `Amount`, `StudentID` FROM `transactions` WHERE `transactions`.`ID` = '$item'";
		$rund = $this->core->database->doSelectQuery($sql);
		
		while ($row = $rund->fetch_row()) {
			$amount = $row[0];
			$userid = $row[1];
		}

		$sql  = "UPDATE `balances` SET  `Amount` =  `Amount`+$amount WHERE  `balances`.`StudentID` = '$userid';";
		$run = $this->core->database->doSelectQuery($sql);


		$this->core->audit(__CLASS__, $item, $userid, "Reversed transaction $item - $amount");

		echo '<div class="successpopup">Payment Reversed</div> ';

	}

	public function assignPayments($item) {
		include $this->core->conf['conf']['formPath'] . "assignpayment.form.php";
	}

	public function confirmPayments($item) {
		include $this->core->conf['conf']['formPath'] . "confirmpayment.form.php";
	}

	public function addPayments($item) {
		if(isset($_GET['amount'])){
			$amount = $_GET['amount'];
			$description = $_GET['description'];
			$type = $_GET['type'];
		}

		$paymentid = "NCE-" . date("Y-m-d-H-i-s-$item");

		include $this->core->conf['conf']['formPath'] . "addpayment.form.php";
	}


	public function savePayments($item) {
		$uid = $this->core->cleanGet['uid'];
		$amount = $this->core->cleanGet['amount'];
		$description = $this->core->cleanGet['description'];
		$type = $this->core->cleanGet['paymenttype'];
		$date = $this->core->cleanGet['date'];
		$reference = $this->core->cleanGet['reference'];

		if($type == "10"){
			$outtype = "ACCOMMODATION (ASSIGNING A ROOM)";
		}else if($type == "15"){
			$outtype = "BILLING STUDENT";
		}else{
			$outtype = "CASH PAYMENT";
		}

		echo'<div class="heading">'. $this->core->translate("Confirm payment/billing") .'</div>
		<form id="savepayment" name="savepayment" method="get" action="'. $this->core->conf['conf']['path'] .'/payments/transact">
		<p><b>Please confirm the following information is correct:</b><br>
		
		<div class="label">'. $this->core->translate("Student") .':</div><div class="label">'.$uid.'</div><br>
		<div class="label">'. $this->core->translate("Payment Amount") .':</div><div class="label">'.$amount.'</div><br>
		<div class="label">'. $this->core->translate("Description") .' :</div><div class="label">'.$description.'</div><br>
		<div class="label">'. $this->core->translate("Payment Type") .':</div><div class="label">'.$outtype.'</div><br>
		<div class="label">'. $this->core->translate("Payment Reference") .':</div><div class="label">'.$reference.'</div><br>
		<div class="label">'. $this->core->translate("Payment Date") .':</div><div class="label">'.$date.'</div><br>

		<input type="hidden" name="uid" value="'.$uid.'">
		<input type="hidden" name="amount" value="'.$amount.'">
		<input type="hidden" name="description" value="'.$description.'">
		<input type="hidden" name="paymenttype" value="'.$type.'">
		<input type="hidden" name="reference" value="'.$reference.'">
		<input type="hidden" name="date" value="'.$date.'">
	
		</p><p><button onclick="window.history.back();" name="no"  id="no" class="input submit" style="font-size: 18px; font-weight: bold; padding: 5px; padding-left: 20px; padding-right: 20px; padding-bottom: 10px; border: 1px solid #000; background-color: #e81f1f"> <span class="glyphicon glyphicon-thumbs-down" aria-hidden="true"> NO</button> 
		<button onclick="this.form.submit();" class="input submit" style="font-size: 18px; font-weight: bold; padding: 5px;  padding-left: 20px; padding-bottom: 10px; padding-right: 20px; border: 1px solid #000; background-color: #39c541"> <span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"> YES</button></p>
		</form>';
	}

	public function deductPayments($item) {
		$item = $this->core->item;

		if(isset($item)){
			$sqld = "SELECT Status, `transactions`.Amount,`balances`.StudentID as SID, `transactions`.TransactionID as TID 
				FROM `transactions`, `balances` 
				WHERE `transactions`.`ID` = '$item' 
				AND `balances`.StudentID = `transactions`.StudentID;";

			$rund = $this->core->database->doSelectQuery($sqld);
		
			while ($rowd = $rund->fetch_assoc()) {
				$status = $rowd["Status"];
				$uid = $rowd["SID"];
				$tid = $rowd["TID"];
				$amount = $rowd["Amount"];
				

				if($status != "SUCCESS"){
					$sql = "UPDATE `balances` SET `Amount` = Amount-$amount, `LastUpdate` = NOW(), `LastTransaction` = '$tid' WHERE `StudentID` = '$uid';";
					$this->core->database->doInsertQuery($sql);
					
					$sql = "UPDATE `transactions` SET `Status` = 'SUCCESS' WHERE `ID` = '$item';";
					$this->core->database->doInsertQuery($sql);
					

					echo '<span class="successpopup">Payment deducted from balance</div>';

				} else {

					echo '<span class="errorpopup">Already deducted from balance</div>';

				}
			}
		}
	}

	public function dosaPayments($item){
		$this->showPayments($item);
	}

	public function transactPayments($item) {
		$uid = $this->core->cleanGet['uid'];
		$amount = $this->core->cleanGet['amount'];
		$description = $this->core->cleanGet['description'];
		$type = $this->core->cleanGet['paymenttype'];
		$date = $this->core->cleanGet['date'];
		$reference = $this->core->cleanGet['reference'];



		if($type == "15"){
			$this->makeBill($item, $uid, $amount, $description, $type, $date, $reference);
		} else {
			$this->makePayments($item, $uid, $amount, $description, $type, $date, $reference);
		}

		//$this->showPayments($uid);
		$this->core->redirect("payments", "show", $uid);

	}

	private function makeBill($item, $uid, $amount, $description, $type, $date, $reference){

		$dates = ("Ymd");

		$sql = "INSERT INTO `billing` (`ID`, `StudentID`, `Amount`, `Date`, `Description`, `PackageName`, `PeriodID`) 
			VALUES (NULL, '$uid', '$amount', '$date 00:00:00', '$description', '$dates$amount', '0');";
		$this->core->database->doInsertQuery($sql);

		echo '<span class="successpopup">Added to include new bill of '.$amount.'</span>';

		$this->core->audit(__CLASS__, $item, $uid, "Added bill $description - $amount");
	}

	public function makePayments($item, $uid, $amount, $description, $type, $date, $reference){
		$sql = "SELECT * FROM  `basic-information` WHERE `basic-information`.ID = '$uid'";

		$run = $this->core->database->doSelectQuery($sql);

	

		$admin = $this->core->userID;

		while ($row = $run->fetch_row()) {
			$results = TRUE;
			$firstname = $row[0]; 
			$middlename = $row[1];
			$surname = $row[2];
			$fullname = $firstname . ' ' . $middlename . ' ' . $surname;
			$fullname = $this->core->database->escape($fullname);
			if(empty($date)){
				$date = date('Y-m-d');
			}
 
			$sql = "INSERT INTO `transactions` (`ID`, `UID`, `RequestID`, `TransactionID`, `StudentID`, `NRC`, `TransactionDate`, `Amount`, `Name`, `Type`, `Hash`, `Timestamp`, `Phone`, `Status`, `Error`, `Data`) 
				VALUES (NULL, '$admin', '$reference', '$reference', '$uid', '', '$date', '$amount', '$fullname', '$type', '', CURRENT_TIMESTAMP, '', 'MANUAL', 'MANUAL', '$description');";
		
			if($this->core->database->doInsertQuery($sql, TRUE) == FALSE){
				echo '<div class="errorpopup">Please check if the payment was added</div> ';
				return;
			} else {
				echo '<div class="successpopup">Payment was added</div> ';



				$sqld = "SELECT * FROM `transactions` WHERE `transactions`.`TransactionID` = '$reference';";
				$rund = $this->core->database->doSelectQuery($sqld);
		
				while ($rowd = $rund->fetch_assoc()) {
					$status = $rowd["Status"];

					if($status != "SUCCESS"){
						$sql = "UPDATE `balances` SET `Amount` = Amount-$amount, `LastUpdate` = NOW(), `LastTransaction` = '$reference' WHERE `StudentID` = '$uid';";
						$this->core->database->doInsertQuery($sql);
					}
				}
 
			}

			$this->core->audit(__CLASS__, $item, $uid, "Added payment $description - $amount");
		}

	}

	public function modifyPayments($item) {
		$uid = $this->core->cleanGet['uid'];
		$date = $this->core->cleanGet['date'];

		$sql = "SELECT * FROM  `basic-information` WHERE `basic-information`.ID = '$uid'";

		$run = $this->core->database->doSelectQuery($sql);

		while ($row = $run->fetch_row()) {
			$results = TRUE;
			$firstname = $row[0];
			$middlename = $row[1];
			$surname = $row[2];
			$fullname = $firstname . ' ' . $middlename . ' ' . $surname;
			$fullname = $this->core->database->escape($fullname);

			$uid = $this->core->cleanGet['uid'];
			if(isset($this->core->cleanGet['reference'])){
				$item = $this->core->cleanGet['reference'];
			}

			$sqld = "SELECT * FROM `transactions` WHERE `transactions`.`ID` = '$item';";
			$rund = $this->core->database->doSelectQuery($sqld);

			while ($rowd = $rund->fetch_assoc()) {
				$transaction = $rowd["TransactionID"];
				$status = $rowd["Status"];
				$amountb = $rowd["Amount"];
				$suserid =  $rowd["StudentID"];

				if($status == "SUCCESS"){
					$sql = "UPDATE `balances` SET `Amount` = Amount+$amountb, `LastUpdate` = NOW(), `LastTransaction` = '$transaction' WHERE `StudentID` = '$suserid';";
					$this->core->database->doInsertQuery($sql);
				}
			}

			$sql = "UPDATE `transactions` SET `Status` = 'SUCCESS', `StudentID` = '$uid', `Name` = '$fullname' WHERE `transactions`.`ID` = $item;";
			$run = $this->core->database->doInsertQuery($sql);

			$sqld = "SELECT * FROM `transactions` WHERE `transactions`.`ID` = $item;";
			$rund = $this->core->database->doSelectQuery($sqld);

			while ($rowd = $rund->fetch_assoc()) {
				$status = $rowd["Status"];
				if($status != "SUCCESS"){
					$sql = "UPDATE `balances` SET `Amount` = Amount-$amountb, `LastUpdate` = NOW(), `LastTransaction` = '$transaction' WHERE `StudentID` = '$uid';";
					$this->core->database->doInsertQuery($sql);
				}
			}

			/*if($amountb == "3600"){
					echo '<div class="successpopup">Room assigned </div> ';

					$sql = "SELECT `rooms`.ID FROM `basic-information`, `hostel`, `rooms` 
					WHERE `basic-information`.Sex = `hostel`.Type
					AND `hostel`.ID = `rooms`.HostelID
					AND `rooms`.RoomCapacity > (SELECT COUNT(RoomID) FROM `housing` WHERE `rooms`.ID = RoomID) 
					AND `basic-information`.ID = '$uid'
					LIMIT 1;";

					$rund = $this->core->database->doSelectQuery($sql);

					while ($rowd = $rund->fetch_assoc()) {
						$roomID = $rowd["ID"];
						$rsql = "INSERT INTO `housing` (`ID`, `StudentID`, `RoomID`, `HousingStatus`, `CheckIn`) VALUES (NULL, '$uid', '$roomID', '1', NOW());";
						$this->core->database->doInsertQuery($rsql);
					}

			}*/



			$sql = "UPDATE `transactions` SET `Status` = 'SUCCESS', `StudentID` = '$uid', `Name` = '$fullname' WHERE `transactions`.`ID` = $item;";
			$run = $this->core->database->doInsertQuery($sql);

			echo '<div class="successpopup">Payment updated</div> ';
			$this->showPayments($uid);
		}
	}

	public function approvePayments($item=NULL, $linked = TRUE) {

	}

	public function rejectPayments($item=NULL, $linked = TRUE) {

	}

	public function managePayments($item=NULL, $linked = TRUE)  {
	$print;
		$today = date("Y-m-d");

		if(isset($_GET['date'])){
			$today = $_GET['date'];
		}

		$date = new DateTime($today);

		if($linked==TRUE){

			if($item==NULL){
				$sql = "SELECT * FROM `transactions` 
				LEFT JOIN `basic-information`
				ON `transactions`.StudentID = `basic-information`.ID 
				WHERE `transactions`.TransactionDate = '$today' AND `transactions`.Amount != '150'
				ORDER BY `transactions`.TransactionDate";

			}else{
				$sql = "SELECT * FROM `transactions` 
				LEFT JOIN `basic-information`
				ON `transactions`.StudentID = `basic-information`.ID 
				WHERE `basic-information`.ID = '$item'";
			}

		} else {

			$sql = "SELECT * FROM `transactions` 
			WHERE `transactions`.StudentID NOT IN (SELECT `basic-information`.ID FROM `basic-information`) AND `transactions`.TransactionDate = '$today'
			ORDER BY `transactions`.TransactionDate";

		}

		$run = $this->core->database->doSelectQuery($sql);


		$yesterday = new DateTime($today);
		$yesterday = $yesterday->sub(new DateInterval('P5D'))->format('Y-m-d');

		
		$todayp = new DateTime($today);
		
		if($print != TRUE){

			if($this->core->role > 10){
				$this->viewMenu();
			}

			echo'<nav>
			<ul class="pagination">
 				<li><a href="?date='. $date->sub(new DateInterval('P4D'))->format('Y-m-d').'">'. $date->format('d-m-Y').'</a></li>
 		   		<li><a href="?date='. $date->add(new DateInterval('P1D'))->format('Y-m-d').'">'. $date->format('d-m-Y').'</a></li>
 		   		<li><a href="?date='. $date->add(new DateInterval('P1D'))->format('Y-m-d').'">'. $date->format('d-m-Y').'</a></li>
 		   		<li><a href="?date='. $date->add(new DateInterval('P1D'))->format('Y-m-d').'">'. $date->format('d-m-Y').'</a></li>';
				if(isset($_GET['date'])){
 		  			echo'<li><a href="?date='.$today.'"><b>'.$todayp->format('d-m-Y') .'</b></a></li>';
				} else {
					echo'<li><a href="?date='.$today.'"><b>TODAY</b></a></li>';
				}
 		   		echo'<li><a href="?date='. $date->add(new DateInterval('P2D'))->format('Y-m-d').'">'. $date->format('d-m-Y').'</a></li>
 		   		<li><a href="?date='. $date->add(new DateInterval('P1D'))->format('Y-m-d').'">'. $date->format('d-m-Y').'</a></li>
 		   		<li><a href="?date='. $date->add(new DateInterval('P1D'))->format('Y-m-d').'">'. $date->format('d-m-Y').'</a></li>
 		 	</ul>
			</nav>';

		}

		echo'<table width="100%" height="" border="0" cellpadding="3" cellspacing="0">'.
		'<tr class="tableheader">
		<td width="100px"><b>Transaction ID</b></td>' .
		'<td width="70px"><b>Time</b></td>' .
		'<td width="70px"><b>Amount</b></td>' .
		'<td width="60px"><b>Student</b></td>' .
		'<td width="150px"><b>Deposit Name</b></td>' .
		'<td width="150px"><b>Linked to</b></td>' .
		'<td width="30px"><b>Match</b></td>';
		if($print != TRUE){
			echo '<td><b>Management</b></td>';
		}
		echo '</tr>';

		$i = 0;
		$percent = 0;
		$percenttwo = 0;
		$color = "";
		$name = "";
		$userid = "";
		$output = "";
		setlocale(LC_MONETARY, 'en_US.UTF-8');

		while ($fetch = $run->fetch_row()) {
			
			
			if($this->core->action != "unknown"){
			$percenttwo = 0;
			$name1 = ucwords(strtolower($fetch[8]));
			$name2 = ucwords(strtolower($fetch[18] . " " . $fetch[16]));
			similar_text($name1, $name2, $percent);
			$percent = floor($percent);

			$name1 = ucwords(strtolower($fetch[8]));
			$name2 = ucwords(strtolower($fetch[18] . " " . $fetch[17] . " " .  $fetch[16]));
			similar_text($name1, $name2, $percenttwo);
			$percenttwo = floor($percenttwo);

			$name1 = ucwords(strtolower($fetch[8]));
			$name2 = ucwords(strtolower($fetch[16] . " " . $fetch[17] . " " .  $fetch[18]));
			similar_text($name1, $name2, $percentthree);
			$percentthree = floor($percentthree);

			if($percenttwo>$percent){
				$percent = $percenttwo;
			}

			if($percentthree>$percent){
				$percent = $percentthree;
			}

			if($percent<70){
				$color = 'style="color: #FF0000;"';
				$match = FALSE;
				$edit = "assign";
			} else {
				$match = TRUE;
				$color = '';
				$edit = "reassign";
			}

			if($fetch[14] == "PROCESSED"){
				$color = 'style="color: #4F8A10;"';
				$edit = "reassign";
			}

			if($fetch[14] == "MANUAL"){
				$color = 'style="color: #D61EBE;"';
			}

			if(!empty($name1)){
				$percent = '<b>('. $percent .'%)</b>';
			}

			$name = $fetch[16] .'  '. $fetch[18];
			$userid = $fetch[20];
			}

			$amount =  money_format('%!.0n', $fetch[7]);
			$amountb =  $fetch[7];
			$total =  $fetch[7]+$total;

			$output .= '<tr ' . $color . '>
			<td><b><a href="' . $this->core->conf['conf']['path'] . '/payments/view/' . $fetch[0] . '"> ' . $fetch[3] . '</a></b></td>
			<td>' . $fetch[6] . '</td>
			<td><b>' . $amount . ' '.$this->core->conf['conf']['currency'].'</b></td>
			<td>' . $fetch[4] . '</td>
			<td>' . ucwords(strtolower($fetch[8])) . ' </td>
			<td><a href="' . $this->core->conf['conf']['path'] . '/information/show/'. $userid .'?payid='.$fetch[0].'&date='.$today.'">'.$name.'</a></td>
			<td>'. $percent .'</td>';

			if($print != TRUE){
				$output .='<td>
				<a href="' . $this->core->conf['conf']['path'] . '/payments/assign/' . $fetch[0] . '?date='.$today.'"> <img src="' . $this->core->fullTemplatePath . '/images/edi.png"> '.$edit.'</a>
 				</td>';
			}
			$output .= '</tr>';
		}

		$total = money_format('%!.0n', $total);

		echo '<div class="successpopup">A total of '.$run->num_rows . " payments for the selected date: $today </div> 
		<div class=\"warningpopup\"> Total income for  $today at the time of printing is: $total ".$this->core->conf['conf']['currency']."</div>";
		echo $output;
		echo '</table>';
	}

	function listPayments($item){
		$this->managePayments($this->core->userID);
	}

	function printPayments($item){
		
		echo'<script type="text/javascript">
			window.print();
		</script>';

		$this->managePayments($item, TRUE, TRUE);
	}

	function monthPayments($item){


		if($this->core->role > 10){
			$this->viewMenu();
		}

		echo '<div class="toolbar">'.
		'<a href="' . $this->core->conf['conf']['path'] . '/payments/month/cash">Cash payments only</a>
		<a href="' . $this->core->conf['conf']['path'] . '/payments/month">All payments</a></div>';
		
		$sql = "SELECT  SUM(Amount), COUNT(ID), MONTH(`transactions`.TransactionDate), YEAR(`transactions`.TransactionDate) FROM `transactions`
		WHERE Status != 'REVERSED' 
		GROUP BY YEAR(`transactions`.TransactionDate), MONTH(`transactions`.TransactionDate)";

		if($item == "cash"){
			$sql = "SELECT  SUM(Amount), COUNT(ID), MONTH(`transactions`.TransactionDate), YEAR(`transactions`.TransactionDate) FROM `transactions`
			WHERE `Type` = 10 OR `Type` = 1 AND `TransactionID` LIKE 'HOUSING%'
			GROUP BY YEAR(`transactions`.TransactionDate), MONTH(`transactions`.TransactionDate)";
			$cash = "/cash";
		}

		$run = $this->core->database->doSelectQuery($sql);


		$theader = '<table class="table table-bordered table-striped table-hover">
			<thead>
				<tr>
					<td bgcolor="#EEEEEE"><b>Month</b></td>
					<td bgcolor="#EEEEEE"><b>Number of payments</b></td>
					<td bgcolor="#EEEEEE"><b>Daily total</b></td>
					<td bgcolor="#EEEEEE"><b>Export report</b></td>
				</tr>
			</thead>
			<tbody>';

		setlocale(LC_MONETARY, 'en_US.UTF-8');
		$counter=0;
		$countyear=0;
		$totalyear=0;
		$lastset = FALSE;
		
		while ($fetch = $run->fetch_row()) {
	
			$lastset = FALSE;
			$date = $fetch[0];
			$amount = $fetch[0];
			$month = $fetch[2];
			$year = $fetch[3];

			if($month == NULL){
				continue;
			}

			$dateObj   = DateTime::createFromFormat('!m', $month);
			$monthName = $dateObj->format('F');
			$monthn = $dateObj->format('m');
		

			$count = $fetch[1];
			$countyear = $countyear+$count;
			$totalyear = $totalyear+$amount;
			$amount = money_format('%!.0n', $amount);

			if($counter == 0){
				echo '<h2>Overview of payments for '.$year.'</h2><br>';
				echo $theader;
			}

			$counter++;

			echo '<tr>
				<td><b><a href="' . $this->core->conf['conf']['path'] . '/payments/daily'.$cash.'?m='.$monthn.'&y='.$year.'">'.$monthName.' '.$year.'</a></b></td>
				<td>'.$count.' transactions</td>
				<td><b>'.$amount.' ZMW</b></td>
				<td style="background-color: #eeeeee;"><a href="#">Generate Report</a></td>
			</tr>';

			$totalyearrd = money_format('%!.0n', $totalyear);
			$finalrow = '	<tr>
					<td style="background-color: #eeeeee;"><b>YEARLY TOTAL</b></td>
					<td style="background-color: #eeeeee;"><b>'.$countyear.' transactions</b></td>
					<td style="background-color: #eeeeee;"><b>'.$totalyearrd.' ZMW</b></td>
					<td style="background-color: #eeeeee;"><b><a href="#">Generate Yearly</a></b></td>
				</tr>';

			if($month == 12){
				echo $finalrow;

				$countyear = 0;
				$totalyear = 0;

				echo'</tbody></table>';
				$nyear = $year+1;
				echo '<h2>Overview of payments for '.$nyear.'</h2><br>';
				echo $theader;
				$lastset = TRUE;
			}
		}
		if($lastset == FALSE){	
			echo $finalrow;
		}
		echo'</tbody></table>';
	}


	function dailyPayments($item){
		$cash = 'payments/manage?date=';

		if(isset($this->core->cleanGet['m'])){
			$month = $this->core->cleanGet['m'];
			$year = $this->core->cleanGet['y'];

			$date = "$year-$month-%"; 
		
			$sql = "SELECT TransactionDate, SUM(Amount), COUNT(ID) FROM `transactions` 
			WHERE `transactions`.TransactionDate LIKE '$date'
			GROUP BY `transactions`.TransactionDate";

			if($item == "cash"){
				$sql = "SELECT TransactionDate, SUM(Amount), COUNT(ID) FROM `transactions` 
				WHERE `transactions`.TransactionDate LIKE '$date'
				AND `Type` IN (1,10) 
				AND `RequestID` LIKE 'HOUSING%'
				OR 
				 `transactions`.TransactionDate LIKE '$date'
				AND `Type` IN (1,10) 
				AND `RequestID` LIKE 'NCE%'
				GROUP BY `transactions`.TransactionDate";

				$cash = 'statistics/collection/';
			}
		} else {
			$sql = "SELECT TransactionDate, SUM(Amount), COUNT(ID) FROM `transactions` 
			GROUP BY `transactions`.TransactionDate";
		}

		$run = $this->core->database->doSelectQuery($sql);

		if($this->core->role > 10){
			$this->viewMenu();
		}

		echo '<div class="toolbar">'.
		'<a href="' . $this->core->conf['conf']['path'] . '/payments/daily/cash">Cash payments only</a>
		<a href="' . $this->core->conf['conf']['path'] . '/payments/daily">All payments</a></div>';

		echo'<table class="table table-bordered table-striped table-hover">
			<thead>
				<tr>
					<td bgcolor="#EEEEEE"><b>Date</b></td>
					<td bgcolor="#EEEEEE"><b>Number of payments</b></td>
					<td bgcolor="#EEEEEE"><b>Daily total</b></td>
				</tr>
			</thead>
			<tbody>';

		setlocale(LC_MONETARY, 'en_US.UTF-8');

		while ($fetch = $run->fetch_row()) {
			$date = $fetch[0];
			$amount = $fetch[1];
			$amount = money_format('%!.0n', $amount);
			$count = $fetch[2];
			echo '<tr><td><b><a href="' . $this->core->conf['conf']['path'] . '/'.$cash.''.$date.'">'.$date.'</a></b></td><td>'.$count.' transactions</td><td><b>'.$amount.' ZMW</b></td></tr>';
		}

	echo'</tbody>
		</table>';
	}


	function renewPayments($item){

		$path = "datastore/output/receipts/";
		$filename = $path. $item . ".pdf";
		unlink($filename);

		echo'<div class="successpopup">RECEIPT WILL BE REGENERATED</div>';

	}

	function viewPayments($item){
		

		$path = "datastore/output/receipts/";
		$filename = $path. $item . ".pdf";


		$sql = "SELECT * FROM `transactions`, `basic-information`
			WHERE `transactions`.ID = '$item'
			AND `transactions`.StudentID = `basic-information`.ID";

		$run = $this->core->database->doSelectQuery($sql);

		$name = $item . "-" .date('Y-m-d');
		include $this->core->conf['conf']['classPath'] . "security.inc.php";
		$security = new security();
		$security->buildView($this->core);
		$name = $security->qrSecurity($name, $owner, $item, $name);



		while ($fetch = $run->fetch_assoc()) {
			
			if(file_exists($filename)){
				continue;
			}


			$today =  date("Y-m-d");
			$admin = $this->core->userID;
			$owner = $this->core->userID;
			$uid =  $fetch["StudentID"];

			$output .= '<div style="position: absolute; right: -20px; font-size: 7pt; text-align: center; float:right; ">
					<img src="/data/website/edurole/datastore/output/secure/'.$name.'.png"><br>'.$name.'
			</div><center>
			<img height="100px" src="/data/website/edurole/templates/edurole/images/header.png" />
			<div style=" font-size: 22pt; color: #333; margin-top: 15px; margin-left: -30px; ">'.$this->core->conf['conf']['organization'].'<div style="font-size: 13pt">PAYMENT CONFIRMATION RECEIPT</div></div>
			<h2>OFFICIAL RECEIPT: KNU-'.$item.'</h2><hr>
			</center>';



			$this->core->audit(__CLASS__, $item, $uid, "Generating receipt $item.pdf");


			$output .=  '<h2>PAYMENT DETAILS '.$item.'</h2><br><table width="768" border="0" cellpadding="5" cellspacing="0">
                  	<tr class="heading">
                  	  <td width="205" height="28" bgcolor="#EEEEEE"><strong>Information</strong></td>
                  	  <td width="200" bgcolor="#EEEEEE"></td>
                	    <td  bgcolor="#EEEEEE"></td>
               	  	 </tr>
               	   	<tr>
                    	<td><strong>Transaction ID</strong></td>
                    	<td> <b>' . $fetch["TransactionID"] . '</b></td>
                    	<td></td>
                  	</tr>
				  <tr>
                    	<td><strong>Receipted by Owner</strong></td>
                    	<td> <b>'.$admin.'</b></td>
                   	 <td></td>
                  	</tr>
                  	<tr>
                  	  <td><strong>Student</strong></td>
                   	 <td><a href="' . $this->core->conf['conf']['path'] . '/information/show/' . $fetch["StudentID"] . '">' . $fetch["FirstName"] . ' ' . $fetch["Surname"] . '</a></td>
                  	 <td></td>
                  	</tr>
                   	<tr>
                   	 <td><strong>Student ID</strong></td>
                    	<td><b>' . $uid . '</b></td>
                    	<td></td>
                  	</tr>
                  	<tr>
                    	<td><strong>Transaction Date</strong></td>
                    	<td>' . $fetch["TransactionDate"] . '</td>
                    	<td></td>
                  	</tr>
                 	 <tr>
                 	   <td><strong>Transaction Received</strong></td>
                 	   <td>' . $fetch["Timestamp"] . '</td>
                 	   <td></td>
                 	 </tr>
                  	<tr>
                  	  <td><strong>Amount</strong></td>
                    	<td><b>' . $fetch["Amount"] . '</b> KWACHA</td>
                    	<td></td>
                 	 </tr>
                  	<tr>
                    	<td><strong>Payee</strong></td>
                    	<td>' . $fetch["Name"] . '</td>
                    	<td></td>
                  	</tr>
                  	<tr>
                    	<td><strong>Type</strong></td>
                    	<td>' . $fetch["Type"] . '</td>
                    	<td></td>
                  	</tr>
                  	<tr>
                   	 <td><strong>Status</strong></td>
                   	 <td>' . $fetch["TS"] . ' TRANSACTION</td>
                   	 <td></td>
                  	</tr>
                 	 <tr>
                    	<td><strong>Status</strong></td>
                    	<td>' . $fetch["Error"] . ' TRANSACTION</td>
                   	 <td></td>
                 	 </tr>
			 </table>
			<br>
			
			<table style="border: 1px solid #000; border-collapse: collapse;" border="1px">	
				<tr>
					<td width="250px">
						Date Stamp
					</td>
					<td width="250px">
						Signature Issuing Officer
					</td>
				</tr>
				<tr>
					<td height="100px">
	
					</td>
					<td height="100px">

					</td>
				</tr>
			</table>';
			


			$sql = "INSERT INTO `receipts` (`ID`, `OfficerID`, `StudentID`, `DateTime`, `Hash`, `TotalAmount`, `PrintCount`) 
			VALUES (NULL, '$owner', '$item', NOW(), '', '$totalpayed', '1');";

			$this->core->database->doInsertQuery($sql);
			$receiptno = $this->core->database->id();
			$receiptno = str_pad($receiptno, 6, '0', STR_PAD_LEFT);



			require_once $this->core->conf['conf']['libPath'] . 'dompdf/dompdf_config.inc.php';



			$dompdf= new Dompdf();
			//$dompdf->setPaper('A4', 'portrait');
			$dompdf->load_html($output);
			$dompdf->render();



			//$dompdf->stream();
			$pdf = $dompdf->output();


			file_put_contents($filename, $pdf);


		}

		echo '<div class="toolbar">'.
		'<a href="/edurole/datastore/output/receipts/'.$item.'.pdf">PRINT RECEIPT FOR PAYMENT</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/payments/renew/'.$item.'">DELETE RECEIPT</a>'.
		'</div>';

		$sql = "SELECT * FROM `transactions`, `basic-information`
			WHERE `transactions`.ID = '$item'
			AND `transactions`.StudentID = `basic-information`.ID";
		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_assoc()) {

		echo '<h2>Payment details</h2><br><table width="768" border="0" cellpadding="5" cellspacing="0">
                  <tr class="heading">
                    <td width="205" height="28" bgcolor="#EEEEEE"><strong>Information</strong></td>
                    <td width="200" bgcolor="#EEEEEE"></td>
                    <td  bgcolor="#EEEEEE"></td>
                  </tr>
                  <tr>
                    <td><strong>Transaction ID</strong></td>
                    <td> <b>' . $fetch["TransactionID"] . '</b></td>
                    <td></td>
                  </tr>
                  <tr>
                    <td><strong>Student</strong></td>
                    <td><a href="' . $this->core->conf['conf']['path'] . '/information/show/' . $fetch["StudentID"] . '">' . $fetch["FirstName"] . ' ' . $fetch["Surname"] . '</a></td>
                    <td></td>
                  </tr>
                   <tr>
                    <td><strong>Student ID</strong></td>
                    <td><b>' . $fetch["StudentID"] . '</b></td>
                    <td></td>
                  </tr>
                  <tr>
                    <td><strong>Transaction Date</strong></td>
                    <td>' . $fetch["TransactionDate"] . '</td>
                    <td></td>
                  </tr>
                  <tr>
                    <td><strong>Transaction Received</strong></td>
                    <td>' . $fetch["Timestamp"] . '</td>
                    <td></td>
                  </tr>
                  <tr>
                    <td><strong>Transaction owner</strong></td>
                    <td><a href="' . $this->core->conf['conf']['path'] . '/information/show/' . $fetch["UID"] . '">' . $fetch["UID"] . '</a></td>
                    <td></td>
                  </tr>
                  <tr>
                    <td><strong>Amount</strong></td>
                    <td><b>' . $fetch["Amount"] . '</b> KWACHA</td>
                    <td></td>
                  </tr>
                  <tr>
                    <td><strong>Payee</strong></td>
                    <td>' . $fetch["Name"] . '</td>
                    <td></td>
                  </tr>
                  <tr>
                    <td><strong>Type</strong></td>
                    <td>' . $fetch["Type"] . '</td>
                    <td></td>
                  </tr>
                  <tr>
                    <td><strong>Status</strong></td>
                    <td>' . $fetch["TS"] . ' TRANSACTION</td>
                    <td></td>
                  </tr>
                  <tr>
                    <td><strong>Status</strong></td>
                    <td>' . $fetch["Error"] . ' TRANSACTION</td>
                    <td></td>
                  </tr>
                 <tr>
                    <td><strong>Data</strong></td>
                    <td>' . nl2br($fetch["Data"]) . ' </td>
                    <td></td>
                  </tr>
		 </table>';

		}

	}

	public function studentPayments($item) {
		$item = $this->core->userID;
		$this->showPayments($item);
	}	

	public function personalPayments($item) {
		
		$balance = $this->getBalance($this->core->userID);

		if($balance <= 0){
			echo'<div class="toolbar">'.
			'<a style="width: 100%; font-size: 14pt; height: 40pt; margin-left: -15pt; width: 530pt; background-color: #000;" href="' . $this->core->conf['conf']['path'] . '/confirmation/print/'.$item.'">Click here to print confirmation slip</a>
			</div>';
		} else {
			echo '<div class="warningpopup">You can only print your confirmation slip here when you have settled your remaining balance of K'.$balance.'</div>';
		}

		$this->showPayments($this->core->userID);
	}

	public function getBalance($item){

			$output = file_get_contents("http://41.63.17.247:8080/AccPackService/balance/$item");

			$p = xml_parser_create();
			xml_parse_into_struct($p, $output, $vals, $index);
			xml_parser_free($p);

			$balance = (float)$vals[1]["value"];

			return $balance;
	} 

	

	public function showPayments($item){

		$balance = $this->getBalance($this->core->userID);

		$sql = "SELECT * FROM `basic-information`
			WHERE `basic-information`.ID = '$item'";


		$run = $this->core->database->doSelectQuery($sql);

		while ($row = $run->fetch_row()) {
			$sstatus = $row[20];
		}


		$cleared = $this->getCleared($item);


		if($this->core->role == 102 || $this->core->role == 1000){
			echo '<div class="toolbar">'.
			'<a href="' . $this->core->conf['conf']['path'] . '/information/show/'.$item.'">Return to profile </a>'.
			'<a href="' . $this->core->conf['conf']['path'] . '/payments/add/'.$item.'?type=10">Collect payment</a>'.
			'<a href="' . $this->core->conf['conf']['path'] . '/billing/add/'.$item.'?amount=0&type=15&description=Bill">Bill student</a>'.
			'<a href="' . $this->core->conf['conf']['path'] . '/payments/confirm/'.$item.'">Confirm payment</a>'.
			'<a href="' . $this->core->conf['conf']['path'] . '/confirmation/print/'.$item.'">Print confirmation slip</a></div>';

			if($cleared == FALSE){
				echo '<div class="approval">
				<a href="' . $this->core->conf['conf']['path'] . '/payments/clear/'.$item.'">Clear Student for period </a></div>';
			}
		}

		if($sstatus!="Approved" && $sstatus != 'Graduated'){
			echo'<a href="' . $this->core->conf['conf']['path'] . '/admission/activate/'.$uid.'"><div style="background-color: red; font-weight: bold; font-size: 14px; border: 1px solid #ccc; text-align: center; padding: 3px; color: #FFF;">ACCOUNT NOT ACTIVE</div></a>';
			return;
		}

		if(empty($item)){
			$item = $this->core->userID;
		}

		
		$sqlx = "SELECT * FROM `balances` WHERE `StudentID` = '$item'";
		$runx = $this->core->database->doSelectQuery($sqlx);
		while ($fetch = $runx->fetch_assoc()) {
			$balance = $fetch['Original'];
			$currentbalance = $fetch['Amount'];
			$code = $fetch['AccountCode'];
		}

		if(!isset($balance)){
			$balance = 0;
		}


		$sqlx = "SELECT * FROM `discount` WHERE `StudentID` = '$item'";
		$runx = $this->core->database->doSelectQuery($sqlx);
		while ($fetch = $runx->fetch_assoc()) {
			$discount = $fetch['Percentage'];

			echo'<div class="successpopup">THIS STUDENT IS ON A DISCOUNTED FEE SCHEDULE ('.$discount.'%)</div>';
		}



		$sqlp = "SELECT * FROM `transactions` 
			LEFT JOIN `basic-information`
			ON `transactions`.StudentID = `basic-information`.ID 
			WHERE `basic-information`.ID = '$item'
			ORDER BY TransactionDate";


		$sqlp = "SELECT `basic-information`.ID, 
				`basic-information`.FirstName, 
				`basic-information`.Surname, 
				`transactions`.ID as TID,
				`transactions`.Amount, 
				`transactions`.TransactionID, 
				`transactions`.TransactionDate  as DATE, 
				`transactions`.Data as DATA, 
				`transactions`.Status,
				`transactions`.Type  as TYPE
			 FROM `transactions`, `basic-information` 
			WHERE `transactions`.`StudentID` = `basic-information`.ID 
			AND `basic-information`.ID = '$item' 
			AND `transactions`.`Status` != 'REVERSED'
				UNION 
			SELECT  `basic-information`.ID, 
				`basic-information`.FirstName, 
				`basic-information`.Surname, 
				`billing`.ID as TID,
				`billing`.Amount, 
				CONCAT('NCU-', `billing`.ID), 
				`billing`.Date as DATE, 
				`billing`.Description as DATA, 
				'BILL',
				'100' as TYPE
			FROM `billing`, `basic-information`
			WHERE `billing`.`StudentID` = `basic-information`.ID 
			AND `basic-information`.ID = '$item'
			AND `billing`.`Description` != 'REVERSED'
			ORDER BY DATE ASC";

		
		
		$runp = $this->core->database->doSelectQuery($sqlp);



		$sqlb = "SELECT * FROM `balances`
			WHERE StudentID = '$item'";
		$runb = $this->core->database->doSelectQuery($sqlb);


		$housing = FALSE;


		$i = 0;
		$total = 0;
		$totalpayed = 0;

		echo '<div style=" padding: 0px; margin-bottom: 10px;">

		<table cellpadding="3"  class="table table-striped"cellspacing="0" border="1" style="font-size: 9pt;  margin-left: 20px; width: 700px;">'.
		'<tr>' .
		'<td width="120px"><b>TRANSACTION</b></td>' .
		'<td width="80px"><b>DATE</b></td>' .
		'<td width=""><b>TYPE</b></td>' .
		'<td width="60px"><b>DESCRIPTION</b></td>' .
		'<td width="60px"><b>OPT</b></td>' .
		'<td width=""><b>DEBIT</b></td>' .
		'<td width=""><b>CREDIT</b></td>' .	
		'<td width=""><b>BALANCE</b></td>' .
		'</tr>';


		setlocale(LC_MONETARY, 'en_US.UTF-8');

		$sqls = "SELECT StudyType FROM `basic-information` WHERE `ID` = '$item'";
		$runs = $this->core->database->doSelectQuery($sqls);
		while ($fetchs = $runs->fetch_assoc()) {
			$type = $fetchs["StudyType"];
		}

	
		echo '<tr>
		<td><b> OPENING BALANCE</b></td>
		<td><b> FROM ACCOUNTS</b></td>
		<td colspan="5"></td>
		<td style="text-align: right;"><b> '.$balance.' </b></td>
		</tr>';

		$i = 0;
		while ($fetch = $runp->fetch_assoc()) {

			$typet = $fetch['TYPE'];
			$amount =  $fetch["Amount"];
			$date = $fetch["DATE"];
			$name = $fetch["FirstName"] .'  '. $fetch["Surname"];
			$description = $fetch["DATA"];

			if($type=="Fulltime"){
				if( strtotime($date) < strtotime('2015-7-31') ) {
					continue;
				} 
			}else{
				if( strtotime($date) < strtotime('2016-3-01') ) {
					continue;
				}
			}

			if($amount < 0){
				$type = "CREDIT";
			}else if ($amount > 0){
				$type = "BANK PAYMENT";
			}

			if($percent<70){
				//$color = 'style="color: #FF0000;"';
			} else {
				$color = '';
			}

			if(!empty($name1)){
				$percent = '<b>('. $percent .'%)</b>';
			}
			
			if($fetch[14] == "REVERSED"){
				continue;
			}

			$ltype = 'payments';

			if($fetch["Status"] == "BILL"){
				$type = "BILLING";
				if($discount > 1){
					$amount = $amount/100*$discount;
				}
				$ltype = 'billing';
			}


			if($fetch["Status"] == "SUCCESS"){
				//$color = 'style="color: #4F8A10;"';
				$edit = "reassign";
			}

			if($fetch["Status"] == "PROCESSED"){
				//$color = 'style="color: #333;"';
				$edit = "reassign";
			}

			if($fetch["Status"] == "REVERSED" || $fetch["DATA"] == "REVERSED"){
				//$color = 'style="color: #CCCCCC;"';
				$type = "REVERSED";
			}

			if($fetch["Status"] == "MANUAL" || $fetch["Status"] == "SUCCESS"){
				//$color = 'style="color: #D61EBE;"';
				$reverse = TRUE; 
				$type = "MANUAL DEPOSIT";
			}


			if($type == "BILLING"){
				$debit = money_format('%!.0n', $amount);
				$balance = $balance+$amount;
			} else {
				$credit = money_format('%!.0n', $amount);
				$balance = $balance-$amount;
			}


			$description = $fetch["DATA"];
			if (strpos($description, '=============') !== false) { $description = "Billmuster Deposit (ZANACO)"; }



			if($typet==20){
				$type = "CREDIT NOTE";
			}

			echo '<tr ' . $color . '>
			<td><b><a href="' . $this->core->conf['conf']['path'] . '/payments/view/' . $fetch["TID"] . '"> ' . $fetch["TransactionID"] . '</a></b></td>

			<td>' . $date . '</td>
			<td><b>'. $type .'</b></td>
			<td><i>'.$description.'</i></td>
			<td>
			<a href="' . $this->core->conf['conf']['path'] . '/'.$ltype.'/edit/' . $fetch['TID'] . '"> <img src="' . $this->core->fullTemplatePath . '/images/edi.png"> edit</a>
 			</td>
			<td style="text-align: right;">' . $debit . ' </td>
			<td style="text-align: right;">' . $credit . '  </td>
			<td style="text-align: right;"><b>' . $balance . ' </b></td>';
	

			$credit = "";
			$debit = "";
		}


		echo '<tr>
		<td colspan="8">&nbsp;</td>
		</tr>';

		echo '<tr>
		<td colspan="7"><b> CURRENT BALANCE</b></td>
		<td style="text-align: right;"> <b>'.$balance.'</b> </td>
		</tr>';

		echo '</table>';

	}
	

	public function updatePayments($item=NULL, $print) {
		$this->updatingPayments("all");
	}


	public function updatingPayments($item=NULL, $print) {

		$today = date("Y-m-d");

		if(isset($_GET['date'])){
			$today = $_GET['date'];
		}

		$date = new DateTime($today);
 

		if($item=="all"){
			$sql = "SELECT * FROM `transactions`, `basic-information`
			WHERE `transactions`.StudentID = `basic-information`.ID 
			AND `StudyType` = 'Distance'
			AND `transactions`.`TransactionDate` > '2016-01-16'
			AND `transactions`.`Error` != 'PROCESSED'
			AND `transactions`.`Error` != 'MANUAL'
			ORDER BY `transactions`.TransactionDate";
		}else if($item=="distance"){
			$sql = "SELECT * FROM `transactions`, `basic-information`
			WHERE `transactions`.StudentID = `basic-information`.ID 
			AND `StudyType` = 'Distance'
			AND `transactions`.`TransactionDate` > '2016-01-16'
			AND `transactions`.`Error` != 'PROCESSED'
			AND `transactions`.`Error` != 'MANUAL'
			ORDER BY `transactions`.TransactionDate";
		}else if($item=="fulltime"){
			$sql = "SELECT * FROM `transactions`, `basic-information`
			WHERE `transactions`.StudentID = `basic-information`.ID 
			AND `StudyType` = 'Fulltime'
			AND `transactions`.`TransactionDate` > '2016-02-01'
			AND `transactions`.`Error` != 'PROCESSED'
			AND `transactions`.`Error` != 'MANUAL'
			ORDER BY `transactions`.TransactionDate";
		}
		
		$run = $this->core->database->doSelectQuery($sql);


		$yesterday = new DateTime($today);
		$yesterday = $yesterday->sub(new DateInterval('P5D'))->format('Y-m-d');

		$todayp = new DateTime($today);
		
		$i = 0;
		$percent = 0;
		$percenttwo = 0;
		$color = "";
		$name = "";
		$userid = "";
		$output = "";
		setlocale(LC_MONETARY, 'en_US.UTF-8');

		while ($fetch = $run->fetch_assoc()) {
			
			$i++;

			$percenttwo = 0;

			if($fetch["SID"] != ""){
				$percent = 100;
			}

			if($percenttwo>$percent){
				$percent = $percenttwo;
			}

			if($percentthree>$percent){
				$percent = $percentthree;
			}

			if($percent<70){
				$color = 'style="color: #FF0000;"';
				$match = FALSE;
				$edit = "assign";
			} else {
				$match = TRUE;
				$color = '';
				$edit = "reassign";
			}


			if(!empty($name1)){
				$percent = '<b>('. $percent .'%)</b>';
			}

			$name = $fetch["FirstName"] .'  '. $fetch["Surname"];
			$userid = $fetch["StudentID"];
			

			$amount =  money_format('%!.0n', $fetch["Amount"]);
			$amountb =  $fetch["Amount"];
			$total =  $fetch["AmountT"]+$total;


			$transaction = $fetch['TID'];
			$date = $fetch['TransactionDate'];


			$sql = "UPDATE `transactions` SET `Status` = 'SUCCESS' WHERE `transactions`.`TransactionID` = '$transaction';";
			//$this->core->database->doInsertQuery($sql);
			
			$sql = "SELECT * FROM `balances` WHERE `StudentID` = '$userid'";
			$runx = $this->core->database->doSelectQuery($sql);
			while ($fetchd = $runx->fetch_assoc()) {
				$balance = $fetchd["Amount"];
			}

			$sql = "UPDATE `balances` SET `Amount` = Amount-$amountb, `LastUpdate` = NOW(), `LastTransaction` = '$transaction' WHERE `StudentID` = '$userid';";
			//$this->core->database->doInsertQuery($sql);
			
			echo "<b>UPDATING: ". $i ." - ". $transaction ."</b>: ". $date . " - Student: <b>" . $userid ."</b>, paid: ". $amountb . ", current balance: ".$balance."<br />"; 

		}
	}

	public function logPayments($item=NULL, $print) {
		$uid = $this->core->cleanGet['uid'];
		$admin = $this->core->cleanGet['admin'];
		$amount = $this->core->cleanGet['amount'];
		$description = $this->core->cleanGet['description'];
		$type = $this->core->cleanGet['paymenttype'];
		$date = $this->core->cleanGet['date'];
		$transactionuid = $this->core->cleanGet['reference'];

		$sql = "UPDATE `transactions` SET `Data` = '$admin' WHERE `transactions`.`TransactionID` = '$transactionuid';";
		$this->core->database->doInsertQuery($sql);

		echo $sql;
	}


}

?>
