<?php
class report {

	public $core;
	public $view;
	public $item = NULL;

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



	function intakeReport($item) {
		$year = $_GET['uid'];
		$time = $_GET['time'];
		$start = $_GET['start'];
		$end = $_GET['end'];

		$sql = "SELECT LEFT(ID,4), COUNT(LEFT(ID,4)), StudyType, Sex 
			FROM `basic-information` 
			WHERE LEFT(ID,4) > 2008 AND  LEFT(ID,4) < 2020 
			GROUP BY LEFT(ID,4), StudyType, Sex ";

		$run = $this->core->database->doSelectQuery($sql);


		while ($fetch = $run->fetch_array()){
			$year = $fetch[0];
			$count = $fetch[1];
			$delivery = $fetch[2];
			$sex = $fetch[3];

			if($sex == 'Male'){
				$data[$year][$delivery]['Male'] = $count;
			} else if($sex == 'Female'){
				$data[$year][$delivery]['Female'] = $count;
			}
		}



		// GET TOTALS FOR FULLTIME
		echo '<h2>Full-Time Students per Intake - UNIVERSITY LEVEL</h2>';
		$mode = 'Fulltime';	

		echo'<table>
			<tr class="heading">
				<td>YEAR</td>
				<td>DELIVERY METHOD</td>
				<td>MALE</td>
				<td>FEMALE</td>
				<td>TOTAL</td>
				<td>GRADUATED</td>
				<td>ACTIVE </td>
				<td>REGISTERED </td>
			</tr>';

		foreach($data as $year => $delivery){
			$male = $delivery[$mode]['Male'];
			$female = $delivery[$mode]['Female'];
			$total = $male + $female;

			$subsql = "SELECT COUNT(LEFT(ID,4)) as Current 
				FROM `basic-information` WHERE `ID` LIKE  '$year%' 
				AND `StudyType` = '$mode' 
				AND `Status` IN ('Requesting', 'Approved')
				UNION
				SELECT COUNT(LEFT(ID,4)) as Graduated 
				FROM `basic-information` WHERE `ID` LIKE  '$year%' 
				AND `StudyType` = '$mode' 
				AND `Status` IN ('Graduated')
				UNION
				SELECT COUNT(DISTINCT `basic-information`.ID) as Registered 
				FROM `basic-information`, `course-electives` 
				WHERE `basic-information`.`ID` LIKE  '$year%' 
				AND `basic-information`.ID = `course-electives`.StudentID
				AND `course-electives`.Approved = 1
				AND `StudyType` = '$mode' 
				AND `Status` IN ('Requesting', 'Approved')";

			$subrun = $this->core->database->doSelectQuery($subsql); 
			$o=0;

			$registered = 0; 
			$current = 0;
			$graduated = 0;
			while ($subfetch = $subrun->fetch_array()){
				if($o == 0){
					$current = $subfetch[0];
				}else if($o == 1){
					$graduated = $subfetch[0];
				} else if($o == 2){
					$registered = $subfetch[0];
				}
				$o++;
			}
			if(empty($registered)){
				$registered = $current;
			}

			echo'<tr>
				<td><b>'.$year.'</b></td>
				<td><b>'.$mode.'</b></td>
				<td>'.$male.'</td>
				<td>'.$female.'</td>
				<td>'.$total.'</td>
				<td>'.$graduated.'</td>
				<td>'.$current.'</td>
				<td>'.$registered.'</td>
			</tr>';

			$tfemale = $tfemale + $female;
			$tmale = $tmale + $male;
			$ttotal = $ttotal + $total;
			$tcur = $tcur + $current;
			$treg = $treg + $registered;
			$tgraduated = $tgraduated + $graduated;

			$total = 0;
		}



		echo'<tr class="heading">
			<td>TOTAL</td>
			<td>'.$mode.'</td>
			<td>'.$tmale.'</td>
			<td>'.$tfemale.'</td>
			<td>'.$ttotal.'</td>
			<td>'.$tgraduated .'</td>
			<td>'.$tcur.'</td>
			<td>'.$treg.'</td>
		</tr>';

		echo'</table>';


			$tfemale = 0;
			$tmale = 0;
			$ttotal = 0;
			$treg = 0;
			$tcur = 0;
			$tgraduated = 0;


		// GET TOTALS FOR DE
		echo '<hr><br /> <h2>Distance Students per Intake - UNIVERSITY LEVEL</h2>';
		$mode = 'Distance';

		echo'<table>
			<tr class="heading">
				<td>YEAR</td>
				<td>DELIVERY METHOD</td>
				<td>MALE</td>
				<td>FEMALE</td>
				<td>TOTAL</td>
				<td>GRADUATED</td>
				<td>ACTIVE </td>
				<td>REGISTERED </td>
			</tr>';

		foreach($data as $year => $delivery){
			$male = $delivery[$mode]['Male'];
			$female = $delivery[$mode]['Female'];
			$total = $male + $female;

			$subsql = "SELECT COUNT(LEFT(ID,4)) as Current 
				FROM `basic-information` WHERE `ID` LIKE  '$year%' 
				AND `StudyType` = '$mode' 
				AND `Status` IN ('Requesting', 'Approved')
				UNION
				SELECT COUNT(LEFT(ID,4)) as Graduated 
				FROM `basic-information` WHERE `ID` LIKE  '$year%' 
				AND `StudyType` = '$mode' 
				AND `Status` IN ('Graduated')
				UNION
				SELECT COUNT(DISTINCT `basic-information`.ID) as Registered 
				FROM `basic-information`, `course-electives` 
				WHERE `basic-information`.`ID` LIKE  '$year%' 
				AND `basic-information`.ID = `course-electives`.StudentID
				AND `course-electives`.Approved = 1
				AND `StudyType` = '$mode' 
				AND `Status` IN ('Requesting', 'Approved')";

			$subrun = $this->core->database->doSelectQuery($subsql); 
			$o=0;


			$registered = 0; 
			$current = 0;
			$graduated = 0;
			while ($subfetch = $subrun->fetch_array()){
				if($o == 0){
					$current = $subfetch[0];
				}else if($o == 1){
					$graduated = $subfetch[0];
				} else if($o == 2){
					$registered = $subfetch[0];
				}
				$o++;
			}

			echo'<tr>
				<td><b>'.$year.'</b></td>
				<td><b>'.$mode.'</b></td>
				<td>'.$male.'</td>
				<td>'.$female.'</td>
				<td>'.$total.'</td>
				<td>'.$graduated.'</td>
				<td>'.$current.'</td>
				<td>'.$registered.'</td>
			</tr>';


			$tfemale = $tfemale + $female;
			$tmale = $tmale + $male;
			$ttotal = $ttotal + $total;
			$tcur = $tcur + $current;
			$treg = $treg + $registered;
			$tgraduated = $tgraduated + $graduated;

			$total = 0;
		}



		echo'<tr class="heading">
			<td>TOTAL</td>
			<td>'.$mode.'</td>
			<td>'.$tmale.'</td>
			<td>'.$tfemale.'</td>
			<td>'.$ttotal.'</td>
			<td>'.$tgraduated .'</td>
			<td>'.$tcur.'</td>
			<td>'.$treg.'</td>
		</tr>';


		echo'</table>';







		echo'<hr>'; 

		echo '<br /> <h2>Students by SCHOOL</h2>';

		$sql = "SELECT COUNT(`basic-information`.ID), StudyType, `schools`.`Name` , `Sex`
			FROM `basic-information`, `student-program-link`, `programmes`, `schools`, `programme-school-link`
			WHERE `programmes`.ID = `student-program-link`.Major 
			AND `basic-information`.ID = `student-program-link`.StudentID 
			AND `programme-school-link`.ProgrammeID = `programmes`.ID 
			AND `schools`.ID = `programme-school-link`.SchoolID 
			AND LEFT(`basic-information`.ID,4) > 2008 
			AND `StudyType` IN ('Fulltime', 'Distance', 'Partime')
			AND `Sex` IN ('Male', 'Female')
			AND `schools`.`Name` != 'Education'
			AND LEFT(`basic-information`.ID,4) < 2020 
			GROUP BY  `StudyType`, `SchoolID`, `Sex`";

		$run = $this->core->database->doSelectQuery($sql); 
		echo'<table>
			<tr class="heading">
				<td>DELIVERY METHOD</td>
				<td>SCHOOL</td>
				<td>MALE</td>
				<td>FEMALE</td>
				<td>TOTAL</td>
			</tr>';

		while ($fetch = $run->fetch_array()){
			$count = $fetch[0];
			$delivery = $fetch[1];
			$school = $fetch[2];
			$delivery = $fetch[1];
			$school = $fetch[2];
			$sex = $fetch[3];

			if($sex == 'Female'){
				$female = $fetch[0];
				$count = $male+$female;

				echo'<tr>
					<td>'.$delivery.'</td>
					<td>'.$school.'</td>
					<td>'.$male.'</td>
					<td>'.$female.'</td>
					<td>'.$count.'</td>
				</tr>';
			} else {
				$male = $fetch[0];
			}
		}
		echo'</table>';





		echo'<hr>'; 

		echo '<br /> <h2>Students by DELIVERY MODE AND PROGRAM</h2>';
		$sql = "SELECT LEFT(ID,4), COUNT(LEFT(ID,4)), StudyType FROM `basic-information` WHERE LEFT(ID,4) > 2008 AND  LEFT(ID,4) < 2020 GROUP BY LEFT(ID,4), StudyType ";

		$sql = "SELECT LEFT(`basic-information`.ID,4), COUNT(LEFT(`basic-information`.ID,4)), StudyType, `ProgramName`, `schools`.`Name` 
			FROM `basic-information`, `student-program-link`, `programmes`, `schools`, `programme-school-link` 
			WHERE `programmes`.ID = `student-program-link`.Major 
			AND `basic-information`.ID = `student-program-link`.StudentID 
			AND `programme-school-link`.ProgrammeID = `programmes`.ID 
			AND `StudyType` IN ('Fulltime', 'Distance', 'Partime')
			AND `schools`.ID = `programme-school-link`.SchoolID 
			AND `ProgramName` != 'Education'
			AND LEFT(`basic-information`.ID,4) > 2008 
			AND LEFT(`basic-information`.ID,4) < 2020 
			GROUP BY `StudyType`, `programmes`.ID";

		$run = $this->core->database->doSelectQuery($sql); 
		echo'<table>
			<tr class="heading">
				<td>DELIVERY METHOD</td>
				<td>PROGRAM</td>
				<td>TOTAL</td>
			</tr>';

		while ($fetch = $run->fetch_array()){
			$year = $fetch[0];
			$count = $fetch[1];
			$delivery = $fetch[2];
			$program = $fetch[3];
			$school = $fetch[4];

			echo'<tr>
				<td>'.$delivery.'</td>
				<td>'.$program.'</td>
				<td>'.$count.'</td>
			</tr>';
		}
		echo'</table>';








		echo'<hr>'; 

		echo '<br /> <h2>Students by YEAR AND PROGRAM</h2>';
		$sql = "SELECT LEFT(ID,4), COUNT(LEFT(ID,4)), StudyType FROM `basic-information` WHERE LEFT(ID,4) > 2008 AND  LEFT(ID,4) < 2020 GROUP BY LEFT(ID,4), StudyType ";

		$sql = "SELECT LEFT(`basic-information`.ID,4), COUNT(LEFT(`basic-information`.ID,4)), StudyType, `ProgramName`, `schools`.`Name` 
			FROM `basic-information`, `student-program-link`, `programmes`, `schools`, `programme-school-link` 
			WHERE `programmes`.ID = `student-program-link`.Major 
			AND `basic-information`.ID = `student-program-link`.StudentID 
			AND `programme-school-link`.ProgrammeID = `programmes`.ID 
			AND `StudyType` IN ('Fulltime', 'Distance', 'Partime')
			AND `ProgramName` != 'Education'
			AND `schools`.ID = `programme-school-link`.SchoolID 
			AND LEFT(`basic-information`.ID,4) > 2008 
			AND LEFT(`basic-information`.ID,4) < 2020 GROUP BY LEFT(`basic-information`.ID,4), `StudyType`, `programmes`.ID";

		$run = $this->core->database->doSelectQuery($sql); 
		echo'<table>
			<tr class="heading">
				<td>YEAR</td>
				<td>DELIVERY METHOD</td>
				<td>PROGRAM</td>
				<td>TOTAL</td>
			</tr>';

		while ($fetch = $run->fetch_array()){
			$year = $fetch[0];
			$count = $fetch[1];
			$delivery = $fetch[2];
			$program = $fetch[3];
			$school = $fetch[4];

			echo'<tr>
				<td>'.$year.'</td>
				<td>'.$delivery.'</td>
				<td>'.$program.'</td>
				<td>'.$count.'</td>
			</tr>';
		}
		echo'</table>';
	}



