<?php
class statistics {

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
	public function asKwachaZMW($value) {
	  return 'K' . number_format($value, 2);
	}

	public function buildView($core) {
		$this->core = $core;
	}

	public function viewMenu($core) {
		echo '<div class="toolbar">'.
		'<a href="' . $this->core->conf['conf']['path'] . '/statistics/payments">Payment collections</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/statistics/housing">Housing allocation</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/statistics/enrolment">Enrolment details</a>
		</div>';
	}

	public function housingStatistics(){
		$this->viewMenu($core);

		//setlocale(LC_MONETARY, 'en_US');

		echo'<div class="col-lg-12 greeter">Housing statistics</div>';

		echo'<div style="border: 5px solid #333; margin-bottom: 20px; padding-top: 20px; ">';

		$sql = "SELECT HostelName, COUNT(HostelName) 
		FROM `housing`, `hostel`, `rooms` 
		WHERE RoomID = `rooms`.ID 
		AND `housing`.RoomID = `rooms`.ID 
		AND HostelID = `hostel`.ID 
		GROUP BY HostelName";

		$run = $this->core->database->doSelectQuery($sql);
		echo'<h1>Students per hostel</h1><hr><p>';

		while ($fetch = $run->fetch_row()) {
			$hostel = $fetch[0];
			$count = $fetch[1];
			echo '<span style="font-size: 14px; "> '.$hostel.' <b>'. $count .'</b> <br></span>';
			$counter = $count+$counter;
		}

		echo '<span style="font-size: 16px; "><b> TOTAL BOARDERS '. $counter .'</b> <br></span></p><p><br></p>';

		$totalm = $counter * 1700;

		//$totalm =  money_format('%!.0n', $totalm);
		$totalm =  number_format($totalm,2);

		echo '</p></div>';

		echo'<div style="border: 5px solid #333; margin-bottom: 20px; padding-top: 20px; ">';

		$sql = "SELECT COUNT(`housing`.ID) as count, RoomCapacity, `hostel`.HostelName, `hostel`.Type FROM `hostel`, `rooms`
			LEFT JOIN `housing` ON `housing`.RoomID = `rooms`.ID 
			WHERE `hostel`.ID = `rooms`.HostelID
			AND `rooms`.RoomCapacity > (SELECT COUNT(RoomID) FROM `housing` WHERE `rooms`.ID = RoomID)
			GROUP BY `rooms`.ID
			ORDER BY `hostel`.ID";

		$run = $this->core->database->doSelectQuery($sql);
		echo'<h1>Available bed-spaces per hostel</h1><hr><p>';

		$start = FALSE;
		$count = 0;
		$totalr = 0;

		while ($fetch = $run->fetch_row()) {
			$hostelold = $hostel;
			$hostel = $fetch[2];

			if($start == FALSE){
				$hostel = $fetch[2];
				$hostelold = $fetch[2];
				$start = TRUE;
			}
			
			if($hostel != $hostelold){
				$totalr = $totalr+$roomcount;
				echo '<span style="font-size: 14px; "> '.$hostelold.' ('.$gender.') <b>'. $roomcount .'</b> <br></span>';
				$roomcount = 0;
			}

			$gender = $fetch[3];
			$room = $fetch[1]-$fetch[0];
			$roomcount = $roomcount+$room;
		}
		echo '<span style="font-size: 14px; "> '.$hostelold.' ('.$gender.')<b>'. $roomcount .'</b> <br></span>';
		echo '<span style="font-size: 16px; "><b> TOTAL AVAILABLE '. $totalr .'</b> <br></span>';
	

		$sql = "SELECT COUNT(`housingapplications`.StudentID) as count FROM `housingapplications`";
		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_row()) {
			echo '<span style="font-size: 16px; "><b> TOTAL APPLICATIONS '. $fetch[0] .'</b> <br></span></p><p><br></p>';
		}

