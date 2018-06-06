<?php
class acceptance {

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

	public function manageAcceptance($item) {

		$sql = "SELECT `study`.ID, `study`.Name, `schools`.name, `schools`.id FROM `study`,`schools` WHERE `study`.ParentID = `schools`.ID ORDER BY `study`.Name";
                $run = $this->core->database->doSelectQuery($sql);

                echo '<table width="768" height="" border="0" cellpadding="3" cellspacing="0"><tr class="tableheader"><td><b>Study</b></td>' .
                     '<td><b>School</b></td>' .
                     '<td><b>Management tools</b></td>' .
                     '</tr>';

                $i = 0;
                while ($row = $run->fetch_row()) {
                        echo '<tr>
                        <td><b>  <a href="' . $this->core->conf['conf']['path'] . '/studies/show/' . $row[0] . '">' . $row[1] . '</b></a></td>' .
                                '<td>' . $row[2] . '</td>' .
                                '<td>
                                <a href="' . $this->core->conf['conf']['path'] . '/acceptance/show/' . $row[0] . '"> View Candidates</a>
                        </td>
                        </tr>';
                }

                echo '</table>
                </p>';
	}

	public function completeAcceptance($item) {
		$sql = "UPDATE `access` SET `RoleID` = 10 WHERE `access`.`ID` = '" . $item . "'";
		$run = $this->core->database->doInsertQuery($sql);

		$sql = "UPDATE `basic-information` SET `Status` = 'Enrolled' WHERE `basic-information`.`ID` = '" . $item . "'";
		$run = $this->core->database->doInsertQuery($sql);

		$sql = "SELECT * FROM `basic-information` WHERE `GovernmentID` = '" . $item . "'";
		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_assoc()) {
			$recipient = $fetch["PrivateEmail"];
			$mailer = serviceBuilder("mailer");
			$mailer->newMail("registrationSuccessful", $recipient);
		}

		$this->core->redirect("acceptance", "manage", NULL);
	}

	public function rejectAcceptance($item) {
		$sql = "UPDATE `basic-information` SET `Status` = 'Rejected' WHERE `basic-information`.`ID` = '" . $item . "';";
		$run = $this->core->database->doInsertQuery($sql);

		$this->core->redirect("acceptance", "manage", NULL);
	}

	public function continueAcceptance($item) {
		$sql = "UPDATE `basic-information` SET `Status` = 'Requesting' WHERE `basic-information`.`ID` = '" . $item . "';";
		$run = $this->core->database->doInsertQuery($sql);

		$this->core->redirect("acceptance", "manage", NULL);
	}

	public function showAcceptance($item) {

		$sql = "SELECT *, Grade as GradeTotal FROM `subject-grades`
			LEFT JOIN `subjects` ON  `subject-grades`.SubjectID = `subjects`.ID
			LEFT JOIN `basic-information` ON  `subject-grades`.StudentID = `basic-information`.ID
			LEFT JOIN `student-study-link` ON `basic-information`.ID = `student-study-link`.StudentID
			LEFT JOIN `study` ON `student-study-link`.StudyID = `study`.ID
			WHERE `basic-information`.Status = 'Requesting' AND `student-study-link`.StudyID = $item AND Grade > 0";


//			GROUP BY `subject-grades`.StudentID

		$run = $this->core->database->doSelectQuery($sql);
		$i = 0;


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


			if($current != $uid){

				if(!empty($current)){

					if ($cstatus == 6) {
						$next = '<a href="' . $this->core->conf['conf']['path'] . '/acceptance/complete/' . $current . '/' . $cstatus . '"><img src="' . $this->core->fullTemplatePath . '/images/exleft.gif"> <b>Complete</b> </a>';
					} else {
						$next = '<a href="' . $this->core->conf['conf']['path'] . '/acceptance/promote/' . $current . '/' . $cstatus . '"><img src="' . $this->core->fullTemplatePath . '/images/exleft.gif">Approve</a>';
					}

					echo'</td>
					<td><b>' . $gradetotal . '</b></td>
					<td>' . $next . '</td>
					</tr>';

					$gradetotal = 0;

				}

				$current = $uid;
				$cstatus = $status;

				if($i == 0){
					echo '<h2>Please determine cut-off for: '.$study.'</h2><p class="title1">Please use the grade points to select the cut off point</p> ';
					echo '<table id="active" class="table table-bordered table-striped table-hover">
					<thead>
						<tr>
							<th bgcolor="#EEEEEE" width="250px" data-sort"string"><b> Applicant Name</b></th>
							<th bgcolor="#EEEEEE"><b> <b>NRC</b></th>
							<th bgcolor="#EEEEEE"><b> Grade points</b></th>
							<th bgcolor="#EEEEEE"><b> Total</b></th>
							<th bgcolor="#EEEEEE" width="90px"><b> Options</b></th>
						</tr>
					</thead>
					<tbody>';
					$i++;
				}

				echo '<tr>
				<td>'.$i.'  - <a href="' . $this->core->conf['conf']['path'] . '/information/show/' . $uid . '"><b>' . $firstname . ' ' . $middlename . ' ' . $surname . '</b></a>  </td>
				<td>' . $nrc . '</td><td>';
			$i++;

			} else {

				echo'	<b>'.$shortcode.'</b> : '.$grade.'';
				$gradetotal = $gradetotal + $grade;
			}
		}

		if($i == 0){
			echo '<h2>Currently no applicants have applied</h2><br/>';
			echo '<table id="active" class="table table-bordered table-striped table-hover">
			<thead>
				<tr>
					<th bgcolor="#EEEEEE" width="300px" data-sort"string"><b> Applicant Name</b></th>
					<th bgcolor="#EEEEEE"><b> <b>National ID</b></th>
					<th bgcolor="#EEEEEE"><b> Grade points</b></th>
					<th bgcolor="#EEEEEE" width="90px"><b> Options</b></th>
				</tr>
			</thead>
			<tbody>';
		} else {

					if ($cstatus == 6) {
						$next = '<a href="' . $this->core->conf['conf']['path'] . '/acceptance/complete/' . $current . '/' . $cstatus . '"><img src="' . $this->core->fullTemplatePath . '/images/exleft.gif"> <b>Complete</b> </a>';
					} else {
						$next = '<a href="' . $this->core->conf['conf']['path'] . '/acceptance/promote/' . $current . '/' . $cstatus . '"><img src="' . $this->core->fullTemplatePath . '/images/exleft.gif">Approve</a>';
					}

					echo'</td>
					<td><b>' . $gradetotal . '</b></td>
					<td>' . $next . '</td>
					</tr>';

}

		echo '</tbody>
		</table>';

	}

}

?>
