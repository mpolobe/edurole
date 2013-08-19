<?php
class register {

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

		if ($this->view->menu != FALSE) {

			echo '<div class="menucontainer">
			<div class="menubar"><div class="menuhdr"><strong>Information for admission</strong></div><div class="menu">
			<a href=".">Home</a>
			<a href="index.php?id=info">Overview of all studies</a>
			<a href="admission">Studies open for intake</a>
			</div></div></div>';

		}

		$function = __FUNCTION__;
		$title = 'Register for study';
		$description = 'Please enter the complete for to be eligible for admission';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);

		$item = $core->cleanGet['item'];

		if ($item) {

			$sql = "SELECT `study`.ID, `study`.Name FROM `study`,`schools` WHERE `study`.ParentID = `schools`.ID AND `study`.ID = $item";

			$run = $this->core->database->doSelectQuery($sql);

			while ($fetch = $run->fetch_row()) {

				echo '<form id="enroll" name="enroll" method="post" action="index.php?id=submit-registration" enctype="multipart/form-data" >
							 <input type="hidden" name="studyid" value="' . $row['0'] . '">
							 <p>You are requesting admission to the following study: <b> ' . $row[1] . ' </b> <br>Please complete the following form entirely to successfully complete your request for admission.</p>';

				include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";

				$study = $fetch[0];

				$optionBuilder = new optionBuilder($core);

				$paymenttypes = $optionBuilder->showPaymentTypes();

				$major = $optionBuilder->showPrograms($study, 1, null);
				$minor = $optionBuilder->showPrograms($study, 2, null);

				include $this->core->conf['conf']['formPath'] . "register.form.php";

			}

			include "js/footer.js";
			include "js/reg.js";

		} else {

			$this->core->throwError('No study was selected, please <a href="?id=admission">select one</a>');

		}
	}
}

?>