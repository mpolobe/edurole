<?php
class correct {

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

		if ($this->core->action == "correct" && $core->role >= 100) {
			$this->correctInformations();
		}
	}

	public function correctInformation() {
		$function = __FUNCTION__;
		$title = 'List of incorrectly imported student number';
		$description = 'Your assignments currently active in your courses and programmes';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);

		$sql = "SELECT * FROM `basic-information` WHERE `basic-information`.ID < 20000000  OR `basic-information`.ID > 2010222117";

		echo '<p>
		<form id="login" name="login" method="post" action="/correct/studentnumbers">
		<input type="hidden" name="course" value="' . $this->core->cleanGet['course'] . '">
		<table width="768" height="" border="0" cellpadding="0" cellspacing="0">
			<tr >
			<td  style="padding:5px"bgcolor="#EEEEEE" <b> Student Name</b></td>
			<td style="padding:5px" bgcolor="#EEEEEE"><b> <b>National ID</b></td>
			<td style="padding:5px" bgcolor="#EEEEEE"><b> Incorrect student number</b></td>
			</tr>';

		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_row()) {
			echo '<tr><td><a href="' . $this->core->conf['conf']['path'] . 'information/view/' . $fetch[4] . '"><b>' . $fetch[0] . " " . $fetch[1] . " " . $fetch[2] . '</a></b></td>' .
				'<td>' . $fetch[5] . '</td>' .
				'<td><input type="textbox" name="sid' . $fetch[4] . '" value="' . $fetch[4] . '" class="submit" style="width: 90px;"></td>' .
				'</tr>';
		}

		echo '</table><br><input type="submit" value="Save changes to system" />
		</form></p>';
	}
}

?>
