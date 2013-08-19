<?php
class login {

	public $core;
	public $view;

	public function configView() {
		$this->view->header = TRUE;
		$this->view->footer = TRUE;
		$this->view->javascript = array(3);
		$this->view->css = array(4, 6);

		return $this->view;
	}

	public function buildView($core) {

		$this->core = $core;

		$this->core->logEvent("Initializing login view", "3");

		include $this->core->conf['conf']['formPath'] . "login.form.php";

	}
}

?>