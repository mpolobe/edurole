<?php
class accommodation{

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
		'<a href="' . $this->core->conf['conf']['path'] . '/accommodation/manage">Room Occupants</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/accommodation/rooms/empty">Available rooms</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/accommodation/hostel/show">Hostel management</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/accommodation/requests">Applications</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/accommodation/assign">Assign room</a>'.
		'</div>';
	}


	public function removeAccommodation($item) {

		$sql = "DELETE FROM `housing` WHERE `StudentID` = $item";
		$this->core->database->doInsertQuery($sql);

		$sql = "DELETE FROM `housingapplications` WHERE `StudentID` = $item";
		$this->core->database->doInsertQuery($sql);

		echo '<span class="successpopup">STUDENT REMOVED FROM ROOM</span>';
	}

	public function clearAccommodation($item) {
		if($item == "TRUE"){
			$sqla = "TRUNCATE TABLE `housing`";
			$sqlb = "TRUNCATE TABLE `housingapplications`";
	
			$this->core->database->doSelectQuery($sqla);
			$this->core->database->doSelectQuery($sqlb);


			echo'<div class="successpopup">All rooms and applications have been cleared</div>';
		} else {
			echo'<div class="heading">ARE YOU VERY SURE YOU WANT TO REMOVE ALL STUDENTS FROM THEIR ROOMS?</div>
			</p><p><button onclick="window.history.back();" name="no"  id="no" class="input submit" style="font-size: 18px; font-weight: bold; padding: 5px; padding-left: 20px; padding-right: 20px; padding-bottom: 10px; border: 1px solid #000; background-color: #e81f1f"> <span class="glyphicon glyphicon-thumbs-down" aria-hidden="true"> NO</button> 
			<a href="' . $this->core->conf['conf']['path'] . '/accommodation/clear/TRUE"><button onclick="" class="input submit" style="font-size: 18px; font-weight: bold; padding: 5px;  padding-left: 20px; padding-bottom: 10px; padding-right: 20px; border: 1px solid #000; background-color: #39c541"> <span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"> YES</button></p></a>';
		}
	}

	public function assignAccommodation($item) {

		if(empty($item)){

			$userid = $this->core->cleanGet['userid'];

			echo'<form id="roomassign" name="roomassign" method="get"  action="'. $this->core->conf['conf']['path'] .'/accommodation/assign/room">';

			$sql = "SELECT * FROM `hostel`";
			$run = $this->core->database->doSelectQuery($sql);

			echo'<div class="label">Select hostel</div><select name="hostel" class="submit">';
			while ($row = $run->fetch_assoc()) {
				$hostelID = $row["ID"];
				$hostelName = $row["HostelName"];
				echo '<option value="'.$hostelID.'">'.$hostelName.'</span>';
			}
			echo'</select><br>';

			echo'<div class="label">Student number</div><input name="userid" value="'.$userid.'"><br>';
			echo'<div class="label">&nbsp;</div><input type="submit" value="Select room"></form>';

		}else if($item == "room"){

			$userid = $this->core->cleanGet['userid'];
			$hostelid = $this->core->cleanGet['hostel'];

			if(empty($userid)){
				echo '<span class="errorpopup">You did not enter a student number</span>';
				return;
			}

			echo'<form id="roomassign" name="roomassign" method="get"  action="'. $this->core->conf['conf']['path'] .'/accommodation/assign/save">';

			$sql = "SELECT * FROM `rooms`,  `hostel` WHERE `HostelID` = $hostelid and `hostel`.ID = `HostelID`";
			$run = $this->core->database->doSelectQuery($sql);

			$rooms .= '<option value="">PLEASE SELECT HERE</span>';
			
			while ($row = $run->fetch_row()) {
				$roomID = $row[0];
				$roomName = $row[3];
				$roomType = $row[2];
				$hostelName = $row[7];
				$rooms .= '<option value="'.$roomID.'">'.$roomName.' ('.$roomType.')</span>';
			}

			echo '<div class="label">Student selected</div>'.$userid.'<br>';
			echo '<div class="label">Hostel selected</div>'.$hostelName.'<br>';
			echo'<div class="label">Select room number</div><select name="room" class="submit">'.$rooms.'</select><br>';


			echo'<input type="hidden" name="userid" value="'.$userid.'">';

			echo'<div class="label">&nbsp;</div><input type="submit" value="Assign room"></form>';

		}else if($item == "save"){

			$userid = $this->core->cleanGet['userid'];
			$roomid = $this->core->cleanGet['room'];

			if(empty($roomid)){
				echo '<span class="errorpopup">You did not select a room</span>';
				return;
			}

			$admin = $this->core->userID;
			$rsql = "INSERT INTO `housing` (`ID`, `StudentID`, `RoomID`, `HousingStatus`, `CheckIn`, `AssignedBy`) VALUES (NULL, '$userid', '$roomid', '1', NOW(), '$admin');";
			$this->core->database->doInsertQuery($rsql);
			

			echo '<span class="successpopup">Room assigned</span>';

			include $this->core->conf['conf']['viewPath'] . "information.view.php";
			$information = new information();
			$information->buildView($this->core);
			$information->showInformation($userid);
			
		}
	}
	
	public function manageAccommodation($item) {
		$item = $this->core->cleanGet['hostel'];
		$this->viewMenu();

		echo '<form id="narrow" name="narrow" method="get" action=""><div class="toolbar">';
		echo'<div class="toolbaritem">Filter to show students from: ';
		echo'<select name="hostel" class="submit" style="width: 230px; margin-top: -17px;">';
		echo'<option value="0"> -- Choose hostel -- </option>';
		$sql = "SELECT ID, HostelName FROM  `hostel`";

		$run = $this->core->database->doSelectQuery($sql);

		while ($row = $run->fetch_row()) {
			echo'<option value="'.$row[0].'">'.$row[1].'</option>';
		}

		echo '</select>';
		echo'<input type="submit" value="filter"  style="width: 80px; margin-top: -15px;"/></div></div></form>';

		if(isset($item)){
			$sql = "SELECT * FROM `housing`, `rooms`, `hostel`, `basic-information`, `periods`
				WHERE `housing`.RoomID = `rooms`.ID 
				AND `rooms`.HostelID = `hostel`.ID 
				AND `housing`.PeriodID = `periods`.ID 
				AND `basic-information`.ID = `housing`.StudentID
				AND `rooms`.HostelID = $item";
		}else{
			// FASTER QUERIES

			$sql = "SELECT *, `minors`.ProgramName AS Min, `majors`.ProgramName AS Maj 
				FROM `housing`, `rooms`, `hostel`, `basic-information`, `periods`, `student-program-link`, `programmes` as majors, `programmes` as minors
				WHERE `housing`.RoomID = `rooms`.ID 
				AND `rooms`.HostelID = `hostel`.ID 
				AND `basic-information`.ID = `student-program-link`.StudentID
				AND `majors`.ID = `student-program-link`.Major
				AND `minors`.ID = `student-program-link`.Minor
				AND `housing`.PeriodID = `periods`.ID 
				AND `basic-information`.ID = `housing`.StudentID
				AND `basic-information`.ID IN (SELECT StudentID FROM `course-electives` WHERE  `Approved` = '1')";


			$sqln = "SELECT * FROM `housing`, `rooms`, `hostel`, `basic-information`, `periods`
				WHERE `housing`.RoomID = `rooms`.ID 
				AND `rooms`.HostelID = `hostel`.ID 
				AND `housing`.PeriodID = `periods`.ID 
				AND `basic-information`.ID = `housing`.StudentID
				AND `basic-information`.ID NOT IN (SELECT StudentID FROM `course-electives` WHERE  `Approved` = '1')";

		}
		
		$run = $this->core->database->doSelectQuery($sql);
		$runn = $this->core->database->doSelectQuery($sqln);


		echo'<table id="results" class="table table-bordered table-striped table-hover">' .
			'<thead><tr>' .
			'<td width="100px"><b>Student ID</b></td>' .
			'<td><b>Student Name</b></td>' .
			'<td><b>Hostel - Room</b></td>' .
			'<td><b>Date assigned</b></td>' .
			'<td><b>Management</b></td>' .
			'</tr></thead><tbody>';

		$i = 0; $c = 1;
		while ($row = $run->fetch_assoc()) {

			$delivery = $row["StudyType"];

			$combination = '<i>'.strtoupper(substr($row["Maj"],0,8) .'/'. substr($row["Min"],0,8)).'</i>';

			if($delivery != "Fulltime"){
				$duration = $combination . "(" . $row["Weeks"] . " Weeks)";
			} else {
				$duration = "";
			}
		
			echo'<tr>' .
				'<td>'.$c.' - <b><a href="' . $this->core->conf['conf']['path'] . '/information/show/' . $row["StudentID"] . '">' . $row["StudentID"] . '</a></b></td>' .
				'<td><b>' . $row["FirstName"] . ' ' . $row["Surname"] . '</b>  '.$duration.'</td>' .
				'<td><a href="' . $this->core->conf['conf']['path'] . '/accommodation/roomlist/' . $row["HostelID"] . '">' . $row["HostelName"] . '</a> - <a href="' . $this->core->conf['conf']['path'] . '/accommodation/room/' . $row["RoomID"] . '">' . $row["RoomNumber"] . '</a> / '.$row["BedSpace"].'</td>' .
				'<td>' . $row["CheckIn"] . '</td>' .
				'<td>' .
				'<a href="' . $this->core->conf['conf']['path'] . '/accommodation/remove/' . $row["StudentID"] . '"> <img src="' . $this->core->conf['conf']['path'] . '/templates/edurole/images/del.png"> remove</a>' .
				'</td></tr>';
			$c++;
		}

		echo '</tbody></table></p>';
 

		// NOT REGISTERED PROPERLY


		echo'<div class="heading">STUDENTS WHO DID NOT REGISTER </div>
			<table id="results" class="table table-bordered table-striped table-hover">' .
			'<thead><tr>' .
			'<td><b>Student ID</b></td>' .
			'<td><b>Student Name</b></td>' .
			'<td><b>Hostel - Room</b></td>' .
			'<td><b>Date assigned</b></td>' .
			'<td><b>Management</b></td>' .
			'</tr></thead><tbody>';

		$i = 0; $c = 1;
		
		while ($row = $runn->fetch_assoc()) {

			echo'<tr>' .
				'<td>'.$c.' - <b><a href="' . $this->core->conf['conf']['path'] . '/information/show/' . $row["StudentID"] . '">' . $row["StudentID"] . '</a></b></td>' .
				'<td><b>' . $row["FirstName"] . ' ' . $row["Surname"] . '</b></td>' .
				'<td><a href="' . $this->core->conf['conf']['path'] . '/accommodation/roomlist/' . $row["HostelID"] . '">' . $row["HostelName"] . '</a> - <a href="' . $this->core->conf['conf']['path'] . '/accommodation/room/' . $row["RoomID"] . '">' . $row["RoomNumber"] . '</a> / '.$row["BedSpace"].'</td>' .
				'<td>' . $row["CheckIn"] . '</td>' .
				'<td>' .
				'<a href="' . $this->core->conf['conf']['path'] . '/accommodation/remove/' . $row["StudentID"] . '"> <img src="' . $this->core->conf['conf']['path'] . '/templates/edurole/images/del.png"> remove</a>' .
				'</td></tr>';
			$c++;
		}

		echo '</tbody></table></p>';
	}


	public function showAccommodation($item) {

	}

	public function roomAccommodation($item) {

		$this->viewMenu();

		$sql = "SELECT * FROM `rooms` LEFT JOIN `housing` ON `rooms`.ID = `housing`.RoomID LEFT JOIN `hostel` ON `rooms`.HostelID = `hostel`.ID 
		LEFT JOIN `basic-information` ON `housing`.StudentID = `basic-information`.ID 
		WHERE`rooms`.ID = $item";

		$run = $this->core->database->doSelectQuery($sql);


		$c = 1;
		$tab = FALSE;

		while ($row = $run->fetch_assoc()) {

			if(empty($row["StudentID"])){
				echo'<span class="errorpopup">Room is empty</span>';

			} else{
				if($c == 1){

					echo'<div class="heading">'.$row["HostelName"].' room '.$row["RoomNumber"].'</div>';
					echo'<div class="heading">CAPACITY: '.$row["RoomCapacity"].'</div>';

					echo'<table id="results" class="table table-bordered table-striped table-hover">' .
					'<thead><tr>' .
					'<td><b>Student ID</b></td>' .
					'<td><b>Student Name</b></td>' .
					'<td><b>Hostel - Room</b></td>' .
					'<td><b>Date assigned</b></td>' .
					'<td><b>Management</b></td>' .
					'</tr></thead><tbody>';

					$tab = TRUE;
				}

				echo'<tr>' .
				'<td>'.$c.' - <b><a href="' . $this->core->conf['conf']['path'] . '/information/show/' . $row["StudentID"] . '">' . $row["StudentID"] . '</a></b></td>' .
				'<td><b>' . $row["FirstName"] . ' ' . $row["Surname"] . '</b></td>' .
				'<td><a href="' . $this->core->conf['conf']['path'] . '/accommodation/roomlist/' . $row["HostelID"] . '">' . $row["HostelName"] . '</a> - <a href="' . $this->core->conf['conf']['path'] . '/accommodation/room/' . $row["RoomID"] . '">' . $row["RoomNumber"] . '</a></td>' .
				'<td>' . $row["CheckIn"] . '</td>' .
				'<td>' .
				'<a href="' . $this->core->conf['conf']['path'] . '/accommodation/remove/' . $row["StudentID"] . '"> <img src="' . $this->core->conf['conf']['path'] . '/templates/edurole/images/del.png"> remove</a>' .
				'</td></tr>';
				$c++;
			}
		}

		if($tab == TRUE){
			echo '</tbody></table></p>';
		}
	}

	private function doBill($uid, $amount, $description, $period, $boarding) {

		$date = ("Ymd");

		$sql = "INSERT INTO `billing` (`ID`, `StudentID`, `Amount`, `Date`, `Description`, `PackageName`) VALUES (NULL, '$uid', '$amount', NOW(), '$description', '$date$amount');";
		$this->core->database->doInsertQuery($sql);

		echo '<span class="successpopup">PLEASE PAY FOR ACCOMMODATION YOU HAVE BEEN BILLED K'.$amount.'</span>';

	}


	public function swapAccommodation($item) {

		$period = $this->core->cleanGet['period'];
		$uid = $this->core->cleanGet['uid'];

		if(isset($period) && isset($uid)){

			$sql = "UPDATE `housing` SET `PeriodID` = '$period' WHERE `housing`.`StudentID` = $uid;";
			$this->core->database->doInsertQuery($sql);

			$sql = "UPDATE `housingapplications` SET `PeriodID` = '$period' WHERE `housingapplications`.`StudentID` = $uid;";
			$this->core->database->doInsertQuery($sql);
	
			if($period == "32"){
				$sql = "UPDATE `billing` SET Amount = '900', `Description` = 'Boarding fee 3 weeks - DE August 2017' WHERE `StudentID` = '$uid' AND Amount = '800' AND Date LIKE '2017-08%';";
				$this->core->database->doInsertQuery($sql);
			}else if($period == "31"){
				$sql = "UPDATE `billing` SET Amount = '800', `Description` = 'Boarding fee 3 weeks - DE August 2017' WHERE `StudentID` = '$uid' AND Amount = '800' AND Date LIKE '2017-08%';";
				$this->core->database->doInsertQuery($sql);
			}

			echo '<div class="successpopup">STUDENT UPDATED</div>';
		} else {

			echo'<form>
			 <div class="label">Period</div>

			<select name="period" style="width: 260px">
				<option value="32" selected>3 weeks</option>
				<option value="31">2 weeks</option>
			</select><br />

			<div class="label">Student Number</div>
			<input type="text" name="uid" id="uid" class="submit" style="width: 260px" value="'. $item .'"/><br>


			<div class="label">Submit</div>
			<input type="submit" class="submit" value="UPDATE!" style="width: 260px"/>

			</form>';
		}

	}

	

	public function roomsAccommodation($item) {
		$this->viewMenu();
		$subitem = $this->core->subitem;

		if($item == "add"){
			include $this->core->conf['conf']['formPath'] . "addroom.form.php";
			return;
		} else if($item == "delete"){
			$sql = "DELETE FROM `rooms` WHERE ID = $subitem";
			$run = $this->core->database->doInsertQuery($sql);
			$this->core->showAlert("The room has been deleted");
		} else if($item == "edit"){
			$room = $this->core->subitem;
			include $this->core->conf['conf']['formPath'] . "editroom.form.php";
			return;
		} else if($item == "save"){
			$id = $this->core->cleanGet['ids'];
			$type = $this->core->cleanGet['type'];
			$number = $this->core->cleanGet['number'];
			$capacity = $this->core->cleanGet['capacity'];
			$price = $this->core->cleanGet['price'];
			$hostel = $this->core->cleanGet['hostel'];


			if(isset($id)){
				$sql = "UPDATE `rooms` SET `RoomType` = '$type', `RoomCapacity` = '$capacity', `RoomPrice` = '$price' WHERE `rooms`.ID = '$id';";
			}else{
				$sql = "INSERT INTO `rooms` (`ID`, `HostelID`, `RoomType`, `RoomNumber`, `RoomCapacity`, `RoomPrice`) VALUES (NULL, '$hostel', '$type', '$number', '$capacity', '$price');";
			}

			$run = $this->core->database->doInsertQuery($sql);
			echo '<div class="successpopup">The room has been updated</div>';
			return;
		}

		echo'<table id="results" class="table table-bordered table-striped table-hover">' .
			'<thead><tr>' .
			'<td><b>Room number</b></td>' .
			'<td><b>Hostel</b></td>' .
			'<td><b>Capacity</b></td>' .
			'<td><b>Occupancy</b></td>' .
			'<td><b>Management</b></td>' .
			'</tr></thead><tbody>';
		

		if(isset($item)){
			$sql = "SELECT *, COUNT(`housing`.ID) as count, `rooms`.ID as roomid FROM `hostel`, `rooms`
			LEFT JOIN `housing` ON `housing`.RoomID = `rooms`.ID 
			WHERE `hostel`.ID = `rooms`.HostelID
			GROUP BY `rooms`.ID ORDER BY  `count`, `hostel`.ID, `rooms`.RoomNumber  DESC";
		}else{
			$sql = "SELECT *, COUNT(`housing`.ID) as count, `rooms`.ID as roomid FROM `hostel`, `rooms`
			LEFT JOIN `housing` ON `housing`.RoomID = `rooms`.ID 
			WHERE `hostel`.ID = `rooms`.HostelID
			GROUP BY `rooms`.ID ORDER BY  `count`, `hostel`.ID, `rooms`.RoomNumber  DESC";
		}
		if($item == "empty"){
			$sql = "SELECT *, COUNT(`housing`.ID) as count, `rooms`.ID as roomid FROM `hostel`, `rooms`
			LEFT JOIN `housing` ON `housing`.RoomID = `rooms`.ID 
			WHERE `hostel`.ID = `rooms`.HostelID
			AND `rooms`.RoomCapacity > (SELECT COUNT(RoomID) FROM `housing` WHERE `rooms`.ID = RoomID)
			GROUP BY `rooms`.ID 
			ORDER BY RoomCapacity ASC";
		} 
		$run = $this->core->database->doSelectQuery($sql);

		$i = 0; $c = 1;
		while ($row = $run->fetch_assoc()) {

			echo'<tr >' . 
				'<td>'.$c.' - <b><a href="' . $this->core->conf['conf']['path'] . '/accommodation/rooms/' . $row["roomid"] . '">' . $row["RoomNumber"] . '</a></b></td>' .
				'<td><a href="' . $this->core->conf['conf']['path'] . '/accommodation/hostel/' . $row["HostelID"] . '">' . $row["HostelName"] . ' </a>('.$row["Type"].')</td>' .
				'<td>' . $row["RoomType"] . ' (' . $row["RoomCapacity"] . ' beds)</td>' .
				'<td>' . $row["count"] . ' from ' . $row["RoomCapacity"] . ' bedspaces</td>' .
				'<td>' .
				'<a href="' . $this->core->conf['conf']['path'] . '/accommodation/rooms/edit' . $row[0] . '"> <img src="' . $this->core->conf['conf']['path'] . '/templates/edurole/images/edi.png"> edit</a>' .
				'</td></tr>';
			$c++;
		}

		echo '</tbody></table></p>';
	}


	public function applyAccommodation($item) {
		$uid = $this->core->userID;

		if(substr($uid,0,1) == 1){
			$masters = TRUE;
		}
	
		if(!isset($_SESSION['weeks']) || !isset($_SESSION['period']) || !isset($_SESSION['group'])){
			echo'<div class="warningpopup">Please open the Term Registration form first</a></div>';
		}

		$closed = TRUE;
		if($closed == TRUE){
//			echo'<div class="warningpopup">APPLICATth OF AUGUST</a></div>';
		}

		$locked = FALSE; 

		$sqlx = "SELECT * FROM `housing` WHERE `housing`.StudentID = $uid";
		$runx = $this->core->database->doSelectQuery($sqlx);

		while ($rowx = $runx->fetch_assoc()) {
			echo'<div class="successpopup">You have applied for a room! No further applications from you are accepted</div>';
			$locked = TRUE;
			die();
		}


		$weeks = $_SESSION['weeks'];
		$periodid = $_SESSION['period'];
		$group = $_SESSION['group'];

		if(!empty($item)){
			$number = $item;

			// FOR FULLTIME
			// include $this->core->conf['conf']['formPath'] . "applyforroom.form.php";

			// FOR DISTANCE
			$userid = $this->core->userID;
			$admin = $this->core->userID;

			$deadline = date("Y-m-d", strtotime("+1 week"));

			$sql = "INSERT INTO `housingapplications` (`StudentID`, `RoomID`, `DateTime`, `Province`, `District`, `Disabled`, `PeriodID`) 
				VALUES ('$userid', '$number', NOW(), 'CT', 'DISTANCE', 'No', '$periodid');";
			$run = $this->core->database->doInsertQuery($sql);

			$sql = "UPDATE `housingapplications` SET `Deadline` = '$deadline' WHERE `StudentID` = $userid;";
			$run = $this->core->database->doInsertQuery($sql);


			$sql = "INSERT INTO `housing` (`ID`, `StudentID`, `RoomID`, `HousingStatus`, `CheckIn`, `AssignedBy`, `PeriodID`) 
				VALUES (NULL, '$userid', '$number', '1', NOW(), '$userid', '$periodid');";
			$run = $this->core->database->doInsertQuery($sql);


			/* CHECK IF STUDENT IS SCIENCE OR BBS AND STAYS FOR 3 WEEKS
			$sql = "SELECT StudentID FROM `student-program-link` 
				WHERE StudentID = '$uid' 
					AND `student-program-link`.Major IN ('2','3','9','10','25')
				OR StudentID = '$uid' 
					AND `student-program-link`.Minor IN ('2','3','9','10','25');";
			*/

				

			// BILL STUDENT IN CASE OF BOARDING
			if($weeks == "2"){
				$amount = '800';
			}else if($weeks == "3"){
				$amount = '900';
			}else if($weeks == "1"){
				$amount = '400';
			}else if($weeks == "4"){
				$amount = '1000';
			}

			$year = date('Y');
			$month = date('F');

			 
			$description = "Boarding fee $weeks weeks - DE $month $year";

			$this->doBill($uid, $amount, $description, $periodid);
			$this->smsOffer($userid, $deadline, $periodid, $amount);
			$locked = TRUE;
			
		} else if($item == "save"){
			$type = $this->core->cleanGet['type'];
			$number = $this->core->cleanGet['number'];
			$disabled = $this->core->cleanGet['disability'];
			$district = $this->core->cleanGet['district'];
			$province = $this->core->cleanGet['province'];

			$sql = "INSERT INTO `housingapplications` (`StudentID`, `RoomID`, `DateTime`, `Province`, `District`, `Disabled`) VALUES ('$uid', '$number', NOW(), '$province', '$district', '$disabled');";
			$run = $this->core->database->doInsertQuery($sql);
		}


		//echo'<div class="warningpopup">ONLY APPLY IF YOU ARE IN DISTANCE EDUCATION GROUP 2 - GROUP 2 REGISTRATION OPENED 24-12-2016. <br> Do not make any payments for accommodation unless you receive an SMS notification to do so.</div>';
		
		
		if($masters == TRUE){
			$sql = "SELECT *, COUNT(`housingapplications`.RoomID) as CFX, `rooms`.ID as RID FROM  `basic-information`, `hostel`, `rooms`
			LEFT JOIN `housingapplications` ON `housingapplications`.RoomID = `rooms`.ID 
			WHERE `rooms`.HostelID = `hostel`.ID
			AND `basic-information`.ID = $uid
			AND `hostel`.HostelName = 'CHIPEPO'
			GROUP BY `housingapplications`.RoomID, `rooms`.ID
			ORDER BY RoomCapacity ASC";

		} else {
			$sql = "SELECT *, COUNT(`housingapplications`.RoomID) as CFX, `rooms`.ID as RID FROM  `basic-information`, `hostel`, `rooms`
			LEFT JOIN `housingapplications` ON `housingapplications`.RoomID = `rooms`.ID 
			WHERE `rooms`.HostelID = `hostel`.ID
			AND `basic-information`.ID = $uid
			AND `hostel`.Type LIKE `basic-information`.Sex 
			GROUP BY `housingapplications`.RoomID, `rooms`.ID
			ORDER BY RoomCapacity ASC";
		}

		echo'<table id="results" class="table table-bordered table-striped table-hover">' .
			'<thead><tr>' .
			'<td><b>Hostel</b></td>' .
			'<td><b>Room number</b></td>' .
			'<td><b>Capacity</b></td>' .
			'<td><b>Applicants</b></td>' .
			'<td><b>Apply</b></td>' .
			'</tr></thead><tbody>';


		$SQX = "SELECT `Sex` FROM `basic-information` WHERE `ID` = $uid";
		$runxx = $this->core->database->doSelectQuery($SQX);
		while ($rowx = $runxx->fetch_assoc()) {
			$sex = $rowx["Sex"];
		}


		$run = $this->core->database->doSelectQuery($sql);

		$i = 0; $c = 1;
		while ($row = $run->fetch_assoc()) {

			$roomno = $row["RoomNumber"];
			if($group == "M" && $sex == "Male"){

				if($roomno % 2 == 0){
					// SKIP EVEN ROOMS
					continue;
				}
		
			} else if($group == "M" && $sex == "Female") {
				if($roomno % 2 != 0){
					// SKIP UNEVEN ROOMS
					continue;
				}
		
			}

			$counter = $row["RoomCapacity"]-$row["CFX"];
			if($counter<0){
				$counter = 0;
			}

			if($counter == 0){	continue; }
	
			if($locked == FALSE){
				$apply = '<a href="' . $this->core->conf['conf']['path'] . '/accommodation/apply/' . $row["RID"] . '">Apply now</a>';
			} else {
				$apply = 'Locked';
			}

			echo'<tr>' . 
				'<td>' . $row["HostelName"] . ' ('.$row["Type"].')</td>' .
				'<td><b>' . $row["RoomNumber"] . '</b></td>' .
				'<td><b>' . $counter . '</b> out of ' . $row["RoomCapacity"] . ' available </td>' .
				'<td><b>'.$row["CFX"].' applicants<b></td>' .
				'<td>' .
				$apply .
				'</td></tr>';
			$c++;
		}

		echo '</tbody></table></p>';
	}

	private function showApplications($item){

		$sql = "SELECT * FROM `rooms` LEFT JOIN `housingapplications` ON `rooms`.ID = `housingapplications`.RoomID LEFT JOIN `hostel` ON `rooms`.HostelID = `hostel`.ID 
		LEFT JOIN `basic-information` ON `housingapplications`.StudentID = `basic-information`.ID 
		WHERE`rooms`.ID = $item";

		$run = $this->core->database->doSelectQuery($sql);


		$c = 1;
		$tab = FALSE;

		while ($row = $run->fetch_assoc()) {
			$deadline = $row["Deadline"];
			if($deadline != "0000-00-00"){
				$ost = '<b>PAYMENT DEADLINE '. $deadline . '</b>';
			} else{
				$ost = "ASSIGN ACCOMMODATION ";
			}

			if(empty($row["StudentID"])){
				echo'<span class="errorpopup">No applications for this room</span>';

			} else{
				if($c == 1){
					echo'<div class="heading">'.$row["HostelName"].' room '.$row["RoomNumber"].'</div>';
					echo'<div class="heading">CAPACITY: '.$row["RoomCapacity"].'</div>';

					echo'<table id="results" class="table table-bordered table-striped table-hover">' .
					'<thead><tr>' .
					'<td><b>Student ID</b></td>' .
					'<td><b>Student Name</b></td>' .
					'<td><b>Hostel - Room</b></td>' .
					'<td><b>Province/District</b></td>' .
					'<td><b>Management</b></td>' .
					'</tr></thead><tbody>';

					$tab = TRUE;
				} 

				echo'<tr>' .
				'<td>'.$c.' - <b><a href="' . $this->core->conf['conf']['path'] . '/information/show/' . $row["StudentID"] . '">' . $row["StudentID"] . '</a></b></td>' .
				'<td><b>' . $row["FirstName"] . ' ' . $row["Surname"] . '</b></td>' .
				'<td><a href="' . $this->core->conf['conf']['path'] . '/accommodation/roomlist/' . $row["HostelID"] . '">' . $row["HostelName"] . '</a> - <a href="' . $this->core->conf['conf']['path'] . '/accommodation/room/' . $row["RoomID"] . '">' . $row["RoomNumber"] . '</a></td>' .
				'<td>' . $row["District"] . '</td>' .
				'<td>' .
				'<a href="' . $this->core->conf['conf']['path'] . '/accommodation/requests/approve/' . $row["StudentID"] . '"> APPROVE</a> / 
				 <a href="' . $this->core->conf['conf']['path'] . '/accommodation/requests/delete/' . $row["StudentID"] . '"> DELETE</a>' .
				'</td></tr>';
				$c++;
			}
		}

		if($tab == TRUE){
			echo '</tbody></table></p>';
		}

	}

	private function smsOffer($uid, $deadline, $period, $amount){

		$sql = "SELECT * FROM `rooms` LEFT JOIN `housingapplications` ON `rooms`.ID = `housingapplications`.RoomID LEFT JOIN `hostel` ON `rooms`.HostelID = `hostel`.ID 
		LEFT JOIN `basic-information` ON `housingapplications`.StudentID = `basic-information`.ID 
		WHERE `housingapplications`.StudentID = $uid";

		$run = $this->core->database->doSelectQuery($sql);

		while ($row = $run->fetch_assoc()) {
			include $this->core->conf['conf']['viewPath'] . "sms.view.php";
			$sms = new sms($this->core);
			$sms->buildView($this->core);

			$hostel =  $row["HostelName"];
			$room = $row["RoomNumber"];
			$roomid = $row["RoomID"];
			$phone = $row["MobilePhone"];

			$sms->directSms("26".$phone, "You are offered room: $hostel / $room. To use this offer pay $amount in ZANACO Billmuster");
			$this->core->throwSuccess("SMS sent with offer");

			$admin = $this->core->userID;
			// $rsql = "INSERT INTO `housing` (`ID`, `StudentID`, `RoomID`, `HousingStatus`, `CheckIn`, `AssignedBy`, `PeriodID`) VALUES (NULL, '$uid', '$roomid', '2', NOW(), '$admin', '$period');";
			// $this->core->database->doInsertQuery($rsql);
			// echo'<span class="successpopup">Room locked</span>';
		}
	}


	public function approveHousing($item){

		$uid = $this->core->subitem;
		$admin = $this->core->userID;

		$sql = "INSERT INTO `housing` SELECT NULL, `StudentID`, `RoomID`, '1', NOW(), '$admin', `PeriodID` FROM `housingapplications` WHERE `StudentID` = $uid;";

		$run = $this->core->database->doInsertQuery($sql);

		include $this->core->conf['conf']['viewPath'] . "sms.view.php";
		$sms = new sms($this->core);
		$sms->buildView($this->core);
		$sms->parseMessage(0, $uid, "Your room has been assigned, please show your confirmation slip to the Matron of your hostel.");

		$this->core->throwSuccess("Accommodation has been allocated. Student Notified. Please continue to print confirmation.");

		
		echo '<div style="border: solid 1px #ccc; padding:10px; width: 200px; text-align: center; width: 100%;">
		<b><a href="' . $this->core->conf['conf']['path'] . '/confirmation/print/'. $uid .'">Print Registration Confirmation Statement</a></b></div>';
		echo'<p><br></p>';

		return;

	}

	public function requestsAccommodation($item) {
		$uid = $this->core->userID;
		$this->viewMenu();

		if(!empty($item) && $item != "approve"){
			$this->showApplications($item);
			return;

		} else if($item == "approve"){
			$this->approveHousing($item);
		} else if($item == "delete"){
			$this->removeHousing($item);
		}
		
		$locked = FALSE; 

		$sql = "SELECT *,  `rooms`.ID as roomid, COUNT(`housingapplications`.StudentID) as COA 
			FROM `housingapplications`,  `basic-information`, `hostel`, `rooms`
			WHERE `basic-information`.ID = `housingapplications`.StudentID
			AND `housingapplications`.RoomID = `rooms`.ID
			AND `rooms`.HostelID = `hostel`.ID
			GROUP BY `rooms`.ID
			ORDER BY `hostel`.HostelName, `rooms`.RoomNumber ASC";


		echo'<table id="results" class="table table-bordered table-striped table-hover">' .
			'<thead><tr>' .
			'<td><b>Hostel</b></td>' .
			'<td><b>Room number</b></td>' .
			
			'<td><b>Capacity</b></td>' .
			'<td><b>Applicants</b></td>' .
			'<td><b>Actions</b></td>' .
			'</tr></thead><tbody>';


		$run = $this->core->database->doSelectQuery($sql);

		$i = 0; $c = 1;
		while ($row = $run->fetch_assoc()) {
			$roomid = $row["roomid"];

			$sqr = "SELECT COUNT(RoomID) as CID FROM `housingapplications` WHERE RoomID = '$roomid'";
			$runr = $this->core->database->doSelectQuery($sqr);
			while ($rowr = $runr->fetch_assoc()) {
				$ct = $rowr["CID"];

				$cr = $row["RoomCapacity"]-$ct;

				if($cr == 0){
					$counter = '<b>ROOM IS FULL</b>';
				} else {
					$counter = '</b>'.$cr.' out of ' . $row["RoomCapacity"] . ' available ';
				}
			}

			echo'<tr>' . 
				'<td>' . $row["HostelName"] . ' ('.$row["Type"].')</td>' .
				'<td><b>' . $row["RoomNumber"] . '</b></td>' .
				'<td><b> '. $counter .'</b></td>' .
				'<td><b>'.$row["COA"].' applicants<b></td>' .
				'<td><a href="' . $this->core->conf['conf']['path'] . '/accommodation/requests/'.$row["roomid"].'">View Applicants</a></td></tr>';
			$c++;
		}

		echo '</tbody></table></p>';
	}




	public function hostelAccommodation($item) {


		$this->viewMenu();
		$subitem = $this->core->subitem;

		if($item == "approve"){
			$name = $this->core->cleanGet['name'];
			$gender = $this->core->cleanGet['gender'];
			$sql = "INSERT INTO `hostel` (`ID`, `HostelName`, `TotalRooms`, `Type`, `Category`) VALUES (NULL, '$name', '0', '$gender', '0');";
			$run = $this->core->database->doInsertQuery($sql);
			$this->core->showAlert("The hostel has been added");
		}

		if($item == "add"){
			include $this->core->conf['conf']['formPath'] . "addhostel.form.php";
		} else if($item == "edit"){
			include $this->core->conf['conf']['formPath'] . "edithostel.form.php";
			return;
		} else if($item == "delete"){
			$sql = "DELETE FROM `hostel` WHERE ID = $subitem";
			$run = $this->core->database->doInsertQuery($sql);
			$this->core->showAlert("The hostel has been deleted");
		} else if($item == "save"){
			$name = $this->core->cleanGet['name'];
			$gender = $this->core->cleanGet['gender'];

			$sql = "INSERT INTO `hostel` (`ID`, `HostelName`, `TotalRooms`, `Type`, `Category`) VALUES (NULL, '$name', '0', '$gender', '1');";
			$run = $this->core->database->doInsertQuery($sql);
			$this->core->showAlert("The hostel has been added");
		}


		echo '<div class="toolbar">'.
		'<a href="' . $this->core->conf['conf']['path'] . '/accommodation/hostel/add">Add a hostel</a>'.
		'</div>';

		echo'<table id="results" class="table table-bordered table-striped table-hover">' .
			'<thead><tr>' .
			'<td><b>Hostel</b></td>'.
			'<td><b>Rooms</b></td>' .
			'<td><b>Bed spaces</b></td>' .
			'<td><b>Management</b></td>' .
			'</tr></thead><tbody>';
				

		$sql = "SELECT `hostel`.ID ,HostelName, COUNT(RoomCapacity) as count, SUM(RoomCapacity) as beds, Type
		FROM  `hostel`
		LEFT JOIN `rooms` ON  HostelID = `hostel`.ID 
		GROUP BY `hostel`.ID";
	

		$run = $this->core->database->doSelectQuery($sql);

		$i = 0; $c = 1;
		while ($row = $run->fetch_assoc()) {

			if ($i == 0) {
				$bgc = 'class="zebra"';
				$i++;
			} else {
				$bgc = '';
				$i--;
			}

			$rooms = $rooms + $row["count"];
			$beds = $beds + $row["beds"];
			
			echo'<tr class="total">' . 
				'<td>' . $row["ID"] . ' - <a href="' . $this->core->conf['conf']['path'] . '/accommodation/roomlist/' . $row["ID"] . '">' . $row["HostelName"] . ' </a>('.$row["Type"].')</a></td>' .
				'<td>' . $row["count"] . ' rooms</td>' .
				'<td>' . $row["beds"] . ' beds</td>' .
				'<td>' .
				'<a href="' . $this->core->conf['conf']['path'] . '/accommodation/hostel/edit/' . $row["ID"] . '"> <img src="' . $this->core->conf['conf']['path'] . '/templates/edurole/images/edi.png"> edit</a>' .
				'<a href="' . $this->core->conf['conf']['path'] . '/accommodation/hostel/delete/' . $row["ID"] . '"> <img src="' . $this->core->conf['conf']['path'] . '/templates/edurole/images/del.png"> delete</a>' .
				'</td></tr>';
			$c++;
		}

			echo'<tr ' . $bgc . '>' . 
				'<td>TOTAL</td>' .
				'<td>' . $rooms . ' rooms</td>' .
				'<td>' . $beds . ' beds</td>' .
				'<td>' .
				'</td></tr>';

		echo '</tbody></table></p>';
		
	}

	public function roomlistAccommodation($item){

		$this->viewMenu();

		echo '<div class="toolbar">'.
		'<a href="' . $this->core->conf['conf']['path'] . '/accommodation/rooms/add/'.$item.'">Add a room</a>'.
		'</div>';

		echo'<table id="results" class="table table-bordered table-striped table-hover">' .
			'<thead><tr>' .
			'<td><b>Room number</b></td>' .
			'<td><b>Hostel</b></td>' .
			'<td><b>Capacity</b></td>' .
			'<td><b>Occupancy</b></td>' .
			'<td><b>Management</b></td>' .
			'</tr></thead><tbody>';
		

		$sql = "SELECT  `hostel`.*, `rooms`.*, COUNT(`rooms`.ID) AS rc FROM `hostel`, `rooms` 
		LEFT JOIN `housing` ON `housing`.RoomID = `rooms`.ID 
		WHERE `rooms`.HostelID = `hostel`.ID 
		AND `hostel`.ID = $item
		GROUP BY `rooms`.ID 
		ORDER BY `rooms`.RoomNumber  * 1 ";

		$run = $this->core->database->doSelectQuery($sql);

		$i = 0; $c = 1;
		while ($row = $run->fetch_assoc()) {

			if ($i == 0) {
				$bgc = 'class="zebra"';
				$i++;
			} else {
				$bgc = '';
				$i--;
			}
			
			$error = "";
			if($row["rc"] > $row["RoomCapacity"]){
				$error = "<b>ROOM IS OVER CAPACITY</b>";
			} else {
				$error = $row["rc"] . ' out of ' . $row["RoomCapacity"] . ' bedspaces filled';
			}

			echo'<tr ' . $bgc . '>' . 
				'<td>'.$c.' - <b><a href="' . $this->core->conf['conf']['path'] . '/accommodation/room/' . $row["ID"] . '">' . $row["RoomNumber"] . '</a></b></td>' .
				'<td><a href="' . $this->core->conf['conf']['path'] . '/accommodation/hostel/' . $row["HostelID"] . '">' . $row["HostelName"] . ' </a>('.$row["Type"].')</td>' .
				'<td>' . $row["RoomType"] . ' (' . $row["RoomCapacity"] . ' beds)</td>' .
				'<td>'.$error.'</td>' .
				'<td>' .
				'<a href="' . $this->core->conf['conf']['path'] . '/accommodation/rooms/edit/' . $row["ID"] . '"> <img src="' . $this->core->conf['conf']['path'] . '/templates/edurole/images/edi.png"> edit</a>' .
				'<a href="' . $this->core->conf['conf']['path'] . '/accommodation/rooms/delete/' . $row["ID"] . '"> <img src="' . $this->core->conf['conf']['path'] . '/templates/edurole/images/del.png"> delete</a>' .
				'</td></tr>';
			$c++;
		}

		echo '</tbody></table></p>';

	}


	function editAccommodation($item) {
		$sql = "SELECT * FROM `accommodation`,`housing`,`rooms` WHERE `housing`.StudentID = '$item' AND `housing`.RoomID = `rooms`.ID AND `accommodation`.ID =  `rooms`.accommodationID";
	
		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_row()) {
			include $this->core->conf['conf']['formPath'] . "editaccommodation.form.php";
		}
	}
	
	function addAccommodation() {
		include $this->core->conf['conf']['formPath'] . "addaccommodation.form.php";
	}

	function deleteAccommodation($item) {
		$sql = 'DELETE FROM `accommodation` WHERE `ID` = "' . $item . '"';
		$run = $this->core->database->doInsertQuery($sql);

		$this->listAccomodation();
		$this->core->showAlert("The accommodation has been deleted");
	}

	function saveAccommodation() {
		$fullname = $this->core->cleanPost['fullname'];
		$shortname = $this->core->cleanPost['shortname'];

		$item = $this->core->item;
		if (isset($item)) {
			$sql = "UPDATE `edurole`.`study` SET `ParentID` = '$school', `IntakeStart` = '$startintake', `IntakeEnd` = '$endintake', `Delivery` = '$delivery', `IntakeMax` = '$maxintake', `Name` = '$fullname', `ShortName` = '$shortname', `Active` = '$active', `StudyType` = '$type', `TimeBlocks` = '$duration', `StudyIntensity` = '$intensity' WHERE `ID` = $item;";
		} else {
			$sql = "INSERT INTO `study` (`ID`, `ParentID`, `IntakeStart`, `IntakeEnd`, `Delivery`, `IntakeMax`, `Name`, `ShortName`, `Active`, `StudyType`, `TimeBlocks`, `StudyIntensity`) VALUES (NULL, '$school', '$startintake', '$endintake', '$delivery', '$maxintake', '$fullname', '$shortname', '$active', '$type', '$duration', '$intensity');";
		}

		$run = $this->core->database->doInsertQuery($sql);

		$this->listAccomodation();
	}

}
?>
