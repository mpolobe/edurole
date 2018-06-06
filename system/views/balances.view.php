<?php
class balances {

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

	function runBalances($item) {

		$sqls = "SELECT `ID` FROM `basic-information` WHERE `Status` = 'Approved' OR  `Status` = 'Requesting'";
		$runs = $this->core->database->doSelectQuery($sqls);

		while ($fetchs = $runs->fetch_assoc()) {
			$uid = $fetchs["ID"];

			$amount = $this->getBalance($uid);

			if(isset($amount)){

				$sql  = "INSERT INTO `balances-report` (`StudentID`, `Balance`, `Date`) VALUES ('$uid', '$amount', NOW());";		 
				$run = $this->core->database->doInsertQuery($sql);
			
				echo "CALCULATED AND SAVED - $uid - $amount<br>";
			} else{
				echo "FAILED TO CALCULATE - $uid - $amount<br>";
			}
		}
	}


	public function getBalance($item){

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
			AND `basic-information`.ID = '$item'
			ORDER BY DATE ASC";

			$runp = $this->core->database->doSelectQuery($sqlp);
	
			setlocale(LC_MONETARY, 'en_US.UTF-8');

			$sqls = "SELECT StudyType FROM `basic-information` WHERE `ID` = '$item'";
			$runs = $this->core->database->doSelectQuery($sqls);
			while ($fetchs = $runs->fetch_assoc()) {
				$type = $fetchs["StudyType"];
			}

	
			$i = 0;
			while ($fetch = $runp->fetch_assoc()) {

		
			$typet = $fetch['TYPE'];
			$date = $fetch["DATE"];


			if($type=="Fulltime"){
				if( strtotime($date) < strtotime('2015-7-31') ) {
					continue;
				} 
			}else{
				if( strtotime($date) < strtotime('2016-4-15') ) {
					continue;
				}
			}

	
			if( strtotime($date) >= strtotime('2017-6-30') ) {
				continue;
			}
		



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

				if($fetch["Status"] == "BILL"){
					$type = "BILLING";
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
					$debit = money_format('%!.0n', $fetch["Amount"]);
					$balance = $balance+$fetch["Amount"];
				} else {
					$credit = money_format('%!.0n', $fetch["Amount"]);
					$balance = $balance-$fetch["Amount"];
				}

				$date = $fetch["DATE"];

				$name = $fetch["FirstName"] .'  '. $fetch["Surname"];
			
				$description = $fetch["DATA"];

				if(substr($description,22,7) == "STARTED"){
					$description = "ZANACO BILLMUSTER: " . $name;
				}

				if($typet==20){
					$type = "CREDIT NOTE";
				}

				$totalpayed = $fetch[7] + $totalpayed;

				$credit = "";
				$debit = "";
			}

			return $balance;

	} 


}
?>