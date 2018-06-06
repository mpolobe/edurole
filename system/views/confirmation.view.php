<?php
class confirmation {

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

	public function printConfirmation($item){

		$room = $_GET['room'];
		$studentID = $_GET['uid'];
		$studentNo = $studentID;

		if($this->core->role < 100){
			$item = $this->core->userID;
		}

		include $this->core->conf['conf']['viewPath'] . "payments.view.php";
		$payments = new payments();
		$payments->buildView($this->core);
		$actual = $payments->getBalance($item);


		if($this->core->role < 100 && $actual > 0){
			echo'YOU ARE OWING K'.$actual.' ONLY WHEN YOU SETTLE THIS BALANCE MAY YOU PRINT';
		}


		$owner = $this->core->userID;
		$name = $owner . "-conf-" .date('Y-m-d').'.html';
		$path = "datastore/output/confirmations/";
		$filename = $path. $name;

		include $this->core->conf['conf']['classPath'] . "security.inc.php";
		$security = new security();
		$security->buildView($this->core);
		$name = $security->qrSecurity($name, $owner, $actual, $name);

		if(empty($item)){
			$item = $this->core->userID;
			$studentID = $item;
			$studentNo = $item;
		}

		$start = substr($studentID, 0, 4);

		$sql = "SELECT Firstname, MiddleName, Surname, Status, Sex, Status, ProgramNo, StudyType 
				FROM `basic-information`, `programmes-link`, `student-program-link` 
				WHERE `basic-information`.ID = '$studentID' AND `student-program-link`.`StudentID` = `basic-information`.ID AND `student-program-link`.`ProgrammeID` = `programmes-link`.ID";

		$sql = "SELECT Firstname, MiddleName, Surname, Status, Sex, Status, StudyType FROM `basic-information` WHERE `basic-information`.ID = '$item'";

		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_row()) {
			$firstname = $fetch[0];
			$lastname  = $fetch[2];
			$status  = $fetch[5];
			$delivery  = $fetch[6];

			$today =  date("Y-m-d");
			$admin = $this->core->userID;

			$output .='<div style="position: absolute; right: 0px; font-size: 7pt; text-align: center; float:right; ">
					<img src="/edurole/datastore/output/secure/'.$name.'.png"><br>'.$name.'
			</div>
			<center><div style="width: 200px;"><a href="'. $this->core->conf['conf']['path'] .'"><img height="100px" src="'. $this->core->fullTemplatePath .'/images/header.png" /></a></div>
			<div style=" font-size: 22pt; color: #333; margin-top: 15px; margin-left: -30px; ">'.$this->core->conf['conf']['organization'].'<div style="font-size: 13pt">REGISTRATION CONFIRMATION SLIP </div></div>
			<div style=" font-size: 12px;">PRINTED BY ' . $admin . ' ON '. $today .'</div>
			</center><hr>';


		$output .='<h2>Financial Statement</h2>';
			$output .="<span style=\"font-size: 12pt; color: #333;\">This confirmation slip is printed for <b>$firstname $lastname (<u>$item</u>)</b> a <b>$delivery student</b> ($status).<br><br>";
			
 		}



		// GET COURSES
		$sqls = "SELECT DISTINCT `courses`.Name, `courses`.CourseDescription,  `EnrolmentDate`, `periods`.Name as PNAME FROM `course-electives`,`courses`, `periods`
			WHERE `course-electives`.StudentID  = '$item'
			AND `course-electives`.CourseID = `courses`.ID
			AND `PeriodID` = `periods`.ID
			AND `EnrolmentDate` IN (SELECT MAX(`EnrolmentDate`) FROM `course-electives` WHERE `course-electives`.StudentID  = '$item')
			AND `course-electives`.Approved IN (1)";
		$runo = $this->core->database->doSelectQuery($sqls);


		if($runo->num_rows == 0){
			$output .= '<h2>NO COURSES REGISTERED - THIS PRINTOUT IS INVALID!</h2>';
		}


		$sqlx = "SELECT * FROM `balances` WHERE `StudentID` = '$item'";
		$runx = $this->core->database->doSelectQuery($sqlx);
		while ($fetch = $runx->fetch_assoc()) {
			$balance = $fetch['Original'];
			$currentbalance = $fetch['Amount'];
			$code = $fetch['AccountCode'];
		}

