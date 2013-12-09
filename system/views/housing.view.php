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

		if (empty($this->core->action) || $this->core->action == "manage" && $this->core->role > 100) {
			$this->viewHousing();
		} elseif ($this->core->action == "manage" && isset($this->core->item)) {
			$this->editHousing($this->core->item);
		} elseif ($this->core->action == "edit" && isset($this->core->item)) {
			$this->editHousing($this->core->item);
		}
	}
	
	function editAccommodation($item) {
		$function = __FUNCTION__;
		$title = 'Edit accommodation';
		$description = 'Edit the currently selected accommodation';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);
		$sql = "SELECT * FROM `accommodation`,`housing`,`rooms` WHERE `housing`.StudentID = '$item' AND `housing`.RoomID = `rooms`.ID AND `accommodation`.ID =  `rooms`.accommodationID";
	
		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_row()) {
			include $this->core->conf['conf']['formPath'] . "edithousing.form.php";
		}
	}
	
	function editRoom($item) {
		$function = __FUNCTION__;
		$title = 'Edit room';
		$description = 'Edit the currently selected room';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);
		$sql = "SELECT * FROM `accommodation`,`housing`,`rooms` WHERE `housing`.StudentID = '$item' AND `housing`.RoomID = `rooms`.ID AND `accommodation`.ID =  `rooms`.accommodationID";
	
		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_row()) {
			include $this->core->conf['conf']['formPath'] . "edithousing.form.php";
		}
	}
	
	function editHousing($item) {
		$function = __FUNCTION__;
		$title = 'Edit housing record';
		$description = 'Edit the currently selected housing record';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);
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
			var_dump($student);
	

			include $this->core->conf['conf']['formPath'] . "edithousing.form.php";
		}
	}

	function addAccommodation() {
		$function = __FUNCTION__;
		$title = 'Add new accommodation';
		$description = 'Add a new place of accommodation for students';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);

		include $this->core->conf['conf']['formPath'] . "addaccommodation.form.php";
	}
	
	function addRoom() {
		$function = __FUNCTION__;
		$title = 'Add new room';
		$description = 'Add a new room to an accommodation';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);

		include $this->core->conf['conf']['formPath'] . "addroom.form.php";
	}
	
	function addHousing() {
		$function = __FUNCTION__;
		$title = 'Add housing record';
		$description = 'Add a new housing record for the selected student';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);

		include $this->core->conf['conf']['formPath'] . "addhousing.form.php";
	}

	function deleteAccommodation($item) {
		$sql = 'DELETE FROM `accommodation` WHERE `ID` = "' . $item . '"';
		$run = $this->core->database->doInsertQuery($sql);

		$this->listAccomodation();
		$this->core->showAlert("The accommodation has been deleted");
	}
	
	function deleteRoom($item) {
		$sql = 'DELETE FROM `rooms` WHERE `ID` = "' . $item . '"';
		$run = $this->core->database->doInsertQuery($sql);
		
		$this->listAccomodation();
		$this->core->showAlert("The room has been deleted");
	}
	
	function deleteHousing($item) {
		$sql = 'DELETE FROM `housing` WHERE `ID` = "' . $item . '"';
		$run = $this->core->database->doInsertQuery($sql);
		
		$this->listHousing();
		$this->core->showAlert("The housing record has been deleted");
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

		$this->listHousing();
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

	public function listAccommodation($item) {
		$function = __FUNCTION__;
		$title = 'Accommodation';
		$description = 'Overview of all accommodation';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);

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
				'<td><b><a href="' . $this->core->conf['conf']['path'] . 'studies/view/' . $row[0] . '"> ' . $row[1] . '</a></b></td>' .
				'<td><a href="' . $this->core->conf['conf']['path'] . 'schools/view/' . $row[3] . '">' . $row[2] . '</a></td>' .
				'<td>' .
				'<a href="' . $this->core->conf['conf']['path'] . 'studies/edit/' . $row[0] . '"> <img src="templates/default/images/edi.png"> edit</a>' .
				'<a href="' . $this->core->conf['conf']['path'] . 'studies/delete/' . $row[0] . '" onclick="return confirm(\'Are you sure?\')"> <img src="templates/default/images/del.png"> delete </a>' .
				'</td></tr>';
		}

		echo '</table></p>';
	}
	
	public function listHousing($item) {
		$function = __FUNCTION__;
		$title = 'Housing records';
		$description = 'Overview of all housing records';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);

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
				'<td><b><a href="' . $this->core->conf['conf']['path'] . 'studies/view/' . $row[0] . '"> ' . $row[1] . '</a></b></td>' .
				'<td><a href="' . $this->core->conf['conf']['path'] . 'schools/view/' . $row[3] . '">' . $row[2] . '</a></td>' .
				'<td>' .
				'<a href="' . $this->core->conf['conf']['path'] . 'studies/edit/' . $row[0] . '"> <img src="templates/default/images/edi.png"> edit</a>' .
				'<a href="' . $this->core->conf['conf']['path'] . 'studies/delete/' . $row[0] . '" onclick="return confirm(\'Are you sure?\')"> <img src="templates/default/images/del.png"> delete </a>' .
				'</td></tr>';
		}

		echo '</table></p>';
	}

}