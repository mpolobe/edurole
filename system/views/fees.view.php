<?php
class fees {

	public $core;
	public $view;
	public $item = NULL;

	public function configView() {
		$this->view->header = TRUE;
		$this->view->footer = TRUE;
		$this->view->menu = TRUE;
		$this->view->javascript = array('jquery.ui.datepicker');
		$this->view->css = array('jquery.ui');

		return $this->view;
	}

	public function buildView($core) {
		$this->core = $core;

		if ($this->core->action == "overview"  && $this->core->role > 104) {
			$this->listFees($this->core->item);
		} elseif ($this->core->action == "list" && $this->core->role > 104 || $this->core->action == "management" && $this->core->role > 104 ) {
			$this->listFees();
		} elseif ($this->core->action == "view" && $this->core->role > 104) {
			$this->showFee($this->core->item);
		} elseif ($this->core->action == "edit" && isset($this->core->item) && $this->core->role > 104) {
			$this->editFee($this->core->item);
		} elseif ($this->core->action == "add" && $this->core->role > 104) {
			$this->addFee();
		} elseif ($this->core->action == "save" && $this->core->role > 104) {
			$this->saveFee($this->core->item);
		} elseif ($this->core->action == "savepackage" && $this->core->role > 104) {
			$this->saveFeePackage($this->core->item);
		} elseif ($this->core->action == "delete" && isset($this->core->item) && $this->core->role > 104) {
			$this->deleteFee($this->core->item);
		}
	}
	
	function editFee($item) {
		$function = __FUNCTION__;
		$title = 'Edit Fee';
		$description = 'Edit the currently selected Fee';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);

		$sql = "SELECT * FROM `fees`, `fees-study-link` WHERE `fees`.ID = '$item' AND `fees`.ID = `fees-study-link`.FeeID";
		$run = $this->core->database->doSelectQuery($sql);

		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
		
		$select = new optionBuilder($this->core);
		$schools = $select->showSchools();

