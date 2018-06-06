<?php
class error {

	public $core;
	public $view;

	public function configView() {
		return $this->view;
	}

	public function buildView($core) {
		$this->core = $core;
	}

	public function showError(){
		$error = $this->core->getViewError();
		echo "<h2>" . $error->message . "</h2>";
		$this->core->throwError($error->description);
	}
}

?>
