<?php
class grades {

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
		include $this->core->conf['conf']['classPath'] . "grades.inc.php";
	}


	public function reviewGrades($item){

		$sql = "SELECT *,  `grade-modified`.ID as GID, 
			`DEANS`.FirstName as DeanFN, `DEANS`.Surname as DeanSN, 
			`BOS`.FirstName as BosFN, `BOS`.Surname as BosSN, 
			`DVC`.FirstName as DVCFN, `DVC`.Surname as DVCSN
			FROM  `grade-modified` 
			LEFT JOIN `basic-information` as `DEANS` ON `DEANS`.ID = `grade-modified`.SubmittedBy
			LEFT JOIN `basic-information` as `BOS` ON `BOS`.ID = `grade-modified`.ReviewedBy
			LEFT JOIN `basic-information` as `DVC` ON `DVC`.ID = `grade-modified`.ApprovedBy
			LEFT JOIN `grade-comment` ON `grade-comment`.`GradeID` = `grade-modified`.ID
			WHERE `grade-modified`.ID = '$item'";

		$run = $this->core->database->doSelectQuery($sql);
		$i=0;

		while ($fetch = $run->fetch_assoc()) {

			$gid = $fetch['GID'];
			$studentid = $fetch['StudentID'];
			$course = $fetch['CID'];
			$ca = $fetch['CA'];
			$exam = $fetch['Exam'];
			$total = $fetch['Total'];
			$grade = $fetch['Grade'];

			$submittedby = $fetch['SubmittedBy'];
			$reviewedby = $fetch['ReviewedBy'];
			$approvedby = $fetch['ApprovedBy'];
			
			$comment = $fetch['Comment'];
			$owner = $fetch['Owner'];
			$date = $fetch['DateTime'];

			$deanfn = $fetch['DeanFN'];
			$deansn = $fetch['DeanSN'];

			$bosfn = $fetch['BosFN'];
			$bossn = $fetch['BosSN'];

			$dvcfn = $fetch['DVCFN'];
			$dvcsn = $fetch['DVCSN'];

			if($i == 0){

				echo '<div class="heading">REQUEST NUMBER '.$gid.'</div>';

				echo '<span class="label">Student</span><b><a href="#">' . $studentid .' </a></b><br>';
				echo '<span class="label">Course code</span><b>' . $course .' </b><br>';
				echo '<span class="label">CA Mark</span><b>' . $ca .' </b><br>';
				echo '<span class="label">Exam Mark</span><b>' . $exam .' </b><br>';
				echo '<span class="label">Total Mark</span><b>' . $total .' </b><br>';
				echo '<span class="label">Grade</span><b>' . $grade .' </b><br>';

				echo '<hr>';

				if($reviewedby == '0'){
					echo '<h2>GRADE IS READY FOR REVIEW</h2>';
				} else if($approvedby == '0'){
					echo '<h2>GRADE IS REVIEWED AND READY FOR APPROVAL</h2>';
				} else {
					echo '<h2>GRADE FULLY APPROVED</h2>';
				}


				if($reviewedby == '1'){
					echo '<h2>GRADE MODIFICATION REVIEW HAS BEEN REJECTED</h2>';
					return;
				} else if($approvedby == '1'){
					echo '<h2>GRADE MODIFICATION APPROVAL HAS BEEN REJECTED</h2>';
					return;
				}

				echo '<hr>';

				if($submittedby != '0'){
					echo '<span class="label">Submitted by</span><b>'. $deanfn .'  '. $deansn .'</b> <a href="#"> ' . $submittedby .'</a> <br>';
					echo '<span class="label">Comment</span><b>' . $comment .' </b><br>';
					echo '<span class="label">When</span><b>' . $date .' </b><br>';
					echo '<hr>';
					if($reviewedby == '0' && $this->core->role == 1021){
						echo '<a href="'.$this->core->conf['conf']['path'].'/grades/approve/'.$item.'" class="submit" role="button"><b>APPROVE REVIEW</b></a>
						<a href="'.$this->core->conf['conf']['path'].'/grades/approve/'.$item.'/reject" class="submit" role="button"><b>REJECT REVIEW</b></a>';
						}
				}
			} 

			if ($i == 1){
				if($reviewedby != '0'){
					echo '<span class="label">Reviewed by</span><b>'. $bosfn .'  '. $bossn .'</b> <a href="#"> ' . $reviewedby .'</a><br>';
					echo '<span class="label">Comment</span><b>' . $comment .' </b><br>';
					echo '<span class="label">When</span><b>' . $date .' </b><br>';
						echo '<hr>';
					if($approvedby == '0' && $this->core->role == 1000){
						echo '<a href="'.$this->core->conf['conf']['path'].'/grades/approve/'.$item.'" class="submit" role="button"><b>APPROVE REVIEW</b></a>
						<a href="'.$this->core->conf['conf']['path'].'/grades/approve/'.$item.'/reject" class="submit" role="button"><b>REJECT REVIEW</b></a>';
					}
				}
			}

			if ($i == 2){
				if($approvedby != '0'){
					echo '<span class="label">Approved by</span><b>'. $dvcfn .'  '. $dvcsn .'</b>   <a href="#"> ' . $approvedby .'</a><br>';
					echo '<span class="label">Comment</span><b>' . $comment .' </b><br>';
					echo '<span class="label">When</span><b>' . $date .' </b><br>';
					echo '<hr>';
				}
			}

			$i++;
		}
	}

	public function saveGrades($item){
		$studentid = $this->core->cleanPost['student'];
		$course = $this->core->cleanPost['course'];
		$ca = $this->core->cleanPost['ca'];
		$exam = $this->core->cleanPost['exam'];
		$comment = $this->core->cleanPost['comment'];
		$gid = $this->core->cleanPost['gid'];
		$owner = $this->core->userID;

		if($gid == ""){
			$gid = '0';
		}

		$sql =  "INSERT INTO `edurole`.`grade-modified` (`ID`, `GradeID`, `StudentID`, `CID`, `CA`, `Exam`, `Total`, `Grade`, `DateTime`, `SubmittedBy`, `ReviewedBy`, `ApprovedBy`) 
			VALUES (NULL, '$gid', '$studentid', '$course', '$ca', '$exam', '$total', '$grade', NOW(), '$owner', '0', '0');";

		$this->core->database->doInsertQuery($sql);
		$last = $this->core->database->id();


		$sql = "INSERT INTO `grade-comment` (`ID`, `GradeID`, `DateTime`, `Owner`, `Comment`) VALUES (NULL, '$last', NOW(), '$owner', '$comment');";
			
		$this->core->database->doInsertQuery($sql);


		$this->processGrades($last);

		$this->core->redirect("grades", "approve", NULL);
	}

	public function addGrades($item){
		$uid = $this->core->userID;
		$role = $this->core->role;

		$disabled = "text";

		if(isset($item)){
			echo'<div class="successpopup">You are modifying an existing grade</div>';

			$sql = "SELECT * FROM `grades` WHERE `grades`.ID = '$item'";

			$run = $this->core->database->doSelectQuery($sql);

			while ($fetch = $run->fetch_assoc()) {
				$course = $fetch['CourseNo'];
				$grade = $fetch['Grade'];
				$ca = $fetch['CAMarks'];
				$exam = $fetch['ExamMarks'];
				$student = $fetch['StudentNo'];
			}

			$disabled = "hidden";
		}

		if($this->core->role == 107 || $this->core->role == 1000){
			echo '<div class="toolbar">
				<a href="' . $this->core->conf['conf']['path'] . '/grades/add">Add/Change grade</a>
				</div>'; 
		}

		echo '<div class="heading">Add or change grade</div>

		<form id="addfee" name="addgrade" method="post" action="'.$this->core->conf['conf']['path'].'/grades/save/'.$this->core->item.'">
		<input type="hidden" name="gid" value="'.$item.'">
		<span class="label">Student Number</span><input type="'.$disabled.'" name="student" value="'.$student.'"> '.$student.'<br>
		<span class="label">Course</span><input type="text" name="course" value="'.$course.'"><br>
		<span class="label">CA Mark</span><input type="text" name="ca" value="'.$ca.'"><br>
		<span class="label">Exam Mark</span><input type="text" name="exam" value="'.$exam.'"><br><br>

		<span class="label">Comment</span><textarea name="comment"  rows="4" cols="37"></textarea ><br>


		<span class="label">Submit</span><input type="submit" name="submit" value="Submit grade">
		</form>';

	}

	public function approveGrades($item){
		$uid = $this->core->userID;
		$role = $this->core->role;
		$subitem = $this->core->subitem;
	

		if($this->core->role == 1021){
			if($subitem == "reject"){
				$sql = "INSERT INTO `grade-comment` (`ID`, `GradeID`, `DateTime`, `Owner`, `Comment`) VALUES (NULL, '$item', NOW(), '$uid', 'REVIEW REJECTED BY BOS');";
				$this->core->database->doInsertQuery($sql);

				$sql = "UPDATE  `grade-modified` SET  `ReviewedBy` =  '1' WHERE  `grade-modified`.`ID` = '$item'; ";
				echo '<div class="successpopup">Review rejected, continue to <a href="' . $this->core->conf['conf']['path'] . '/grades/approve">grade overview</a></div>';
				$this->core->database->doInsertQuery($sql);
			} else if(isset($item)){
				$sql = "INSERT INTO `grade-comment` (`ID`, `GradeID`, `DateTime`, `Owner`, `Comment`) VALUES (NULL, '$item', NOW(), '$uid', 'REVIEWED BY BOARD OF STUDIES');";
				$this->core->database->doInsertQuery($sql);

				$sql = "UPDATE  `grade-modified` SET  `ReviewedBy` =  '$uid' WHERE  `grade-modified`.`ID` = '$item'; ";
				echo '<div class="successpopup">Reviewed, continue to <a href="' . $this->core->conf['conf']['path'] . '/grades/approve">grade overview</a></div>';
				$this->core->database->doInsertQuery($sql);
			} 
		} else if($this->core->role == 1000){
			if($subitem == "reject"){
				$sql = "INSERT INTO `grade-comment` (`ID`, `GradeID`, `DateTime`, `Owner`, `Comment`) VALUES (NULL, '$item', NOW(), '$uid', 'REJECTED BY DVC');";
				$this->core->database->doInsertQuery($sql);

				$sql = "UPDATE  `grade-modified` SET  `ApprovedBy` =  '1' WHERE  `grade-modified`.`ID` = '$item'; ";
				echo '<div class="successpopup">Rejected, continue to <a href="' . $this->core->conf['conf']['path'] . '/grades/approve">grade overview</a></div>';
				$this->core->database->doInsertQuery($sql);
			} else if(isset($item)){
				$sql = "INSERT INTO `grade-comment` (`ID`, `GradeID`, `DateTime`, `Owner`, `Comment`) VALUES (NULL, '$item', NOW(), '$uid', 'APPROVED BY DVC');";
				$this->core->database->doInsertQuery($sql);

				$sql = "UPDATE  `grade-modified` SET  `ApprovedBy` =  '$uid' WHERE  `grade-modified`.`ID` = '$item'; ";
				echo '<div class="successpopup">Approved, continue to <a href="' . $this->core->conf['conf']['path'] . '/grades/approve">grade overview</a></div>';
				$this->core->database->doInsertQuery($sql);
			} 
		}


		if($this->core->role == 105 || $this->core->role == 1000){
			echo '<div class="toolbar">
				<a href="' . $this->core->conf['conf']['path'] . '/grades/add">Add/Change grade</a>
				</div>'; 
		}

		echo '<div class="heading">Queue for approval</div>';

		echo'<table id="results" class="table table-bordered table-striped table-hover">
			<thead>
				<tr>
					<th bgcolor="#EEEEEE" width="50px"><b>#</b></th>
					<th bgcolor="#EEEEEE" width=""><b>Student ID</b></th>
					<th bgcolor="#EEEEEE" width=""><b>Course</b></th>
					<th bgcolor="#EEEEEE" width=""><b>CA mark</b></th>
					<th bgcolor="#EEEEEE" width=""><b>Exam mark</b></th>
					<th bgcolor="#EEEEEE" width=""><b>Grade</b></th>
					<th bgcolor="#EEEEEE" width=""><b>Options</b></th>
				</tr>
			</thead>
			<tbody>';

		if($role == 105){
			$sql = "SELECT *, `grade-modified`.ID as GID
				FROM  `grade-modified`
				LEFT JOIN `basic-information` ON `basic-information`.ID = `grade-modified`.SubmittedBy
				WHERE `ReviewedBy` = '0' AND `ApprovedBy` = '0' AND `SubmittedBy` = '$uid'";
		}else if($role == 1021){
			$sql = "SELECT *, `grade-modified`.ID as GID
				FROM  `grade-modified`
				LEFT JOIN `basic-information` ON `basic-information`.ID = `grade-modified`.SubmittedBy
				WHERE `ReviewedBy` = '0'";
		}else if($role == 1000){
			$sql = "SELECT *, `grade-modified`.ID as GID
				FROM  `grade-modified` 
				LEFT JOIN `basic-information` as `DEANS` ON `DEANS`.ID = `grade-modified`.SubmittedBy
				LEFT JOIN `basic-information` as `BOS` ON `BOS`.ID = `grade-modified`.ReviewedBy
				WHERE `ReviewedBy` != '0' AND  `ApprovedBy` = '0'";
		}

		$run = $this->core->database->doSelectQuery($sql);
		$i=0;

		while ($fetch = $run->fetch_assoc()) {
			$i++;

			$id = $fetch['GID'];
			$studentid = $fetch['StudentID'];
			$course = $fetch['CID'];
			$ca = $fetch['CA'];
			$exam = $fetch['Exam'];
			$total = $fetch['Total'];
			$grade = $fetch['Grade'];
			$submittedby = $fetch['SubmittedBy'];


			echo'<tr>
				<td>'.$i.'</td>
				<td><b>'.$studentid.'</b></td>
				<td><b>'.$course.'</b></td>
				<td>'.$ca.'</td>
				<td>'.$exam.'</td>
				<td><b>'.$grade.'</b></td>
				<td><a href="' . $this->core->conf['conf']['path'] . '/grades/review/'.$id.'">Review submission</a></td>
			</tr>';

		}

		echo'</tbody></table>';
	}

	function statementGrades() {
		include $this->core->conf['conf']['formPath'] . "searchstatement.form.php";
	}

	function reportGrades() {
		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
		
		$select = new optionBuilder($this->core);
		$programs = $select->showPrograms(NULL);

		include $this->core->conf['conf']['formPath'] . "searchreport.form.php";
	}

	function transcriptGrades() {
		include $this->core->conf['conf']['formPath'] . "searchtranscript.form.php";
	}

	function viewMenu(){

		if($this->core->role == 107 || $this->core->role == 1000){
			echo '<div class="toolbar">
				<a href="' . $this->core->conf['conf']['path'] . '/grades/search">Search</a>
				<a href="' . $this->core->conf['conf']['path'] . '/grades/selectcourse">Add Grades</a>
				<a href="' . $this->core->conf['conf']['path'] . '/grades/transcript">Get Result Transcript</a>
				<a href="' . $this->core->conf['conf']['path'] . '/grades/statement">Get Result Statement</a>
				<a href="' . $this->core->conf['conf']['path'] . '/grades/report">Print Senate Report</a>
				</div>'; 
		}else if($this->core->role > 100){
			echo '<div class="toolbar">
				<a href="' . $this->core->conf['conf']['path'] . '/grades/selectcourse">Add Grades</a>
				</div>'; 
		}
	}

	// PROCESS CALCULATE WEIGHTED AVERAGES
	function processGrades($item) {

		echo'<table id="results" class="table table-bordered table-striped table-hover">
			<thead>
				<tr>
					<th bgcolor="#EEEEEE" width="50px"><b>#</b></th>
					<th bgcolor="#EEEEEE" width=""><b>Student Number</b></th>
					<th bgcolor="#EEEEEE" width=""><b>Course code</b></th>
					<th bgcolor="#EEEEEE" width=""><b>Course mark</b></th>
					<th bgcolor="#EEEEEE" width=""><b>Exam mark</b></th>
					<th bgcolor="#EEEEEE" width=""><b>Total mark</b></th>
					<th bgcolor="#EEEEEE" width=""><b>Letter grade</b></th>
				</tr>
			</thead>
			<tbody>';

		if($item == ""){

			$sql = "SELECT DISTINCT GradeID, StudentNo, CAMarks, ExamMarks, CourseNo 
			FROM `grade-update`, `grades` WHERE `Marked` = '' 
			AND `GradeID` = `grades`.`ID` 
			AND `grades`.Grade NOT IN ('EX', 'LT')
			AND `grades`.ExamMarks NOT IN ('NE', 'DSQ', 'DISQ', 'EX', 'LT', 'DEF', 'WD', 'WP', '')";

		} else {
		
			$sql = "SELECT `ID`, `StudentID`, `CA`, `Exam`, `CID`
			FROM `grade-modified` 
			WHERE `grade-modified`.`ID` = '$item'";

		}

		$run = $this->core->database->doSelectQuery($sql);
		$i=0;

		while ($fetch = $run->fetch_row()) {

			$gradeid = $fetch[0];
			$studentid = $fetch[1];
			$ca = trim($fetch[2]);
			$exam = trim($fetch[3]);
			$course = strtoupper(trim($fetch[4]));
			$program =  substr($course, 0, 3);

			$caweight = 0;
			$exweight = 0;

			$sqlb = "SELECT * FROM `courses` WHERE `Name` = '$course'";
			$runb = $this->core->database->doSelectQuery($sqlb);

			while ($fetchb = $runb->fetch_row()) {
				
				$caweight = $fetchb[5];
				$exweight = $fetchb[6];
			}

			$sqlb = "SELECT * FROM `grade-weight` WHERE `Program` = '$program'";
			$runb = $this->core->database->doSelectQuery($sqlb);

			while ($fetchb = $runb->fetch_row()) {
				if($caweight == 0){
					$caweight = $fetchb[2];
				}
				if($exweight == 0){
					$exweight = $fetchb[3];
				}

				$i++;

				$cac = $ca*$caweight;
				$examc = $exam*$exweight;
				$totalmark = $cac+$examc;

				$totalmark = round($totalmark);

				$sqlc = "SELECT * FROM `grade-standards` WHERE $totalmark BETWEEN `GradePointLow` AND `GradePointHigh`;";

				$runc = $this->core->database->doSelectQuery($sqlc);

				while ($fetchc = $runc->fetch_row()) {
					$lettergrade = $fetchc[1];
				}

				if($ca < 40 || $exam < 40){
					$lettergrade = "D";
				}

				if($exam > 29 && $exam < 40){
					$lettergrade = "D+";
				}


				echo'<tr><td>'.$i.'</td><td><b>'.$studentid.'</b></td><td><b>'.$course.'</b></td><td>'. $ca . '</td><td>' . $exam . '</td><td>'. $totalmark .'</td><td><b>' .  $lettergrade . '</b></td></tr>';
			
				if($item == ""){

					$sqld = "UPDATE  `grades` SET  `TotalMarks` =  '$totalmark', `Grade` =  '$lettergrade' 
					WHERE  `grades`.`ID` = '$gradeid';"; 
					$insert = $this->core->database->doInsertQuery($sqld);

					$hash = sha1($ca.$exam.$totalmark.$lettergrade.$gradeid);


					$sqle = "UPDATE  `grade-update` SET `Marked` =  '$hash' 
					WHERE   `grade-update`.`GradeID` = '$gradeid';"; 
					$insert = $this->core->database->doInsertQuery($sqle);

				} else {

					$sqld = "UPDATE  `grade-modified` 
					SET  `Total` =  '$totalmark', `Grade` =  '$lettergrade' 
					WHERE  `grade-modified`.`ID` = '$gradeid';"; 

					$insert = $this->core->database->doInsertQuery($sqld);

				}
				
			}
		}

		echo'</tbody></table>';

	}


	// PROCESS CALCULATE WEIGHTED AVERAGES
	function notexaminedGrades() {

		echo'<table id="results" class="table table-bordered table-striped table-hover">
			<thead>
				<tr>
					<th bgcolor="#EEEEEE" width="50px"><b>#</b></th>
					<th bgcolor="#EEEEEE" width=""><b>Student Number</b></th>
					<th bgcolor="#EEEEEE" width=""><b>Course code</b></th>
					<th bgcolor="#EEEEEE" width=""><b>Course mark</b></th>
					<th bgcolor="#EEEEEE" width=""><b>Exam mark</b></th>
					<th bgcolor="#EEEEEE" width=""><b>Total mark</b></th>
					<th bgcolor="#EEEEEE" width=""><b>Letter grade</b></th>
				</tr>
			</thead>
			<tbody>';

		$sql = "SELECT DISTINCT GradeID, StudentNo, CAMarks, ExamMarks, CourseNo 
			FROM `grade-update`, `grades` WHERE `Marked` = '' 
			AND `GradeID` = `grades`.`ID` 
			AND `grades`.ExamMarks IN ('NE', 'DSQ', 'DISQ', 'EX', 'LT', 'DEF', 'WD', 'WP', '')";

		$run = $this->core->database->doSelectQuery($sql);
		$i=0;

		while ($fetch = $run->fetch_row()) {

			$gradeid = $fetch[0];
			$studentid = $fetch[1];
			$ca = trim($fetch[2]);
			$exam = trim($fetch[3]);
			$course = trim($fetch[4]);
			$program =  substr($course, 0, 3);

			if($exam == ''){
				$exam = 'NE';
			}
			
			$lettergrade = $exam;
			$totalmark = $exam;
			$lettergrade = $exam;

			echo'<tr><td>'.$i.'</td><td><b>'.$studentid.'</b></td><td><b>'.$course.'</b></td><td>'. $ca . '</td><td>' . $exam . '</td><td>'. $totalmark .'</td><td><b>' .  $lettergrade . '</b></td></tr>';
			

			$sqld = "UPDATE  `grades` SET  `TotalMarks` =  '$totalmark', `Grade` =  '$lettergrade' 
				WHERE  `grades`.`ID` = '$gradeid';"; 
			$insert = $this->core->database->doInsertQuery($sqld);

			$hash = sha1($ca.$exam.$totalmark.$lettergrade.$gradeid);


			$sqle = "UPDATE  `grade-update` SET `Marked` =  '$hash' 
				WHERE   `grade-update`.`GradeID` = '$gradeid';"; 
				$insert = $this->core->database->doInsertQuery($sqle);
			
		}

		echo'</tbody></table>';

	}


	function importbatchGrades() {
		$path = $this->core->conf['conf']['dataStorePath'] . 'tmp/grades/all';
		$this->importer($path, "both");
	}

	private function importer($path, $type){

		$coursegrade = NULL;

		foreach (glob("$path/*") as $filename) {
			echo "<h1>IMPORT STARTED</h1>";
			echo "<h2>IMPORTING $filename</h2>";
			$filenamearray = explode("-", $filename);
			$academicyear = "2017";

			$file = file_get_contents($filename);
			$document = explode("\n", $file);

			foreach($document as $line){

				$linearray = explode(",", $line);
				if(!isset($linearray[1])){
					continue;
				}

				$studentnumber = trim($linearray[0]);
				$course = trim($linearray[1]);
				
				if($type == "both"){
					$coursegrade = trim($linearray[2]);
					$examgrade = trim($linearray[3]);
				} else if($type == "single"){
					$examgrade = trim($linearray[2]);
				}

				if($currentstudent == null || $currentstudent != $studentnumber){
					if($studentnumber != ""){
						$currentstudent = $studentnumber;

						if($started == TRUE){
							$sql = "";
						} 

						$started = TRUE;

						$i++;
						echo $i . " - <b>IMPORTING - $studentnumber</b> -";
					}
				}

				if($currentcombination == null || $currentcombination != $combination){
					if($combination!= ""){
						$currentcombination = $combination;
					}
				}

				if(empty($examgrade)){
					echo 'NO EXAM GRADE SKIPPING <br>';
					continue;
				}


				$sqlo = "SELECT * FROM `grades`
						WHERE  `grades`.`StudentNo` = '$currentstudent' 
						AND `grades`.`CourseNo` = '$course' AND AcademicYear = '$academicyear'";
				
				$runo = $this->core->database->doSelectQuery($sqlo);

				if($runo->num_rows == 0 ){
					if(empty($coursegrade)){
						$sqlx = "INSERT INTO `grades` (`ID`, `user`, `userdate`, `usertime`, `StudentNo`, `AcademicYear`, `Semester`, `ProgramNo`, `CourseNo`, `CAMarks`, `ExamMarks`, `TotalMarks`, `Grade`, `Points`) 
						VALUES (NULL, '', CURDATE(), CURTIME(), '$currentstudent', '2016', '', '', '$course', '', '$examgrade', '', '', '0')";

						if( $this->core->database->doInsertQuery($sqlx) ){	
							echo ' NEW EXAM ONLY -';
						}else{
							echo " - FAILED TO INSERT: $sqlx || ";
							die();
						}
					} else{
						if($examgrade != "EX" && $examgrade != "WP" && $examgrade != "NE" && $examgrade != "WD" && $examgrade != "DSQ" && $examgrade != "LT" ){
							$sqlx = "INSERT INTO `grades` (`ID`, `user`, `userdate`, `usertime`, `StudentNo`, `AcademicYear`, `Semester`, `ProgramNo`, `CourseNo`, `CAMarks`, `ExamMarks`, `TotalMarks`, `Grade`, `Points`) 
							VALUES (NULL, '', CURDATE(), CURTIME(), '$currentstudent', '$academicyear', '', '', '$course', '$coursegrade', '$examgrade', '', '', '0')";
						}else{
							echo ' - NOT EXAMINED ';
							$sqlx = "INSERT INTO `grades` (`ID`, `user`, `userdate`, `usertime`, `StudentNo`, `AcademicYear`, `Semester`, `ProgramNo`, `CourseNo`, `CAMarks`, `ExamMarks`, `TotalMarks`, `Grade`, `Points`) 
							VALUES (NULL, '', CURDATE(), CURTIME(), '$currentstudent', '$academicyear', '', '', '$course', '0', '0', '0', '$examgrade', '0')";
						}

						if( $this->core->database->doInsertQuery($sqlx) ){	
							echo ' NEW -';
						}else{
							echo " - FAILED TO INSERT: $sqlx || ";
							die();
						}
					}

				} else {

					if($type == "both"){
						if($examgrade != "EX" && $examgrade != "WP" && $examgrade != "NE" && $examgrade != "WD" && $examgrade != "DSQ" && $examgrade != "LT" ){
							$sqlu = "UPDATE `grades` 
							SET `ExamMarks` =  '$examgrade', `CAMarks` =  '$coursegrade' 
							WHERE  `grades`.`StudentNo` = '$currentstudent' 
							AND `grades`.`CourseNo` = '$course' AND AcademicYear = '$academicyear';"; 
						}else{
							echo ' - NOT EXAMINED ';
							$sqlu = "UPDATE  `grades` SET `Grade` = '$examgrade'
							WHERE  `grades`.`StudentNo` = '$currentstudent' 
							AND `grades`.`CourseNo` = '$course' AND AcademicYear = '$academicyear';";  
						}
					}else if($type == "single"){
						$sqlu = "UPDATE `grades`
						SET `ExamMarks` =  '$examgrade'
						WHERE  `grades`.`StudentNo` = '$currentstudent' 
						AND `grades`.`CourseNo` = '$course' AND AcademicYear = '$academicyear';"; 
					}



					if( $this->core->database->doInsertQuery($sqlu) ){	
						echo "COMPLETED $course -";	
						$udp = $this->core->database->mysqli->affected_rows;

						if($udp==0){
							echo 'NO UPDATE';
						}
					}else{
						echo " - FAILED TO IMPORT GRADE : $sqly || ";
						die();
					}
				}					

				$coursegrade = "";
				$examgrade = "";
				


				if(isset($course) && isset($currentstudent)){
					$sqly = "INSERT INTO `grade-update` SELECT NULL, `grades`.ID, NOW(), NULL 
					FROM  `grades` WHERE  `grades`.`StudentNo` = '$currentstudent' 
					AND `grades`.`CourseNo` = '$course' AND AcademicYear = '$academicyear';";
				
					if( $this->core->database->doInsertQuery($sqly) ){	
						echo " UP - ";	
					}else{
						echo " - FAILED TO ADD UPDATE : $sqly || ";
						die();
					}
				}	

				echo "COMPLETED<br>";	
			}

			echo "COMPLETED<br>";	

			//unlink($filename); 
			if($started == TRUE){ 	echo "<h2>DELETING FILE</h2><br>";	}

		}
	}

	function importGrades() {
		$path = $this->core->conf['conf']['dataStorePath'] . 'tmp/grades/course';

		foreach (glob("$path/*") as $filename) {
			echo "<h1>IMPORT STARTED</h1>";
			echo "<h2>IMPORTING $filename</h2>";
			$filenamearray = explode("-", $filename);

			$academicyear = "2016";

			$file = file_get_contents($filename);
			$document = explode("\n", $file);

			foreach($document as $line){

				$linearray = explode(",", $line);
				if(!isset($linearray[1])){
					continue;
				}

				$studentnumber = $linearray[0];
				$course = trim($linearray[1]);
				$coursegrade = trim($linearray[2]);

				if($currentstudent == null || $currentstudent != $studentnumber){
					if($studentnumber != ""){
						$currentstudent = $studentnumber;
						$i++;
						echo $i . " - <b>Importing grades for $studentnumber</b> .... ";
					}
				}

				if($currentcombination == null || $currentcombination != $combination){
					if($combination!= ""){
						$currentcombination = $combination;
					}
				}

				$sqlc = "INSERT INTO `grades` (`ID`, `user`, `userdate`, `usertime`, `StudentNo`, `AcademicYear`, `Semester`, `ProgramNo`, `CourseNo`, `CAMarks`, `ExamMarks`, `TotalMarks`, `Grade`, `Points`) 
					VALUES (NULL, '', CURDATE(), CURTIME(), '$currentstudent', '$academicyear', '', '', '$course', '$coursegrade', '', '', '', '0') 
					ON DUPLICATE KEY UPDATE `CAMarks` =  '$coursegrade';";
	//echo $sqlc . '<br>';
				if( $this->core->database->doInsertQuery($sqlc) ){	
					echo " ADDED CA <br>";	
				}else{
					echo " - FAILED TO ADD CA : $sqlc || <br>";
					die();
				}
			}


			//unlink($filename); 
			if($started == TRUE){ 	echo "<h2>DELETING FILE</h2><br>";	}

		}
	}

	function verifyGrades() {
		$path = $this->core->conf['conf']['dataStorePath'] . 'tmp/grades/all';
		$courselist = array();

		foreach (glob("$path/*") as $filename) {
			echo "<hr><br><h2>VERIFY $filename</h2>";
			$filenamearray = explode("-", $filename);
			$academicyear = basename($filenamearray[0]);

			$semester = $filenamearray[3];
			$semester = explode(".", $semester);
			$semester = $semester[0];

			$file = file_get_contents($filename);
			$document = explode("\n", $file);

			foreach($document as $line){

				$linearray = explode(",", $line);
				if(!isset($linearray[1])){
					continue;
				}

				$studentnumber = trim($linearray[0]);
				$course = trim($linearray[1]);

				$coursemark = trim($linearray[2]);
				$exammark = trim($linearray[3]);
				$totalmark = trim($linearray[4]);

				$grade = trim($linearray[5]);
				$comment = trim($linearray[6]);

				if(empty($studentnumber) || empty($course) || empty($grade)){
					echo 'INVALID LINE <br>';
					continue;
				}

				$i++;
				echo $i . " - <b>DATA: $studentnumber</b> | $academicyear - $semester - <b><u>$course</u></b> - $coursemark $exammark $totalmark - <b>$grade</b> - $comment ";

				if(!empty($coursegrade) || empty($examgrade) || empty($totalgrade) || empty($grade)){

					$sqlc = "INSERT INTO `grades` (`ID`, `user`, `userdate`, `usertime`, `StudentNo`, `AcademicYear`, `Semester`, `ProgramNo`, `CourseNo`, `CAMarks`, `ExamMarks`, `TotalMarks`, `Grade`, `Points`, `Comment`) 
					VALUES (NULL, '', CURDATE(), CURTIME(), '$studentnumber', '$academicyear', '', '', '$course', '$coursemark', '$exammark', '$totalmark', '$grade', '0', '$comment') 
					ON DUPLICATE KEY UPDATE `CAMarks` =  '$coursemark';";

				} else if(!empty($coursemark) || !empty($examgrade) || !empty($totalgrade) || !empty($grade)){

					// COURSE MARK 

					$sqlc = "INSERT INTO `grades` (`ID`, `user`, `userdate`, `usertime`, `StudentNo`, `AcademicYear`, `Semester`, `ProgramNo`, `CourseNo`, `CAMarks`, `ExamMarks`, `TotalMarks`, `Grade`, `Points`, `Comment`) 
					VALUES (NULL, '', CURDATE(), CURTIME(), '$studentnumber', '$academicyear', '', '', '$course', '$coursemark', '$exammark', '$totalmark', '$grade', '0', '$comment') 
					ON DUPLICATE KEY UPDATE `CAMarks` =  '$coursemark', `ExamMarks` =  '$exammark', `TotalMarks` =  '$totalmark, `Grade` = '$grade';";

				} else if(empty($coursemark) || empty($examgrade) || empty($totalgrade) || !empty($grade)){

					// GRADE ONLY

					$sqlc = "INSERT INTO `grades` (`ID`, `user`, `userdate`, `usertime`, `StudentNo`, `AcademicYear`, `Semester`, `ProgramNo`, `CourseNo`, `CAMarks`, `ExamMarks`, `TotalMarks`, `Grade`, `Points`, `Comment`) 
					VALUES (NULL, '', CURDATE(), CURTIME(), '$studentnumber', '$academicyear', '', '', '$course', '$coursemark', '$exammark', '$totalmark', '$grade', '0', '$comment') 
					ON DUPLICATE KEY UPDATE `Grade` = '$grade';";

				} else if(empty($coursemark) || !empty($examgrade) || !empty($totalgrade) || empty($grade)){

					// EXAM AND TOTALMARK ONLY

					$sqlc = "INSERT INTO `grades` (`ID`, `user`, `userdate`, `usertime`, `StudentNo`, `AcademicYear`, `Semester`, `ProgramNo`, `CourseNo`, `CAMarks`, `ExamMarks`, `TotalMarks`, `Grade`, `Points`, `Comment`) 
					VALUES (NULL, '', CURDATE(), CURTIME(), '$studentnumber', '$academicyear', '', '', '$course', '$coursemark', '$exammark', '$totalmark', '$grade', '0', '$comment') 
					ON DUPLICATE KEY UPDATE `ExamMarks` =  '$exammark', `TotalMarks` =  '$totalmark;";

				}

				if( $this->core->database->doInsertQuery($sqlc)){	
					echo " - <b>ADDED</b><br>";	
				}else{
					echo " - <b>FAILED</b><br>";
				}
			}

		}
	}


	function searchGrades() {
		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";

		$select = new optionBuilder($this->core);

		$study = $select->showStudies(null);
		$program = $select->showPrograms(null, null, null);
		$courses = $select->showCourses(null);

		include $this->core->conf['conf']['formPath'] . "searchgrades.form.php";
	}

	public function showGrades() {
		$academicyear = $this->core->cleanGet['year'];
		$course = $this->core->cleanGet['course'];
		$programme = $this->core->cleanGet['programme'];
		$semester = $this->core->cleanGet['semester'];

		$sql = "SELECT AcademicYear, Semester, ProgramNo, CourseNo, user, b1.FirstName, b1.Surname, Grade, CAMarks, ExamMarks, TotalMarks, b2.ID, b2.FirstName, b2.Surname, `grades`.ID  FROM `grades`
			LEFT JOIN `basic-information` as b1 ON `grades`.`user` = `b1`.ID
			LEFT JOIN `basic-information` as b2 ON `grades`.`StudentNo` = `b2`.ID
			WHERE AcademicYear = '$academicyear'
			AND Semester = '$semester'
			AND CourseNo = '$course'
			ORDER BY `grades`.`AcademicYear`, `grades`.`CourseNo`  ASC";

		$this->viewMenu();
		$this->manageGrades($sql);
	}

	public function accountsGrades($item) {
		$this->studentGrades($item);
	}

	public function studentGrades($item) {
		if(empty($item)){
			$item = $this->core->userID;
		}

		// PAYMENT VERIFICATION FOR GRADES

		include $this->core->conf['conf']['viewPath'] . "payments.view.php";
		$payments = new payments();
		$payments->buildView($this->core);
		$actual = $payments->getBalance($item);
	

		if($actual > 100){
			echo ' <h2>OUTSTANDING BALANCE!</h2><div class="errorpopup">According to our financial records you are owing the institution <u>K'.$actual.'</u>. 
				<br>Please check your payments and settle your balance to be able to access your grades
				<br>  <a href="' . $this->core->conf['conf']['path'] . '/payments/show/'.$item.'">View your recent payments</a> </div>';
			if($this->core->role != 1000){
				return;
			}
		}
		

		$sql = "SELECT AcademicYear, Semester, ProgramNo, CourseNo, user, b1.FirstName, b1.Surname, Grade, CAMarks, ExamMarks, TotalMarks, b2.ID, b2.FirstName, b2.Surname  FROM `grades`
			LEFT JOIN `basic-information` as b1 ON `grades`.`user` = `b1`.ID
			LEFT JOIN `basic-information` as b2 ON `grades`.`StudentNo` = `b2`.ID
			WHERE b2.ID = '$item'
			ORDER BY `grades`.`AcademicYear` DESC";

		$this->viewMenu();
		$this->manageGrades($sql);

	}


	public function courseGrades($item) {
		if(empty($item)){
			$item = $this->core->userID;
		}

		if($this->core->role < 11){
			$item = $this->core->userID;
		}


		$sql = "SELECT AcademicYear, Semester, ProgramNo, CourseNo, user, b1.FirstName, b1.Surname, CAMarks, CAMarks, ExamMarks, TotalMarks, b2.ID, b2.FirstName, b2.Surname  FROM `grades`
			LEFT JOIN `basic-information` as b1 ON `grades`.`user` = `b1`.ID
			LEFT JOIN `basic-information` as b2 ON `grades`.`StudentNo` = `b2`.ID
			WHERE b2.ID = '$item'
			ORDER BY `grades`.`AcademicYear`, `grades`.`CourseNo`  ASC";

		$this->viewMenu();
		$this->manageGrades($sql, TRUE);

	}


	public function personalGrades() {
		$item = $this->core->userID;

		// PAYMENT VERIFICATION FOR GRADES

		include $this->core->conf['conf']['viewPath'] . "payments.view.php";
		$payments = new payments();
		$payments->buildView($this->core);
		$actual = $payments->getBalance($item);

		if($actual > 100){
			echo ' <h2>OUTSTANDING BALANCE!</h2><div class="errorpopup">According to our financial records you are owing the institution <u>K'.$actual.'</u>. 
				<br>Please check your payments and settle your balance to be able to access your grades
				<br>  <a href="' . $this->core->conf['conf']['path'] . '/payments/show/'.$item.'">View your recent payments</a> </div>';


			if($this->core->role != 1000){
				return;
			}
		}

		/* $sql = "SELECT AcademicYear, Semester, ProgramNo, CourseNo, user, b1.FirstName, b1.Surname, Grade, CAMarks, ExamMarks, TotalMarks, b2.ID, b2.FirstName, b2.Surname  FROM `grades`
			LEFT JOIN `basic-information` as b1 ON `grades`.`user` = `b1`.ID
			LEFT JOIN `basic-information` as b2 ON `grades`.`StudentNo` = `b2`.ID
			WHERE b2.ID = '$item'
			ORDER BY `grades`.`AcademicYear`, `grades`.`CourseNo`  ASC";

		$this->viewMenu();
		$this->manageGrades($sql); */


		$sql = "SELECT * FROM `basic-information`WHERE ID = '$item'";
		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_assoc()) {
			$status = $fetch['Status'];
		}

		if($status == "Locked"){
			echo'<div class="errorpopup">Your account is locked because you have not fully registered. Complete your registration first.</div>';
			return;
		}

		include $this->core->conf['conf']['viewPath'] . "statement.view.php";
		$statement = new statement();
		$statement->buildView($this->core);
		$statement->resultsStatement($item);

	} 

	public function manageGrades($sql, $ca) {

		$user = $this->core->userID;

		if($this->core->role == 1000 && empty($sql)){
			$sql = "SELECT AcademicYear, Semester, ProgramNo, CourseNo, user, FirstName, Surname, count(StudentNo), userdate  FROM `grades`
			LEFT JOIN `basic-information` ON `grades`.user = `basic-information`.ID
			GROUP BY `grades`.`AcademicYear`, `grades`.`Semester`, `grades`.`ProgramNo`, `grades`.`CourseNo`, `grades`.`user`, `grades`.`userdate` ASC";
			$noedit=false;
		}else if(empty($sql)){
			$sql = "SELECT AcademicYear, Semester, ProgramNo, CourseNo, user, FirstName, Surname, count(StudentNo), userdate  FROM `grades`
			LEFT JOIN `basic-information` ON `grades`.user = `basic-information`.ID
		
			GROUP BY `grades`.`AcademicYear`, `grades`.`CourseNo` ASC";
			$noedit=true;
		} else {
			$noedit=true;
		}

		$run = $this->core->database->doSelectQuery($sql);




		$init = FALSE;


		while ($fetch = $run->fetch_row()) {

			$validator = $fetch[6];
			$did = $fetch[4];
			$firstname = $fetch[5];
			$lastname = $fetch[6];
			$studentno = $fetch[11];
			$count = $fetch[7];
			$coursename = $fetch[3];
			$programme = $fetch['2'];
			$semester = $fetch[1];
			$year = $fetch[0];
			$date = $fetch[8];
			$uid = $fetch[2];

			if($count == "" ){
				continue;
			}


			if(isset($fetch[12])){
				$studentname = $fetch[12] . " " . $fetch[13];
			}

			if($init == FALSE){
				echo	'<div style="border:solid 0px #ccc; padding-left: 10px; margin-bottom: 4px;">
				<table width="100%">' .
				'<tr>' .
				'<td width="100px"><b>Course:</b></td>';
				if(isset($studentname)){
					echo '<td><b>Student:</b></td>';
				}

				if($ca == TRUE){ $td = "Course Mark"; } else { $td = "Grades"; }

				echo '<td width="100px" style="text-align: right;"><b>'.$td.'</b></td>' .
				'<td width="70px"><b>Year</b></td>' .
				'<td width="150px"><b>Submitted by</b></td>';

				if($this->core->role == 107 || $this->core->role == 1000){
					echo '<td width="100px"><b>Management</b></td>';
				}

				echo '</tr>';

				$init = TRUE;
			}

			
			if($year != $curyear && $this->core->action != "manage"){
				$curyear = $year;
				echo '<tr class="heading"><td colspan="7" ><b>YEAR '.$year.' RESULTS</b></td></tr>';
			} else if($year != $curyear && $this->core->action == "manage"){
				$curyear = $year;
				echo '<tr class="heading"><td colspan="7" ><b>YEAR '.$year.' RESULTS</b></td></tr>';
			}

			if($count>1){
				$type = "grades";
			} else {
				$type = "grade";
			}

			if($ca == TRUE){
				$type = "CA mark";
			}

			if($this->core->role == 10){
				$link = $this->core->conf['conf']['path'] . '/courses/show/?course=' . urlencode($coursename);
			}else{
				$link = $this->core->conf['conf']['path'] . '/grades/show/?course=' . urlencode($coursename) . '&programme=' . urlencode($programme) . '&year=' . urlencode($year) . '&semester=' . urlencode($semester);
			}

			echo '<tr><td><a href="'.$link.'"><b>' . $coursename . ' </b> </a></td>';

			if(isset($studentname) && $this->core->role != 10){
				echo '<td><a href="' . $this->core->conf['conf']['path'] . '/information/show/'.$studentno.'">'.$studentname.'</a></td>';
			} else {
				echo '<td><b>'.$studentname.'</b></td>';
			}


			echo'<td style="text-align: right;"><b>'.$count.'</b> '. $type .'</td>';


			if($this->core->role == 10){
		
				echo'<td>' . $semester . '</td>';
			}else{
				echo'<td><a href="' . $this->core->conf['conf']['path'] . '/grades/year/' . $semester. '"> ' . $year . '</a></td>';
			}


			echo '<td><a href="' . $this->core->conf['conf']['path'] . '/information/show/' . $did . '">' . $firstname . ' ' . $lastname . '</a></td>';


			if($this->core->role == 107 || $this->core->role == 1000){
				echo'<td><a href="' . $this->core->conf['conf']['path'] . '/grades/edit/' . urlencode($fetch[2]) . '"> <img src="'.$this->core->fullTemplatePath.'/images/edi.png"> edit</a>
				<a href="' . $this->core->conf['conf']['path'] . '/grades/delete/' . urlencode($fetch[2]) . '" onclick="return confirm(\'Are you sure?\')"> <img src="'.$this->core->fullTemplatePath.'/images/del.png"> delete </a></td>';
			}else{
				
			}



		}


		if($init == TRUE){
			echo '</tr>' .
			'</table>';
			echo'</div>';
		} else {

			echo'<div class="successpopup">No grades are available for you yet. Please check again after exam time.</div>';
		}

		
	}


	public function shortGrades($sql, $ca) {

		$user = $this->core->userID;

		if($this->core->role == 1000 && empty($sql)){
			$sql = "SELECT AcademicYear, Semester, ProgramNo, CourseNo, user, FirstName, Surname, count(StudentNo), userdate  FROM `grades`
			LEFT JOIN `basic-information` ON `grades`.user = `basic-information`.ID
			GROUP BY `grades`.`AcademicYear`, `grades`.`Semester`, `grades`.`ProgramNo`, `grades`.`CourseNo`, `grades`.`user`, `grades`.`userdate` ASC";
			$noedit=false;
		}else if(empty($sql)){
			$sql = "SELECT AcademicYear, Semester, ProgramNo, CourseNo, user, FirstName, Surname, count(StudentNo), userdate  FROM `grades`
			LEFT JOIN `basic-information` ON `grades`.user = `basic-information`.ID
		
			GROUP BY `grades`.`AcademicYear`, `grades`.`CourseNo` ASC";
			$noedit=true;
		} else {
			$noedit=true;
		}

		$run = $this->core->database->doSelectQuery($sql);




		$init = FALSE;


		while ($fetch = $run->fetch_row()) {

			$validator = $fetch[6];
			$did = $fetch[4];
			$firstname = $fetch[5];
			$lastname = $fetch[6];
			$studentno = $fetch[11];
			$count = $fetch[7];
			$coursename = $fetch[3];
			$programme = $fetch['2'];
			$semester = $fetch[1];
			$year = $fetch[0];
			$date = $fetch[8];
			$uid = $fetch[2];

			if($count == "" ){
				continue;
			}


			if(isset($fetch[12])){
				$studentname = $fetch[12] . " " . $fetch[13];
			}

			if($init == FALSE){
				echo	'<div style="border:solid 0px #ccc; padding-left: 10px; margin-bottom: 4px;">
				<table width="100%">' .
				'<tr>' .
				'<td width="30%"><b>Course:</b></td>';

				if($ca == TRUE){ $td = "Course Mark"; } else { $td = "Grades"; }

				echo '<td width="30%"><b>'.$td.'</b></td>' .
				'<td width="30%"><b>Year</b></td>';

				echo '</tr>';

				$init = TRUE;
			}




			echo '<tr><td><a href="'.$link.'"><b>' . $coursename . ' </b> </a></td>';
			echo'<td><b>'.$count.'</b> '. $type .'</td>';


			if($this->core->role == 10){
		
				echo'<td>' . $semester . '</td>';
			}else{
				echo'<td><a href="' . $this->core->conf['conf']['path'] . '/grades/year/' . $semester. '"> ' . $year . '</a></td>';
			}




		}


		if($init == TRUE){
			echo '</tr>' .
			'</table>';
			echo'</div>';
		} else {

			echo'<div class="successpopup">No grades are available for you yet. Please check again after exam time.</div>';
		}

		
	}

	private function enterGrades($programselected, $courseselected, $year) {
		if (isset($programselected) && isset($courseselected)) {

			$sql = 'SELECT * FROM `basic-information`, `student-program-link`, `programmes-link`
				WHERE `programmes-link`.ID = `student-program-link`.ProgrammeID
				AND `student-program-link` .StudentID = `basic-information`.ID  
				AND Major = "'.$programselected.'" 
				AND `basic-information`.ID LIKE "'.$year.'%"
				OR `programmes-link`.ID = `student-program-link`.ProgrammeID
				AND `student-program-link` .StudentID = `basic-information`.ID  
				AND Minor = "'.$programselected.'" 
				AND `basic-information`.ID LIKE "'.$year.'%"
				ORDER BY Surname';
		}

		$run = $this->core->database->doSelectQuery($sql);
		$validator = mt_rand(100000, 9999999999999999);

		echo '<p><b>Enter grades </b></p><p>
		<form id="login" name="login" method="post" action="'.$this->core->conf['conf']['path'].'/grades/submit">
		<input type="hidden" name="id" value="grades-submit">
		<input type="hidden" name="validator" value="' . $validator . '">
		<input type="hidden" name="course" value="' . $courseselected . '">
		<input type="hidden" name="program" value="' . $programselected . '">
		<table width="768" height="" border="0" cellpadding="0" cellspacing="0">
		<tr class="tableheader">
		<td width="20px"></td>
		<td><b>Student name</b></td>
		<td width="120px"><b>Student number</b></td>
		<td width="80px"><b>Course mark</b></td>
		<td width="80px"><b>Exam mark</b></td>
		<td width="80px"><b>Total mark</b></td>
		<td width="80px"><b>Grade</b></td>
		</tr>';

		while ($fetch = $run->fetch_row()) {
			echo '<tr>'.
			'<td><img src="'.$this->core->fullTemplatePath.'/images/bullet_user.png"></td>'.
			'<td><b><a href="' . $this->core->conf['conf']['path'] . '/information/show/' . $fetch[4] . '">' . $fetch[0] . ' ' . $fetch[2] . '</a></b></td>' .
			'<td>' . $fetch[4] . '</td>' .
			'<td><input type="textbox" name="c' . $fetch[4] . '" size="4" class="submit"></td>' .
			'<td><input type="textbox" name="g' . $fetch[4] . '" size="4" class="submit"></td>' .
			'<td><input type="textbox" name="t' . $fetch[4] . '" size="4" class="submit"></td>' .
			'<td><input type="textbox" name="l' . $fetch[4] . '" size="4" class="submit"></td>' .
			'</tr>';
		}

		echo '</table>
		<br><hr><br><input type="submit" value="Submit grades to board of studies" />
		</form></p>';
	}


	public function selectcourseGrades() {
		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";

		$programselected = $this->core->cleanPost['program'];
		$courseselected = $this->core->cleanPost['course'];
		$year = $this->core->cleanPost['year'];

		$select = new optionBuilder($this->core);
		$program = $select->showPrograms(null, $programselected);
		$courses = $select->showCoursesV($programselected, null);

		echo'<div class="heading">' . $this->core->translate("Upload by course") . ' </div>';

		if (!isset($courseselected)) {

			echo '<p><form id="selectcourse" name="selectcourse" method="POST" action="'.$this->core->conf['conf']['path'].'/grades/selectcourse">

			<div class="label">Show all students from: </div>
			<select name="program" id="program" class="submit" width="250" style="width: 250px">
				' . $program . '
			</select>

			<br />

			<div class="label">Show all students from YEAR: </div>
			<select name="year" id="year" class="submit" width="250" style="width: 250px">
				 <option value="'.$year.'">'.$year.'</option>
				 <option value="2008">2008</option>
				 <option value="2009">2009</option>
				 <option value="2010">2010</option>
				 <option value="2011">2011</option>
				 <option value="2012">2012</option>
				 <option value="2013">2013</option>
				 <option value="2014">2014</option>
				 <option value="2015">2015</option>
				 <option value="2016">2016</option>
				 <option value="2017">2017</option>
				 <option value="2018">2018</option>
			</select>';

			if (isset($programselected)) {
				echo '<br />
				<div class="label">And submit grades for: </div>
				<select name="course" id="course" class="submit" width="250" style="width: 250px">
					' . $courses . '
				</select>';
			}

			echo '<br />
			<div class="label"> </div>
			<input type="button" value="Reset" class="submit" name="reset" onclick="history.back(-1)" /> <input type="submit" value="Next step" class="submit" />
			</form></p>';
		

			echo'<div class="heading">' . $this->core->translate("Upload results by file") . ' </div>';
			echo '<p><form id="upload" name="upload" method="POST" action="'.$this->core->conf['conf']['path'].'/grades/upload" enctype="multipart/form-data">
			<div class="label" style="float:left;">Upload CSV file: </div>
			<div style="float:left; width:200px"><input type="file" name="grades" id="grades" class="submit" /></div>
			<br><div class="label" style="clear:both;"> </div><br>

			<div class="label" style="clear:left;"> Type of results being uploaded </div>
			<select name="type" id="type" class="submit" width="250" style="width: 250px">
				 <option value="course">ONLY Course marks</option> 
				 <option value="onlyexam">ONLY Exam marks</option>
				 <option value="both">BOTH Course and Exam marks</option>
			</select>

			<br><br><br><div class="label" style="clear:both;"> </div>
			<input type="submit" value="Begin upload" class="submit" />
			</form></p>';

		} 


		if (isset($programselected) && isset($courseselected) && isset($year)) {
			$this->enterGrades($programselected, $courseselected, $year);
		}
	}



	public function uploadGrades() {

		if (isset($_FILES["grades"])) {
			
			$file = $_FILES["grades"];
			$type = $this->core->cleanPost["type"];
			$home = getcwd();

			if($type == "course"){
				$path = $this->core->conf['conf']['dataStorePath'] . 'tmp/grades/course';
			}else if($type == "onlyexam"){
				$path = $this->core->conf['conf']['dataStorePath'] . 'tmp/grades/exam';		
			}else if($type == "both"){
				$path = $this->core->conf['conf']['dataStorePath'] . 'tmp/grades/all';	
			}


			if (!is_dir($path)) {
				mkdir($path, 0755, true);
			}

			$filename = $path . "/" . $file["grades"]["name"];

			if ($_FILES["grades"]["error"] > 0) {
				echo "Error: " . $_FILES["grades"]["error"] . "<br>";
			} else {
				$rand = mt_rand(1,1000);
				$filename = $path . "/".$rand."-" . $_FILES["grades"]["name"];

				while (file_exists($filename)) {
					$rand = mt_rand(1,1000);
					$filename = $path . "/".$rand."-" . $_FILES["grades"]["name"];
				}

				if (file_exists($filename)) {
					echo "ERROR THIS FILE ALREADY EXISTS: $filename";
				} else{
					move_uploaded_file($_FILES["grades"]["tmp_name"], $filename);

					echo'<div class="successpopup">Upload of file: "'.$filename.'" succeeded.<br> Contact ICT to run import.</div><br>';
				}
			}
		}
	}

	public function submitGrades() {
		$salt = sha1(md5(date('YmdH') . $this->core->username . $this->core->userid . $this->core->role . $this->core->cleanPost['course']));
		$sql = "START TRANSACTION;";
		$this->core->database->doInsertQuery($sql);

		$output = '<table width="768" height="" border="0" cellpadding="3" cellspacing="0">
		<tr class="tableheader">
		<td width="400"><b>Student number</b></td>
		<td><b>Grade submitted</b></td>
		</tr>';

		$programid = $this->core->cleanPost['program'];
		$courseid = $this->core->cleanPost['course'];
		$validatorid = $this->core->cleanPost['validator'];

		$sql = 'INSERT INTO `gradebatches` (`ID`, `GlobalHash`, `Owner`, `Status`, `DateTime`, `Course`, `ValidatorID`) VALUES (NULL, "' . $salt . '", "' . $this->core->userID . '",  "1", NULL, "' . $courseid . '", "' . $validatorid . '");';

		if ($this->core->database->doInsertQuery($sql)) {

			foreach ($_POST as $student => $grade) {
				if ($grade != "" && $grade != "grades-submit" && $student != "course" && $student != "program" && $student != "validator") {
					$studentids[substr($student, 1)][substr($student, 0,1)] = $grade;
				}
			}

			foreach ($studentids as $studentidd => $grade) {
					$coursemark = $grade['c'];
					$exammark = $grade['g'];
					$totalmark = $grade['t'];
					$lettermark = $grade['l'];

					$date = date('YmdH');
					$hash = sha1("$totalmark$student$coursemark$exammark$date$salt");

					$output = $output . "<tr><td><b>" . $studentidd. "</b></td><td>" . $coursemark . "," . $exammark . "," . $totalmark . " <b>" . $lettermark . "</td></tr>";
					$year = date(Y);
					$enrollmentyear = substr($studentidd, 0, 4);
					$yt = $year-$enrollmentyear;

					$sql = "INSERT INTO `grades` (`ID`, `user`, `userdate`, `usertime`, `StudentNo`, `AcademicYear`, `Semester`, `ProgramNo`, `CourseNo`, `CAMarks`, `ExamMarks`, `TotalMarks`, `Grade`, `Points`) 
					VALUES (NULL, '".$this->core->userID."', CURRENT_DATE(), CURRENT_TIME(), '$studentidd', '$year', '$yt', '$programid', '$courseid', '$coursemark', '$exammark', '$totalmark', '$lettermark', '$hash/$batch');";

					$this->core->database->doInsertQuery($sql);
				
			}

			$sql = "COMMIT;";

			$this->core->logEvent("Grades submitted $courseid - $hash", "4");

			if ($this->core->database->doInsertQuery($sql)) {
				echo $output . "</table><p><b>All grades have been submitted for approval.</b></p>";
			} else {
				$this->core->throwerror("ERROR UNKNOWN");
			}

		} else {
			$this->core->throwerror("Please continue home");
		}
	}


	function listGrades() {

		$date = $this->core->cleanGet['date'];

		if($date == ""){
			$date = '2017-06-06';
		}

		$sql = "SELECT *, COUNT(`CourseNo`) as COUNT
			FROM `grades`, `courses`
			WHERE `UserDate` > '$date' 
			AND `courses`.Name = `grades`.CourseNo
			GROUP BY `CourseNo` ORDER BY `CourseNo` ASC";

		$run = $this->core->database->doSelectQuery($sql);

		$current = date('Y-m-d');

		echo'<form method="GET" action="">
			<div class="label">Start date to show grades: </div>
			<input type="text" class="submit" name="date" value="'.$date.'">
			<input class="submit" type="submit" value="Filter">
			</form><br><br>';

		echo '<h2>Selected Time Frame: '.$date.' to '.$current.'</h2><br>';

		echo'<table class="table table-bordered table-striped table-hover">
			<thead>
			<tr class="heading">
				<td>#</td>
				<td>Course Code</td>
				<td>Course Name</td>
				<td>Uploaded results</td>
			</tr>
			</thead>';

		$i = 1;

		while ($fetch = $run->fetch_assoc()) {
			$count = $fetch['COUNT'];
			$name = $fetch['CourseNo'];
			$description = $fetch['CourseDescription'];

			if($name == ""){ continue; }

			echo '<tr>
				<td>'.$i.'</td>
				<td><b><a href="'.$this->core->conf['conf']['path'].'/grades/consignment/'.urlencode($name).'?date='.$date.'">'.$name.'</a></b></td>
				<td><b>'.$description.'</b></td>
				<td>'.$count.'</td>
				</tr>';
			$i++;
		}

		echo'</table>';

	}


	public function consignmentGrades($item){
		echo'<div class="toolbar">
			<a href="'.$this->core->conf['conf']['path'].'/grades/list">Back to Results Overview</a>
		</div>';

		$course = urldecode($item);
		$date = $this->core->cleanGet['date'];

		if($date == ""){
			$date = '2017-06-06';
		}

		$sql = "SELECT * FROM `grades`
			WHERE `UserDate` > '$date' 
			AND `CourseNo` = '$course'
			ORDER BY `StudentNo` ASC";

		$run = $this->core->database->doSelectQuery($sql);

		$current = date('Y-m-d');

		echo'<form method="GET" action="">
			<div class="label">Start date to show grades: </div>
			<input type="text" class="submit" name="date" value="'.$date.'">
			<input class="submit" type="submit" value="Filter">
			</form><br><br>';

		echo '<h2>Selected Time Frame: '.$date.' to '.$current.'</h2><br>';

		echo'<table class="table table-bordered table-striped table-hover">
			<thead>
			<tr class="heading">	
				<td>#</td>
				<td>Student</td>
				<td>Course Code</td>
				<td>Course Mark</td>
				<td>Exam Mark</td>
				<td>Total Mark</td>
				<td>Grade</td>
			</tr>
			</thead>';

		$i = 1;

		while ($fetch = $run->fetch_assoc()) {
			$course = $fetch['CourseNo'];
			$student = $fetch['StudentNo'];
			$ca = $fetch['CAMarks'];
			$exam = $fetch['ExamMarks'];
			$total = $fetch['TotalMarks'];
			$grade = $fetch['Grade'];

			echo '<tr>
				<td>'.$i.'</td>
				<td>'.$student.'</td>
				<td><b>'.$course.'</td>
				<td>'.$ca.'</td>
				<td>'.$exam.'</td>
				<td>'.$total.'</td>
				<td><b>'.$grade.'</b></td>
				</tr>';
			$i++;
		}

		echo'</table>';


	}


}

?>
