<?php
class approval {

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

	public function menuApproval() {
		
		$sql = 'SELECT `basic-information`.StudyType, COUNT(DISTINCT `course-electives`.StudentID) FROM `course-electives`, `basic-information`
			WHERE `course-electives`.StudentID = `basic-information`.ID
			GROUP BY `basic-information`.StudyType ORDER BY StudyType';

		$run = $this->core->database->doSelectQuery($sql);
		$i=0;

		echo '<div class="toolbar">';

		while ($fetch = $run->fetch_row()) {
			echo '<a href="' . $this->core->conf['conf']['path'] . '/approval/'.$fetch[0].'">'.ucwords($fetch[0]).' students ('.$fetch[1].')</a>';
		}

		echo'</div>';
	}


	public function fulltimeApproval() {
		$this->menuApproval();
		$this->manageApproval("Fulltime");
	}

	public function distanceApproval() {
		$this->menuApproval();
		$this->manageApproval("Distance");
	}

	public function partimeApproval() {
		$this->menuApproval();
		$this->manageApproval("Partime");
	}

	public function blockApproval() {
		$this->menuApproval();
		$this->manageApproval("Block");
	}

	public function approveApproval($item) {
		$elective = $this->core->subitem;

		if($elective == "all"){
			$sql = "UPDATE `course-electives` SET `Approved` = '1' WHERE `course-electives`.`StudentID` = $item;";
		}else{
			$sql = "UPDATE `course-electives` SET `Approved` = '1' WHERE `course-electives`.`ID` = $elective;";
		}

		$run = $this->core->database->doInsertQuery($sql);

		$this->core->redirect("approval", "show", $item);
	}

	public function rejectApproval($item) {
		$elective = $this->core->subitem;

		if($elective == "all"){
			$sql = "UPDATE `course-electives` SET `Approved` = '2' WHERE `course-electives`.`StudentID` = $item;";
		}else{
			$sql = "UPDATE `course-electives` SET `Approved` = '2' WHERE `course-electives`.`ID` = $elective;";
		}

		$run = $this->core->database->doInsertQuery($sql);

		$this->core->redirect("approval", "show", $item);
	}

	public function showApproval($item) {

		$sql = "SELECT DISTINCT   `courses`.ID as CEID, `course-electives`.`StudentID`, `courses`.`Name`, `CourseCredit`, `CourseDescription`, `Approved` 
			FROM `course-electives`, `courses`, `basic-information`
			LEFT JOIN `student-study-link` ON `basic-information`.ID = `student-study-link`.StudentID
			LEFT JOIN `study` ON `student-study-link`.StudyID = `study`.ID
			WHERE `basic-information`.Status = 'Requesting' 
			AND `basic-information`.ID = $item
			AND `course-electives`.StudentID = `basic-information`.ID
			AND `courses`.ID = `course-electives`.CourseID";


		$run = $this->core->database->doSelectQuery($sql);
		$i = 1;

		echo '<div class="toolbar">'.
		'<a href="' . $this->core->conf['conf']['path'] . '/approval/manage">Back to Students</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/approval/approve/'.$item.'/all">Approve all</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/approval/reject/'.$item.'/all">Reject all</a>'.

		'</div>';

		echo '<table id="active" class="table table-bordered  table-hover">
					<thead>
						<tr>
							<th bgcolor="#EEEEEE" width="30px" data-sort"string"><b> #</b></th>
							<th bgcolor="#EEEEEE" width="300px" data-sort"string"><b> Course name</b></th>
							<th bgcolor="#EEEEEE"><b> <b>StudentID</b></th>
							<th bgcolor="#EEEEEE" width="100px"><b> Credits</b></th>
							<th bgcolor="#EEEEEE" width="150px"><b> Options</b></th>
						</tr>
					</thead>
					<tbody>';
		$total = 0;

		while ($fetch = $run->fetch_assoc()) {
			$uid = $fetch['StudentID'];
			$course = $fetch['Name'];
			$cid = $fetch['ID'];
			$description = $fetch['CourseDescription'];
			$credits = $fetch['CourseCredit'];
			$approved = $fetch['Approved'];
			$apid = $fetch['CEID'];

			if ($approved == 0) {
				$class = 'class="info"';
				$next = '<a href="' . $this->core->conf['conf']['path'] . '/approval/approve/'.$uid.'/' . $apid .'"> <b>Approve </b></a> | ';
				$next = $next.'<a href="' . $this->core->conf['conf']['path'] . '/approval/reject/'.$uid.'/' . $apid .'"><b>Reject</b></a>';

			} elseif ($approved == 1){
				$class = 'class="success"';
				$next = ' <b>Approve </b> | ';
				$next = $next.'<a href="' . $this->core->conf['conf']['path'] . '/approval/reject/'.$uid.'/' . $apid .'"><b>Reject</b></a>';
			} elseif ($approved == 2){
				$class = 'class="danger"';
				$next = '<a href="' . $this->core->conf['conf']['path'] . '/approval/approve/'.$uid.'/' . $apid .'"> <b>Approve </b></a> | ';
				$next = $next.'<b>Reject</b>';
			}

			echo '<tr '.$class.'>
				<td>'.$i.'</td>
				<td><a href="' . $this->core->conf['conf']['path'] . '/course/show/' . $cid . '"><b>' . $description .'</b></a>  </td>
				<td>' . $uid . '</td>
				<td><b>' . $credits . '</b></td>
				<td>'.$next.'</td>
				</tr>';


			$i++;
			$total = $total+$credits;


		}

		echo '<tr class="warning">
			<td></td>
			<td colspan="2"><b>Total number of credits</b></td>

			<td colspan="2"><b>' . $total . '</b></td>
		
			</tr>';
		echo '</tbody>
		</table>';

	}



