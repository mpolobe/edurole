<?php
class example {

	public $core;
	public $view;

	public function configView() {
		$this->view->header = TRUE;
		$this->view->footer = TRUE;
		$this->view->menu = FALSE;
		$this->view->javascript = array(2, 3, 9);
		$this->view->css = array(4, 1, 2);

		return $this->view;
	}

	public function buildView($core) {
		$this->core = $core;

		if (empty($this->core->action) || $this->core->action == "one") {
			$this->actionFunctionOne();
		}else if ($this->core->action == "two" && $core->role > 1) {
			$this->actionFunctionOne();
		}else if ($this->core->action == "two" && $core->role = 1000) {
			$this->actionFunctionThree();
		}
	}

	public function actionFunctionOne(){
		echo "<h1> Hello World! <h1>";
		echo "<p>This is the default example.</p>
		 	Try opening the individual action pages by:
		 		<ul>
		 			<li>http://".$_SERVER['HTTP_HOST']."/example/one</li>
		 			<li>http://".$_SERVER['HTTP_HOST']."/example/two</li>
		 			<li>http://".$_SERVER['HTTP_HOST']."/example/three</li>
		 		</ul>";
	}

	public function actionFunctionTwo(){
		$function = __FUNCTION__;
		$title = 'Custom Action Page Title';
		$description = 'Custom description subtext.';

		echo component::generateBreadcrumb(get_class(), $function);		// Generate breadcrumb
		echo component::generateTitle($title, $description);			// Generate Title/Description pagehead

		echo"Load some content here! Try including a file and calling up functions from the class to do whatever.";
	}

	public function actionFunctionThree(){
		echo"You must be an administrator";
	}
}

?>