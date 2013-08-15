<?php
class request {

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

		include "includes/classes/showoptions.inc.php";

		$select = new optionBuilder($core);

		$study = $select->showStudies(null);
		$program = $select->showPrograms(null, null, null);

		$function = __FUNCTION__;
		$title = 'Search student records';
		$description = 'Please use the form below to search through the student information database';

		echo component::generateBreadcrumb(get_class(), $function);
		echo component::generateTitle($title, $description);

		if ($this->core->role > 100) {
			echo '<p>You can search for a single record or a group by utilizing the various search categories.</p>
			<div class="heading">Search by student number</div>';

			include "includes/forms/searchform.form.php";
		} else {
			$this->core->throwError("You do not have the authority to do system wide searches");
		}
	}
}

?>


