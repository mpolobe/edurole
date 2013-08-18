<?php
class checkValue {

	public $core;
	public $view;
	public $item = NULL;

	public function configView() {
		$this->view->header = TRUE;
		$this->view->footer = TRUE;
		$this->view->javascript = array(3);
		$this->view->css = array(1, 4);

		return $this->view;
	}

	/*
	 * Government ID taken check in forms
	 */
	public function runService($core) {
		$this->core = $core;

		if (isset($this->core->cleanPost['username'])) {

			$check = $this->core->cleanPost['username'];
			$sql = "SELECT * FROM `basic-information` WHERE `GovernmentID` = '$check'";

			$run = $this->core->database->doSelectQuery($sql);

			if ($this->run->num_rows($run) > 0) {
				echo '<img src="error.png"> <b>This ID is already registered please log in instead of completing this registration form again</b>';
			}

		}

	}

}
?>