		echo'</p><p><br></p></div>';
	}

	public function enrolmentStatistics($item){
		$this->viewMenu($core);

		echo'<div class="col-lg-12 greeter">Enrolment statistics</div>';


		$this->paymentsStatistic("2017");
		$this->paymentsStatistic("2016");
		$this->paymentsStatistic("2015");
		$this->paymentsStatistic("2014");
		$this->paymentsStatistic("2013");
		$this->paymentsStatistic("2012");
		$this->paymentsStatistic("2011");
		$this->paymentsStatistic("2010");
		$this->paymentsStatistic("2009");
		
	}

	public function paymentsStatistics($item){
		$this->viewMenu($core);

		echo'<div class="col-lg-12 greeter" style="">Cash Payment Statistics</div>';

		//setlocale(LC_MONETARY, 'en_US.UTF-8');

		echo'<div style="border: 5px solid #333; margin-bottom: 20px; padding-top: 20px; ">';


		// AMOUNT TOTAL
		$sql = "SELECT SUM(Amount) as total FROM `transactions` 
			WHERE `Amount` BETWEEN  -1 AND 701
			AND TransactionDate LIKE '2017-04%' 
			AND Type IN ('10','1')
			AND TransactionID LIKE 'KNU%'";
		$run = $this->core->database->doSelectQuery($sql);
		echo'<h1>Cash payments collected in total for APRIL 2017</h1><hr> <p>';

		while ($fetch = $run->fetch_assoc()) {
			//$amount =  money_format('%!.0n', $fetch['total']);
			$amount =  number_format($fetch['total'],2);
			echo '<span style="font-size: 14px; color: #6297C3; padding-top: 5px;"> The total amount collected is: <b>'. $amount .'</b> Kwacha.</span>';
		}

		echo '</p><p><br></p></div>';

		$amount = 0;


	}

	public function paymentsStatistic($item){

		$year = $_GET['year'];

		if(isset($item)){
			$year = $item;
		}


		echo'<div style="border: 5px solid #333; margin-bottom: 20px; padding-top: 20px; ">';
			echo' <h1>'.$year.' OVERVIEW</h1>';

		//setlocale(LC_MONETARY, 'en_US.UTF-8');

		// STUDENTS WHO PAID IN FULL
		$sql = "SELECT COUNT(`StudentID`) as number, `Sex`, `StudyType`  FROM `balances`
		LEFT JOIN `basic-information` ON `basic-information`.ID = `balances`.StudentID
		WHERE `StudentID` LIKE '$year%'
		AND `Status` = 'Requesting'
		AND `Amount` <= 0
		AND `LastUpdate` BETWEEN  '2016-09-09 00:00:00' AND  '2016-09-31 00:00:00'
		GROUP BY `Sex`, `StudyType`";


		// STUDENTS WHO HAVE REGISTERED
		$sqld = "SELECT COUNT(`StudentID`) as number, `Sex`, `StudyType`  FROM `student-program-link`
		LEFT JOIN `basic-information` ON `basic-information`.ID = `student-program-link`.StudentID
		WHERE `StudentID` LIKE '$year%'
		AND `Status` = 'Requesting'
		AND `DateOfEnrollment` >  '2016-09-09'
		GROUP BY `Sex`, `StudyType`";


		// STUDENTS WHO PAID IN FULL
		$sqle = "SELECT COUNT(`StudentID`) as number, `Sex`, `StudyType` FROM `balances`
		LEFT JOIN `basic-information` ON `basic-information`.ID = `balances`.StudentID
		WHERE `StudentID` LIKE '$year%'
		AND `Status` != 'Requesting'
		AND `Amount` < 3000
		AND `LastUpdate` BETWEEN  '2016-09-09 00:00:00' AND  '2016-09-31 00:00:00'
		GROUP BY `Sex`, `StudyType`";

		$run = $this->core->database->doSelectQuery($sql);
		$rund = $this->core->database->doSelectQuery($sqld);
		$rune = $this->core->database->doSelectQuery($sqle);

		echo'<hr> <h2>'.$year.' Students who paid in full</h2><p>';
		$total = 0;

		while ($fetch = $run->fetch_assoc()) {
			$sex =  $fetch['Sex'];
			$number =  $fetch['number'];
			$totalq = $totalq + $number;
			$stype =  $fetch['StudyType'];

			if(empty($sex)){
				$sex = 'Other';
			}

			echo '<b>'. $sex .'</b>: '.$number.' '.$stype.' Students <br>';
		}
		echo '<b>TOTAL</b>: '.$totalq.'   Students <br></p>';




		$totala = $total;
		$total = 0;
		
		echo'<hr> <h2>'.$year.' Students who registered</h2><p>';
		while ($fetch = $rund->fetch_assoc()) {
			$sex =  $fetch['Sex'];
			$number =  $fetch['number'];
			$stype =  $fetch['StudyType'];

			$totala = $totala + $number;

			if(empty($sex)){
				$sex = 'Other';
			}

			echo '<b>'. $sex .'</b>: '.$number.' '.$stype.' Students <br>';
		}
		echo '<b>TOTAL</b>: '.$totala.' Students <br></p>';


/*
		
		echo'<hr> <h2>'.$year.' Students who did not register but paid in part</h2><p>';
		while ($fetch = $rune->fetch_assoc()) {
			$sex =  $fetch['Sex'];
			$number =  $fetch['number'];
			$stype =  $fetch['StudyType'];
			$totalb = $totalb + $number;

			if(empty($sex)){
				$sex = 'Other';
			}

			echo '<b>'. $sex .'</b>: '.$number.'  '.$stype.' Students <br>';
		}
		echo '<b>TOTAL</b>: '.$totalb.' Students <br></p>';

*/



		// STUDENTS WHO ARE IN BOARDING
		$sql = " SELECT COUNT(StudentID) as number, Sex FROM `housing`
		LEFT JOIN `basic-information` ON `basic-information`.ID = `housing`.StudentID
		WHERE `housing`.`StudentID` LIKE '$year%'
		GROUP BY `Sex`";
		$run = $this->core->database->doSelectQuery($sql);


		echo' <hr><h2>'.$year.' Students who are in boarding</h2><p>';
		$total = 0;

		while ($fetch = $run->fetch_assoc()) {
			$sex =  $fetch['Sex'];
			$number =  $fetch['number'];

			if($sex == NULL){
				echo '<b>Reserved</b>: '.$number.' bedspaces reserved <br>';
			}else{
				$total = $total + $number;
				echo '<b>'. $sex .'</b>: '.$number.' bedspaces assigned<br>';
			}
		}
		echo '<br><b>TOTAL</b>: '.$total.' bedspaced assigned <br><p><br></p>';

		echo'</div>';


	}


	public function collectionStatistics($item){
		if(isset($item)){
			echo'<div class="col-lg-12 greeter">Daily statistics '.$item.'</div>';

			$this->dailyStatistics($item);
		}
	}

	private function dailyStatistics($day){
		//setlocale(LC_MONETARY, 'en_US.UTF-8');

		// AMOUNTS PER DAY PER INDIVIDUAL
		$sql = "SELECT `UID`, COUNT(Amount) as count, SUM(Amount) as total, FirstName, Surname, Data
		FROM `transactions`
		LEFT JOIN `basic-information` ON `basic-information`.ID = `transactions`.UID
		WHERE  TransactionID LIKE 'HOUSING%'
		AND TransactionDate LIKE '$day%'
		AND Error NOT LIKE 'REVERSED'
		AND Type IN ('10','1')
		OR TransactionID LIKE 'NCE%'
		AND TransactionDate LIKE '$day%'
		AND Error NOT LIKE 'REVERSED'
		AND Type IN ('10','1')
		GROUP BY `transactions`.`UID`";
		
		$run = $this->core->database->doSelectQuery($sql);
 		echo'<hr> <h1>Cash payments collected per member of staff '.$day.'</h1><p>';
		$total = 0;

		while ($fetch = $run->fetch_assoc()) {
			$total = $fetch['total'] + $total;
			//$amount =  money_format('%!.0n', $fetch['total']);
			$amount =  number_format($fetch['total'],2);
			echo '<a href="' . $this->core->conf['conf']['path'] . '/statistics/individual/'.$day.'/'.$fetch['UID'].'"><b>'. $fetch['FirstName'] .' '. $fetch['Surname'] .'</b></a> '. $fetch['UID'] .' collected <b>'.  $amount . '</b> Kwacha in total. '.$fetch['count'] .' Payments <br>';
		}

		//$total =  money_format('%!.0n', $total);
		$total =  number_format($total,2);
		echo'<span style="font-size: 14px; color: #6297C3; padding-top: 5px;">The daily total collected was: <b>'.$total.'</b></span></p>';

		
	}

	public function individualStatistics($day){
		//setlocale(LC_MONETARY, 'en_US.UTF-8');
		$user = $this->core->subitem;

		if(isset($user)){

			// AMOUNTS INDIVIDUAL
			$sql = "SELECT `UID`, Amount, FirstName, Surname, Data, StudentID, Name, Timestamp, TransactionID
			FROM `transactions`
			LEFT JOIN `basic-information` ON `basic-information`.ID = `transactions`.UID
			WHERE TransactionID LIKE 'HOUSING%'
			AND Error NOT LIKE 'REVERSED'
			AND TransactionDate LIKE '$day%'
			AND Type IN ('10','1')
			OR
			TransactionID LIKE 'NCE%'
			AND Error NOT LIKE 'REVERSED'
			AND TransactionDate LIKE '$day%'
			AND Type IN ('10','1')

			AND UID = '$user'";
		}

		$run = $this->core->database->doSelectQuery($sql);
 		echo'<hr> <h1>Cash payments collected per member of staff '.$day.'</h1><p>';
		$total = 0;

		while ($fetch = $run->fetch_assoc()) {
			$total = $fetch['Amount'] + $total;
			//$amount =  money_format('%!.0n', $fetch['Amount']);
			$amount =  number_format($fetch['Amount'],2);
			echo  $fetch['Timestamp'] .'  - <a href="#"><b>'. $fetch['FirstName'] .' '. $fetch['Surname'] .'</b></a> collected <b> '. $amount .'</b> from <a href="' . $this->core->conf['conf']['path'] . '/information/show/'. $fetch['StudentID'] .'">'. $fetch['StudentID'] .'  - '. $fetch['Name'] .'</a> - '. $fetch['TransactionID'] .'<br>';
		}

		//$total =  money_format('%!.0n', $total);
		$total =  number_format($total,2);
		echo'<span style="font-size: 14px; color: #6297C3; padding-top: 5px;">The daily total collected was: <b>'.$total.'</b></span></p>';
	
	}
}
?>