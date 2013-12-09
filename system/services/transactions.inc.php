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

		if ($_SERVER['REMOTE_ADDR'] == '127.0.0.1'
			|| $_SERVER['REMOTE_ADDR'] == '182.72.15.23'
			|| $_SERVER['REMOTE_ADDR'] == '182.72.15.237' 
			|| $_SERVER['REMOTE_ADDR'] == '72.229.186.130' 
			|| $_SERVER['REMOTE_ADDR'] == '87.208.174.40' 
			|| $_SERVER['REMOTE_ADDR'] == '41.72.96.130' 
			|| $_SERVER['REMOTE_ADDR'] == '182.72.15.234' 
			|| $_SERVER['REMOTE_ADDR'] == '41.133.54.17' 
			|| $_SERVER['REMOTE_ADDR'] == '213.160.196.99') {

			if($this->core->item == "QueryStatus"){
				$this->queryStatus();
			}else {
				$this->logAll();
			}
		}
        }

	public function saveTransAction($data, $statusheader, $status, $error){

		$requestid = $this->core->cleanGet['RequestId'];
		$tranid = $this->core->cleanGet['TranID'];
		$key = $this->core->cleanGet['Key'];
		$date = $this->core->cleanGet['Date'];
		$amount = $this->core->cleanGet['Amount'];
		$type = $this->core->cleanGet['Type'];
		$studentid = $this->core->cleanGet['StudentID'];
		$phone = $this->core->cleanGet['Phone'];
		$name = $this->core->cleanGet['Name'];


		$sisstudentid = $this->getStudent($studentid);

		if(!empty($sisstudentid)){
			$uid = $sisstudentid;
		}

		$sql = "INSERT INTO `transactions` (`ID`, `UID`, `RequestID`, `TransactionID`, `StudentID`, `NRC`, `TransactionDate`, `Amount`, `Name`, `Type`, `Hash`, `Timestamp`, `Phone`, `Status`, `Error`, `Data`)
			VALUES (NULL, '$uid', '$requestid', '$tranid', '$studentid', '$nrc', '$date', '$amount', '$name', '$type', '$key', CURRENT_TIMESTAMP, '$phone', '$statusheader', '$status', '$data');";

		try{
			$this->core->database->doInsertQuery($sql);
			return TRUE;
		} catch (Exception $e) {
			return FALSE;
		}
	}

	public function getStudent($studentid) {
		$sql = "SELECT * FROM `access` WHERE `ID` = '$studentid'";

		$run = $this->core->database->doSelectQuery($sql);
		$fetch = $run->fetch_assoc();

		return $fetch['ID'];
	}

	public function logAll(){

		$ipaddr = $_SERVER['REMOTE_ADDR']; 
		$file = "/tmp/zanaco.txt";
		$filetwo = "log/zanaco-live-insecure.txt";

		$input = "\n\n" . date("Y-m-d H:i:s") . " STARTED input from: $ipaddr ============= \n";

		$transactionid = $this->core->cleanGet['TranID'];

		$keyset = $this->core->cleanGet['Key'];
		$requestid = $this->core->cleanGet['RequestId'];
		$date = $this->core->cleanGet['Date'];
		$amount = $this->core->cleanGet['Amount'];
		$type = $this->core->cleanGet['Type'];
		$studentid = $this->core->cleanGet['StudentID'];
		$phone = $this->core->cleanGet['Phone'];


		$calkey = base64_encode(sha1( $this->core->config['transactionkey'] . "$transactionid"));

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

		if(!$this->saveTransAction($input, $statusheader, $status, $error)){
			$statusheader = "STATUS_ERROR_INTERFACE";
			$status = "ERR_GENERAL_FAILURE";
			$error = $status;
		}

		$output ='<?xml version="1.0"?>' . "\n" .
			'<PostTranResponse status="'.$statusheader.'" errorMessage="">'  . "\n" .
			'<Transaction id="'.$transactionid.'" status="'.$status.'" errorMessage="'.$error.'"></Transaction>'  . "\n" .
			'</PostTranResponse>';


		// TEMPORARY LOGGING FEATURE
		file_put_contents($file, $input, FILE_APPEND);
		file_put_contents($file, $output, FILE_APPEND);

		file_put_contents($filetwo, $input, FILE_APPEND);
		file_put_contents($filetwo, $output, FILE_APPEND);

		echo $output;
	}

	public function queryStatus(){

		$transactionid = $this->core->cleanGet['TranID'];	

		$sql = "SELECT * FROM `transactions` WHERE `TransactionID` = '$transactionid'";

		$run = $this->core->database->doSelectQuery($sql);

		while($run->fetch_assoc){

	                $output = '<?xml version="1.0"?>'. "\n" .
			'<QueryStatusResponse status="SUCCESS">' . "\n" .
			'<Transaction id="'.$transactionid.'" status="SUCCESS" errorMessage="SUCCESS"></Transaction>' . "\n" .
			'</QueryStatusResponse>';
			
		}

		echo $output;
	}
}
?>
