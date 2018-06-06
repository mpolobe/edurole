<?php
class assignments {

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
		include $this->core->conf['conf']['classPath'] . "files.inc.php";
	}

	function showAssignments() {
		$user = $this->core->userID;

		$sql = "SELECT *, assignments.ID as `AID`
		FROM `assignments`, `basic-information` as `bi`, `course-electives`, `basic-information` as `ci`, `courses`
		WHERE `bi`.ID = '$user'
		AND `course-electives`.StudentID = `bi`.ID
		AND `assignments`.CourseID = `courses`.ID  
		AND `course-electives`.CourseID = `assignments`.CourseID
		AND `ci`.ID = `assignments`.CreatorID
		ORDER BY `DateCreated`";

		$run = $this->core->database->doSelectQuery($sql);

		$init = TRUE;

		while ($fetch = $run->fetch_assoc()) {

			$firstname = $fetch['FirstName'];
			$lastname = $fetch['Surname'];
			$assignmentid = $fetch['AID'];
			$assignmentname = $fetch['AssignmentName'];
			$assignmentfile = $fetch['AssignmentFiles'];
			$assignmentdescription = $fetch['AssignmentDescription'];	
			$upload = $fetch['UploadNeeded'];
			$uid = $fetch['CreatorID'];
			$date = $fetch['DateCreated'];
			$deadline = $fetch['SubmissionDeadline'];

			$sqlx = "SELECT * FROM  `assignments-uploaded` WHERE `AssignmentID` = '$assignmentid'";
			$runx = $this->core->database->doSelectQuery($sqlx);


			echo '<div style="border:solid 1px #ccc; padding-left: 10px; margin-bottom: 4px;">
				<table width="756">' .
				'<tr>' .
				'<td width="200px"><b>Assignment name:</b></td>' .
				'<td width="150px"><b>Assigned by</b></td>' .
				'<td width="200px"><b>Deadline for submission</b></td>' .
				'<td width="100px"><b></td>' .
				'</tr><tr>' .
				'<td><a href="' . $this->core->conf['conf']['path'] . '/assignments/view/' . $assignmentid . '"><b>' . $assignmentname . '</b></a></td>' .
				'<td><b>' . $firstname . ' ' . $lastname . '</b></td>' .
				'<td>' . $deadline . '</td>';

				if($runx->num_rows > 0){
					echo'<td>
					<b>You have submitted your assignment</b>
					</td>';
				}elseif($upload == "1"){
					echo'<td>
					<b><a href="' . $this->core->conf['conf']['path'] . '/assignments/submit/' . $assignmentid  . '"> Submit result </a></b>
					</td>';
				}else{
					echo'<td>
					<b></b>
					</td>';
				}

				'</tr></table></div>';

		}

		echo '</table></div></p>';

	}

	function submitAssignments($item) {

		$comment = $this->core->cleanPost['comment'];

		$studentid = $this->core->userID;


		if (isset($_FILES["file"])) {

			$file = $_FILES["file"];
		
			$home = getcwd();
			$path = $this->core->conf['conf']["dataStorePath"] . 'uploads/assignments/' . $item;

			if (!is_dir($path)) {
				mkdir($path, 0755, true);
			}
		
			if ($_FILES["file"]["error"] > 0) {
				echo "Error: " . $file["error"]["file"] . "<br>";
			} else {
		
				$fname = $_FILES["file"]["name"];
				$fname = $studentid . '-' .$fname;
				$destination = $path."/".$fname;
				

				move_uploaded_file($_FILES["file"]["tmp_name"], $destination);
				
				if(file_exists($destination)){
					echo'<div class="successpopup">YOUR ASSIGNMENT HAS BEEN UPLOADED</div>';

					$sql = "INSERT INTO `assignments-uploaded` (`ID`, `StudentID`, `AssignmentID`, `Filename`, `DateTime`, `Grade`, `Approved`, `Feedback`, `Comment`) 
					VALUES (NULL, '$studentid', '$item', '$fname', NOW(), '0', '0', '', '$comment');";

					$run = $this->core->database->doInsertQuery($sql);

					
				}
			}

		} else {

			echo'<h2>Upload your assignment here.<h2><br>';

				echo'<form id="assignments" name="assignments" enctype="multipart/form-data"  method="post" action="'. $this->core->conf['conf']['path'] . '/assignments/submit/'. $item .'">
				<div class="label">File: </div><div class="label"><input type="file" name="file" accept=".pdf,*.doc,*.docx"></div><br><br>
				<div class="label">Comment: </div><textarea name="comment" cols="40" rows="5"></textarea><br><br>

				<input type="submit" value="Upload your assignment">
			</form>';

		}
	}

	function editAssignments($item) {

		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
		$optionBuilder = new optionBuilder($this->core);

		$internal = $optionBuilder->showUsers(99);
		$distance = $optionBuilder->showUsers(99);

		include $this->core->conf['conf']['formPath'] . "addassignment.form.php";

	}

	function saveAssignments($item) {
		$course = $item;
		$user = $this->core->userID;

		$name = $this->core->cleanPost['name'];
		$description = $this->core->cleanPost['description'];
		$deadline = $this->core->cleanPost['deadline'];
		$links = $this->core->cleanPost['links'];
		$upload = $this->core->cleanPost['upload'];

		if (isset($_FILES["file"])) {

			$file = $_FILES["file"];
		
			$home = getcwd();
			$path = $this->core->conf['conf']["dataStorePath"] . 'uploads/' . $course;

	
		
			if (!is_dir($path)) {
				mkdir($path, 0755, true);
			}
		
			if ($_FILES["file"]["error"] > 0) {
				echo "Error: " . $file["error"]["file"] . "<br>";
			} else {
		
				$fname = $_FILES["file"]["name"];
				$destination = $path."/".$fname;
		
				if (file_exists($destination)) {
					$fname = rand(1,999) . '-' .$fname;
					$destination = $path."/".$fname;
				}

				move_uploaded_file($_FILES["file"]["tmp_name"], $destination);
				
				if(file_exists($destination)){
					echo'<div class="successpopup">File uploaded as '.$fname.'</div>';
				}
			}
		}

		foreach($links as $link){
			$linked = $link . ',';
		}
		
		$base = $this->core->conf['conf']['path'] . '/datastore/uploads/' . $item . '/'. $fname;

		$sql = "INSERT INTO `assignments` (`ID`, `CreatorID`, `AssignmentName`, `AssignmentDescription`, `CourseID`, `ProgrammeID`, `StudyID`, `AssignmentWeight`, `UploadNeeded`, `AssignmentFiles`, `AssignmentLinks`, `DateCreated`, `SubmissionDeadline`, `ExamID`) 
			VALUES (NULL, '$user', '$name', '$description', '$course', '', '', '$weight', '$upload', '$base', '$linked', CURRENT_TIMESTAMP, '$deadline', '');";

		echo'<div class="successpopup">Assignment created</div>';

		$run = $this->core->database->doInsertQuery($sql);

		$this->courseAssignments($item);
	}

	function addAssignments($item) {

		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
		$optionBuilder = new optionBuilder($this->core);

		$internal = $optionBuilder->showUsers(99);
		$distance = $optionBuilder->showUsers(99);

		include $this->core->conf['conf']['formPath'] . "addassignment.form.php";

	}

	function courseAssignments($item) {
		
		echo'<div class="toolbar">
			<a href="' . $this->core->conf['conf']['path'] . '/assignments/add/'.$item.'">Add New Assignment</a>
			</div>';

		$sql = "SELECT *, `assignments`.ID as AID FROM `assignments` 
			LEFT JOIN `courses` ON `assignments`.CourseID = `courses`.ID  
			LEFT JOIN `basic-information` ON `basic-information`.ID = `assignments`.CreatorID 
			WHERE `assignments`.CourseID = '$item'
			ORDER BY DateCreated";

		$run = $this->core->database->doSelectQuery($sql);

		echo'<table class=".table-hover" width="100%">
			<tr class="heading">
			<td>Date Added</td>
			<td>Name Assignment</td>
			<td>Deadline Date</td>
			<td>Uploaded By</td>
			<td>Options</td>
			</tr>';

		while ($fetch = $run->fetch_assoc()) {

			$id = $fetch['AID'];
			$name = $fetch['AssignmentName'];
			$files = $fetch['AssignmentFiles'];
			$description = $fetch['AssignmentDescription'];
			$creator = $fetch['CreatorID'];
			$deadline = $fetch['SubmissionDeadline'];
			$date = $fetch['DateCreated'];

			$creator = $fetch['FirstName'] . ' ' . $fetch['Surname'];

			echo'<tr>
				<td>'.$date.'</td>
				<td><a href="' . $this->core->conf['conf']['path'] . '/assignments/view/' . $id . '"><b>'.$name.'</b></a></td>
				<td>'.$deadline.'</td>
				<td>'.$creator.'</td>
				<td>
				<a href="' . $this->core->conf['conf']['path'] . '/assignments/edit/' . $id . '"> <img src="' . $this->core->fullTemplatePath . '/images/edi.png"> Edit</a>
				<a href="' . $this->core->conf['conf']['path'] . '/assignments/delete/'.$item.'/' . $id . '"> <img src="' . $this->core->fullTemplatePath . '/images/del.png"> Delete</a>
				</td>
			</tr>';

			$set = TRUE;
		}

		echo'</table>';


		if($set != TRUE){
			echo'<div class="warningpopup">No assignments exist for this course</div>';
		}

	}

	function deleteAssignments($item) {

		$assignment = $this->core->subitem;

		$sql = "DELETE FROM `assignments` WHERE `ID` = '$assignment'";
		$run = $this->core->database->doInsertQuery($sql);

		echo'<div class="successpopup">Assignment Deleted</div>';

		$this->courseAssignments($item);

	}

	function viewAssignments($item) {
		
		if($this->core->role >= 100){
			echo'<div class="toolbar">
			<a href="' . $this->core->conf['conf']['path'] . '/assignments/results/'.$item.'">View Uploaded Results</a>
			</div>';
		}

		$sql = "SELECT *, `assignments`.ID as AID FROM `assignments` 
			LEFT JOIN `courses` ON `assignments`.CourseID = `courses`.ID  
			LEFT JOIN `basic-information` ON `basic-information`.ID = `assignments`.CreatorID 
			WHERE `assignments`.ID = '$item'";

		$run = $this->core->database->doSelectQuery($sql);

		echo'<table class=".table-hover" width="100%">
			<tr class="heading">
			<td width="150px">Field</td>
			<td>Details</td>
			</tr>';

		while ($fetch = $run->fetch_assoc()) {

			$id = $fetch['AID'];
			$name = $fetch['AssignmentName'];
			$files = $fetch['AssignmentFiles'];
			$description = nl2br($fetch['AssignmentDescription']);
			$creator = $fetch['CreatorID'];
			$deadline = $fetch['SubmissionDeadline'];
			$date = $fetch['DateCreated'];

			$creator = $fetch['FirstName'] . ' ' . $fetch['Surname'];

			echo'<tr>
				<td><b>Date Created:</b></td>
				<td>'.$date.'</td>
			</tr>
			<tr>
				<td><b>Assignment Name:</b></td>
				<td><b>'.$name.'</b></td>
			</tr>
			<tr>
				<td><b>Deadline Date:</b></td>
				<td>'.$deadline.'</td>
			</tr>
			<tr>
				<td><b>Created By:</b></td>
				<td><b>'.$creator.'</b></td>
			</tr>
			<tr class="heading">
				<td><b>Assignment Details:</b></td>
				<td style="background-color: #ccc solid 1px; padding-top: 20px; padding-bottom: 20px; font-size: 13px;"><b>'.$description.'</b></td>
			</tr>
			<tr>
				<td><b>Assignment Files:</b></td>
				<td><b><a href="'.$files.'">DOWNLOAD ATTACHED FILE</a></b></td>
			</tr>';

			$set = TRUE;
		}

		echo'</table>';


		if($set != TRUE){
			echo'<div class="warningpopup">No assignments exist for this course</div>';
		}

	}


	function manageAssignments() {
		$user = $this->core->userID;

		if($this->core->role == 1000){
			$sql = "SELECT `courses`.*, `bi`.ID AS CID, `bi`.FirstName, `bi`.Surname, `courses`.Name as namesd 
			FROM `courses` 
			LEFT JOIN `basic-information` as `bi` ON `courses`.CourseCoordinatorInternal = `bi`.ID  
			ORDER BY namesd";
		}else{
			$sql = "SELECT `courses`.*, `bi`.ID AS CID, `bi`.FirstName, `bi`.Surname, `courses`.Name as namesd 
			FROM `courses` 
			LEFT JOIN `basic-information` as `bi` ON `courses`.CourseCoordinatorInternal = `bi`.ID 
			WHERE `courses`.CourseCoordinatorInternal = '$user'
			ORDER BY namesd";
		}


		$run = $this->core->database->doSelectQuery($sql);

		echo '<table width="768" height="" border="0" cellpadding="3" cellspacing="0">
          		 <tr class="tableheader"><td width="400"><b>Course Name</b></td>' .
			'<td><b>Course Coordinator</b></td>' .
			'<td><b>Students</b></td>' .
			'<td><b>Options</b></td>' .
			'</tr>';

		$i = 0;
		while ($fetch = $run->fetch_assoc()) {
			$id = $fetch['ID'];
			$count = "";

			$sql = "SELECT COUNT(`StudentID`) as CT 
				FROM `course-electives`, `periods` 
				WHERE `course-electives`.CourseID = '$id' 
				AND CURDATE() BETWEEN `PeriodStartDate` AND  `PeriodEndDate`
				AND `course-electives`.PeriodID = `periods`.ID";

			$runx = $this->core->database->doSelectQuery($sql);

			while ($fetchx = $runx->fetch_assoc()) {
				$count = $fetchx['CT'];
			}


			$count = "<b>" . $count . " students</b>";
			

			echo '<tr ' . $bgc . '>
                    <td><b><a href="' . $this->core->conf['conf']['path'] . '/courses/show/' . $fetch['ID'] . '"> ' . $fetch['Name'] . ' - ' . $fetch['CourseDescription'] . '</a></b></td>
                    <td>
                    <a href="' . $this->core->conf['conf']['path'] . '/information/show/' . $fetch[5] . '">' . $fetch['FirstName'] . ' ' . $fetch[Surname] . '</a>
                    </td>
                    <td>
                    <a href="' . $this->core->conf['conf']['path'] . '/information/search?search=course&q=' . $fetch['ID'] . '">'.$count.'</a>
                    </td>
                    <td>
                    <a href="' . $this->core->conf['conf']['path'] . '/assignments/course/' . $fetch['ID'] . '"> <img src="' . $this->core->fullTemplatePath . '/images/list.gif"> Manage Assignments</a>
                     </td>
                    </tr>';
		}

		echo '</table>';
	}
}

?>
