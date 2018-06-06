<?php
class transactions{

	public $core;
	public $service;

	public function configService() {
		$this->service->output = TRUE;
		return $this->service;
	}

        public function runService($core) {
              $this->core = $core;
		$status = $this->core->cleanGet['TranStatus'];	

		if($this->core->item == "QueryStatus"){
			$this->queryStatus();
		} else if($status == "v" || $status == "V"){
			$this->reverseTransaction();
		} else {
			$this->postTransaction();
		}
        }

	public function reverseTransaction() {

		$sql = "SELECT `TransactionID` FROM `transactions` WHERE `TransactionID` = '$transactionid'";
		$run = $this->core->database->doSelectQuery($sql);

      	 	$output = '<?xml version="1.0"?>'. "\n" .
		'<QueryStatusResponse status="ERROR">' . "\n" .
		'<Transaction id="'.$transactionid.'" status="ERROR" errorMessage="NO_SUCH_ENTRY"></Transaction>' . "\n" .
		'</QueryStatusResponse>';

		if($run->num_rows > 0){
			$sql = "DELETE FROM `transactions` WHERE `TransactionID` = '$transactionid'";
			$run = $this->core->database->doSelectQuery($sql);

        	        $output = '<?xml version="1.0"?>'. "\n" .
			'<QueryStatusResponse status="SUCCESS">' . "\n" .
			'<Transaction id="'.$transactionid.'" status="SUCCESS" errorMessage="SUCCESS"></Transaction>' . "\n" .
			'</QueryStatusResponse>';
		}

		echo $output;
	}

	public function postTransaction(){

		$ipaddr = $_SERVER['REMOTE_ADDR']; 
		$file = "/tmp/zanaco.txt";
	
		$input = "\n\n" . date("Y-m-d H:i:s") . " STARTED input from: $ipaddr ============= \n";

		$transactionid = $this->core->cleanGet['TranID'];

		$keyset = $this->core->cleanGet['Key'];
		$requestid = $this->core->cleanGet['RequestId'];
		$tranid = $this->core->cleanGet['TranID'];
		$key = $this->core->cleanGet['Key'];
		$date = $this->core->cleanGet['Date'];
		$amount = $this->core->cleanGet['Amount'];
		$type = $this->core->cleanGet['Type'];
		$studentid = $this->core->cleanGet['StudentID'];
		$phone = $this->core->cleanGet['Phone'];
		$name = $this->core->cleanGet['Name'];
		$status = $this->core->cleanGet['TranStatus'];


		$calkey = base64_encode(sha1("dcb74b1413de4e83ade08fa6f0467268d74971f7" . "$transactionid"));

		$status = "SUCCESS";
		$error = "";

		// LOG ALL ZANACO INPUT FOR TESTING AND ACCOUNTING PURPOSES (THEY SOMETIMES SEND STRANGE THINGS)

		foreach ($_POST as $key => $value){
			$input .=  "Field POST ".htmlspecialchars($key)." is ".htmlspecialchars($value). "\n";
		}

		foreach ($_GET as $key => $value){
			$input .=  "Field GET ".htmlspecialchars($key)." is ".htmlspecialchars($value). "\n";
		}


		// SET THE VARIOUS ERROR MESSAGES, ORDERD BY ERROR LEVEL

		if(empty($transactionid)){
			$statusheader = "STATUS_ERROR_INTERFACE";
			$status = "ERR_MISSING_TRANID";
			$error = $status;
		}

		if(empty($requestid)){
			$statusheader = "STATUS_ERROR_INTERFACE";
			$status = "ERR_MISSING_REQUESTID";
			$error = $status;
		}

		if($keyset != $calkey){
			$statusheader = "STATUS_ERROR_INTERFACE";
			$status = "ERR_INVALID_KEY";
			$error = $status;
		}



		$sisstudentid = $this->getStudent($studentid);

		if(!empty($sisstudentid)){
			$uid = $sisstudentid;
		}



		// TEMPORARY LOGGING FEATURE
		file_put_contents($file, $input, FILE_APPEND);
		file_put_contents($file, $output, FILE_APPEND);



		$sql = "SELECT * FROM `transactions` WHERE `TransactionID` = '$tranid'";

		$run = $this->core->database->doSelectQuery($sql);

		if($run->num_rows > 0){

	                $output = '<?xml version="1.0"?>'. "\n" .
			'<QueryStatusResponse status="SUCCESS">' . "\n" .
			'<Transaction id="'.$tranid.'" status="SUCCESS" errorMessage="SUCCESS"></Transaction>' . "\n" .
			'</QueryStatusResponse>';
			echo $output;
			return;
			
		}

		$data = $this->core->database->escape($input);
		$sql = "INSERT INTO `transactions` (`ID`, `UID`, `RequestID`, `TransactionID`, `StudentID`, `NRC`, `TransactionDate`, `Amount`, `Name`, `Type`, `Hash`, `Timestamp`, `Phone`, `Status`, `Error`, `Data`)
			VALUES (NULL, '$uid', '$requestid', '$tranid', '$studentid', '$nrc', '$date', '$amount', '$name', '$type', '$keyset', CURRENT_TIMESTAMP, '$phone', '$statusheader', '$status', '$data');";


		$qr = $this->core->database->doInsertQuery($sql, TRUE);

		if($qr == TRUE){
			
		} else {
			$statusheader = "STATUS_ERROR_INTERFACE";
			$status = "ERR_GENERAL_FAILURE";
			$error = $status;
		}

		$output ='<?xml version="1.0"?>' . "\n" .
			'<PostTranResponse status="'.$statusheader.'" errorMessage="">'  . "\n" .
			'<Transaction id="'.$transactionid.'" status="'.$status.'" errorMessage="'.$error.'"></Transaction>'  . "\n" .
			'</PostTranResponse>';


		echo $output;
	}

	public function queryStatus(){

		$transactionid = $this->core->cleanGet['TranID'];	

		$sql = "SELECT * FROM `transactions` WHERE `TransactionID` = '$transactionid'";

		$run = $this->core->database->doSelectQuery($sql);

		if($run->num_rows > 0){

	                $output = '<?xml version="1.0"?>'. "\n" .
			'<QueryStatusResponse status="SUCCESS">' . "\n" .
			'<Transaction id="'.$transactionid.'" status="SUCCESS" errorMessage="SUCCESS"></Transaction>' . "\n" .
			'</QueryStatusResponse>';
			
		}

		echo $output;
	}

	public function getStudent($studentid) {
		$sql = "SELECT * FROM `basic-information` WHERE `ID` = '$studentid'";

		$run = $this->core->database->doSelectQuery($sql);
		$fetch = $run->fetch_assoc();
	}

}
?>
