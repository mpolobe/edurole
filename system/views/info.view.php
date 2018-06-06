<?php
class info {

	public $core;
	public $view;

	public function configView() {
		$this->view->header = TRUE;
		$this->view->footer = TRUE;
		$this->view->menu = FALSE;
		$this->view->javascript = array();
		$this->view->css = array();

		return $this->view;
	}

	public function buildView($core) {
		$this->core = $core;

		echo '<div class="menucontainer">
		<div class="menubar">
		<div class="menuhdr"><strong>Home menu</strong></div>
		<div class="menu">
		<a href=".">Home</a>
		<a href="' . $this->core->conf['conf']['path'] . '/info">Overview of all studies</a>
		<a href="admission">Studies open for intake</a>
		<a href="password">Recover lost password</a>
		</div>
		</div>
		</div>';

		$function = __FUNCTION__;
		$title = 'Overview of studies';
		$description = 'Overview of all studies.';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);

		$this->core->throwSuccess("PLEASE OBSERVE THE START AND END DATE FOR THE ONLINE INTAKE, ONLINE REGISTRATION WILL BE POSSIBLE BETWEEN THESE DATES ONLY.");

		echo '<p>' .
			'<table width="710">' .
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
				'<td>' . $fetch[15] . '</td>' .
				'<td>' . date("d-m-Y", strtotime($fetch[2])) . ' <b>until</b> ' . date("d-m-Y", strtotime($fetch[3])) . ' </td>' .
				'</tr>';
		}

		echo '</table></p>';

	}

}

?>
