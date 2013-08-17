<?php
class error {

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

		echo '<div class="menucontainer">';

		$function = __FUNCTION__;
		$title = 'An error occurred';
		$description = 'Please try something else!';

		echo component::generateBreadcrumb(get_class(), $function);
		echo component::generateTitle($title, $description);

		echo '<div class="errorpopup">' . $core->error . '</div></div>';
	}

}

?>