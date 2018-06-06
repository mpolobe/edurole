<?php
class sms {

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

	public function buildView($core) {
		$this->core = $core;
	}

	private function viewMenu(){
		echo '<div class="toolbar">'.
		'<a href="' . $this->core->conf['conf']['path'] . '/sms/manage">Manage SMS</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/sms/new">Send SMS</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/information/search">Send a bulk SMS message</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/sms/approve/all">Approve all SMS</a>'.
		'</div>';
	}

	public function unitsSms(){

		$url = $this->core->conf['sms']['units'];
		$username = urlencode($this->core->conf['sms']['user']);
		$password = urlencode($this->core->conf['sms']['password']);

		$url = 'http://smsapi.probasesms.com/apis/balance/index.php?username='.$username.'&password='. $password;
	
		$get = file_get_contents($url);
		$output = simplexml_load_string($get);

		
		foreach($output as $arr => $val){
			
			if($arr == "units"){
				$units = $val;
			}elseif($arr == "accountType"){
				$account = $val;
			}

			echo  $units;
		}
	}

	public function manageSms() {
		$this->viewMenu();
		$userid = $this->core->userID;

		if($this->core->role < 1000){
			$sql = "SELECT * FROM `sms` LEFT JOIN `basic-information` ON `sms`.Author = `basic-information`.ID WHERE `sms`.Author = '$userid' ORDER BY Date DESC";
		} else {
			$sql = "SELECT * FROM `sms` LEFT JOIN `basic-information` ON `sms`.Author = `basic-information`.ID ORDER BY Date DESC";
		}

		$run = $this->core->database->doSelectQuery($sql);

		if(!isset($this->core->cleanGet['offset'])){
			echo'<table id="results" class="table table-bordered table-striped table-hover">
			<thead>
				<tr>
					<th bgcolor="#EEEEEE" width="30px"></th>
					<th bgcolor="#EEEEEE" width="120px"><b>Date/Time</b></th>
					<th bgcolor="#EEEEEE" width=""><b>Message</b></th>
					<th bgcolor="#EEEEEE" width="70px"><b>Targeted</b></th>
					<th bgcolor="#EEEEEE" width="70px"><b>Delivered</b></th>
					<th bgcolor="#EEEEEE" width="180px"><b>Status</b></th>
				</tr>
			</thead>
			<tbody>';
		}

		while ($row = $run->fetch_row()) {
			$results == TRUE;

			$id = $row[0];
			$date = $row[1];
			$message = $row[2];
			$author = $row[3];
			$sent = $row[4];
			$received = $row[5];
			$status = $row[6];
			$guids = $row[7];

			$firstname = $row[8];
			$lastname = $row[10];

			if($status == "Awaiting approval"){
				$status = '<b><a onClick="SMS=window.open(\''. $this->core->conf['conf']['path'] .'/sms/approve/'.$id.'\',\'SMS\',\'width=600,height=300\'); return false;" href="#">Awaiting approval</a></b> <br>
					<a href="'. $this->core->conf['conf']['path'] .'/sms/delete/'.$id.'">Delete message</a> <br>
					Author: ' . $firstname . ' ' . $lastname;
			} else {
				$status =  '<b>' . $status . '</b><br> Author: ' . $firstname . ' ' . $lastname;
			}
			

			$sent = substr_count($sent, ',')+1;

			
			echo'<tr>
				<td><img src="'. $this->core->conf['conf']['path'] .'/templates/edurole/images/user.png"></td>
				<td> '.$date.'</td>
				<td> '.$message.'</td>
				<td> '.$sent.'</td>
				<td> '.$received.'</td>
				<td> '.$status.'</td>
				</tr>';
			$results = TRUE;


		}

		if($this->core->pager == FALSE){
			if ($results != TRUE) {
				$this->core->throwError('Your search did not return any results');
			}
		}


		if(!isset($this->core->cleanGet['offset'])){
			echo'</tbody>
			</table>';
		}

	}

	public function newSms($item) {
		$celphone = $item;

		$length = strlen((string)$celphone);
		if($length == 10) {
			$recipients = $celphone;
		}elseif($length == 9) {
			if (substr($celphone, 0, 1) === '9') { $celphone = "0".$celphone; }
			$recipients = $celphone;
		}else if($length == 12) {
			$celphone = substr($celphone, 2);
			$recipients = $celphone;
		}

		include $this->core->conf['conf']['formPath'] . "newsms.form.php";
	}

