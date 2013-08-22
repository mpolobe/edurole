<?php
class intake {

	public $core;
	public $view;

	public function configView() {
		$this->view->header = TRUE;
		$this->view->footer = TRUE;
		$this->view->menu = FALSE;
		$this->view->javascript = array(3);
		$this->view->css = array(4);

		return $this->view;
	}

	public function buildView($core) {
		$this->core = $core;

		if ($this->view->menu == FALSE) {

			echo '<div class="menucontainer">
				<div class="menubar">
				<div class="menuhdr"><strong>Home menu</strong></div>
				<div class="menu">
				<a href=".">Home</a>
				<a href="' . $this->core->conf['path'] . '/info">Overview of all studies</a>
				<a href="admission">Studies open for intake</a>
				<a href="password">Recover lost password</a>
				</div>
				</div>
				</div>';

		}

		$function = __FUNCTION__;
		$title = 'Studies open for intake';
		$description = 'The following studies are currently open for intake';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);

		if ($this->core->cleanGet['action'] == "view") {
			$item = $this->core->cleanGet['item'];
			$sql = "SELECT * FROM `study`,`schools` WHERE `study`.ParentID = `schools`.ID AND `study`.ParentID = `schools`.ID AND `study`.ID = $item";
			$this->showitem($sql);
		} else {
			$sql = "SELECT * FROM `study`,`schools` WHERE `study`.ParentID = `schools`.ID AND CURRENT_TIMESTAMP <= `study`.IntakeEnd ORDER BY `study`.Name";
			$this->showlist($sql);
		}

	}

	function showlist($sql) {

		echo '<p>Overview of all studies for which intake is currently open, click on the study to proceed to filing your request for admission. </p>
		<p>
		<table width="768" cellspacing="0" cellpadding="5" >
		<tr><td bgcolor="#EEEEEE"> <b>Study</b></td>' .
			'<td bgcolor="#EEEEEE"><b>School</b></td>' .
			'<td bgcolor="#EEEEEE"><b>End of intake</b></td>' .
			'</tr>';

		$run = $this->core->database->doSelectQuery($sql);

		while ($row = $run->fetch_row()) {
			echo '<tr>
			<td><b><a href="' . $this->core->conf['path'] . '/register/view/' . $row[0] . '"> ' . $row[6] . '</a></b></td>' .
				'<td>' . $row[16] . '</td>' .
				'<td>' . $row[3] . '</td>' .
				'</tr>';
		}

		echo '</table>
		</p>';
	}
}

?>
