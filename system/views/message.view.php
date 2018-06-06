<?php
class message{

	public $core;
	public $view;

	public function configView() {
		$this->view->header = FALSE;
		$this->view->footer = FALSE;
		$this->view->menu = FALSE;
		$this->view->javascript = array();
		$this->view->css = array();

		return $this->view;
	}

	public function buildView($core) {
		$this->core = $core;
	}

	private function viewMenu($item, $phone){
		echo '<div class="toolbar">'.
		'<a href="' . $this->core->conf['conf']['path'] . '/message/inbox">Inbox</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/message/delete/'.$item.'">Delete message</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/sms/new/'.$phone.'">Reply by SMS</a>'.
		'</div>';
	}
	
	public function showMessage($item) {
		include $this->core->conf['conf']['formPath'] . "message.form.php";
	}

	public function inboxMessage() { 

		$sql = "SELECT * FROM `messages` WHERE `RecipientID` = '1' 
			AND `SenderID` NOT LIKE '2016%'  
			AND `SenderID` NOT LIKE '0' 
			ORDER BY `ID` DESC";

		$run = $this->core->database->doSelectQuery($sql);

		echo'<table id="messages" class="table table-bordered  table-hover">
			<thead>
				<tr>
					<th bgcolor="#EEEEEE" width="30px"></th>
					<th bgcolor="#EEEEEE" width="30px">#</th>
					<th bgcolor="#EEEEEE" width="120px"><b>Date/Time</b></th>
					<th bgcolor="#EEEEEE" width=""><b>Message</b></th>
					<th bgcolor="#EEEEEE" width="70px"><b>Sender</b></th>
					<th bgcolor="#EEEEEE" width="100px"><b>Phone</b></th>
					<th bgcolor="#EEEEEE" width="50px">Delete</th>
				</tr>
			</thead>
			<tbody>';

		while ($fetch = $run->fetch_row()) {
			$item = $fetch[0];
			$sender = $fetch[1];
			$phone = $fetch[4];
			$subject = $fetch[8];
			$date = $fetch[3];
			$status = $fetch[9];
			$message = $fetch[5];

			if($message == ""){
				continue;
			}

			if($status == "1"){
				$color = "#FFFFFF";
			} else { 
				$color = "#B1CADE";
			}

			echo'<tr style="background-color: '.$color.';">
				<td><img src="/edurole/templates/edurole/images/user.png"> </td>
				<td> '.$item.'</td>
				<td> '.$date.'</td>
				<td> <b><a href="' . $this->core->conf['conf']['path'] . '/message/read/'.$item.'">'.$message.'</a></b></td>
				<td> <a href="' . $this->core->conf['conf']['path'] . '/information/show/'.$sender.'">'.$sender.'</a></td>
				<td> <a href="' . $this->core->conf['conf']['path'] . '/message/read/'.$item.'">'.$phone.'</a></td>
				<td> <a href="' . $this->core->conf['conf']['path'] . '/message/delete/'.$item.'"><img src="'.$this->core->fullTemplatePath.'/images/del.png"></a></td>
				</tr>';
		}

			echo'</tbody>
			</table>';
	}


	public function readMessage($item) { 

		

		$sql = "SELECT * FROM `messages` WHERE `ID` = '$item'";

		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_row()) {
			$item = $fetch[0];
			$sender = $fetch[1];
			$phone = $fetch[4];
			$message = $fetch[5];
			$subject = $fetch[8];
			$date = $fetch[3];
			$status = $fetch[9];
		}

		$this->viewMenu($item, $phone);

		echo'<table id="messages" class="table table-bordered  table-hover">
			<thead>
				<tr>
					<th bgcolor="#EEEEEE" width="30px"></th>
					<th bgcolor="#EEEEEE" width="30px">#</th>
					<th bgcolor="#EEEEEE" width="120px"><b>Date/Time</b></th>
					<th bgcolor="#EEEEEE" width=""><b>Subject</b></th>
					<th bgcolor="#EEEEEE" width="70px"><b>Sender</b></th>
					<th bgcolor="#EEEEEE" width="180px"><b>Phone</b></th>
				</tr>
			</thead>
			<tbody>';



			if($status != "1"){
				$color = "#EEEEEE";
			}
 
			echo'<tr style="background-color: '.$color.';">
				<td><img src="/edurole/templates/edurole/images/user.png"> </td>
				<td> '.$item.'</td>
				<td> '.$date.'</td>
				<td> <a href="' . $this->core->conf['conf']['path'] . '/message/read/'.$item.'">'.$subject.'</a></td>
				<td> <a href="' . $this->core->conf['conf']['path'] . '/information/show/'.$sender.'">'.$sender.'</a></td>
				<td> '.$phone.'</td>
				</tr>';

		$sql = "UPDATE `messages` SET `Read` = '1' WHERE `ID` = '$item'";

		$run = $this->core->database->doInsertQuery($sql);	

			echo'<tr style="background-color: #cccccc;">
				<td colspan="6"><b>'.$message.'</b></td>
				</tr>';
		

			echo'</tbody>
			</table>';
	}

	
	public function deleteMessage($item) {

		$sql = "DELETE FROM `messages` WHERE `ID` = '$item'";

		$run = $this->core->database->doInsertQuery($sql);		

		$this->core->redirect("message", "inbox", NULL);
	}


	public function sendMessage($item) {
		$uid = $this->core->cleanPost['uid'];
		$phone = $this->core->cleanPost['phone'];
		$message = $this->core->cleanPost['message'];
		$ip = $_SERVER['REMOTE_ADDR'];

		$sql = "INSERT INTO `messages` (`ID`, `SenderID`, `RecipientID`, `Date`, `Phone`, `Message`, `IP`, `Subject`) 
			VALUES (NULL, '$uid', '1', NOW(), '$phone', '$message', '$ip', 'CA Mark Complaint');";

		$run = $this->core->database->doInsertQuery($sql);		

		echo '<div class="successpopup">'. $this->core->translate("Your message was sent.") .'</div>';
	}
}
?>