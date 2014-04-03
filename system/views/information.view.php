<?php
class information {

	public $core;
	public $view;
	public $limit;
	public $offset;
	public $pager = FALSE;

	public function configView() {
		$this->view->header = TRUE;
		$this->view->footer = TRUE;
		$this->view->menu = TRUE;
		$this->view->javascript = array('jquery.form-repeater');
		$this->view->css = array();

		return $this->view;
	}

	public function buildView($core) {
		$this->core = $core;

		$this->limit = 50;
		$this->offset = 0;

		include $this->core->conf['conf']['classPath'] . "users.inc.php";


		if(empty($this->core->item)){
			if(isset($this->core->cleanGet['uid'])){
				$this->core->item = $this->core->cleanGet['uid'];
			}
		}
		if(isset($this->core->cleanGet['offset'])){
			$this->offset = $this->core->cleanGet['offset'];
		}
		if(isset($this->core->cleanGet['limit'])){
			$this->limit = $this->core->cleanGet['limit'];
			$this->pager = TRUE;
		}
	} 

	public function studentsInformation($item) {
		$this->searchInformation($item);
	}

	public function saveInformation($item){
		$users = new users($this->core);
		$users->saveEdit($this->core->item, TRUE);
		header('Location: ' . $_SERVER['HTTP_REFERER']);
	}

	public function personalInformation($item){
		$userid = $this->core->userID;

		$sql = "SELECT * FROM  `basic-information` as bi, `access` as ac WHERE ac.`ID` = '" . $userid . "' AND ac.`ID` = bi.`ID`";

		$this->showInfoProfile($sql);
	}

	public function searchInformation($item) {
		if(isset($this->core->cleanGet['studies'])){
			$studies = $this->core->cleanGet['studies'];
		}
		if(isset($this->core->cleanGet['programmes'])){
			$programmes = $this->core->cleanGet['programmes'];
		}
		if(isset($this->core->cleanGet['search'])){
			$search = $this->core->cleanGet['search'];
		}
		if(isset($this->core->cleanGet['q'])){
			$q = $this->core->cleanGet['q'];
		}
		if(isset($this->core->cleanGet['studentfirstname'])){
			$firstName = $this->core->cleanGet['studentfirstname'];
		}
		if(isset($this->core->cleanGet['studentlastname'])){
			$lastName = $this->core->cleanGet['studentlastname'];
		}
		if(isset($this->core->cleanGet['listtype'])){
			$listType = $this->core->cleanGet['listtype'];
		}

		if (isset($lastName) || isset($firstName)) {
			$this->bynameInformation($firstName, $lastName, $listType);
		} else if ($this->core->action == "search" && isset($q) && $search == "study" || $this->core->action == "students" && isset($q) && $search == "study") {
			$this->bystudyInformation($q, $listType);
		} else if ($this->core->action == "search" && isset($q) && $search == "programme" || $this->core->action == "students" && isset($q) && $search == "programme") {
			$this->byprogramInformation($q, $listType);
		} else if ($this->core->action == "search" && isset($q) && $search == "courses" || $this->core->action == "students" && isset($q) && $search == "courses") {
			$this->bycourseInformation($q, $listType);
		} else if ($this->core->action == "search" && isset($item)) {
			$this->showInformation($item);
		}else{

			include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";

			$select = new optionBuilder($this->core);

			$study = $select->showStudies(null);
			$program = $select->showPrograms(null, null, null);
			$courses = $select->showCourses(null);

			if ($this->core->role >= 100) {
				include $this->core->conf['conf']['formPath'] . "searchform.form.php";
			} else {
				$this->core->throwError($this->core->translate("You do not have the authority to do system wide searches"));
			}
		}
	}

	public function showInformation($item) {
		if(empty($item)){
			$this->searchInformation();
		} else {
			$sql = "SELECT * FROM `basic-information` WHERE `ID` = '" . $item . "'";
			$this->showInfoProfile($sql);
		}
	}

	private function bynameInformation($firstName, $lastName, $listType = NULL) {
		if (empty($firstName)) {
			$firstName = "%";
		}
		if (empty($lastName)) {
			$lastName = "%";
		}

		$sql = "SELECT * FROM `basic-information` WHERE `Surname` LIKE '" . $lastName . "' AND `Firstname` LIKE '" . $firstName . "'";

		if ($listType == "profiles") {
			$this->showInfoProfile($sql);
		} elseif ($listType == "list") {
			$this->showInfoList($sql);
		}
	}

	private function bystudyInformation($study, $listType) {
		if ($study != "" && is_numeric($study)) {
			$sql = "SELECT * FROM `basic-information`, `student-study-link` WHERE `student-study-link`.StudentID = `basic-information`.ID AND StudyID = '" . $study . "'";
		}

		if ($listType == "profiles") {
			$this->showInfoProfile($sql);
		} elseif ($listType == "list") {
			$this->showInfoList($sql);
		}
	}

