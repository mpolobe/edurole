<?php
class intake {

	public $core;
	public $view;

	public function configView() {
		$this->view->header = TRUE;
		$this->view->footer = TRUE;
		$this->view->menu = FALSE;
		$this->view->internalMenu = TRUE;
		$this->view->javascript = array(3);
		$this->view->css = array(4);

		return $this->view;
	}

	public function buildView($core) {
		$this->core = $core;

		if ($this->view->internalMenu == TRUE) {

			echo '<div class="menucontainer">
				<div class="menubar">
				<div class="menuhdr"><strong>Home menu</strong></div>
				<div class="menu">
				<a href="' . $this->core->conf['conf']['path'] . '">Home</a>
				<a href="' . $this->core->conf['conf']['path'] . '/intake/studies">Overview of all studies</a>
				<a href="' . $this->core->conf['conf']['path'] . '/intake">Studies open for intake</a>
				<a href="' . $this->core->conf['conf']['path'] . '/password">Recover lost password</a>
				</div>
				</div>
				</div><div class="contentpadfull">';
				
		}

		$item = $this->core->item;
					
		if ($this->core->action == "view") {
			$this->showitem($item);
		} else if ($this->core->action == "studies") {
			$this->showStudies();
		} else {
			$this->showIntake();
		}

	}

	function showIntake($item) {
	
		if(isset($item)){
			$sql = "SELECT * FROM `study`,`schools` WHERE `study`.ParentID = `schools`.ID AND `study`.ParentID = `schools`.ID AND `study`.ID = $item";
		}else{
			$sql = "SELECT * FROM `study`,`schools` WHERE `study`.ParentID = `schools`.ID AND CURRENT_TIMESTAMP <= `study`.IntakeEnd ORDER BY `study`.Name";
		}
		
		$function = __FUNCTION__;
		$title = 'Studies open for intake';
		$description = 'The following studies are currently open for intake';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);
		
		echo '<p>Overview of all studies for which intake is currently open, click on the study to proceed to filing your request for admission. </p>
		<p>
		<table width="768" cellspacing="0" cellpadding="5" >
		<tr><td bgcolor="#EEEEEE"> <b>Study</b></td>' .
			'<td bgcolor="#EEEEEE"><b>School</b></td>' .
			'<td bgcolor="#EEEEEE"><b>End of intake</b></td>' .
			'</tr>';

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
	
	function showStudies() {

		$function = __FUNCTION__;
		$title = 'Overview of studies';
		$description = 'Overview of all studies.';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);

		$this->core->throwSuccess("PLEASE OBSERVE THE START AND END DATE FOR THE ONLINE INTAKE, ONLINE REGISTRATION WILL BE POSSIBLE BETWEEN THESE DATES ONLY.");

		echo '<p>' .
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
