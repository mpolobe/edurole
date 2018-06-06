<?php
class periods {

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

	public function editPeriods($item) {
		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
		$sql = "SELECT * FROM `periods` WHERE `periods`.ID = $item";

		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_assoc()) {
			$psdate = $fetch['PeriodStartDate'];
			$pedate = $fetch['PeriodEndDate'];
			$csdate = $fetch['CourseRegStartDate'];
			$cedate = $fetch['CourseRegEndDate'];
			$delivery = $fetch['Delivery'];
			$name = $fetch['Name'];

			include $this->core->conf['conf']['formPath'] . "period.form.php";
		}

	}

	public function addPeriods() {
		include $this->core->conf['conf']['formPath'] . "period.form.php";
	}

	public function deletePeriods($item) {
		$sql = 'DELETE FROM `periods`  WHERE `ID` = "' . $item . '"';
		$run = $this->core->database->doInsertQuery($sql);

		$this->core->redirect("periods", "manage", NULL);
	}

	public function savePeriods($item) {

		$name = $this->core->cleanPost['name'];
		$year = $this->core->cleanPost['year'];
		$semester = $this->core->cleanPost['term'];
		$startdate = $this->core->cleanPost['start'];
		$enddate = $this->core->cleanPost['end'];
		$cstartdate = $this->core->cleanPost['cstart'];
		$cenddate = $this->core->cleanPost['cend'];
		$delivery = $this->core->cleanPost['delivery'];

		if (!empty($item)) {
			$sql = "UPDATE `periods` 
			SET `Name` = '$name', 
			`PeriodStartDate` = '$startdate', 
			`PeriodEndDate` = '$enddate', 
			`CourseRegStartDate` = '$cstartdate',
			`CourseRegEndDate` = '$cenddate',
			`Delivery` = '$delivery'
			WHERE `periods`.`ID` = $item;";
			$run = $this->core->database->doInsertQuery($sql);
		} else {
			$sql = "INSERT INTO `periods` (`ID`, `PeriodStartDate`, `PeriodEndDate`, `Year`, `Semester`, `Name`, `Delivery`, `CourseRegStartDate`, `CourseRegEndDate`) 
				VALUES (NULL, '$startdate', '$enddate', '$year', '$semester', '$name', '$delivery', '$cstartdate', '$cenddate');";
			$run = $this->core->database->doInsertQuery($sql);
		}

		$this->core->redirect("periods", "manage", NULL);
	}

	public function managePeriods($item = NULL) {

		$sql = "SELECT * FROM `periods` ORDER BY `periods`.PeriodStartDate ASC";
		$run = $this->core->database->doSelectQuery($sql);

		echo '<div class="toolbar"><a href="' . $this->core->conf['conf']['path'] . '/periods/add">Add Period</a></div>';


		echo '<table width="100%" height="" border="0" cellpadding="3" cellspacing="0">' .
		'<tr class="heading">' .
			'<td><b>Period name</b></td>' .
			'<td><b>Start Date</b></td>' .
			'<td><b>End Date</b></td>' .
			'<td><b>Year</b></td>' .
			'<td><b>Quarter</b></td>' .
			'<td><b>Delivery</b></td>' .
			'<td><b>Registered</b></td>' .
			'<td><b>Management</b></td>' .
		'</tr>';

		$count = 0;

		$date = date("Y-m-d");
		$pastcomplete = FALSE;
		$currentcomplete = FALSE;
		$futurecomplete = FALSE;

		while ($fetch = $run->fetch_assoc()) {

				$count++;

				$startdate = $fetch['PeriodStartDate'];
				$enddate = $fetch['PeriodEndDate'];
				$name = $fetch['Name'];
				$id = $fetch['ID'];
				$year = $fetch['Year'];
				$semester = $fetch['Semester'];
				$mode = $fetch['Delivery'];

				if($startdate < $date && $pastcomplete == FALSE){
					echo '<tr>
						<td colspan="8" class="heading">PAST PERIODS</td>
					</tr>';
					$class = "past";
					$pastcomplete = TRUE;
				} else 	if($startdate <= $date && $enddate >= $date && $currentcomplete == FALSE){
					echo '<tr>
						<td colspan="8" class="heading">CURRENT PERIODS</td>
					</tr>';
					$class = "currentc";
					$currentcomplete = TRUE;
				} else if($startdate > $date && $enddate > $date && $futurecomplete == FALSE){
					echo '<tr>
						<td colspan="8" class="heading">FUTURE PERIODS</td>
					</tr>';
					$class = "future";
					$futurecomplete = TRUE;
				} 

				// NUMBER OF REGISTERED STUDENTS FOR THIS PERIOD
				$sql = "SELECT COUNT(StudentID) as count FROM `course-electives` WHERE `PeriodID` = '$id' AND `EnrolmentDate` > '2017-04-10' GROUP BY `StudentID`";
				$runx = $this->core->database->doSelectQuery($sql);
				$count = 0;
				$count = $runx->num_rows;

				echo '<tr class="'.$class.'">
					<td><b><a href="' . $this->core->conf['conf']['path'] . '/periods/show/' . $id . '"> ' . $name . '</a></b></td>

					<td> ' . $startdate . ' </td>
					<td> ' . $enddate . ' </td>
					<td> ' . $year . ' </td>
					<td> ' . $semester . ' </td>
					<td> ' . $mode . ' </td>
					<td> ' . $count . ' students </td>
					<td>
					<a href="' . $this->core->conf['conf']['path'] . '/periods/edit/' . $id . '"> <img src="'. $this->core->fullTemplatePath .'/images/edi.png"> </a>
					<a href="' . $this->core->conf['conf']['path'] . '/periods/delete/' . $id . '" onclick="return confirm(\'Are you sure?\')"> <img src="'. $this->core->fullTemplatePath .'/images/del.png"> </a>
					</td>
				</tr>';

			
		}

		echo '</table>';

	}


	public function showPeriods($item) {

		echo '<div class="heading">Options</div>
			<div class="toolbar">
				<a href="' . $this->core->conf['conf']['path'] . '/register/retire/'.$item.'">Retire courses registered for this period</a>
				<a href="' . $this->core->conf['conf']['path'] . '/accommodation/clear/'.$item.'">Clear all rooms</a>
			</div>
			<div class="toolbar">
				<a href="' . $this->core->conf['conf']['path'] . '/periods/delete/'.$item.'">Delete this period</a>
				<a href="' . $this->core->conf['conf']['path'] . '/periods/edit/'.$item.'">Edit this period</a>
				<a href="' . $this->core->conf['conf']['path'] . '/approval/manage/'.$item.'">Registered Students</a>
				<a href="' . $this->core->conf['conf']['path'] . '/billing/all/'.$item.'">Bill Students</a>
			</div>';

		$sql = "SELECT * FROM `periods`	WHERE `periods`.ID = '$item'";
		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_assoc()) {

			$startdate = $fetch['PeriodStartDate'];
			$enddate = $fetch['PeriodEndDate'];

			$cstart = $fetch['CourseRegStartDate'];
			$cend = $fetch['CourseRegEndDate'];

			$name = $fetch['Name'];
			$id = $fetch['ID'];
			$year = $fetch['Year'];
			$semester = $fetch['Semester'];
			$delivery = $fetch['Delivery'];

			echo '<div class="heading">Details</div><h2>'.$name.'</h2>';

			echo '<p><div class=""><div class="label">Period Start/End date: </div> '.$startdate.'  to  '.$enddate.'</div></p>';
			echo '<p><div class=""><div class="label">Course Registration dates: </div> '.$cstart.'  to  '.$cend.'</div></p>';
			echo '<p><div class=""><div class="label">Year: </div> '.$year.'</div></p>';
			echo '<p><div class=""><div class="label">Delivery: </div> '.$delivery.'</div></p>';

			// NUMBER OF REGISTERED STUDENTS FOR THIS PERIOD
			$sql = "SELECT COUNT(StudentID) as count FROM `course-electives` WHERE `PeriodID` = '$id' AND `EnrolmentDate` > '2017-04-10' GROUP BY `StudentID`";
			$runx = $this->core->database->doSelectQuery($sql);
			$count = $runx->num_rows;


			// NUMBER OF REGISTERED STUDENTS FOR THIS PERIOD
			$sql = "SELECT COUNT(Sex) as count, `Sex` FROM `course-electives`, `basic-information` WHERE `PeriodID` = '$id' AND `course-electives`.StudentID = `basic-information`.ID  AND `EnrolmentDate` > '2017-04-10' GROUP BY `StudentID`, `Sex`";
			$runx = $this->core->database->doSelectQuery($sql);

			echo'<div class="heading">Period Statistics</div>';
			echo '<p><div class=""><div class="label">Total Registered Students: </div> <b>'.$count.'</b></div></p>';

			while ($fetch = $runx->fetch_assoc()) {
				$gender = $fetch['count'];
				$sex = $fetch['Sex'];
				if($sex == "Male"){
					$male++;
				}elseif($sex == "Female"){
					$female++;
				}
			}

			echo '<p><div class=""><div class="label">Total Male Students: </div> <b>'.$male.'</b></div></p>';
			echo '<p><div class=""><div class="label">Total Female Students: </div> <b>'.$female.'</b></div></p>';

		}
	}
}

?>
