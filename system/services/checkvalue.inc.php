<?php
class checkValue {

	public $core;
	public $service;
	public $item = NULL;

	public function configService() {
		$this->service->output = TRUE;
		return $this->service;
	}

	/*
	 * Government ID taken check in forms
	 */
	public function runService($core) {
		$this->core = $core;

		if (isset($this->core->item)) {
			if($this->core->item == "student"){
				if(isset($this->core->cleanGet["query"])){
					$check = $this->core->cleanGet["query"];
					$sql = "SELECT * FROM `basic-information` WHERE `ID` LIKE '%$check%' OR concat(`FirstName`, ' ', `Surname`) LIKE '%$check%' LIMIT 0,20";

					$run = $this->core->database->doSelectQuery($sql);
					$out = "";
			
					while ($fetch = $run->fetch_row()) {
						$firstname = $fetch[0];
						$lastname = $fetch[2];
						$nrc = $fetch[5];
						$id = $fetch[4];

						$out = $out . '{ "value": "'.$id.' - '.$firstname.' '.$lastname.' ", "data": "'.$id.'" },';
					}

					$out = rtrim($out, ',');

					echo '{
						"query": "Unit",
						"suggestions": [
     
						'.$out.'
						]
					}';

				}
			}else if($this->core->item == "payment"){
				if(isset($this->core->cleanGet["query"])){
					$check = $this->core->cleanGet["query"];
					$sql = "SELECT * FROM `transactions` WHERE `TransactionID` LIKE '%$check%' OR  `Name` LIKE '%$check%' LIMIT 0, 20";

					$run = $this->core->database->doSelectQuery($sql);
					$out = "";
			
					while ($fetch = $run->fetch_row()) {
						$uid = $fetch[0];
						$paymentid = $fetch[3];
						$amount = $fetch[7];
						$name = $fetch[8];

						$out = $out . '{ "value": "'.$name.' - '.$paymentid.' - '.$amount.'", "data": "'.$uid.'" },';
					}

					$out = rtrim($out, ',');
	
					echo '{
						"query": "Unit",
						"suggestions": [
     
						'.$out.'
						]
					}';
	
				}
			}

			if(isset($this->core->cleanGet["nrc"])){
				$check = $this->core->cleanGet["nrc"];
				$sql = "SELECT * FROM `basic-information` WHERE `GovernmentID` = '$check'";

				$run = $this->core->database->doSelectQuery($sql);

				$out = "FALSE";
			
				while ($fetch = $run->fetch_row()) {
					$out = "TRUE";
				}
			
				echo '{"status":"'. $out . '"}';

			}
		}
	}
}

?>