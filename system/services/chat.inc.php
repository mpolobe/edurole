<?php
class chat {

	public $core;
	public $service;
	public $item = NULL;

	public function configService() {
		$this->service->output = TRUE;
		return $this->service;
	}


	public function runService($core) {
		$this->core = $core;


		if($this->core->item == "send"){
			if(isset($this->core->cleanGet["msg"])){
				$message = $this->core->cleanGet["msg"];
				$sender = $this->core->userID;
				$recipient = $this->core->cleanGet["uid"];

				$sql = "INSERT INTO `chat` (`ID`, `SenderID`, `RecipientID`, `Message`, `DateTime`, `Read`) 
					VALUES (NULL, '$sender', '$recipient', '$message', NOW(), '0');";

				$run = $this->core->database->doInsertQuery($sql);

				$date = date('Y-m-d G:i:s');
				echo '{
					"query": "send",
					"status": "Sent",
					"message": "'.$message.'",
					"date": "'.$date.'"
				}';

			}
		} else 	if($this->core->item == "check"){
			if(isset($this->core->cleanGet["last"])){
				$lastid = $this->core->cleanGet["last"];
				$recipient = $this->core->userID;
				$sender = $_SESSION['lastchat'];

					$lastid = $this->core->cleanGet["last"];


					if (file_exists("datastore/identities/pictures/$recipient.png")) {
						$ravatar = $this->core->conf['conf']['path'].'/datastore/identities/pictures/'.$recipient.'.png';
					} else {
						$ravatar = ''.$this->core->conf['conf']['path'].'/templates/default/images/noprofile.png';
					}

					if (file_exists("datastore/identities/pictures/$sender.png")) {
						$savatar = $this->core->conf['conf']['path'].'/datastore/identities/pictures/'.$sender.'.png';
					} else {
						$savatar = ''.$this->core->conf['conf']['path'].'/templates/default/images/noprofile.png';
					}


					$sqlu = "UPDATE `chat` SET `Read` = '1' WHERE `RecipientID` = '$recipient' AND `SenderID` = '$sender'";
					$this->core->database->doInsertQuery($sqlu);

					$sql = "SELECT * FROM `chat` 
						WHERE `RecipientID` = '$recipient' AND `SenderID` = '$sender' 
						AND `ID` > '$lastid'
						OR `RecipientID` = '$sender' AND `SenderID` = '$recipient' 
						AND `ID` > '$lastid'
						ORDER BY `DateTime` ASC ";

					$run = $this->core->database->doSelectQuery($sql);

					while($row = $run->fetch_assoc()){
						$id = $row['ID'];
						$cmessage = $row['Message'];
						$cdatetime = $row['DateTime'];
						$crecipient = $row['RecipientID'];
						$csender = $row['SenderID'];
						$gravatar = "";
						$gsavatar = "";

						if($sender == $csender){
							$status = 'sent';
							$avatar = $savatar;
							$gsavatar = '<div class=\"gavatar\"><img src=\"'.$avatar.'\" class=\" img-responsive\"></div>';
						} else {
							$status = 'received';
							$avatar = $ravatar;
							$gravatar = '<div class=\"gavatar\"><img src=\"'.$avatar.'\" class=\" img-responsive\"></div>';
						}

						$messages .= '<div class=\"row msg_container base_'.$status.'\">'.$gravatar.'<div class=\"col-md-10 col-xs-10\" id=\"count'.$id.'\"><div class=\"messages msg_'.$status.'\"><p>'.$cmessage.'</p><time datetime=\"'.$cdatetime .'\">'.$username.' - '.$cdatetime .'</time></div></div>'.$gsavatar.'</div>';
					}

					$out = rtrim($out, ',');

					echo '{
						"query": "history",
						"messages": "'.$messages.'"
					}';

			}

		} else 	if($this->core->item == "history"){
			
			if(isset($this->core->cleanGet["uid"])){
				$sender = $this->core->cleanGet["uid"];
				$recipient = $this->core->userID;

				$_SESSION['lastchat'] = $sender;

				if (file_exists("datastore/identities/pictures/$recipient.png")) {
					$ravatar = $this->core->conf['conf']['path'].'/datastore/identities/pictures/'.$recipient.'.png';
				} else {
					$ravatar = ''.$this->core->conf['conf']['path'].'/templates/default/images/noprofile.png';
				}

				if (file_exists("datastore/identities/pictures/$sender.png")) {
					$savatar = $this->core->conf['conf']['path'].'/datastore/identities/pictures/'.$sender.'.png';
				} else {
					$savatar = ''.$this->core->conf['conf']['path'].'/templates/default/images/noprofile.png';
				}

				$sqlu = "UPDATE `chat` SET `Read` = '1' WHERE `RecipientID` = '$recipient' AND `SenderID` = '$sender'";
				$this->core->database->doInsertQuery($sqlu);

				$sql = "SELECT * FROM `chat` 
					WHERE `RecipientID` = '$recipient' AND `SenderID` = '$sender' 
					OR `RecipientID` = '$sender' AND `SenderID` = '$recipient' 
					ORDER BY `DateTime` ASC LIMIT 30";
				$run = $this->core->database->doSelectQuery($sql);

				while($row = $run->fetch_assoc()){
					$id = $row['ID'];
					$cmessage = $row['Message'];
					$cdatetime = $row['DateTime'];
					$crecipient = $row['RecipientID'];
					$csender = $row['SenderID'];
					$gravatar = "";
					$gsavatar = "";

					if($sender == $csender){
						$status = 'sent';
						$avatar = $savatar;
						$gsavatar = '<div class=\"gavatar\"><img src=\"'.$avatar.'\" class=\" img-responsive\"></div>';
					} else {
						$status = 'received';
						$avatar = $ravatar;
						$gravatar = '<div class=\"gavatar\"><img src=\"'.$avatar.'\" class=\" img-responsive\"></div>';
					}

						$messages .= '<div class=\"row msg_container base_'.$status.'\">'.$gravatar.'<div class=\"col-md-10 col-xs-10\" id=\"count'.$id.'\"><div class=\"messages msg_'.$status.'\"><p>'.$cmessage.'</p><time datetime=\"'.$cdatetime .'\">'.$username.' - '.$cdatetime .'</time></div></div>'.$gsavatar.'</div>';
				}

				$out = rtrim($out, ',');

				echo '{
					"query": "history",
					"messages": "'.$messages.'"
				}';

			}
		}
	}
}

?>