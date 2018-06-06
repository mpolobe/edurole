<?php
class cards{

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


	public function requestCards($item) {
		include $this->core->conf['conf']['formPath'] . "requestcards.form.php";
	}

	function editCards($item) {
		$sql = "SELECT * FROM `accommodation`,`housing`,`rooms` WHERE `housing`.StudentID = '$item' AND `housing`.RoomID = `rooms`.ID AND `accommodation`.ID =  `rooms`.accommodationID";
	
		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_row()) {
			include $this->core->conf['conf']['formPath'] . "editaccommodation.form.php";
		}
	}

	function printCards(){
		$start = $this->core->cleanGet['start'];
		$finish = $this->core->cleanGet['finish'];
		$sql = "SELECT `StudentID` FROM `housing` LIMIT $start, $finish";
		$run = $this->core->database->doSelectQuery($sql);

		while ($row = $run->fetch_assoc()) {

			$uid = $row['StudentID'];
			$this->frontCards($uid);
			//$this->backCards($uid);
		}
	}

	function frontCards($item){ 
		$sql = "SELECT * FROM `basic-information` WHERE `ID` = '$item'";
		$run = $this->core->database->doSelectQuery($sql);

		while ($row = $run->fetch_row()) {
			$results = TRUE;
			$firstname = ucfirst($row[0]);
			$middlename = ucfirst($row[1]);
			$surname = ucfirst($row[2]);

			$sex = $row[3];
			$uid = $row[4];
			$nrc = $row[5];
			$dob = $row[6];

			$mode = $row[19];
			$sstatus = $row[20]; 

			if (file_exists("datastore/identities/pictures/$uid.png_final.png")) {
				$profile =  '<img width="100%" src="'.$this->core->conf['conf']['path'].'/datastore/identities/pictures/' . $uid . '.png_final.png">';
			} else 	if (file_exists("datastore/identities/pictures/$uid.png")) {
				$profile =  '<img width="100%" src="'.$this->core->conf['conf']['path'].'/datastore/identities/pictures/' . $uid . '.png">';
			} else {
				return;
			}


			$date = date('d-M-Y', strtotime('+4 years'));
			if($mode == "Fulltime"){ $mode = "Full-time"; } 

			echo'<div style="padding: 15px; height: 250px; break-inside: avoid; text-align:center; width: 350pt;">
				<div class="university" style="font-size: 19pt; font-weight:bold; color: #FFF;  text-align: center; font-family: arial; background-color: green; padding: 2pt;"> 
					KWAME NKRUMAH UNIVERSITY
				</div>
				<div class="subtitle" style="text-align: left;">
					<span style="font-size: 14pt; font-weight:bold; text-align: left; font-family: arial; padding: 2pt;"> EduCard ID  |</span>   
					<span style=" font-size: 14pt; font-weight:bold; font-family: arial;color: #dc7400;">'.$mode.' student</span>
				</div>

				<div style="width: 105pt; border: 2px solid #000; float: left; margin-top: 15px; ">'.$profile.'</div>
					<div style="width: 200pt; float: left; padding-left: 20pt;">
					<div style="width: 100%; text-align: center;  font-family: arial; padding-top: 0px;"><img width="92" src="/edurole/templates/edurole/images/logo-large.png"></div>
					<div class="studentname" style="padding-top: 0pt; padding-bottom: 10pt; font-size: 15pt; text-align: left;  font-family: arial; text-align:center;">' . $firstname . ' ' . $middlename . '<br> <span style="font-size: 18pt; font-weight:bold;  ">' . $surname . '</span> </div>
					<div class="studentid" style=" font-family: arial;  float:left; width: 85pt; text-align: left;"> STUDENT ID:  </div> <div class="studentid" style=" font-family: arial; font-weight: bold; float:left;">  '.$uid.' </div>
					<div class="studentid" style=" font-family: arial;  float:left; width: 85pt; text-align: left;"> VALID UNTIL:  </div> <div class="studentid" style=" font-family: arial; font-weight: bold;  float:left;"> '.$date.' </div>
				</div>
			</div>

			<div style="page-break-before: always; text-align: center; width: 350pt;">
				<br><br>
				<p style="font-size: 14pt;">
					<b>THIS CARD REMAINS THE PROPERTY OF<br> KWAME NKRUMAH UNIVERSITY</b> 
					<br> IF FOUND PLEASE RETURN TO THE UNIVERSITY OR NEAREST POLICE STATION.
				</p>
				<br><img src="http://nkrumah.edu.zm/barcodes/src/test.php?id='.$uid.'"> <br> '.$uid.'
			</div>';
		}
	}



	function backCards($item){ 
		$sql = "SELECT * FROM `basic-information` WHERE `ID` = '$item'";
		$run = $this->core->database->doSelectQuery($sql);

		while ($row = $run->fetch_row()) {
			$results = TRUE;
			$uid = $row[4];

			if (file_exists("datastore/identities/pictures/$uid.png_final.png")) {
				$profile =  '<img width="100%" src="'.$this->core->conf['conf']['path'].'/datastore/identities/pictures/' . $uid . '.png_final.png">';
			} else 	if (file_exists("datastore/identities/pictures/$uid.png")) {
				$profile =  '<img width="100%" src="'.$this->core->conf['conf']['path'].'/datastore/identities/pictures/' . $uid . '.png">';
			} else {
				return;
			}

			echo'<div style="padding: 15px; height: 250px; break-inside: avoid; text-align:center; width: 350pt;">';
			echo'<div style="page-break-before: always">
			<p style="font-size: 14pt;"><b>THIS CARD REMAINS THE PROPERTY OF<br> KWAME NKRUMAH UNIVERSITY</b> <br> IF FOUND PLEASE RETURN TO THE UNIVERSITY OR NEAREST POLICE STATION. <br> <br>www.nkrumah.edu.zm</p>
			<br><img src="http://nkrumah.edu.zm/barcodes/src/test.php?id='.$uid.'"> <br> '.$uid.'
			</div>';
			echo'</div>';
		}
	}



	function staffCards($item){ 
		$sql = "SELECT * FROM `basic-information`, `access`, `roles` WHERE `basic-information`.`ID` = '$item' AND `basic-information`.ID = `access`.ID AND `roles`.ID = `access`.RoleID";
		$run = $this->core->database->doSelectQuery($sql);

		while ($row = $run->fetch_row()) {
			$results = TRUE;
			$firstname = strtoupper($row[0]);
			$middlename = strtoupper($row[1]);
			$surname = strtoupper($row[2]);

			$sex = $row[3];
			$uid = $row[4];
			$nrc = $row[5];
			$dob = $row[6];
			$role = $row[26];

			$mode = $row[19];
			$sstatus = $row[20]; 

			if (file_exists("datastore/identities/pictures/$uid.png_final.png")) {
				$profile =  '<img width="100%" src="'.$this->core->conf['conf']['path'].'/datastore/identities/pictures/' . $uid . '.png_final.png">';
			} else 	if (file_exists("datastore/identities/pictures/$uid.png")) {
				$profile =  '<img width="100%" src="'.$this->core->conf['conf']['path'].'/datastore/identities/pictures/' . $uid . '.png">';
			} else {
				return;
			}

 
			$date = date('d-M');

			echo'<div style="padding: 15px; height: 250px; break-inside: avoid; text-align:center; width: 100%;">';

			echo '<div class="university" style="font-size: 19pt; font-weight:bold; color: #FFF;  text-align: center; font-family: arial; background-color: #0879ca; padding: 2pt;"> KWAME NKRUMAH UNIVERSITY</div>
				<div class="subtitle" style="text-align: left;"><span style="font-size: 14pt; font-weight:bold; text-align: left; font-family: arial; padding: 2pt;"> EduCard ID  |</span>   <span style=" font-size: 14pt; font-weight:bold; font-family: arial;color: red;">OFFICIAL STAFF IDENTITY CARD</span></div>';


			echo'<div style="width: 105pt; border: 2px solid #000; float: left; margin-top: 15px; ">'.$profile.'</div>';


			echo'<div style="width: 200pt; float: left; padding-left: 20pt;">
			<div style="width: 100%; height: 100pt;">
				<div style="width: 100%; float: left; width: 100pt; text-align: center;  font-family: arial; padding-top: 0px;"><img width="120" src="/edurole/templates/edurole/images/logo-large.png"></div>
				<div class="studentname" style=" float: left; vertical-align: middle; height: 80pt; padding-top: 0pt; padding-bottom: 10pt; font-size: 15pt; font-weight:bold;  font-family: arial; text-align:center;"><br>' . $firstname . ' <br> <span style="font-size: 19pt;">' . $surname . ' </span></div>
			</div>
			<div class="studentid" style=" font-family: arial;  float:left; width: 85pt;  text-align: left;"> STAFF ID:  </div> <div class="studentid" style=" font-family: arial; font-weight: bold; float:left;">  '.$uid.' </div>
			<div class="studentid" style=" font-family: arial;  float:left; width: 85pt;  text-align: left;"> VALID UNTIL:  </div> <div class="studentid" style=" font-family: arial; font-weight: bold;  float:left;"> '.$date.'-2020 </div>
			<div class="studentid" style=" font-family: arial;  float:left; width: 85pt;  text-align: left;"> ROLE:  </div> <div class="studentid" style=" font-family: arial; font-weight: bold;  float:left;"> '.$role.' </div>';

			echo'</div>';


		}
	}

	
	function addCards() {
		include $this->core->conf['conf']['formPath'] . "addaccommodation.form.php";
	}

	function deleteCards($item) {
		$sql = 'DELETE FROM `accommodation` WHERE `ID` = "' . $item . '"';
		$run = $this->core->database->doInsertQuery($sql);

		$this->listAccomodation();
		$this->core->showAlert("The accommodation has been deleted");
	}

	function replaceCards() {
		$uid = $this->core->userID;
		$phone = $this->core->cleanPost['phone'];

		$sql = "INSERT INTO `accesscardsreplace` (`ID`, `StudentID`, `Phone`, `Payment`) VALUES (NULL, '$uid', '$phone', '', NOW());";
		$run = $this->core->database->doInsertQuery($sql);

		echo '<div class="successpopup">'. $this->core->translate("Succesfully submitted your request for ID card replacement.") .'</div>
		<div class="warningpopup">Please pay exactly 100 kwacha through ZANACO Billmuster to '.$this->core->conf['conf']['organization'].'.</div>';
	}


	function replacementsCards($item) {
		$uid = $this->core->subitem;

		if($item == "replaced"){
			$sql = "UPDATE `accesscardsreplace` SET `Payment` = '1' WHERE `StudentID` = '$uid';";
			$run = $this->core->database->doInsertQuery($sql);

			echo'<div class="successpopup">This card is marked as replaced</div>';
			return;
		} else if($item == "delete"){
			$sql = "DELETE FROM `accesscardsreplace` WHERE `StudentID` = '$uid';";
			$run = $this->core->database->doInsertQuery($sql);

			echo'<div class="successpopup">This request has been deleted</div>';
			return;
		}

		$sql = "SELECT DISTINCT `StudentID`, `DateTime`, `FirstName`, `Surname` 
			FROM `accesscardsreplace`, `basic-information`
			WHERE `accesscardsreplace`.StudentID = `basic-information`.ID
			AND `Payment` != '1'";

		$run = $this->core->database->doSelectQuery($sql);

		$results = FALSE;

		echo'<table id="results" class="table table-bordered table-striped table-hover">
			<thead>
				<tr>
					<th bgcolor="#EEEEEE" width="30px"></th>
					<th bgcolor="#EEEEEE"><b>StudentID</b></th>
					<th bgcolor="#EEEEEE"><b>Student Name</b></th>
					<th bgcolor="#EEEEEE"><b>Date Requested</b></th>
					<th bgcolor="#EEEEEE"><b>Action</b></th>
				</tr>
			</thead>
			<tbody>';

		while ($row = $run->fetch_assoc()) {
			$results = TRUE;

			$date= $row["DateTime"];
			$name = $row["FirstName"] . " " . $row["Surname"];
			$phone = $row["Phone"];
			$account = $row["StudentID"];

			echo'<tr>
				<td><img src="/edurole/templates/edurole/images/user.png"></td>
				<td> '.$account.'</td>
				<td> <a href="#">'.$name.'</a></td>
				<td> '.$date.'</td>
				<td> <a href="'.$this->core->conf['conf']['path'].'/cards/replacements/delete/'.$account.'">Delete</a> |
						 <a href="'.$this->core->conf['conf']['path'].'/cards/replacements/replaced/'.$account.'">Replaced</a></td>
				</tr>';

			$results = TRUE;
		}

		echo'</table>';
	}



	public function studentsCards($item) {
		$this->searchCards($item);
	}

	public function tokenCards($item){
		include $this->core->conf['conf']['formPath'] . "searchcard.form.php";


		if(!empty($this->core->cleanGet['userid'])){
			$userid = $this->core->cleanGet['userid'];
			$card = $this->core->cleanGet['card'];


			$sql = "INSERT INTO `accesscards` (`ID`, `UserID`, `CardID`, `Debit`, `DebitHash`, `CardNumber`, `CardCreated`) VALUES (NULL, '$userid', '$card', '', '', '1', CURDATE());";
			$run = $this->core->database->doInsertQuery($sql);

			$this->core->throwSuccess("Card assigned.");
		}


		if(!empty($this->core->cleanGet['card'])){
			$card = $this->core->cleanGet['card'];

			if(!empty($this->core->cleanGet['token'])){
				$token = $this->core->cleanGet['token'];
				$this->addtokenCards($card, $token, "Account deposit");
			}

			include $this->core->conf['conf']['formPath'] . "addtokencard.form.php";

			echo'<div style="border: 1px solid #ccc; background-color: #fefefe; margin-top: 20px; padding: 10px; height: 270px; width: 740px">';
			$exists = $this->showCards(NULL, $card);

			if($exists == FALSE){
				include $this->core->conf['conf']['formPath'] . "addcard.form.php";
			}

			echo '</div>';
		}

	}

	private function addtokenCards($card, $token, $desc){
		$sql = "UPDATE `accesscards` SET  `Debit` =  Debit + $token WHERE  `CardID` = '$card';";
		$run = $this->core->database->doInsertQuery($sql);

		$userid = $this->core->userID;
		$sql = "INSERT INTO `tokens` (`ID`, `TokenAmount`, `Mutation`, `CardID`, `MutatorID`, `DateTime`, `Hash`, `Description`) VALUES (NULL, '$token', 'ADD', '$card', '$userid', NOW(), '', '$desc');";
		$run = $this->core->database->doInsertQuery($sql);
	}


	private function subtokenCards($card, $token, $desc){
		$sql = "UPDATE `accesscards` SET  `Debit` =  Debit - $token WHERE  `CardID` = '$card';";
		$run = $this->core->database->doInsertQuery($sql);

		$userid = $this->core->userID;
		$sql = "INSERT INTO `tokens` (`ID`, `TokenAmount`, `Mutation`, `CardID`, `MutatorID`, `DateTime`, `Hash`, `Description`) VALUES (NULL, '$token', 'SUB', '$card', '$userid', NOW(), '', '$desc');";
		$run = $this->core->database->doInsertQuery($sql);
	}

	public function submealCards($item){

		echo '<div style="text-align: center; border: 1px solid #ccc; background-color: #fefefe; font-size: 30px;  margin-top: 20px;"> SWIPE CARD TO PAY </div>';

		include $this->core->conf['conf']['formPath'] . "searchcardhidden.form.php";

		if(!empty($this->core->cleanGet['card'])){
			$card = $this->core->cleanGet['card'];

			echo '<div style="text-align: center; font-size: 25px; color: #2FB70D; margin-top: 20px; border: 1px solid #ccc; background-color: #fefefe; font-weight: bold; padding: 20px;">
			One meal - 2 tokens paid
			</div>';
			
			echo'<div style="border: 1px solid #ccc; background-color: #fefefe; margin-top: 20px; padding: 10px; height: 270px;">';
			$this->subtokenCards($card, 2, "Payment for meal");
			$this->showCards(NULL, $card);

			echo '</div>';

		}
	}

	public function saveCards($item){
		$this->core->throwSuccess($this->core->translate("The user account has been updated"));
		$this->editCards($item);
	}

	public function personalCards($item){
		$userid = $this->core->userID;

		$sql = "SELECT * FROM  `basic-information` as bi, `access` as ac WHERE ac.`ID` = '" . $userid . "' AND ac.`ID` = bi.`ID`";

		$this->showInfoProfile($sql, TRUE);
	}

	public function showCards($item, $card) {
		if(empty($item)){
			$item = $this->core->userID;
		}

		if(isset($card)){
			$sql = "SELECT * FROM `accesscards`, `basic-information` WHERE `CardID` LIKE '" . $card . "' AND `UserID` = `basic-information`.ID";
		}else{
			$sql = "SELECT * FROM `accesscards`, `basic-information` WHERE `UserID` LIKE '" . $item . "' AND `UserID` = `basic-information`.ID";
		}

		$run = $this->core->database->doSelectQuery($sql);
		$results = FALSE;

		while ($row = $run->fetch_row()) {
			$results = TRUE;
			$ID = $row[0];
			$UserID = $row[1];
			$CardID = $row[2];
			$Debit = $row[3];
			$DebitHash = $row[4];
			$CardNumber = $row[5];
			$CardCreated = $row[6];

			$fname = $row[7];
			$mname = $row[8];
			$lname = $row[9];
			$uid = $row[11];


			echo '<div class="profilepic">';

			if (file_exists("datastore/identities/pictures/$uid.png")) {
				echo '<img width="100%" src="'.$this->core->conf['conf']['path'].'/datastore/identities/pictures/' . $uid . '.png">';
			} else {
				echo '<img width="100%" src="'.$this->core->conf['conf']['path'].'/templates/default/images/noprofile.png">';
			}

			echo'</div>';

			echo '<div style="float: left;">';
			echo'<span class="label">Student</span> <span style="font-size: 24px;">' . $fname . ' '. $mname.' '. $lname.'</span><br/>';
			echo'<span class="label"><b>Card Debit</b></span> <span style="font-size: 20px;">' . $Debit . ' TOKENS</span><br/>';
			echo'<span class="label">Card ID</span> ' . $CardID . '<br/>';

			echo'</div>';

			echo '<div class="toolbar" style="float:left; width: 70%">'.
			'<a href="' . $this->core->conf['conf']['path'] . '/cards/transactions/'.$card.'">Show Transactions</a>'.
			'</div>';


			return true;
		}

		if($results == false){
			$this->core->throwSuccess("This user currently does not have an EduCard.");

			return false;
		}

	}


	public function userCards($item) {
		$sql = "SELECT * FROM `accesscards` WHERE `CardID` LIKE '" . $item . "'";

		$run = $this->core->database->doSelectQuery($sql);

		while ($row = $run->fetch_row()) {
			$results == TRUE;

			$ID = $row[0];
			$UserID = $row[1];
			$CardID = $row[2];

			include $this->core->conf['conf']['viewPath'] . "information.view.php";
			$information = new information($this->core);

			$sql = "SELECT * FROM `basic-information` WHERE `ID` LIKE '" . $UserID . "'";
			$information->showInfoProfile($sql, FALSE);

		}
	}



	public function transactionsCards($card) {

		if(empty($item)){
			$item = $this->core->userID;

			$sql = "SELECT * FROM `accesscards` WHERE `UserID` LIKE '" . $item . "'";

			$run = $this->core->database->doSelectQuery($sql);

			while ($row = $run->fetch_row()) {

				$card = $row[2];
			}

		}

		$sql = "SELECT * FROM tokens WHERE CardID = '$card'";

		$run = $this->core->database->doSelectQuery($sql);


		if(!isset($this->core->cleanGet['offset'])){

			echo'<table id="results" class="table table-bordered table-striped table-hover">
			<thead>
				<tr>
					<th bgcolor="#EEEEEE" width="30px"></th>
					<th bgcolor="#EEEEEE"><b>Date/Time</b></th>
					<th bgcolor="#EEEEEE"><b>Description</b></th>
					<th bgcolor="#EEEEEE"><b>Amount</b></th>
					<th bgcolor="#EEEEEE"><b>Account</b></th>
				</tr>
			</thead>
			<tbody>';
		}



		while ($row = $run->fetch_row()) {
			$results == TRUE;


			$date= $row[5];
			$description= $row[7];
			$amount = $row[1];
			$account = $row[4];

			echo'<tr>
				<td><img src="/edurole/templates/edurole/images/user.png"></td>
				<td> '.$date.'</td>
				<td> '.$description.'</td>
				<td> '.$amount.'</td>
				<td> '.$account.'</td>
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


}
?>