	private function byprogramInformation($program, $listType) {
		if ($program != "" && is_numeric($program)) {
			$sql = "SELECT * FROM `basic-information`, `nkrumah-student-program-link` as sp, `programmes` as p, `programmes-link` as pl 
				WHERE sp.`StudentID` = `basic-information`.ID 
				AND sp.`ProgrammeID` = pl.`ID`
				AND p.`ID` = pl.`Major`
				AND p.`ID` = '$program'
				OR  sp.`StudentID` = `basic-information`.ID 
				AND sp.`ProgrammeID` = pl.`ID`
				AND p.`ID` = pl.`Minor`
				AND p.`ID` = '$program'";
		}

		if ($listType == "profiles") {
			$this->showInfoProfile($sql);
		} elseif ($listType == "list") {
			$this->showInfoList($sql);
		}
	}

	private function showInfoProfile($sql) {

		$run = $this->core->database->doSelectQuery($sql);

		while ($row = $run->fetch_row()) {

			$results = TRUE;

			$firstname = $row[0];
			$middlename = $row[1];
			$surname = $row[2];
			$sex = $row[3];
			$uid = $row[4];
			$nrc = $row[5];
			$dob = $row[6];
			$pob = $row[7];
			$nationality = $row[8];

			$streetname = $row[9];
			$postalcode = $row[10];
			$town = $row[11];
			$country = $row[12];
			$homephone = $row[13];
			$mobilephone = $row[14];

			$disability = $row[15];
			$disabilitytype = $row[16];
			$email = $row[17];
			$maritalstatus = $row[18];

			if(isset($row[23])){
				$role = $row[23];
			} else {
				$role = "10";
			}

			$picid = substr($uid, 4);
			$picid = ltrim($picid, '0');

			echo '<div class="student">
			<div class="studentname"> ' . $firstname . ' ' . $middlename . ' ' . $surname . ' </div>';

			echo '<div class="profilepic">';

			if (file_exists("datastore/identities/pictures/picture-$picid.jpg")) {
				echo '<img width="100%" src="'.$this->core->conf['conf']['path'].'/datastore/identities/pictures/picture-' . $picid . '.jpg">';
			} else {
				echo '<img width="100%" src="'.$this->core->conf['conf']['path'].'/templates/default/images/noprofile.png">';
			}


			if ($this->core->role >= 100) {
				echo '<div style="margin-top: 1px; border-top: solid 1px #ccc; padding:10px;"><b><a href="' . $this->core->conf['conf']['path'] . '/information/edit/' . $uid . '">Edit user information</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px;"><b><a href="' . $this->core->conf['conf']['path'] . '/housing/edit/' . $uid . '">Edit housing information</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px;"><b><a href="' . $this->core->conf['conf']['path'] . '/grades/student/' . $uid . '">Show grades</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px;"><b><a href="' . $this->core->conf['conf']['path'] . '/payments/show/' . $uid . '">Show all payments</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px;"><b><a href="' . $this->core->conf['conf']['path'] . '/payments/balance/' . $uid . '">Show payment status</a></b></div>';
			} else {
				echo '<div style="margin-top: 1px; border-top: solid 1px #ccc; padding:10px;"><b><a href="' . $this->core->conf['conf']['path'] . '/information/edit/' . $uid . '">Edit user information</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px;"><b><a href="' . $this->core->conf['conf']['path'] . '/grades/student/">Show grades</a></b></div>';
				echo '<div style="border-top: solid 1px #ccc; padding:10px;"><b><a href="' . $this->core->conf['conf']['path'] . '/payments/balance/">Show payment status</a></b></div>';
			}

			echo '</div>
			<table width="400" height="63" border="0" cellpadding="0" cellspacing="0">
			  <tr>
			<td>Student Number</td>
			<td><b>' . $uid . '</b></td>
			  </tr>
			  <tr>
			<td width="200">Gender/Sex</td>
			<td><u>' . $sex . '</u></td>
	 		 </tr>
	
	 		 <tr>
			<td>NRC</td>
			<td>' . $nrc . '</td>
	 		 </tr>
	 		 <tr>
			<td>Date of Birth</td>
			<td>' . $dob . '</td>
	 		 </tr>
			  <tr>
			<td>Nationality</td>
			<td>' . $nationality . '</td>
			  </tr>
			  <tr>
			<td>Marital Status</td>
			<td>' . $maritalstatus . '</td>
	 		 </tr>';

			$sql = "SELECT * FROM `roles` WHERE `ID` LIKE '$role'";
			$run = $this->core->database->doSelectQuery($sql);

			while ($row = $run->fetch_row()) {

				echo '<tr>
			<td>Access Level</td>
			<td>' . $row[1] . '</td>
			</tr>';

			}

			echo '</table>';

			$sql = "SELECT * FROM `nkrumah-student-program-link` as sp, `programmes` as p, `programmes-link` as pl 
				WHERE sp.`StudentID` = '$uid' 
				AND sp.`ProgrammeID` = pl.`ID`
				AND p.`ID` = pl.`Major`
				OR sp.`StudentID` = '$uid' 
				AND sp.`ProgrammeID` = pl.`ID`
				AND p.`ID` = pl.`Minor`";

			$run = $this->core->database->doSelectQuery($sql);

			while ($row = $run->fetch_row()) {

				$name = $row[5];

				if (!isset($major)) {
					$major = $name;
					echo '<div class="segment">Student course information</div>
					<table width="500" height="" border="0" cellpadding="0" cellspacing="0">
					<tr>
					<td width="200">Major</td>
					<td width=""><b>' . $major . '</b></td>
					</tr>';
					} else {
						$minor = $name;
						echo '<tr>
					<td>Minor</td>
					<td width=""><b>' . $minor . '</b></td>
					</tr>
					</table>';
					unset($major);
					$student = TRUE;
				}
				
			}

			$sql = "SELECT * FROM `accommodation`,`housing`,`rooms` WHERE `housing`.StudentID = '$uid' AND `housing`.RoomID = `rooms`.ID AND `accommodation`.ID =  `rooms`.accommodationID";
			$run = $this->core->database->doSelectQuery($sql);

			while ($fetch = $run->fetch_assoc()) {

				$AccommodationName = $fetch['Name'];
				$RoomNumber = $fetch['RoomNumber'];
				$RoomType = $fetch['RoomType'];	
				
				echo '<div class="segment">Housing information</div>
				<table width="500" height="" border="0" cellpadding="0" cellspacing="0">
				<tr>
				<td width="200">Accommodation</td>
				<td width="">' . $AccommodationName . '</td>
				</tr>
				<tr>
				<td>Room</td>
				<td width="">' . $RoomNumber . ' (' . $RoomType . ')</td>
				</tr>
				</table>';

			}
			
			if (!isset($minor) && isset($student) == TRUE) {
				$minor = $name;
				echo '<tr>
				<td>Minor</td>
				<td width=""><b>' . $minor . '</b></td>
				</tr>
				</table>';
			}
			
			echo '<div class="segment">Contact information</div>
			<table width="400" height="" border="0" cellpadding="0" cellspacing="0">
			  <tr>
				<td width="200">Streetname</td>
				<td width="">' . $streetname . '</td>
			  </tr>';

			if ($postalcode != "") {
				echo '<tr>
				<td>Postal code</td>
				<td>' . $postalcode . '</td>
			 	</tr>';
			}

			if ($town != "") {
				echo '<tr>
				<td>Town</td>
				<td>' . $town . '</td>
			 	</tr>';
			}

			if ($country != "") {
				echo '<tr>
				<td>Country</td>
				<td>' . $country . '</td>
			  	</tr>';
			}

			echo '<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			</tr>';

			if ($homephone != "" && $homephone != "0") {
				echo '<tr>
				<td>Home Phone</td>
				<td>' . $homephone . '</td>
	 			</tr>';
			}

			if ($mobilephone != "" && $mobilephone != "0") {
				echo '<tr>
				<td>Mobile Phone</td>
				<td>' . $mobilephone . '</td>
				</tr>';
			}

			if ($email != "") {
				echo '<tr>
				<td>Private Email</td>
				<td><a href="mailto:' . $email . '">' . $email . '</td>
				</tr>';
			}
			echo'</table>';

			$sql = "SELECT * FROM `emergency-contact` WHERE `StudentID` = '" . $nrc . "'";
			$run = $this->core->database->doSelectQuery($sql);

			while ($fetch = $run->fetch_row()) {

				$fullname = $fetch[2];
				$relationship = $fetch[3];
				$phonenumber = $fetch[4];
				$street = $fetch[5];
				$town = $fetch[6];
				$postalcode = $fetch[7];

				echo '<div class="segment">Emergency information</div>
				<table width="500" height="" border="0" cellpadding="0" cellspacing="0">
				  <tr>
					<td width="200">Full Name</td>
					<td width="">' . $fullname . '</td>
				  </tr>
				  <tr>
					<td>Relationship</td>
					<td>' . $relationship . '</td>
				  </tr>';

				if ($phonenumber != "" && $phonenumber != "0") {
					echo '<tr>
					<td>Phonenumber</td>
					<td>' . $phonenumber . '</td>
					</tr>';
				}

				echo '<tr>
				<td>Street</td>
				<td>' . $street . '</td>
			  </tr>
			  <tr>
				<td>Town</td>
				<td>' . $town . '</td>
			  </tr>
			  <tr>
				<td>Postalcode</td>
				<td>' . $postalcode . '</td>
			  </tr>
			</table>';

			}

			$sql = "SELECT * FROM `education-background` WHERE `StudentID` = '" . $nrc . "'";
			$run = $this->core->database->doSelectQuery($sql);
			$n = 0;

			while ($row = $run->fetch_row()) {

				$name = $row[2];
				$type = $row[3];
				$institution = $row[4];
				$filename = $row[5];

				if ($n == 0) {
					echo '<div class="segment">Education history</div>';
					$n++;
				} else {
					echo '<div style="border-bottom: 1px solid #ccc; width:500px; margin-top: 15px; margin-bottom: 15px;" > </div>';
				}

				echo '<table width="500" height="" border="0" cellpadding="0" cellspacing="0">
			  <tr>
				<td width="200">Name of institution</td>
				<td width="">' . $institution . '</td>
			  </tr>
			  <tr>
				<td>Level of certificate</td>
				<td>' . $type . '</td>
			  </tr>
			  <tr>
				<td>Name of certificate</td>
				<td>' . $name . '</td>
			  </tr>';

				if ($filename != "") {
					echo '<tr>
					<td>Image of certificate</td>
					<td><a href="' . $this->core->conf['conf']['path'] . 'download/educationhistory/' . $filename . '"><b>View file</b></a></td>
			 		</tr>';
				}
				echo '</table>';
			}
			echo "</div>";
		}

		if ($results != TRUE && $this->core->action != "personal") {
			$this->core->throwError('Your search did not return any results');
		} else if($results != TRUE) {
                        $this->core->throwSuccess($this->core->translate("Please take the time to enter your profile information first,  you can do this <a href='". $this->core->conf['conf']['path'] ."/information/edit/personal'>here</a>."));
		}
	}

