<?php
class home {

	public $core;
	public $view;

	public function configView() {
		$this->view->header = TRUE;
		$this->view->footer = TRUE;
		$this->view->javascript = array(3);
		$this->view->css = array(4);

		return $this->view;
	}

	public function buildView($core) {
		$this->core = $core;

		$function = __FUNCTION__;
		require_once "system/components.inc.php";
		echo $this->core->breadcrumb->generate(get_class(), $function);

		$this->infoSheet();

		if ($this->core->role > 100) {
			$this->globalStatistics();
		}

		$this->easyMenu();

		$this->showItem(1);
		$this->newsoverview();
	}

	function globalStatistics() {

		$this->core->logEvent("Initializing global user statistics count", "3");

		$sql = "SELECT  (
                SELECT count(ID) FROM `basic-information` WHERE `StudyType` = 'Distance' AND `Status` = 'Enrolled'
        ) AS distancestudents, (
                SELECT count(ID) FROM `basic-information` WHERE `StudyType` = 'Fulltime' AND `Status` = 'Enrolled'
        ) AS fulltimestudents, (
                SELECT count(ID) FROM `basic-information` WHERE `StudyType` = 'Partime'  AND `Status` = 'Enrolled'
        ) AS parttimestudents, (
                SELECT count(ID) FROM `basic-information` WHERE `Status` = 'Requesting'
        ) AS requestingstudents";

		$run = $this->core->database->doSelectQuery($sql);

		while ($row = $run->fetch_row()) {
			$distance = $row[0];
			$fulltime = $row[1];
			$parttime = $row[2];
			$requesting = $row[3];

			$total = $fulltime + $distance + $parttime;
		}

		echo '<div class="easymencontainer">
        <div class="statistics">Total students: <div class="statistic" style="color: #2C89D4;">' . $total . '</div></div>
        <div class="statistics">Fulltime students: <div class="statistic">' . $fulltime . '</div></div>
        <div class="statistics">Distance students: <div class="statistic">' . $distance . '</div></div>
        <div class="statistics">Part-time students: <div class="statistic">' . $parttime . '</div></div>
        <div class="statistics">Currently in admission: <div class="statistic">' . $requesting . '</div></div>
        </div>';

	}

	public function infoSheet() {

		$this->core->logEvent("Initializing info-sheet", "3");

		$sql = "SELECT * FROM `basic-information` as bi, `access` as ac WHERE ac.`ID` = '" . $this->core->userid . "' AND ac.`ID` = bi.`ID`";
		$run = $this->core->database->doSelectQuery($sql);

		while ($row = $run->fetch_row()) {

			$id = $this->core->userid;
			$firstname = $row[0];
			$middlename = $row[1];
			$surname = $row[2];
			$sex = $row[3];
			$idnumber = $row[4];
			$nrc = $row[5];
			$studytype = $row[22];

			echo '<div class="greeter">Welcome ' . $firstname . ' ' . $surname . ' </div><div class="homecontainers">
                <table width="600" border="0" cellpadding="0" cellspacing="0"><tr>';

			if ($this->core->role < 100) {
				echo '<td  width="117">Student number</td>';
			} else {
				echo '<td  width="200">Employee number</td>';
			}

			echo '<td>' . $idnumber . '</td></tr>';
			echo '<tr><td>Current role</td> <td>' . $this->core->rolename . '</td></tr>';
			echo '<tr><td>Selected template</td> <td>' . $this->core->template . '</td></tr>';

			$sql = "SELECT * FROM `access` as ac, `student-study-link` as ss, `study` as st, `student-program-link` as pl WHERE ac.`ID` = '" . $this->core->userid . "' AND ss.`StudyID` = st.`ID` AND pl.`StudentID` = $nrc AND ss.`StudentID` = $nrc";

			$run = $this->core->database->doSelectQuery($sql);

			while ($row = $run->fetch_row()) {

				$status = $row[7];
				$study = $row[14];

				echo '<tr>
                        <td>Selected study</td>
                        <td><b><a href="?id=studies&action=view&item=' . $row[8] . '">' . $study . '</a></b></td>
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

					echo '<tr>
                                <td>Selected ' . $majmin . '</td>
                                <td>' . $programme . '</td>
                                </tr>';
				}

				if ($majmin == "major") {
					echo '<tr>
                                <td>Selected minor</td>
                                <td>' . $programme . '</td>
                                </tr>';
				}

			}
		}

		echo '</table></div>';
	}

	function newsoverview() {

		$sql = "SELECT * FROM `content` WHERE `ContentCat` = 'news'";
		$run = $this->core->database->doSelectQuery($sql);

		echo '<div class="newscontainers">	<h2>News and updates</h2> <p>';

		while ($row = $run->fetch_row()) {
			echo ' <li> <b><a href="?id=item&item=' . $row[0] . '">' . $row[1] . '</a></b></li>';
		}

		echo '</p></div>';
	}


	function showItem($id) {

		$sql = "SELECT * FROM `content` WHERE `ContentID` = $id";
		$run = $this->core->database->doSelectQuery($sql);

		echo '<div class="welcomecontainers">';

		if ($this->core->role == 1000) {
			echo '<div style="float: right;"><a href="?id=item&action=edit&item=' . $id . '">edit</a></div>';
		}

		while ($row = $run->fetch_row()) {
			echo ' <h2>' . $row[1] . '</h2>';
			echo ' <p>' . $row[2] . '</p>';
		}

		echo '</div>';

	}

	public function easyMenu() {

		if ($this->core->role >= 10) {
			echo '<div class="easymencontainer">
                 <div class="easymen"><a href="'. $this->core->conf['path'] .'/mail"><img src="'. $this->core->fullTemplatePath .'/images/mail.png"> <br>  Email</a></div>
                 <div class="easymen"><a href="'. $this->core->conf['path'] .'/grades"><img src="'. $this->core->fullTemplatePath .'/images/chart.png"> <br> Grades</a></div>
                 <div class="easymen"><a href="'. $this->core->conf['path'] .'/calendar"><img src="'. $this->core->fullTemplatePath .'/images/calendar.png"> <br> Calendar</a></div>
                 <div class="easymen"><a href="'. $this->core->conf['path'] .'/books"><img src="'. $this->core->fullTemplatePath .'/images/books.png"> <br> Books</a></div>
				 <div class="easymen"><a href="'. $this->core->conf['path'] .'/files"><img src="'. $this->core->fullTemplatePath .'/images/box.png"> <br>  Files</a></div>
                 <div class="easymen"><a href="'. $this->core->conf['path'] .'/assignments"><img src="'. $this->core->fullTemplatePath .'/images/clipboard.png">  <br> Assignments</a></div>
                 <div class="easymen"><a href="'. $this->core->conf['path'] .'/help"><img src="'. $this->core->fullTemplatePath .'/images/info.png">  <br> Help</a></div>
                </div>';
		}
	}
}
?>