	public function newbulkSms() {
		$sql = $_SESSION["recipients"];
		$run = $this->core->database->doSelectQuery($sql);

		$prefix = "26";
		$recipients = "0963493849,0967860242,0977780593,0969886998,097738878,0975963257,0977861574";
		$guids = "2010226276";

		while ($row = $run->fetch_row()) {
			$firstname = $row[0];
			$lastname = $row[2];
			$uid = $row[4];
			$status = $row[20];
			$celphone = $row[14];

			if($status == "Requesting" ||  $status == "Approved" ||  $status == "New" || $status == "Employed"){

				$guids = $guids . "," . $uid;

				$celphone = $this->parseCelphone($celphone);
				$recipients = $recipients . "," . $celphone;
			

				$names = $names . '<a href="' . $this->core->conf['conf']['path'] . '/information/show/'.$uid.'"><div style="border-radius: 25px; width: auto; float: left; border: 2px solid #6297BF; padding-top: 3px; padding-bottom: 3px; padding-left: 15px; padding-right: 15px; font-weight:bold; ">
				'.$firstname . " " . $lastname .'	</div></a>';
			}
					
		}

		$count = substr_count($recipients, ',')+1;
		
		echo'<div class="names" style="height: 200px; padding: 10px; overflow: scroll; overflow-x: hidden; border: 1px solid #ccc; border-bottom: 4px solid #6297BF;">'.$names.'</div>
		<div style="font-weight: bold; font-size: 14px; text-align: center;">This SMS will be sent to <u>'.$count.'</u> people</div>';

		$recipients = str_replace(",\n,", ",", $recipients); 
		$recipients = str_replace(",,", ",", $recipients);



		include $this->core->conf['conf']['formPath'] . "newsms.form.php";
	}

	private function parseCelphone($celphone){
		$celphone = preg_replace('/[^\da-z ,]/i', '', $celphone);
		$celphone = str_replace(" ", ",", $celphone);

		if (strpos($celphone, ',')) {
			$cs = explode($celphone);

			foreach($cs as $celphone){
				$length = strlen((string)$celphone);
				if($length == 10) {
					return $celphone;
				}elseif($length == 9) {
					if (substr($celphone, 0, 1) === '9') { $celphone = "0".$celhphone; }
					return $celphone;
				}else if($length == 12) {
					$celphone = substr($celphone, 2);
					return $celphone;
				}
			}
		} else {
				$length = strlen((string)$celphone);
				if($length == 10) {
					return $celphone;
				}elseif($length == 9) {
					if (substr($celphone, 0, 1) === '9') { $celphone = "0".$celhphone; }
					return $celphone;
				}else if($length == 12) {
					$celphone = substr($celphone, 2);
					return $celphone;
				}
		}
			
	}


	public function sendSms() {
		$message = $this->core->cleanPost['message'];
		$recipients = $this->core->cleanPost['recipients'];
		$uids = $this->core->cleanPost['uids'];
		$author = $this->core->userID;

		$rcv = explode(",", $recipients);
		
		$prefix = "26";
		$multi = FALSE;

		$reps = "";
		foreach($rcv as $number){
			$reps = $reps . "$prefix$number,";
			$multi = TRUE;
		}

		if($multi == FALSE){
			$reps = $prefix . $recipients;
		}

		$reps = rtrim($reps, ",");

		$sql = "INSERT INTO `sms` (`ID`, `Date`, `Message`, `Author`, `Receipients`, `Successful`, `Status`, `RecipientID`) 
			 VALUES (NULL, NOW(), '$message', '$author', '$reps', '', 'Awaiting approval', '$uids');";


		echo'<h2>SMS set for approval</h2>';

		$this->core->database->doInsertQuery($sql);

		$this->manageSMS();

	}

	public function approveSms($item){
		if ($item == "all"){
			$sql = 'SELECT * FROM `sms` WHERE `Status` = "Awaiting approval"';
			$run = $this->core->database->doSelectQuery($sql);

			while ($row = $run->fetch_row()) {
				$ids = $row[0];
				echo'<h2>SMS approved. Sending has started</h2>';

				$sqls = 'UPDATE `sms` SET `Status` = "Approved" WHERE `ID` = '.$ids.';';
				$rund = $this->core->database->doInsertQuery($sqls);

				$this->queSms($ids);
			}
		} else {
			echo'<h2>SMS approved. Sending has started</h2>';

			$sql = 'UPDATE `sms` SET `Status` = "Approved" WHERE `ID` = '.$item.';';
			$run = $this->core->database->doInsertQuery($sql);

			$this->queSms($item);
		}
	}

	public function deleteSms($item){
		echo'<h2>SMS deleted.</h2>';

		$sql = 'DELETE FROM `sms` WHERE `ID` = '.$item.';';
		$run = $this->core->database->doInsertQuery($sql);

		$this->manageSMS();
	}
 
