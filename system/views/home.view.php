<?php
class home {

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

	public function showHome() {

		require_once "system/components.inc.php";

		$this->infoSheet();

		if ($this->core->role > 100) {
			$this->globalStatistics();
		}

		$this->easyMenu();

		$this->showItem(1);
		$this->newsoverview();
	}

	private function globalStatistics() {

		$this->core->logEvent("Initializing global user statistics count", "3");

		$sql = "SELECT  (SELECT count(ID) FROM `basic-information` WHERE `StudyType` = 'Distance' AND `Status` = 'Enrolled') AS distancestudents, 
		(SELECT count(ID) FROM `basic-information` WHERE `StudyType` = 'Fulltime' AND `Status` = 'Enrolled') AS fulltimestudents, 
		(SELECT count(ID) FROM `basic-information` WHERE `StudyType` = 'Partime'  AND `Status` = 'Enrolled') AS parttimestudents, 
		(SELECT count(ID) FROM `basic-information` WHERE `Status` = 'Requesting') AS requestingstudents";

		$run = $this->core->database->doSelectQuery($sql);

		while ($row = $run->fetch_row()) {
			$distance = $row[0];
			$fulltime = $row[1];
			$parttime = $row[2];
			$requesting = $row[3];

			$total = $fulltime + $distance + $parttime;
		}

		echo '<div class="col-lg-12 padding20">
        <div class="statistics">'.$this->core->translate("Total students").': <div class="statistic" style="color: #2C89D4;">' . $total . '</div></div>
        <div class="statistics">'.$this->core->translate("Fulltime students").': <div class="statistic">' . $fulltime . '</div></div>
        <div class="statistics">'.$this->core->translate("Distance students").': <div class="statistic">' . $distance . '</div></div>
        <div class="statistics">'.$this->core->translate("Part-time students").': <div class="statistic">' . $parttime . '</div></div>
        <div class="statistics">'.$this->core->translate("Currently in admission").': <div class="statistic">' . $requesting . '</div></div>
        </div>';

	}

	private function infoSheet() {

		$this->core->logEvent("Initializing info-sheet", "3");

		$sql = "SELECT * FROM `basic-information` as bi, `access` as ac WHERE ac.`ID` = '" . $this->core->userID . "' AND ac.`ID` = bi.`ID`";

		$run = $this->core->database->doSelectQuery($sql);

		if ($run->num_rows == 0) {
			$this->core->throwSuccess($this->core->translate("Please take the time to enter your profile information first, you can do this <a href='". $this->core->conf['conf']['path'] ."/information/edit/personal'>here</a>."));
		}


		while ($row = $run->fetch_row()) {

			$id = $this->core->userID;
			$firstname = $row[0];
			$middlename = $row[1];
			$surname = $row[2];
			$sex = $row[3];
			$idnumber = $row[4];
			$nrc = $row[5];
			$studytype = $row[22];

			$username = $row[22];

			if(empty($firstname) && empty($lastname)){
				$this->core->throwSuccess($this->core->translate("Please take the time to enter your profile information first, you can do this <a href='". $this->core->conf['conf']['path'] ."/information/edit/personal'>here</a>."));
			}

			echo '<div class="col-lg-12 greeter">';
			echo $this->core->translate("Welcome");
			echo ' ' . $firstname . ' ' . $surname . ' </div>
			<div class="col-lg-12 padding20 panel panel-default">
               	 	<table width="600" border="0" cellpadding="0" cellspacing="0"><tr>';

			if ($this->core->role < 100) {
				echo'<td  width="117">';
				echo $this->core->translate("Student number");
				echo'</td>';
			} else {
				echo '<td  width="200">';
				echo $this->core->translate("Employee number");
				echo '</td>';
			}

			echo '<td>' . $idnumber . '</td></tr>';
			echo '<tr><td>';
			echo $this->core->translate("Current role");
			echo'</td> <td>' . $this->core->roleName . '</td></tr>';
			echo '<tr><td>';
			echo $this->core->translate("Selected Template");
			echo '</td> <td>' . $this->core->template . '</td></tr>';

			$sql = "SELECT * FROM `access` as ac, `study` as st
				LEFT JOIN  `student-study-link` as ss ON ss.`StudentID` = ''
				LEFT JOIN  `nkrumah-student-program-link` as pl ON pl.`StudentID` = '$id'
				WHERE ac.`ID` = '' 
				AND ss.`StudyID` = st.`ID`";
	
			$run = $this->core->database->doSelectQuery($sql);

			while ($row = $run->fetch_row()) {

				$status = $row[7];
				$study = $row[14];

				echo '<tr><td>';
				echo $this->core->translate("Selected Study");
                        	echo'</td><td><b><a href="' . $this->core->conf['conf']['path'] . '/studies/show/' . $row[8] . '">' . $study . '</a></b></td>
                        	</tr>';

				$sql = "SELECT * FROM `programmes` as pr WHERE pr.`ID` = '$row[23]' OR pr.`ID` = '$row[24]'";
				$run = $this->core->database->doSelectQuery($sql);

				$i = 0;

				while ($row = $run->fetch_row()) {

					$programme = $row[2];

					if ($i == 0) {
						$majmin = "major";
						$i++;
					} else {
						$majmin = "minor";
						$n = 1;
					}

				echo '<tr><td>';
				echo $this->core->translate("Selected");
                                echo ' ' . $majmin . '</td>
                                <td>' . $programme . '</td>
                                </tr>';
				}

				if ($majmin == "major") {
					echo '<tr><td>';
					echo $this->core->translate("Selected Minor");
					echo'</td><td>' . $programme . '</td></tr>';
				}
			}

			echo '</table></div>';

		}

	}

