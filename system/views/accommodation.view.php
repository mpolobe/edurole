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
	
	public function manageAccommodation($item) {
		echo'<a href="' . $this->core->conf['conf']['path'] . 'studies/add">Add housing record</a></p><p>' .
			'<table width="768" height="" border="0" cellpadding="3" cellspacing="0">' .
			'<tr class="tableheader">' .
			'<td><b>Student</b></td>' .
			'<td><b>Accommodation</b></td>' .
			'<td><b>Room/Section</b></td>' .
			'<td><b>Management tools</b></td>' .
			'</tr>';

		if(isset($item)){
			$sql = "SELECT * FROM `accommodation`,`housing`,`basic-information` WHERE `housing`.AccommodationID = `accommodation`.AccommodationID AND `housing`.StudentID = '$item'";
		}else{
			$sql = "SELECT * FROM `accommodation`,`housing`,`basic-information` WHERE `housing`.AccommodationID = `accommodation`.AccommodationID AND `housing`.StudentID = `basic-information`.ID";
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
				'<td><b><a href="' . $this->core->conf['conf']['path'] . 'studies/show/' . $row[0] . '"> ' . $row[1] . '</a></b></td>' .
				'<td><a href="' . $this->core->conf['conf']['path'] . 'schools/show/' . $row[3] . '">' . $row[2] . '</a></td>' .
				'<td>' .
				'<a href="' . $this->core->conf['conf']['path'] . 'studies/edit/' . $row[0] . '"> <img src="templates/default/images/edi.png"> edit</a>' .
				'<a href="' . $this->core->conf['conf']['path'] . 'studies/delete/' . $row[0] . '" onclick="return confirm(\'Are you sure?\')"> <img src="templates/default/images/del.png"> delete </a>' .
				'</td></tr>';
		}

		echo '</table></p>';
	}

	public function showAccommodation($item) {

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