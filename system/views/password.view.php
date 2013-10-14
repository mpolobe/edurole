<?php
class password {

	public $core;
	public $view;

	public function configView() {
		$this->view->header = TRUE;
		$this->view->footer = TRUE;
		$this->view->menu = FALSE;
		$this->view->javascript = array('');
		$this->view->css = array(4);

		return $this->view;
	}

	public function buildView($core) {
		$this->core = $core;

		if ($this->core->action == "recover") {
			$this->recoverPassword();
		} else {
			$this->recoverPassword();
		}
	}

	public function recoverPassword() {
		echo '<div class="menucontainer">
			<div class="menubar">
			<div class="menuhdr"><strong>Home menu</strong></div>
			<div class="menu">
			<a href="' . $this->core->conf['conf']['path'] . '">Home</a>
			<a href="' . $this->core->conf['conf']['path'] . '/intake/studies">Overview of all studies</a>
			<a href="' . $this->core->conf['conf']['path'] . '/intake">Studies open for intake</a>
			<a href="' . $this->core->conf['conf']['path'] . '/password">Recover lost password</a>
			</div>
			</div>
			</div><div class="contentpadfull">';

		$function = __FUNCTION__;
		$title = 'Recover password';
		$description = 'Recover password your password using your email';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);

		include $this->core->conf['conf']['classPath'] . "changepassword.form.php";
	}


}

?>
