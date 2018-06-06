<?php
class helpdesk{

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

	private function viewMenu($item, $uid, $message){
		if(isset($uid)){
			$uid = "/".$uid;
		}

		echo '<div class="toolbar">'.
		'<a href="' . $this->core->conf['conf']['path'] . '/helpdesk/inbox"><span class="glyphicon glyphicon-inbox"></span> Inbox</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/helpdesk/delete/'.$item.'"><span class="glyphicon glyphicon-remove"></span> Delete</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/helpdesk/reply/'.$item . $uid.'"><span class="glyphicon glyphicon-share"></span> Reply </a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/helpdesk/forward/'.$item . $uid.'"><span class="glyphicon glyphicon-forward"></span> Forward </a>'.
		'</div>';
	}
	
	public function messageHelpdesk($item) {
		include $this->core->conf['conf']['formPath'] . "createticket.form.php";
	}

	public function forwardHelpdesk($item) {
		$recipient = $this->core->userID;

		echo '<div class="toolbar">'.
		'<a href="' . $this->core->conf['conf']['path'] . '/helpdesk/inbox"><span class="glyphicon glyphicon-inbox"></span> Inbox</a>'.
		'</div>';

		$sql = "SELECT * FROM `helpdesk`	
			LEFT JOIN `basic-information` ON `basic-information`.ID = `helpdesk`.SenderID 
			WHERE `helpdesk`.`ID` = '$item' AND `RecipientID` = '$recipient' 
			OR  `helpdesk`.`ID` = '$item' AND `RecipientID` = 'ALL'";

		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_assoc()) {
			$item = $fetch['ID'];
			$sender = $fetch['SenderID'];
			$item = "FWD: " . $fetch['Subject'];
			$date = $fetch['Date'];
			$message = "$sender WROTE ON $date: \n ------------------------------------------------------------------------------------------------------------\n" . $fetch['Message'];
			$name = $fetch['FirstName'] . ' ' . $fetch['Surname'];
		}