	function overviewReport($item) {
		$year = $_GET['uid'];
		$time = $_GET['time'];
		$start = $_GET['start'];
		$end = $_GET['end'];

		$sql = "SELECT COUNT( DISTINCT  `grades`.StudentNo) FROM `grades`, `basic-information`
			WHERE `grades`.AcademicYear LIKE '$year' AND `basic-information`.ID = `grades`.StudentNo AND `basic-information`.StudyType = '$time'";


		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_array()){
			$count = $fetch[0];

			$pages = $count / 100;
			$count = 1;

			while($count < $pages){
				$show = $count * 100;
				$old  = $show-100;
				echo 'Print results from '.$old.' to '.$show.' - <a href="' . $this->core->conf['conf']['path'] . '/report/batch/?uid='.$year.'&start='.$old.'&end='.$show.'&time='.$time.'">CLICK HERE</a><br>';
				$count++;
			}
		}
	}

	public function paymentsReport($item) {
		$year = $item;
		$month = $this->core->subitem;
		
		if($this->core->role != 105 && $this->core->role != 107 && $this->core->role != 1000){
			echo '<div>NO RIGHTS</div>';
			return;
		}

		$sql = "SELECT  `basic-information`.ID, `GovernmentID`, `balances`.`AccountCode`, `FirstName`,`MiddleName`,`Surname`, `student-data-other`.`ExamCentre`, `schools`.`Name`, `programmes`.`ProgramName`, SUM(`courses`.`CourseCredit`) as Credits, `basic-information`.`StudyType`,`basic-information`.`Status`, `basic-information`.`MobilePhone`, `ChargeType`
				FROM `basic-information`
				LEFT JOIN `student-study-link` ON `basic-information`.ID = `student-study-link`.`StudentID`
				LEFT JOIN `study` ON `study`.`ID` = `student-study-link`.`StudyID`
				LEFT JOIN `schools` ON `study`.ParentID = `schools`.`ID`
				LEFT JOIN `student-program-link` ON `student-program-link`.`StudentID` = `basic-information`.ID
				LEFT JOIN `course-electives` ON  `course-electives`.`StudentID` = `basic-information`.ID
				LEFT JOIN `programmes` ON `student-program-link`.`Major` = `programmes`.`ID`
				LEFT JOIN `courses` ON `course-electives`.`CourseID` = `courses`.`ID`
				LEFT JOIN `balances` ON  `balances`.`StudentID` = `basic-information`.ID
				LEFT JOIN `student-data-other` ON  `student-data-other`.`StudentID` = `basic-information`.ID
				LEFT JOIN `fee-package-charge-link` ON `basic-information`.ID = `fee-package-charge-link`.StudentID
				WHERE `basic-information`.`Status` IN ('New', 'Requesting')
				AND `course-electives`.Approved = '1'
				GROUP BY `course-electives`.`StudentID`, `basic-information`.ID  
				ORDER BY `Credits`  DESC"; 

		$run = $this->core->database->doSelectQuery($sql);

		$count = $this->offset+1;

		header("Content-type: text/csv");
		header("Content-Disposition: attachment; filename=edurole-export.csv");
		header("Pragma: no-cache");
		header("Expires: 0");




		while ($row = $run->fetch_row()) {
			$results = TRUE;

			$uid = $row[0];
			$nrc = $row[1];
			$acccode = $row[2];

			$firstname = $row[3];
			$middlename = $row[4];
			$surname = $row[5];

			$campus = $row[6];
			$school = $row[7];
			$major = $row[8];
			$credits = $row[9];
			$mode = $row[10];
			$status = $row[11];
			$phone = $row[12];

			$charge = $row[13];
			
			echo	$count.','.
				$uid.','.
				$nrc.','.
				$acccode.','.
				$firstname.' '.$middlename.' ' . $surname . ','.
				$campus.','.
				$school.','.
				$major.','.
				$credits.','.
				$mode.','.
				$status.','.
				$phone.','.
				$charge.',
';

			

			$count++;
			$results = TRUE;
		}
	}



	function batchReport($item) {
		$year = $_GET['uid'];
		$time = $_GET['time'];
		$start = $_GET['start'];
		$end = $_GET['end'];

		if(empty($start)){
			$start = 0;
		}
		if(empty($end)){
			$end = 1000;
		}

		$major = $_GET['major'];
		$minor = $_GET['minor'];

		$sql = "SELECT DISTINCT `basic-information`.ID 
			FROM `basic-information`, `student-program-link`, `programmes`, `grades`
			WHERE 
			`student-program-link`.Major = '$major'
			AND `student-program-link`.StudentID LIKE `basic-information`.ID  
			AND `student-program-link`.Major = `programmes`.ID 
			AND `basic-information`.Status = 'Requesting'
			AND `grades`.AcademicYear = '$year'
			AND `grades`.StudentNo = `basic-information`.ID
			AND `basic-information`.StudyType = '$time'
			AND
			`student-program-link`.Minor = '$minor'
			AND `student-program-link`.StudentID LIKE `basic-information`.ID  
			AND `student-program-link`.Major = `programmes`.ID 
			AND `basic-information`.Status = 'Requesting'
			AND `grades`.AcademicYear = '$year'
			AND `grades`.StudentNo = `basic-information`.ID
			AND `basic-information`.StudyType = '$time'
			OR
			`student-program-link`.Major = '$minor'
			AND `student-program-link`.StudentID LIKE `basic-information`.ID  
			AND `student-program-link`.Major = `programmes`.ID 
			AND `basic-information`.Status = 'Requesting'
			AND `grades`.AcademicYear = '$year'
			AND `grades`.StudentNo = `basic-information`.ID
			AND `basic-information`.StudyType = '$time'
			AND
			`student-program-link`.Minor = '$major'
			AND `student-program-link`.StudentID LIKE `basic-information`.ID  
			AND `student-program-link`.Major = `programmes`.ID 
			AND `basic-information`.Status = 'Requesting'
			AND `grades`.AcademicYear = '$year'
			AND `grades`.StudentNo = `basic-information`.ID
			AND `basic-information`.StudyType = '$time'
			GROUP BY `basic-information`.ID 
			ORDER BY  `basic-information`.ID DESC
			LIMIT $start, $end";


		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_array()){
			$studentid = $fetch[0];
			$this->resultsReport($studentid, $year);
			$first = FALSE;
			$i++;
			$x++;
		}

		echo'<script type="text/javascript">
			window.print();
		</script>';

	}
	
	function resultsReport($item, $year) {

		if(!isset($item) || $this->core->role <= 10){
			$item = $this->core->userID;
		}

		if($item == ""){
			$studentID = $_GET['uid'];
		} else {
			$studentID = $item;
		}

		$studentNo = $studentID;
		$start = substr($studentID, 0, 4);

		$sql = "SELECT Firstname, MiddleName, Surname, Status, Sex, `programmes`.ProgramName, YearOfStudy FROM `programmes`, `student-program-link`, `basic-information`, `student-data-other`
			WHERE `basic-information`.ID = `student-program-link`.`StudentID` AND `programmes`.ID = `student-program-link`.`Major` AND `basic-information`.`ID` = '$studentID' AND `student-data-other`.StudentID = `basic-information`.ID
			OR   `basic-information`.ID = `student-program-link`.`StudentID` AND `programmes`.ID = `student-program-link`.`Minor` AND `basic-information`.`ID` = '$studentID' AND `student-data-other`.StudentID = `basic-information`.ID";

		$run = $this->core->database->doSelectQuery($sql);
 
		$started = FALSE;
		$counter = 1;
		$program = 1;

		while ($fetch = $run->fetch_array()){
			$started = TRUE;

			$firstname = $fetch[0];
			$middlename = $fetch[1];
			$surname = $fetch[2];
			$remark=$fetch[5];
			$gender=$fetch[4];
			$year=$fetch[6];

			$studentname = $firstname . " " . $middlename . " " . $surname;

			$school=$fetch[7];

			if($program == 1){
				$program = $fetch[5];
			}else{
				$programtwo = $fetch[5];
			}

			$session=$fetch[9];
			$remark=$fetch[8];
		}

		if($program == $programtwo || empty($programtwo)){
			$programs = "$program";
		} else {
			$programs = "$program /<br> $programtwo";
		}

		echo "<div style=\" float: left; clear: left; padding: 2px; page-break-inside: avoid; font-size: 14px;\">
			<div style=\"clear: left; float: left; width: 200px; padding-right: 15px; padding-top: 20px; \"><b>$studentID</b>
			<div style=\"float: left; width: 250px; padding-right: 15px;\"><b>$studentname</b></div>
			<div style=\"float: left; width: 250px; padding-right: 15px;\">Program: <b>$programs</b></div>
			<div style=\"float: left; width: 250px; padding-right: 15px;\">Gender: <b>$gender</b></div></div>
			";
	
		$overallremark= $this->academicyear($studentNo, $year);

		//$overallremark= $this->detail($studentNo, 2016, TRUE, $year);

		echo'</div></div>';
	}

	private function academicyear($studentNo, $year) {

		$sql = "SELECT distinct academicyear FROM `grades` WHERE StudentNo = '$studentNo' order by academicyear";

		$run = $this->core->database->doSelectQuery($sql);
		$countyear = 1;

		echo '<div style="float: left; padding: 20px; border: 5px solid #f1f1f1; font-size: 12px;">';
		while ($fetch = $run->fetch_array()){

			$acyr = $fetch[0];

			if($countyear == 1){
				$set = FALSE;
			} else {
 				$set = TRUE;
			}
	
			echo '<div style="float: left; width: 110px; padding-right: 15px;">';

			echo '<div style="width: 100px float:left; "><b>YEAR '.$acyr.'</b></div>';

			$overallremark = $this->detail($studentNo, $acyr, $set);

			echo '</div>';

			$remark = $overallremark[0];
			$repeat = $overallremark[1];
			$countyear++;

		}
		echo '</div>';echo '</div>';
	}

	private function detail($studentNo, $acyr, $set, $year) {

		$sql = "SELECT DISTINCT
				p1.CourseNo,
				p1.Grade
			FROM 
				`grades` as p1,
				`courses` as p2
			WHERE 	p1.StudentNo = '$studentNo'
			AND	p1.AcademicYear = '$acyr'
			AND	p1.CourseNo = p2.Name  
			AND 	p1.Grade != ''
			ORDER BY p1.courseNo";

		$run = $this->core->database->doSelectQuery($sql);

		$output = "";
		$count2 = 0;
		$countwp=0;
		$suppoutput1="";
		$suppoutput2="";
		$suppoutput3="";
		$countb = 0;
		$i=0;
		$repeatlist = array();
		$out = FALSE;
		$qualcount = 0;
		$suppposscount = 0;
		$count = 0;
		$passcount = 0;
		$hiderepeat = FALSE;
		
		while ($row = $run->fetch_array()){
			$count++;
			$course = $row[0];
			$grade = $row[1];

			if (substr($course, -1) == '1'){
				$coursetype = 0.5;
			}else{
				$coursetype = 1;
			}

	
			$output .= '<div style="width: 100px float:left; font-size: 10px; ">'.$course.': <b>'.$grade.'</b></div>';

			if ($grade == "IN" or $grade == "D" or $grade=="F" or $grade=="NE") {
				$repeatoutput .= "REPEAT $course <br>";
				$failcount++;
			}
			

			if ($grade== "A+" or $grade=="A" or $grade=="B+" or $grade=="B" or $grade=="C+") {
				$qualcount++;
			}

			if ($grade== "A+" or $grade=="A" or $grade=="B+" or $grade=="B" or $grade=="C+" or $grade=="C" or $grade=="P") {
				$passcount++;

				if($grade== "A+"){
					$points = 5 * $coursetype + $points;
				}else if($grade== "A"){
					$points = 4 * $coursetype + $points;
				}else if($grade== "B+"){
					$points = 3 * $coursetype + $points;
				}else if($grade== "B"){
					$points = 2 * $coursetype + $points;
				}else if($grade== "C+"){
					$points = 1 * $coursetype + $points;
				}else if($grade== "C"){
					//$points = 0 * $coursetype + $points;
				}
			}

			if ($grade == "D+") {
				$suppcount++;
				$failcount++;

				$suppoutput[$suppcount] = "SUPP IN $course <br>";
				$repeatoutput .= "REPEAT $course <br>";

				if ($grade == "WP") {
					$suppoutput3 .= "DEF IN $course;";
					$countwp=$countwp + 1;
				}
				if ($grade == "DEF") {
					$suppoutput3 = "DEFFERED";
				}
				if ($grade == "EX") {
					$suppoutput3 .= "EXEMPTED IN $course; ";
				}
				if ($grade == "DISQ") {
					$suppoutput3 = "DISQUALIFIED";
					$overallremark .="DISQUALIFIED";
				}
				if ($grade == "SP") {
					$suppoutput3 = "SUSPENDED";
					$overallremark.="SUSPENDED";
				}
				if ($grade == "LT") {
					$suppoutput3 = "EXCLUDE";
					$overallremark.="EXCLUDE";
				}
				if ($grade == "WH") {
					$suppoutput3 = "WITHHELD";
					$overallremark.="WITHHELD";
					$count = 0;
				}
			}
		}

		if ($suppcount >= 2 && $qualcount >1) {
			$failcount = $failcount-2;
			$overallremark .= $suppoutput[1] . $suppoutput[2];
			$hiderepeat = TRUE;
		} else if ($suppcount == 1 && $qualcount >=1) {
			$failcount = $failcount-1;
			$overallremark .= $suppoutput[1];
			$hiderepeat = TRUE;
		} else if ($suppcount > 1 && $qualcount ==1) {
			$failcount = $failcount-1;
			$overallremark .= $suppoutput[1]; 
			$hiderepeat = TRUE;
		}

		$percentage = ($failcount/$count*100)-100;

		if($hiderepeat == FALSE){
			$overallremark .= $repeatoutput;
		}

		if($passcount == $count){
			$percentage = 100;
		}

		if ($year=='1') {

			if ($percentage < 50) {
				$overallremark .="EXCLUDE";
			}else {
				if ($failcount == 0) {
					if ($overallremark=="") {
						$overallremark .=  "CLEAR PASS";
					} else { 
						$overallremark .=  "<br>";
					}
	
					if ($countwp>2){
						$overallremark .="$countwp<br> $suppoutput3<br>";
						$overallremark .= "WITHDRAWN WITH PERMISSION";
					} else {
						$overallremark .=  "$suppoutput3"; 
					}

				}else {
					if ($failcount <= 2) {
						$overallremark .= $suppoutput1;

					}else {
						$overallremark .=  $suppoutput2;
					}
				}
			}
		} else {
		
			if ($percentage < 75) {
				$overallremark="EXCLUDE";
			}else {
				if ($failcount == 0) {
					if ($overallremark=="") {
						$overallremark .=  "CLEAR PASS";
					} else { 
						$overallremark .=  "<br>";
					}
	
					if ($countwp>2){
						$overallremark .="$countwp<br> $suppoutput3<br>";
						$overallremark .= "WITHDRAWN WITH PERMISSION";
					} else {
						$overallremark .=  "$suppoutput3"; 
					}

				}else {
					if ($failcount <= 2) {
						$overallremark .= $suppoutput1;

					}else {
						$overallremark .=  $suppoutput2;
					}
				}
			}
		}
	

		$mincount = 4;
		if($count < $mincount){
			$overallremark = 'INCOMPLETE';

			if($output == ""){
				echo'<div style="float: left; width: 150px; padding-right: 15px;"><b>EMPTY</b></div>';
			}
			echo $output;
		} else {
			// print results
			echo $output;


		}

		$percentage = number_format((float)$percentage, 2, '.', '');

		$overallremark = rtrim($overallremark,'<br>');
		echo'<div style="float: left; width: 150px; padding-right: 15px; font-size: 12px;"><b>'.$overallremark.' <br>('.$percentage.'%)</b></div>';



}	

}
?>
