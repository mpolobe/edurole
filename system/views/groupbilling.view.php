<?php
class groupbilling {

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



	public function revertGroupbilling(){

		$sql = "SELECT `StudentID`, `billing`.ID as BID, `billing`.Amount FROM `billing` 
			WHERE `Description` LIKE 'FIRST YEAR AUGUST FEES' 
			OR `Description` LIKE 'Second Year August fees'
			OR `Description` LIKE 'Third year August 2016' 
			ORDER BY `billing`.`StudentID` DESC";

		$run = $this->core->database->doSelectQuery($sql);

		while ($row = $run->fetch_assoc()) {
			$i++;

			$uid = $row['StudentID'];
			$bill = $row['BID'];
			$amount = $row['Amount'];

			if($uid == '20162168' || $uid == '20162169'){
				continue;
			}

			$sql = "DELETE FROM `billing` WHERE `ID` = '$bill'";
			$this->core->database->doInsertQuery($sql);
 
			$sql = "UPDATE `balances` SET `Amount` = Amount-$amount, `LastUpdate` = NOW(), `LastTransaction` = 'Reverting August Billing' WHERE `StudentID` = '$uid';";
			$this->core->database->doInsertQuery($sql);

			$sql = "SELECT * FROM `basic-information`, `balances` WHERE `basic-information`.`ID` = '$uid' AND `basic-information`.ID = `balances`.StudentID";
			$runx = $this->core->database->doSelectQuery($sql);

			while ($rowx = $runx->fetch_assoc()) {
				$newbalance = $rowx['Amount'];
			}

			echo $i . ' - Student <b>'.$uid.'</b> was wrongly billed for August 2016 an amount of <b>K'.$amount.'</b>, the bill has been removed and <b>the students balance is now K'.$newbalance.'</b><br>';
		}
	}



	public function billGroupbilling($month){

		$sql = "SELECT * FROM `basic-information`, `balances` 
			WHERE `StudyType` = 'Distance' 
			AND `basic-information`.`ID` > '20130000' 
			AND `basic-information`.`ID` < '20170000' 
			AND `basic-information`.`ID` = `balances`.StudentID";

		$run = $this->core->database->doSelectQuery($sql);

		$total = 0;

		while ($row = $run->fetch_assoc()) {
			
			$uid = $row['ID'];
			$balance = $row['Amount'];

			$i++;

			echo $i;
			$amount = $this->doGroupbilling($uid, $balance, $month);
			$total = $amount + $total;
			
		}

		echo'TOTAL BILLED WAS ' . $total;
	}

	public function doGroupbilling($item, $balance, $month) {

		$intake = substr($item, 0, 4);

		if(strlen($item) > 8){
			return;
		}

		// STEP 2 SET LIST OF BILLS

		$bill['august']['2013'] = '2350';
		$bill['august']['2014'] = '2700';
		$bill['august']['2015'] = '2750';
		$bill['august']['2016'] = '2950';
		$bill['august']['description'] = 'Tuition August 2016';


		$bill['december']['2013'] = '2350';
		$bill['december']['2014'] = '2700';
		$bill['december']['2015'] = '2750';
		$bill['december']['2016'] = '2950';
		$bill['december']['description'] = 'Tuition December 2016';

		$bill['april']['2013'] = '2050';
		$bill['april']['2014'] = '2050';
		$bill['april']['2015'] = '2450';
		$bill['april']['2016'] = '2650';
		$bill['april']['description'] = 'Tuition April 2017';


		// STEP 3 BILL STUDENT 

		$amount = $bill[$month][$intake];
		$description = $bill[$month]['description'];

		$sql = "INSERT INTO `billing` (`ID`, `StudentID`, `Amount`, `Date`, `Description`) VALUES (NULL, '$item', '$amount', NOW(), '$description');";
		$this->core->database->doInsertQuery($sql);
 
		$sql = "UPDATE `balances` SET `Amount` = Amount+$amount, `LastUpdate` = NOW(), `LastTransaction` = 'Bill for $description' WHERE `StudentID` = '$item';";
		$this->core->database->doInsertQuery($sql);


		$sql = "SELECT * FROM `basic-information`, `balances` WHERE `basic-information`.`ID` = '$item' AND `basic-information`.ID = `balances`.StudentID";
		$run = $this->core->database->doSelectQuery($sql);

		while ($row = $run->fetch_assoc()) {
			$newbalance = $row['Amount'];
		}

		echo ' - Student <b>'.$item.'</b> from intake '.$intake.' was billed for '.$month.' an amount of <b>K'.$amount.'</b>, the students balance was K'.$balance.' and is now K'.$newbalance.'<br>';

		return $amount;
	}
}
?>