		include $this->core->conf['conf']['formPath'] . "sendmessage.form.php";
	}
	
	public function replyHelpdesk($item) {
		$recipient = $this->core->userID;

		echo '<div class="toolbar">'.
		'<a href="' . $this->core->conf['conf']['path'] . '/helpdesk/inbox"><span class="glyphicon glyphicon-inbox"></span> Inbox</a>'.
		'</div>';

		$sql = "SELECT * FROM `helpdesk`	
			LEFT JOIN `basic-information` ON `basic-information`.ID = `helpdesk`.SenderID 
			WHERE `helpdesk`.`ID` = '$item' AND `RecipientID` = '$recipient' 
			OR  `helpdesk`.`ID` = '$item' AND `RecipientID` = 'ALL'";

		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_assoc()) {
			$item = $fetch['ID'];
			$uid = $fetch['SenderID'];
			$item = "RE: " . $fetch['Subject'];
			$name = $fetch['FirstName'] . ' ' . $fetch['Surname'];
		}

		include $this->core->conf['conf']['formPath'] . "sendmessage.form.php";
	}

	public function inboxHelpdesk() { 
		$inbox = $this->core->userID;
		
		echo'<div class="toolbar">
			<a href="' . $this->core->conf['conf']['path'] . '/helpdesk/reply/"><span class="glyphicon glyphicon-send"></span> Send message </a>
			</div>';

		$sql = "SELECT *, `helpdesk`.ID as MID FROM `helpdesk`
			LEFT JOIN `basic-information` ON `basic-information`.ID = `helpdesk`.SenderID 
			WHERE `RecipientID` LIKE '$inbox'
			OR `RecipientID` LIKE 'ALL'
			ORDER BY `MID` DESC";

		$run = $this->core->database->doSelectQuery($sql);

		echo'<table id="helpdesk" class="table table-bordered  table-hover">
			<thead>
				<tr>
					<th bgcolor="#EEEEEE" width="30px"></th>
					<th bgcolor="#EEEEEE" width="30px">#</th>
					<th bgcolor="#EEEEEE" width="120px"><b>Date/Time</b></th>
					<th bgcolor="#EEEEEE" width=""><b>Subject</b></th>
					<th bgcolor="#EEEEEE" width="150px"><b>Sender</b></th>
					<th bgcolor="#EEEEEE" width="50px">Delete</th>
				</tr>
			</thead>
			<tbody>';

		while ($fetch = $run->fetch_assoc()) {
			$i++;
			
			$item = $fetch['MID'];
			$sender = $fetch['SenderID'];
			$recipient = $fetch['RecipientID'];
			$subject = $fetch['Subject'];
			$date = $fetch['Date'];
			$read = $fetch['Read'];
			$message = $fetch['Message'];

			if(empty($subject)){
				$subject = "No subject";
			}
			
			$firstname = $fetch['FirstName'];
			$surname = $fetch['Surname'];

			if($read == "1"){
				$color = "#FFFFFF";
			} else { 
				$color = "#B1CADE";
			}

			echo'<tr style="background-color: '.$color.';">
				<td><img src="' . $this->core->conf['conf']['path'] . '/templates/edurole/images/user.png"> </td>
				<td> '.$i.'</td>
				<td> '.$date.'</td>
				<td> <b><a href="' . $this->core->conf['conf']['path'] . '/helpdesk/read/'.$item.'">'.$subject.'</a></b></td>
				<td> <a href="' . $this->core->conf['conf']['path'] . '/information/show/'.$sender.'">'.$firstname.' '.$surname.'</a></td>
				<td> <a href="' . $this->core->conf['conf']['path'] . '/helpdesk/delete/'.$item.'"><img src="'.$this->core->fullTemplatePath.'/images/del.png"></a></td>
				</tr>';
		}

			echo'</tbody>
			</table>';
	}


	public function readHelpdesk($item) { 
		$recipient = $this->core->userID;

		$sql = "SELECT * FROM `helpdesk` WHERE `ID` = '$item' AND `RecipientID` = '$recipient' OR  `ID` = '$item' AND `RecipientID` = 'ALL'  ";

		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_assoc()) {
			$item = $fetch['ID'];
			$sender = $fetch['SenderID'];
			$recipient = $fetch['RecipientID'];
			$subject = $fetch['Subject'];
			$date = $fetch['Date'];
			$read = $fetch['Read'];
			$message = $fetch['Message'];
		}

		$this->viewMenu($item, $sender);

		echo'<table id="helpdesk" class="table table-bordered  table-hover">
			<thead>
				<tr>
					<th bgcolor="#EEEEEE" width="30px"></th>
					<th bgcolor="#EEEEEE" width="30px">#</th>
					<th bgcolor="#EEEEEE" width="120px"><b>Date/Time</b></th>
					<th bgcolor="#EEEEEE" width=""><b>Subject</b></th>
					<th bgcolor="#EEEEEE" width="70px"><b>Sender</b></th>
				</tr>
			</thead>
			<tbody>';



			if($read != "1"){
				$color = "#EEEEEE";
			}
 
			echo'<tr style="background-color: '.$color.';">
				<td><img src="' . $this->core->conf['conf']['path'] . '/templates/edurole/images/user.png"> </td>
				<td> '.$item.'</td>
				<td> '.$date.'</td>
				<td> <a href="' . $this->core->conf['conf']['path'] . '/helpdesk/read/'.$item.'">'.$subject.'</a></td>
				<td> <a href="' . $this->core->conf['conf']['path'] . '/information/show/'.$sender.'">'.$sender.'</a></td>
				</tr>';

		$sql = "UPDATE `helpdesk` SET `Read` = '1' WHERE `ID` = '$item' AND `RecipientID` != 'ALL'";

		$run = $this->core->database->doInsertQuery($sql);	

			echo'<tr style="background-color: #cccccc;">
				<td colspan="6"><b>'.$message.'</b></td>
				</tr>';
		

			echo'</tbody>
			</table>';
	}

	
	public function deleteHelpdesk($item) {
		
		$inbox = $this->core->userID;

		$sql = "DELETE FROM `helpdesk` WHERE `ID` = '$item' AND `RecipientID` = '$inbox'";

		$run = $this->core->database->doInsertQuery($sql);		

		$this->core->redirect("helpdesk", "inbox", NULL);
	}


	public function sendHelpdesk($item) {
		$title = $this->core->cleanPost['title'];
		$message = $this->core->cleanPost['message'];
		$recipient = $this->core->cleanPost['recipient'];
		
		$ip = $_SERVER['REMOTE_ADDR'];
		$uid = $this->core->userID;

		echo '<div class="toolbar">'.
		'<a href="' . $this->core->conf['conf']['path'] . '/helpdesk/inbox"><span class="glyphicon glyphicon-inbox"></span> Inbox</a>'.
		'</div>';

		$sql = "INSERT INTO `helpdesk` (`ID`, `SenderID`, `RecipientID`, `Date`, `Message`, `IP`, `Subject`) 
			VALUES (NULL, '$uid', '$recipient', NOW(), '$message', '$ip', '$title');";
			

		$run = $this->core->database->doInsertQuery($sql);		

		echo '<div class="successpopup">'. $this->core->translate("Your message has been sent.") .'</div>';
	}
}
?>