		while ($fetch = $run->fetch_row()) {
			$selectedstudies = $select->showStudies();
			$selectedstudies = $select->showStudies($fetch[8]);		//EDIOT

			include $this->core->conf['conf']['formPath'] . "editFee.form.php";
		}
	}

	function addFee() {
		$function = __FUNCTION__;
		$title = 'Add Fee Package';
		$description = 'Add a new Fee Package, afterwards you can add new fees to the package';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);

		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
		
		$select = new optionBuilder($this->core);
		$schools = $select->showSchools();
		$owner = $select->showUsers("100", null);

		include $this->core->conf['conf']['formPath'] . "addfee.form.php";
	}

	function deleteFeePackage($item) {
		$sql = 'DELETE FROM `fee-package` WHERE `ID` = "' . $item . '"';
		$run = $this->core->database->doInsertQuery($sql);

		$this->listFees();
		$this->core->showAlert("The Fee has been deleted");
	}

	function deleteFee($item) {
		$sql = 'DELETE FROM `fees` WHERE `ID` = "' . $item . '"';
		$run = $this->core->database->doInsertQuery($sql);

		$this->listFees();
		$this->core->showAlert("The Fee has been deleted");
	}

	function saveFeePackage($item) {
		$name = $this->core->cleanPost['name'];
		$description = $this->core->cleanPost['description'];
		$owner = $this->core->cleanPost['owner'];

		if (!empty($item)) {
			$sql = "UPDATE `fee-package` SET `Name` = '$name', `Description` = '$description', `Owner` = '$owner' WHERE `ID` = $item;";
			$run = $this->core->database->doInsertQuery($sql);
		} else {
			$sql = "INSERT INTO `fee-package` (`ID`, `Name`, `Description`, `Date`, `Owner`) VALUES (NULL, '$name', '$description', NOW(), '$owner');";
			$run = $this->core->database->doInsertQuery($sql);
		}

		$this->listFees();
	}

	function saveFee($item) {
		$status = $this->core->cleanPost['status'];
		$name = $this->core->cleanPost['name'];
		$description = $this->core->cleanPost['description'];

		$ammount = $this->core->cleanPost['ammount'];
		$year = $this->core->cleanPost['year'];
		$term = $this->core->cleanPost['term'];
		$mandatory = $this->core->cleanPost['mandatory'];

		$selected = $this->core->cleanPost['selected'];
		$nselected = $this->core->cleanPost['nselected'];
		
		if (!empty($nselected)) {
			foreach ($nselected as $nsel) {
				$sql = "INSERT INTO `fee-study-link` (`ID`, `FeeID`, `StudyID`) VALUES (NULL, '$item', '$nsel');";
				$run = $this->core->database->doInsertQuery($sql);
			}
		} elseif (!empty($selected)) {
			foreach ($selected as $sel) {
				$sql = "DELETE FROM `fee-study-link` WHERE `FeeID` = $item AND `StudyID` = $sel";
				$run = $this->core->database->doInsertQuery($sql);
			}
		} elseif (!empty($item)) {
			$sql = "UPDATE `edurole`.`fees` SET `FeeName` = '$name', `FeeAmmount` = '$ammount', `Year` = '$year', `Term` = '$term', `FeeDescription` = '$description', `Status` = '$status', `Mandatory` = '$percentage' WHERE `ID` = $item;";
			$run = $this->core->database->doInsertQuery($sql);
		} else {
			$sql = "INSERT INTO `fees` (`ID`, `FeeName`, `FeeAmmount`, `Year`, `Term`, `FeeDescription`, `Status`, `Mandatory`) VALUES (NULL, '$name', '$ammount', '$year', '$term', '$description', '$status', '$mandatory');";
			$run = $this->core->database->doInsertQuery($sql);

		}
		
		$this->listFees();
	}


	public function listFees($item=null) {
		$function = __FUNCTION__;
		$title = 'Overview of Fees';
		$description = 'Overview of all Fees';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);
		echo   '<div class="toolbar"><a href="' . $this->core->conf['conf']['path'] . '/fees/add">Add Fee</a></div>'. 
			'<table width="768" height="" border="0" cellpadding="3" cellspacing="0"><tr class="tableheader"><td><b>Fee Package</b></td>' .
			'<td><b>Total Ammount</b></td>' .
			'<td><b>Management tools</b></td>' .
			'</tr>';


		$sql = "SELECT `fee-package`.ID, `fee-package`.Name, SUM(`fees`.Amount) FROM `fee-package` LEFT JOIN `fees` ON `fees`.PackageID = `fee-package`.ID GROUP BY `fees`.PackageID";
		
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

			echo '<tr ' . $bgc . '>'.
			'<td><b><a href="' . $this->core->conf['conf']['path'] . '/fees/view/' . $row[0] . '"> ' . $row[1] . '</a></b></td>' .
			'<td>' . $row[2] . '</td>' .
			'<td>'.
			'<a href="' . $this->core->conf['conf']['path'] . '/fees/edit/' . $row[0] . '"> <img src="'.$this->core->fullTemplatePath.'/images/edi.png"> edit</a>'.
			'<a href="' . $this->core->conf['conf']['path'] . '/fees/delete/' . $row[0] . '" onclick="return confirm(\'Are you sure?\')"> <img src="'.$this->core->fullTemplatePath.'/images/del.png"> delete </a>'.
			'</td>'.
			'</tr>';
		}

		echo '</table>
		</p>';
	}

	function showFee($item) {
			
		$function = __FUNCTION__;
		$title = 'Fee information';
		$description = 'Information about the selected fee';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);
	
		$sql = "SELECT * FROM `fee-package` LEFT JOIN `fees` ON `fees`.PackageID = `fee-package`.ID ";
		
		$run = $this->core->database->doSelectQuery($sql);

		$i = 0;
		while ($fetch = $run->fetch_row()) {

			if ($i == 0) {
				$method = $fetch[4];
				echo '<table width="768" border="0" cellpadding="5" cellspacing="0">
					 <tr>
						<td width="205" height="28" bgcolor="#EEEEEE"><strong>Fee package</strong></td>
						<td width="500" bgcolor="#EEEEEE"></td>
						<td  bgcolor="#EEEEEE"></td>
					  </tr>
					  <tr>
						<td><strong>Fee package</strong></td>
						<td>' . $fetch[1] . '</td>
						<td></td>
					  </tr>
					  <tr>
						<td><strong>Package description</strong></td>
						<td>' . $fetch[2] . ' </td>
					  </tr>'; 

					echo'</table>';

				$i++;


				echo '<br /><table width="768" border="0" cellpadding="5" cellspacing="0">
					 <tr>
						<td width="205" height="28" bgcolor="#EEEEEE"><strong>Fee information</strong></td>
						<td width="200" bgcolor="#EEEEEE">Description</td>
						<td bgcolor="#EEEEEE">Cost</td>
					  </tr>

					 <tr>
						<td><strong>' . $fetch[7] . '</strong></td>
						<td>' . $fetch[8] . ' </td>
						<td><b>' . $fetch[9] . '</b></td>
					  </tr>';

					$total = $fetch[9] + $total;

			 }else{


					 echo' <tr>
						<td><strong>' . $fetch[7] . '</strong></td>
						<td>' . $fetch[8] . ' </td>
						<td><b>' . $fetch[9] . '</b></td>
					  </tr>';

					$total = $fetch[9] + $total;
			}


		}


		echo' <tr>
			<td width="205" height="28" bgcolor="#EEEEEE"><strong>Total fees in package</strong></td>
			<td width="200" bgcolor="#EEEEEE"></td>
			<td bgcolor="#EEEEEE"><strong>' . $total . '</strong></td>
		  </tr>
		</table>';


	}
}
?>
