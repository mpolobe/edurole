<?php
class intake {

	public $core;
	public $view;

	public function configView() {
		$this->view->open = TRUE;
		$this->view->header = TRUE;
		$this->view->footer = TRUE;
		$this->view->menu = FALSE;
		$this->view->internalMenu = TRUE;
		$this->view->javascript = array();
		$this->view->css = array();

		return $this->view;
	}

	public function buildView($core) {
		$this->core = $core;
        }

	private function viewMenu(){
		echo '<div class="">
		<ul class="nav side-nav">
		<li class="active"><strong>Home menu</strong></li>
		<li class="menu"><a href="' . $this->core->conf['conf']['path'] . '/">' . $this->core->translate("Home") . '</a></li>
		<li class="menu"><a href="' . $this->core->conf['conf']['path'] . '/intake/studies">' . $this->core->translate("All programmes") . '</a></li>
		<li class="menu"><a href="' . $this->core->conf['conf']['path'] . '/intake">' . $this->core->translate("Open for intake") . '</a></li>
		<li class="menu"><a href="' . $this->core->conf['conf']['path'] . '/intake/register">' . $this->core->translate("Current student registration") . '</a></li>
		<li class="menu"><a href="' . $this->core->conf['conf']['path'] . '/password/recover">' . $this->core->translate("Recover lost password") . '</a></li>
		</ul><div id="page-wrapper">';
	}

	function adminIntake() {
		$sql = "SELECT * FROM `study`,`schools` WHERE `study`.ParentID = `schools`.ID ORDER BY `study`.Name";

		echo'<table class="table table-bordered table-striped table-hover">
			<thead>
				<tr>
					<td bgcolor="#EEEEEE"><b>' . $this->core->translate("Study") . '</b></td>
					<td bgcolor="#EEEEEE"><b>' . $this->core->translate("School") . '</b></td>
					<td bgcolor="#EEEEEE"><b>' . $this->core->translate("End of intake") . '</b></td>
				</tr>
			</thead>
			<tbody>';

		$run = $this->core->database->doSelectQuery($sql);

		while ($row = $run->fetch_row()) {
			echo '<tr><td><b><a href="' . $this->core->conf['conf']['path'] . '/register/study/' . $row[0] . '"> ' . $row[6] . '</a></b></td>' .
				'<td>' . $row[16] . '</td>' .
				'<td>' . $row[3] . '</td>' .
				'</tr>';
		}

		echo '</tbody>
		</table>';
	}

	function registerIntake() {

		$this->viewMenu();

		$sql = "SELECT * FROM `study`,`schools` WHERE `study`.ParentID = `schools`.ID  AND CURRENT_TIMESTAMP <= `study`.IntakeEnd ORDER BY `study`.Name";

		echo'<p class="title">Select your study</p>
			<p>Select the study you are currently enrolled for</p>
			<table class="table table-bordered table-striped table-hover">
			<thead>
				<tr>
					<td bgcolor="#EEEEEE"><b>' . $this->core->translate("Study") . '</b></td>
					<td bgcolor="#EEEEEE"><b>' . $this->core->translate("School") . '</b></td>
					<td bgcolor="#EEEEEE"><b>' . $this->core->translate("End of intake") . '</b></td>
				</tr>
			</thead>
			<tbody>';

		$run = $this->core->database->doSelectQuery($sql);

		while ($row = $run->fetch_row()) {
			echo '<tr><td><b><a href="' . $this->core->conf['conf']['path'] . '/register/study/' . $row[0] . '?existing=yes"> ' . $row[6] . '</a></b></td>' .
				'<td>' . $row[16] . '</td>' .
				'<td>' . $row[3] . '</td>' .
				'</tr>';
		}

		echo '</tbody>
		</table>';
	}


	function showIntake($item = NULL) {
		$this->viewMenu();

		if(isset($item)){
			$sql = "SELECT * FROM `study`,`schools` WHERE `study`.ParentID = `schools`.ID AND `study`.ParentID = `schools`.ID AND `study`.ID = $item";
		}else{
			$sql = "SELECT * FROM `study`,`schools` WHERE `study`.ParentID = `schools`.ID AND CURRENT_TIMESTAMP <= `study`.IntakeEnd ORDER BY `study`.Name";
		}

		echo'<p class="title">Select the study for which you are applying</p>
			<p>You can only select one study for your first application</p>
			<table class="table table-bordered table-striped table-hover">
			<thead>
				<tr>
					<td bgcolor="#EEEEEE"><b>Study</b></td>
					<td bgcolor="#EEEEEE"><b>School</b></td>
					<td bgcolor="#EEEEEE"><b>End of intake</b></td>
				</tr>
			</thead>
                        <tbody>';

		$run = $this->core->database->doSelectQuery($sql);

		while ($row = $run->fetch_row()) {
			echo '<tr><td><b><a href="' . $this->core->conf['conf']['path'] . '/register/study/' . $row[0] . '"> ' . $row[6] . '</a></b></td>' .
				'<td>' . $row[16] . '</td>' .
				'<td>' . $row[3] . '</td>' .
				'</tr>';
		}

		echo '</table>
		</p>';
	}

	function studiesIntake() {
		$this->viewMenu();

		echo '<p class="title">Overview of all studies on offer</p>
			<p>Please note when applications will be accepted in the last column of the table.</p>';
		$this->core->throwSuccess("PLEASE OBSERVE THE START AND END DATE FOR THE ONLINE INTAKE, ONLINE REGISTRATION WILL BE POSSIBLE BETWEEN THESE DATES ONLY.");



		echo'<p>' .
		'<table width="768">' .
		'<tr class="tableheader"><td><b>Study</b></td>' .
		'<td><b>School</b></td>' .
		'<td><b>Intake start and end date</b></td>' .
		'</tr>';

		$sql = "SELECT * FROM `study`,`schools` WHERE `study`.ParentID = `schools`.ID ORDER BY `study`.Name";
		$run = $this->core->database->doSelectQuery($sql);
		$i = 0;

		while ($fetch = $run->fetch_row()) {

			if ($i == 0) {
				$bgc = 'class="zebra"';
				$i++;
			} else {
				$bgc = '';
				$i--;
			}

			echo '<tr ' . $bgc . '>' .
			'<td><b>' . $fetch[6] . '</b></td>' .
			'<td>' . $fetch[16] . '</td>' .
			'<td>' . date("d-m-Y", strtotime($fetch[2])) . ' <b>until</b> ' . date("d-m-Y", strtotime($fetch[3])) . ' </td>' .
			'</tr>';
		}

		echo '</table></p>';
	}
}

?>
