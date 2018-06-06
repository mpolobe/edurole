<?php
class housing {

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

	function editHousing($item) {
		$sql = "SELECT * FROM `accommodation`,`housing`,`rooms` WHERE `housing`.StudentID = '$item' AND `housing`.RoomID = `rooms`.ID AND `accommodation`.ID =  `rooms`.accommodationID";

		$run = $this->core->database->doSelectQuery($sql);

		while ($results = $run->fetch_assoc()) {

			$AccommodationID = $results['AccommodationID'];

			include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
			$select = new optionBuilder($this->core);
			$accommodation = $select->showAccommodation(null);

			include $this->core->conf['conf']['classPath'] . "users.inc.php";
			$users = new users($this->core);
			$student = $users->getStudent($item);

			include $this->core->conf['conf']['formPath'] . "edithousing.form.php";
		}
	}


	function addHousing() {
		include $this->core->conf['conf']['formPath'] . "addhousing.form.php";
	}

	function deleteHousing($item) {
		$sql = 'DELETE FROM `housing` WHERE `ID` = "' . $item . '"';
		$run = $this->core->database->doInsertQuery($sql);

		$this->core->redirect("housing", "manage", NULL);
	}

	function saveHousing() {
		$fullname = $this->core->cleanPost['fullname'];
		$shortname = $this->core->cleanPost['shortname'];

		$item = $this->core->item;

		if (isset($item)) {
			$sql = "UPDATE `edurole`.`study` SET `ParentID` = '$school', `IntakeStart` = '$startintake', `IntakeEnd` = '$endintake', `Delivery` = '$delivery', `IntakeMax` = '$maxintake', `Name` = '$fullname', `ShortName` = '$shortname', `Active` = '$active', `StudyType` = '$type', `TimeBlocks` = '$duration', `StudyIntensity` = '$intensity' WHERE `ID` = $item;";
		} else {
			$sql = "INSERT INTO `study` (`ID`, `ParentID`, `IntakeStart`, `IntakeEnd`, `Delivery`, `IntakeMax`, `Name`, `ShortName`, `Active`, `StudyType`, `TimeBlocks`, `StudyIntensity`) VALUES (NULL, '$school', '$startintake', '$endintake', '$delivery', '$maxintake', '$fullname', '$shortname', '$active', '$type', '$duration', '$intensity');";
		}

		$run = $this->core->database->doInsertQuery($sql);

		$this->core->redirect("housing", "manage", NULL);
	}

	public function manageHousing($item) {
		echo'<a href="' . $this->core->conf['conf']['path'] . 'studies/add">Add housing record</a></p><p>' .
			'<table width="768" height="" border="0" cellpadding="3" cellspacing="0">' .
			'<tr class="tableheader">' .
			'<td><b>Hostel</b></td>' .
			'<td><b>Room Number</b></td>' .
			'<td><b>Room Type</b></td>' .
			'<td><b>Student ID</b></td>' .
			'<td><b>Student Name</b></td>' .
			'<td><b>Management Tool</b></td>' .


			'</tr>';

		if(!empty($item)){
			$sql = "SELECT * FROM `accommodation`,`housing`,`basic-information` WHERE `housing`.AccommodationID = `accommodation`.AccommodationID AND `housing`.StudentID = '$item'";
		}else{
			$sql = "SELECT `housing`.ID,`hostels`.Name, `rooms`.RoomNumber,`rooms`.RoomType,`housing`.StudentID,`basic-information`.FirstName, `basic-information`.Surname 
				FROM `basic-information`,`hostels`,`housing`,`rooms`
				WHERE `housing`.RoomID = `rooms`.ID AND `rooms`.HostelID=`hostels`.ID AND `housing`.StudentID=`basic-information`.ID";
		}
		$run = $this->core->database->doSelectQuery($sql);

		$i = 0;
		while ($row = $run->fetch_row()) {
			if ($i == 0) {
				$bgc = 'class="zebra"';
				$i++;
			} else {
				$bgc = '';
				$i--;
			}

			echo'<tr ' . $bgc . '>' .
				'<td><b><a href="' . $this->core->conf['conf']['path'] .'housing/show/' . $row[0] . '"> ' . $row[1] . '</a></b></td>' .
				'<td><b>' . $row[2] . '<b></td>' .
				'<td><b>'.$row[3].' <b></td>' .
				'<td><b>'.$row[4].' <b></td>' .
				'<td><b>'.$row[5].' '.$row[6].' <b></td>' .
				'<td>' .
				'<a href="' . $this->core->conf['conf']['path'] . 'housing/edit/' . $row[0] . '"> <img src="templates/default/images/edi.png"> edit</a>' .
				'<a href="' . $this->core->conf['conf']['path'] . 'housing/delete/' . $row[0] . '" onclick="return confirm(\'Are you sure?\')"> <img src="templates/default/images/del.png"> delete </a>' .
				'</td></tr>';
		}

		echo '</table></p>';
	}

}
