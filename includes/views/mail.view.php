<?php
class mail {

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

		global $conf;

		$function = __FUNCTION__;
		echo breadcrumb::generate(get_class(), $function);

		echo '<div class="contentpadmail"><p class="title2">Personal email</p> <br /><div style="margin-left: -15px; height: 100%;">';

		// PROOF OF CONCEPT ROUNDCUBE INTEGRATION, NEEDS OVERHAUL TO SHARED SESSION AUTHENTICATION
		echo '<iframe scrolling="no" width="768" height="100%" frameborder="0" src="http://' . $conf['conf']['domain'] . '/edurole/lib/roundcube?_autologin=1&username=' . $_SESSION['username'] . '&password=' . $_SESSION['password'] . '" seamless="seamless"></iframe>';
		// NOT PRODUCTION CODE

		echo '</div>';

	}

}

?>
