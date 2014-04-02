<?php
class register {

	public $core;
	public $view;

	public function configView() {
		$this->view->open = TRUE;
		$this->view->header = TRUE;
		$this->view->footer = TRUE;
		$this->view->menu = FALSE;
		$this->view->internalMenu = TRUE;
		$this->view->javascript = array('register', 'jquery.form-repeater');
		$this->view->css = array();

		return $this->view;
	}

	public function buildView($core) {
		$this->core = $core;
		$this->viewMenu();
	}

	private function viewMenu(){
		echo '<div class="collapse navbar-collapse  navbar-ex1-collapse">
			<ul class="nav navbar-nav side-nav">
				<li class="active"><strong>Home menu</strong></li>
				<li class="menu"><a href="' . $this->core->conf['conf']['path'] . '">Home</a></li>
				<li class="menu"><a href="' . $this->core->conf['conf']['path'] . '/intake/studies">Overview of all studies</a></li>
				<li class="menu"><a href="' . $this->core->conf['conf']['path'] . '/intake">Studies open for intake</a></li>
				<li class="menu"><a href="' . $this->core->conf['conf']['path'] . '/intake/register">Current student registration</a></li>
				<li class="menu"><a href="' . $this->core->conf['conf']['path'] . '/password">Recover lost password</a></li>
				</ul><div id="page-wrapper">';
	}

	public function submitRegister() {
		
		include $this->core->conf['conf']['classPath'] . "students.inc.php";
		$students = new students($this->core);

		$students->registerStudent();
	}

	public function studyRegister($item) {

		echo'<div id="templatepath" style="display:none">' . $this->core->fullTemplatePath .'</div>';
		echo'<div id="path" style="display:none">' . $this->core->conf['conf']['path'] .'</div>';
	
		if ($item) {

			if($_GET['existing'] == yes){
				$existing = TRUE;
			}

			$sql = "SELECT `study`.ID, `study`.Name FROM `study`,`schools` WHERE `study`.ParentID = `schools`.ID AND `study`.ID = $item";

			$run = $this->core->database->doSelectQuery($sql);

			while ($fetch = $run->fetch_row()) {

				echo '<form id="enroll" name="enroll" method="post" action="' . $this->core->conf['conf']['path'] . '/register/submit" enctype="multipart/form-data" >
				<input type="hidden" name="studyid" value="' . $fetch['0'] . '">
				<p>You are requesting admission to the following study: <b> ' . $fetch[1] . ' </b> <br>Please complete the following form entirely to successfully complete your request for admission.</p>';

				include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";

				$study = $fetch[0];

				$optionBuilder = new optionBuilder($this->core);

				$paymenttypes = 	$optionBuilder->showPaymentTypes();
				$major = 		$optionBuilder->showPrograms($study, 1, null);
				$minor = 		$optionBuilder->showPrograms($study, 2, null);

				include $this->core->conf['conf']['formPath'] . "register.form.php";

			}

			include $this->core->conf['conf']['path'] . "/lib/edurole/footer.js";
			include $this->core->conf['conf']['path'] . "/lib/edurole/reg.js";

		} else {

			$this->core->throwError('No study was selected, please <a href="' . $this->core->conf['conf']['path'] . '/intake">select one</a>');

		}
	}
}

?>