	private function showInfoList($sql) {

		$sql = $sql . " LIMIT ". $this->limit ." OFFSET ". $this->offset;
		$run = $this->core->database->doSelectQuery($sql);

		if($this->core->pager == FALSE){
			echo '<table width="768" height="" border="0" cellpadding="5" cellspacing="0">
			<tr>
			<td bgcolor="#EEEEEE"></td>
			<td bgcolor="#EEEEEE"><b> Student Name</b></td>
			<td bgcolor="#EEEEEE"><b> Student ID</b></td>
			<td bgcolor="#EEEEEE"><b> National ID</b></td>
			<td bgcolor="#EEEEEE"><b> Date of Birth</b></td>
			<td bgcolor="#EEEEEE"><b> Status</b></td>
			</tr>
			</table>';
		}
	
		while ($row = $run->fetch_row()) {

			$results = TRUE;
			$firstname = $row[0];
			$middlename = $row[1];
			$surname = $row[2];
			$sex = $row[3];
			$uid = $row[4];
			$nrc = $row[5];
			$dob = $row[6];
			$studentstatus = $row[20];

			echo '<div class="resultrow">
			<div style="width: 20px; float:left;"><img src="'.$this->core->fullTemplatePath.'/images/bullet_user.png"></div>
			<div style="width: 205px; float:left;"><a href="' . $this->core->conf['conf']['path'] . '/information/show/' . $uid . '"><b>' . $firstname . ' ' . $middlename . ' ' . $surname . '</b></a></div>
			<div style="width: 140px; float:left;"><i>' . $uid . '</i></div>
			<div style="width: 145px; float:left;">' . $nrc . '</div>
			<div style="width: 165px; float:left;">' . $dob . '</div>
			<div style="width: 90px; float:left;">' . $studentstatus . '</div>
			</div>';

		}

		if($this->core->pager == FALSE){
			if ($results != TRUE) {
				$this->core->throwError('Your search did not return any results');
			}
		}
	}

	public function editInformation($item) {
		
		if($item == "personal"){
			$item = $this->core->userID;
		}

		$sql = "SELECT * FROM  `basic-information` as bi 
		LEFT JOIN `access` as ac ON ac.`ID` = '" . $item . "' 
		WHERE bi.`ID` = '" . $item . "'";

		$run = $this->core->database->doSelectQuery($sql);
 
		while ($row = $run->fetch_row()) {
			$id = $row[4];
			$NID = $row[5];
			$firstname = $row[0];
			$middlename = $row[1];
			$surname = $row[2];
			$gender = $row[3];
			$dob = $row[6];
			$nationality = $row[8];
			$street = $row[9];
			$postal = $row[10];
			$town = $row[11];
			$country = $row[12];
			$homephone = $row[13];
			$celphone = $row[14];
			$disability = $row[15];
			$email = $row[16];
			$relation = $row[18];
			$status = $row[19];
			$role = $row[21];
		}

		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
		$select = new optionBuilder($this->core);
		$select = $select->showRoles($role);

		include $this->core->conf['conf']['formPath'] . "edituser.form.php";

	}
}

?>