	private function newsoverview() {

		$sql = "SELECT * FROM `content` WHERE `ContentCat` = 'news'";
		$run = $this->core->database->doSelectQuery($sql);

		echo '<div class="col-lg-6 padding20 panel panel-default fixedheightpanel">';

		if ($this->core->role == 1000) {
			echo '<div style="float: right;"><a href="' . $this->core->conf['conf']['path'] . '/item/add/news">add</a></div>';
		}

		echo '<h2>';
		echo $this->core->translate("News and updates");
		echo '</h2> <p>';
		while ($row = $run->fetch_row()) {
			echo ' <li> <b><a href="' . $this->core->conf['conf']['path'] . '/item/show/' . $row[0] . '">' . $row[1] . '</a></b></li>';
		}

		echo '</p></div>';
	}


	private function showItem($id) {

		$sql = "SELECT * FROM `content` WHERE `ContentID` = $id";
		$run = $this->core->database->doSelectQuery($sql);

		echo '<div class="col-lg-6 panel panel-default fixedheightpanel">';

		if ($this->core->role == 1000) {
			echo '<div style="float: right;"><a href="' . $this->core->conf['conf']['path'] . '/item/edit/' . $id . '">edit</a></div>';
		}

		while ($row = $run->fetch_row()) {
			echo ' <h2>' . $row[1] . '</h2>';
			echo ' <p>' . $row[2] . '</p>';
		}

		echo '</div>';

	}

	private function easyMenu() {
		if ($this->core->role >= 10) {
			echo '<div class="col-lg-12 padding20 panel panel-default">
                 	<div class="easymen"><a href="' . $this->core->conf['conf']['path'] . '/mail"><img src="' . $this->core->fullTemplatePath . '/images/mail.png"> <br>  Email</a></div>
                 	<div class="easymen"><a href="' . $this->core->conf['conf']['path'] . '/grades"><img src="' . $this->core->fullTemplatePath . '/images/chart.png"> <br> Grades</a></div>
                 	<div class="easymen"><a href="' . $this->core->conf['conf']['path'] . '/calendar"><img src="' . $this->core->fullTemplatePath . '/images/calendar.png"> <br> Calendar</a></div>
		 	<div class="easymen"><a href="' . $this->core->conf['conf']['path'] . '/files/personal"><img src="' . $this->core->fullTemplatePath . '/images/box.png"> <br>  Files</a></div>
                 	<div class="easymen"><a href="' . $this->core->conf['conf']['path'] . '/assignments"><img src="' . $this->core->fullTemplatePath . '/images/clipboard.png">  <br> Assignments</a></div>
                 	<div class="easymen"><a href="' . $this->core->conf['conf']['path'] . '/payments/balance"><img src="' . $this->core->fullTemplatePath . '/images/money.png"> <br> Payments</a></div>
                 	<div class="easymen"><a href="' . $this->core->conf['conf']['path'] . '/help"><img src="' . $this->core->fullTemplatePath . '/images/info.png">  <br> Help</a></div>
                	</div>';
		}
	}
}

?>