	private function queSms($item){

		ignore_user_abort(true);
		set_time_limit(0);

		$sql = 'SELECT * FROM `sms` WHERE ID = '.$item.' AND `Status` = "Approved";';
		$run = $this->core->database->doSelectQuery($sql);

		while ($row = $run->fetch_row()) {
			$item = $row[0];
			$reps = $row[4];
			$message = $row[2];
			$message = urlencode($message);
			$status = $row[6];
			$guids = $row[7];

			$sql = 'UPDATE `sms` SET `Status` = "Sending" WHERE `ID` = '.$item.';';
			$run = $this->core->database->doInsertQuery($sql);

			$count = substr_count($reps, ',')+1;

			$success = 0;
			$failed= 0;

			/*
				echo'PARSER DISABLED BY ROWAN';
				die();
				$i = 0;
				$countd = substr_count($guids, ',')+1;
				$rcv = explode(",", $guids);
				echo 'Custom parsed message, sending to parser, ';
				while($i < $countd){
					$recipientID = $rcv[$i];
					$sd = $this->parseMessage($item, $recipientID, $message);
					$success = $success + $sd;
					echo $success . " - ";
					$i++;
				}
			*/


				if($count > 50){
					echo '<br> Starting multiple que: <br>';
					$rcv = explode(",", $reps);
					$reps = "";

					$i = 0;
					while($i < $count){
						if($d<50){
							$reps = $rcv[$i] . "," . $reps;
							$i++; $d++;
						} else {
							$sd = $this->submitSms($item, $message, $reps);
							$success = $success + $sd;
							echo $success . " - ";
							$reps = "";
							$d = 0;
						}
					}

					$sd = $this->submitSms($item, $message, $reps);
					$success = $success + $sd;
					echo $success . " - ";

					$sql = 'UPDATE `sms` SET `Status` = "Sent", `Successful` = "'.$success.'" WHERE `ID` = '.$item.';';
					$this->core->database->doInsertQuery($sql);
					break;
				} else {
					$success = $this->submitSms($item, $message, $reps);

					$sql = 'UPDATE `sms` SET `Status` = "Sent", `Successful` = "'.$success.'" WHERE `ID` = '.$item.';';
					$this->core->database->doInsertQuery($sql);
					break;
				}
			}
		
	}

	public function parseMessage($item, $recipientID, $message){
		
		$sql = "SELECT *  FROM `basic-information` WHERE `ID` = '$recipientID'";
		$run = $this->core->database->doSelectQuery($sql);

		$success = 0;
		$message = urlencode($message);
	
		while ($row = $run->fetch_assoc()) {
			
			$phone = $row['MobilePhone'];
			$phone = $this->parseCelphone($phone);
			$prefix = "26";
			$phone = $prefix . $phone;

			$message = str_replace("%25ID%25", $row['ID'], $message);
			$message = str_replace("%25NAME%25", $row['FirstName'] . " " . $row['LastName'], $message);
			$message = str_replace("%25PHONE%25", $row['MobilePhone'], $message);
			$message = str_replace("%25NRC%25", $row['GovernmentID'], $message);
			$message = str_replace("%25MODE%25", $row['StudyType'], $message);
			$message = str_replace("%25STATUS%25", $row['Status'], $message);

			$success = $this->submitSms($item, $message, $phone);
		}

		return $success;
	}

	public function submitSms($item, $message, $reps){

		$url = $this->core->conf['sms']['server'];
		$username = urlencode($this->core->conf['sms']['user']);
		$password = urlencode($this->core->conf['sms']['password']);
		$sender = urlencode($this->core->conf['sms']['name']);

		$url = $url . '?username='.$username.'&password='.$password.'&sender='.$sender.'&mobiles='.$reps.'&message='.$message.'&type=TEXT';
		

		if($item != 0){
			$sql = 'UPDATE `sms` SET `Status` = "Sending" WHERE `ID` = '.$item.';';
			$run = $this->core->database->doInsertQuery($sql);
		}

		$get = file_get_contents($url);
		$output = simplexml_load_string($get);

		$pos = strpos($get, "NOT_ENOUGH_UNITS");
		if ($pos === false) {
			$units = TRUE;
		} else {
			echo'<div class="errorpopup">NO UNITS LEFT PLEASE CALL SMS PROVIDER</div>';
			return;
		}


		$success = 0;

		foreach($output->response as $arr){
			$status = $arr->messagestatus;
			$phone = $arr->mobile;

			if($status == "SUCCESS"){
				$success++;
			}else{
				echo $phone . ",";
				$failed++;
			}
		}

		if($item != 0){
			$sql = 'UPDATE `sms` SET `Status` = "Sending", `Successful` = `Successful`+'.$success.' WHERE `ID` = '.$item.';';
			$this->core->database->doInsertQuery($sql);
		}

		return $success;
	}

	public function directSms($reps, $message){
		if(empty($reps) || empty($message)){
			$message = $this->core->cleanGet['message'];
			$reps = $this->core->cleanGet['recipients'];
		}

		$message = urlencode($message);

		$url = $this->core->conf['sms']['server'];
		$username = urlencode($this->core->conf['sms']['user']);
		$password = urlencode($this->core->conf['sms']['password']);
		$sender = urlencode($this->core->conf['sms']['name']);

		$url = $url . '?username='.$username.'&password='.$password.'&sender='.$sender.'&mobiles='.$reps.'&message='.$message.'&type=TEXT';

		$get = file_get_contents($url);
		$output = simplexml_load_string($get);

		$pos = strpos($get, "NOT_ENOUGH_UNITS");
		if ($pos === false) {
			$units = TRUE;
		} else {
			echo'<div class="errorpopup">NO UNITS LEFT PLEASE CALL SMS PROVIDER</div>';
			return;
		}
	}
}

?>