		$sqlx = "SELECT * FROM `discount` WHERE `StudentID` = '$item'";
		$runx = $this->core->database->doSelectQuery($sqlx);
		while ($fetch = $runx->fetch_assoc()) {
			$discount = $fetch['Percentage'];

			$output .='<div class="successpopup">THIS STUDENT IS ON A DISCOUNTED FEE SCHEDULE ('.$discount.'%)</div>';
		}


		$sqlp = "SELECT * FROM `transactions` 
			LEFT JOIN `basic-information`
			ON `transactions`.StudentID = `basic-information`.ID 
			WHERE `basic-information`.ID = '$item'
			ORDER BY TransactionDate";


		$sqlp = "SELECT `basic-information`.ID, 
				`basic-information`.FirstName, 
				`basic-information`.Surname, 
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
				`billing`.Amount, 
				CONCAT('NCU-', `billing`.ID), 
				`billing`.Date as DATE, 
				`billing`.Description as DATA, 
				'BILL',
				'100' as TYPE
			FROM `billing`, `basic-information`
			WHERE `billing`.`StudentID` = `basic-information`.ID 
			AND `billing`.`Description` != 'REVERSED'
			AND `basic-information`.ID = '$item'
			ORDER BY DATE ASC";



		$runp = $this->core->database->doSelectQuery($sqlp);

		$i = 0;
		$total = 0;
		$totalpayed = 0;

		$output .= '<div style=" padding: 0px; margin-bottom: 10px;">

		<table cellpadding="3" cellspacing="0" border="1" style="width: 100%; font-size: 9pt; border-style:solid;  border: 1px solid #000;">'.
		'<tr style="border-bottom: 1px solid #000;">' .
		'<td width="120px"><b>TRANSACTION</b></td>' .
		'<td width="80px"><b>DATE</b></td>' .
		'<td width=""><b>TYPE</b></td>' .
		'<td width="60px"><b>DESCRIPTION</b></td>' .
		'<td width=""><b>DEBIT</b></td>' .
		'<td width=""><b>CREDIT</b></td>' .	
		'<td width=""><b>BALANCE</b></td>' .
		'</tr>';


		setlocale(LC_MONETARY, 'en_US.UTF-8');


			$sqlk = "SELECT StudyType FROM `basic-information` WHERE `ID` = '$item'";
			$runk = $this->core->database->doSelectQuery($sqlk);
			while ($fetchs = $runk->fetch_assoc()) {
				$type = $fetchs["StudyType"];
			}


		$output .= '<tr>
		<td><b> OPENING BALANCE</b></td>
		<td><b> FROM ACCOUNTS</b></td>
		<td colspan="4"></td>
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

			if($percentwo>$percent){
				$percent = $percenttwo;
			}

			if($percentthree>$percent){
				$percent = $percentthree;
			}

			if($fetch["Amount"] < 0){
				$type = "CREDIT";
			}else if ($fetch["Amount"] > 0){
				$type = "BANK PAYMENT";
			}

			if($percent<70){
				$color = 'style="color: #FF0000;"';
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

			if($fetch["Status"] == "MANUAL"){
				//$color = 'style="color: #D61EBE;"';
				$reverse = TRUE; 
				$type = "MANUAL DEPOSIT";
			}


			if($type == "BILLING"){
				$debit = money_format('%!.0n', $amount);
				$balance = $balance+$amount;
				$totaldebit = $totaldebit+$amount;
			} else {
				$credit = money_format('%!.0n', $amount);
				$balance = $balance-$amount;
				$totalcredit = $totalcredit+$amount; 
			}


			if(substr($description,22,7) == "STARTED"){
				$description = "ZANACO BILLMUSTER: " . $name;
			}

			if($typet==20){
				$type = "CREDIT NOTE";
			}

			$output .= '<tr ' . $color . '>
			<td><b> ' . $fetch["TransactionID"] . '</b></td>
			<td>' . $date . '</td>
			<td><b>'. $type .'</b></td>
			<td><i>'.$description.'</i></td>
			<td style="text-align: right;">' . $debit . ' </td>
			<td style="text-align: right;">' . $credit . '  </td>
			<td style="text-align: right;"><b>' . $balance . ' </b></td>';


			$credit = "";
			$debit = "";


		}


		$output .= '<tr>
		<td colspan="7">&nbsp;</td>
		</tr>';

