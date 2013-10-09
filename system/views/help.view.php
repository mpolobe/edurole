<?php
class help {

	public $core;
	public $view;

	public function configView() {
		$this->view->header = TRUE;
		$this->view->footer = TRUE;
		$this->view->menu = TRUE;
		$this->view->javascript = array(3);
		$this->view->css = array(4);

		return $this->view;
	}

	public function buildView($core) {
		$this->core = $core;

		include $this->core->conf['conf']['viewPath'] . "item.view.php";

		$function = __FUNCTION__;
		$title = 'Grades submitted';
		$description = 'Overview of personally submitted grades';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);

		function globalstatistics() {

			$sql = "SELECT  (
				SELECT count(ID) FROM `basic-information` WHERE `StudyType` = 'Distance' AND `Status` = 'Enrolled'
			) AS distancestudents, (
				SELECT count(ID) FROM `basic-information` WHERE `StudyType` = 'Fulltime' AND `Status` = 'Enrolled'
			) AS fulltimestudents, (
				SELECT count(ID) FROM `basic-information` WHERE `StudyType` = 'Partime'  AND `Status` = 'Enrolled'
			) AS parttimestudents, (
				SELECT count(ID) FROM `basic-information` WHERE `Status` = 'Requesting'
			) AS requestingstudents";

			$run = doSelectQuery($sql);

			while ($std = mysql_fetch_row($run)) {
				$distance = $std[0];
				$fulltime = $std[1];
				$parttime = $std[2];
				$requesting = $std[3];

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

		function helpoverview() {
			global $connection;
			$sql = "SELECT * FROM `content` WHERE `ContentCat` = 'help'";

			if (!$pep = mysql_query($sql, $connection)) {
				die('Error: ' . mysql_error());
			}

			echo '<div class="newscontainers">	<h2>Help and information articles</h2> <p>';

			while ($fetch = mysql_fetch_row($pep)) {
				echo ' <li> <b><a href="' . $this->core->conf['conf']['path'] . 'help/' . $fetch[0] . '">' . $fetch[1] . '</a></b></li>';
			}

			echo '</p></div>';
		}

		echo '<p class="title2">Help and information for system use</p>';

		if (!isset($this->core->cleanGet['item'])) {
			$itemid = "3";
		} else {
			$itemid = mysql_real_escape_string($this->core->cleanGet['item']);
		}

		item($itemid);
		helpoverview();
	}
}

?>

 