	public function manageApproval($item) {
		$uid = $this->core->userID;

		if($item == ""){
			$item = "%";
		}

		$sql = "SELECT COUNT(`course-electives`.StudentID) as CT, FirstName, Surname, `basic-information`.ID as StudentID FROM `course-electives`, `courses`, `basic-information`
			LEFT JOIN `student-study-link` ON `basic-information`.ID = `student-study-link`.StudentID
			LEFT JOIN `study` ON `student-study-link`.StudyID = `study`.ID
			WHERE `basic-information`.Status = 'Requesting' 
			AND `basic-information`.ID = `course-electives`.StudentID
			AND `course-electives`.CourseID = `courses`.ID
			AND `course-electives`.PeriodID LIKE '$item'
			GROUP BY `course-electives`.StudentID";


		$run = $this->core->database->doSelectQuery($sql);
		$i = 1;

		
		echo '<table id="active" class="table table-bordered table-striped table-hover">
					<thead>
						<tr>
							<th bgcolor="#EEEEEE" width="30px" data-sort"string"><b> #</b></th>
							<th bgcolor="#EEEEEE" width="300px" data-sort"string"><b> Applicant Name</b></th>
							<th bgcolor="#EEEEEE"><b> <b>Student ID</b></th>
							<th bgcolor="#EEEEEE"><b> Courses</b></th>
							<th bgcolor="#EEEEEE" width="100px"><b> Options</b></th>
						</tr>
					</thead>
					<tbody>';

		while ($fetch = $run->fetch_assoc()) {
			$study = $fetch['Name'];
			$firstname = $fetch['FirstName'];
			$middlename = $fetch['MiddleName'];
			$surname = $fetch['Surname'];
			$sex = $fetch['Sex'];
			$uid = $fetch['StudentID'];
			$nrc = $fetch['GovernmentID'];
			$grade = $fetch['GradeTotal'];
			$gradeno = $fetch['GradeNo'];
			$shortcode = $fetch['Shortcode'];
			$courses = $fetch['CT'];

				echo '<tr>
				<td>'.$i.'</td>
				<td><a href="' . $this->core->conf['conf']['path'] . '/information/show/' . $uid . '"><b>' . $firstname . ' ' . $middlename . ' ' . $surname . '</b></a>  </td>
				<td>' . $uid . '</td>
				<td><b>' . $courses . '</b></td>
				<td><a href="' . $this->core->conf['conf']['path'] . '/approval/show/' . $uid . '"><img src="' . $this->core->fullTemplatePath . '/images/exleft.gif"> <b>Approval</b> </a></td>
				</tr>';


			$i++;



		}

		echo '</tbody>
		</table>';

	}

}

?>