			$output .= '<tr>
			<td colspan="4">Total credit/debit</td>
			<td style="text-align: right;">' . $totaldebit . ' </td>
			<td style="text-align: right;">' . $totalcredit . '  </td>
			<td style="text-align: right;">'.$balance.'</td>';

		$output .= '<tr>
		<td colspan="6"><b> CURRENT BALANCE</b></td>
		<td style="text-align: right;"> <b>'.$balance.'</b> </td>
		</tr>';

		$output .= '</table>';

			
		if($room != "no"){

			$sql = "SELECT * 
				FROM `housing`, `rooms`, `hostel`, `basic-information`, `periods`
				WHERE `housing`.RoomID = `rooms`.ID 
				AND `rooms`.HostelID = `hostel`.ID 
				AND `basic-information`.ID = `housing`.StudentID 
				AND `basic-information`.ID = '$item'
				AND `housing`.PeriodID = `periods`.ID";

			$run = $this->core->database->doSelectQuery($sql);

			while ($fetch = $run->fetch_assoc()) {
			$output .='<hr>';
				if($n == 0){
					$weeks = $fetch['Weeks'];
					if($delivery == "Distance"){
						$output .= '<h2>Housing Records</h2><b style="font-weight: bold; font-size: 12pt;">This student has been assigned the following accommodation <u>for '.$weeks.' WEEKS</u></b>';
					}else{
						$output .= '<h2>Housing Records</h2> <b style="font-weight: bold; font-size: 12pt;">This student has been assigned the following accommodation:</b>';
					}
					$n++;
				}

				$AccommodationName = $fetch['HostelName'];
				$RoomNumber = $fetch['RoomNumber'];
				$RoomType = $fetch['RoomType'];	
				
				$output .= '<br>
				<div style="font-weight: bold; font-size: 14px; width: 750px; margin: 0px; margin-bottom: 5px; padding-left: 10px;"><b>'.$housing.'</b></div>
				<table width="750" height="" border="0" cellpadding="0" cellspacing="0">
				<tr>
				<td width="200">Hostel name:</td>
				<td width=""><b>' . $AccommodationName . ' </b></td>
				</tr>
				<tr>
				<td>Room number:</td>
				<td width=""><b>' . $RoomNumber . ' (' . $RoomType . ')</b></td>
				</tr>
				</table></div>';

			}

		}



		if($actual < 100 && $delivery == "Distance"){


			$year = date("Y");

			$output .='<hr>';
			$output .= '<h2>Statement of Results '.$year.'</h2>';


			include $this->core->conf['conf']['viewPath'] . "grades.view.php";

			$grades = new grades();
			$grades->buildView($this->core);
			$output .= ' <h2>OUTSTANDING BALANCE!</h2><div class="errorpopup">According to our financial records you are owing the institution <u>K'.$actual.'</u>. 
				<br>Please check your payments and settle your balance to be able to access your grades';

			require_once $this->core->conf['conf']['viewPath'] . "statement.view.php";
				$statement = new statement();
			$statement->buildView($this->core);
			$year = date('Y');
			$statement->resultsStatement($item, $year);

		}





	// COURSE DETAILS

	$output .='<hr>';

	$output .='<h2>Registered Courses</h2>';


	while ($fetchw = $runo->fetch_assoc()) {
		$pname = $fetchw['PNAME'];
		if($i==0){
			$output .= '<b  style="font-weight: bold; font-size: 12pt;">This student has registered for the following courses for the term: <u>'.$pname.'</u></b>';
			$i++;
		}
		$output .='<li style="font-size: 10pt"><b>'.$fetchw['Name'].'</b>  - <i>'.$fetchw['CourseDescription'].'</i></li>';
	}

	if($runo->num_rows == 0){
		$output .= '<h2>NO COURSES REGISTERED - THIS PRINTOUT IS INVALID!</h2>';
	}	

	$today =  date("Y-m-d H:i:s");
	$admin = $this->core->userID;
	$output .='<hr>PRINTED BY ' . $admin . ' ON '. $today;
	$output .='<br><b>PLEASE KEEP THIS DOCUMENT FOR FUTURE REFERENCE</b>';


		


	file_put_contents($filename, $output);

	
	$output .='<script type="text/javascript">
			window.print();
		</script>';
	echo $output;

	}
}
?>