<?php
class vote{

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

	public function showVote($item) {

		$uid = $this->core->userID;
		$role = $this->core->role;

		$sql = "SELECT * FROM `votes` WHERE `VoterID` = '$uid' AND `ElectionID` = '$item'";
		$run = $this->core->database->doSelectQuery($sql);

		if($run->num_rows > 0){
			echo '<div class="warningpopup">YOU HAVE ALREADY VOTED!</div><br/>';
			$this->manageVote();
			return;
		}

		if($role < 100 || $role == 1000){

			$sql = "SELECT * FROM `candidates`, `basic-information` 
				WHERE `candidates`.ID = `basic-information`.ID
				AND `ElectionID` = '$item'";

			$run = $this->core->database->doSelectQuery($sql);

			echo'<h1>Please click on your candidate below to vote:</h1> Please note you have one vote!<br><br>
				<div style="width: 750px;">';

			while ($fetch = $run->fetch_assoc()) {
				$name = $fetch['FirstName'] . ' ' . $fetch['Surname'];
				$id = $fetch['ID'];
				$description = $fetch['Description'];


				if (file_exists("datastore/identities/pictures/$id.png_final.png")) {
					$img =  '<img width="100%" src="'.$this->core->conf['conf']['path'].'/datastore/identities/pictures/' . $id . '.png_final.png">';
				} else 	if (file_exists("datastore/identities/pictures/$id.png")) {
					$img =  '<img width="100%" src="'.$this->core->conf['conf']['path'].'/datastore/identities/pictures/' . $id . '.png">';
				} else {
					$img =  '<img width="100%" src="'.$this->core->conf['conf']['path'].'/templates/default/images/noprofile.png">';
				}

				echo '<div style="float: left; width: 250px; border: solid 2px #666; padding: 15px; position: relative; text-align: center;"><h1>VOTE FOR</h1><a href="'.$this->core->conf['conf']['path'].'/vote/cast/'.$id.'">'.$img.'<span style="font-size: 20px;"<b>'.$name.'</b></span><br></a>'.$description.'</div>';
			}
			
		} else {
			echo '<div class="warningpopup">ONLY STUDENTS CAN VOTE</h2><br/>'; 
		}

		echo'</div><p><br></p>';
	}
	

	public function castVote($item) {
		$sid = $this->core->userID;

		$sql = "INSERT INTO `votes` (`ID`, `VoterID`, `CandidateID`, `DateTime`) 
			VALUES ('', '$sid', '$item',  NOW());";

		$run = $this->core->database->doInsertQuery($sql);
		echo '<div class="successpopup">Your vote has been cast!</div>';
	}

	public function manageVote($item) {
	
		echo'<h1>CURRENT VOTE TOTALS</h1>';

		$sql = "SELECT `CandidateID`, COUNT(`VoterID`) as total, `FirstName`, `Surname` FROM `votes`, `basic-information` WHERE `CandidateID` = `basic-information`.ID GROUP BY `CandidateID`";
		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_assoc()) {
			$name = $fetch['FirstName'] . ' ' . $fetch['Surname'];
			$candidate = $fetch['CandidateID'];
			$votes = $fetch['total'];
			$total = $total + $votes;

			echo "$name $votes";
		}
	}
}
?>