<?php
class example {

	public $core;
	public $view;

	public function configView() {
		$this->view->header = TRUE;
		$this->view->footer = TRUE;
		$this->view->menu = TRUE;
		$this->view->javascript = array();
		$this->view->css = array();

		return $this->view;
	}

	public function buildView($core) {
		$this->core = $core;
	}

	public function showExample() {
		echo "<h1> Hello World! <h1>";
		echo "<p>This is the default example.</p>
		 	Try opening the individual action pages by:
		 		<ul>
		 			<li>http://" . $_SERVER['HTTP_HOST'] . "/example/ <- This page</li>
		 			<li>http://" . $_SERVER['HTTP_HOST'] . "/example/actiontwo</li>
		 			<li>http://" . $_SERVER['HTTP_HOST'] . "/example/actionthree</li>
		 		</ul>";
	}

	public function actiontwoExample() {
		echo "Load some content here! Try including a file and calling up functions from the class to do whatever.";
	}

	public function actionthreeExample() {
		echo "You must be an administrator";
	}
}

?>
