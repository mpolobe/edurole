<?php
class mail {

	public $core;
	public $view;

	public function configView() {
		$this->view->header = TRUE;
		$this->view->footer = TRUE;
		$this->view->menu = TRUE;
		$this->view->javascript = array(3);
		$this->view->css = array(4);

		return $this->view;
	}

	public function buildView($core) {

		$this->core = $core;

		global $conf;

		$function = __FUNCTION__;
		$title = "Personal email";
		echo breadcrumb::generate(get_class(), $function);
		echo component::generateTitle($title, NULL);

		echo '<div style="margin-left: -15px; height: 100%;">';

		// PROOF OF CONCEPT ROUNDCUBE INTEGRATION, NEEDS OVERHAUL TO SHARED SESSION AUTHENTICATION
		echo '<iframe scrolling="no" width="768" height="100%" frameborder="0" src="http://' . $conf['conf']['domain'] . '/edurole/lib/roundcube?_autologin=1&username=' . $_SESSION['username'] . '&password=' . $_SESSION['password'] . '" seamless="seamless"></iframe>';
		// NOT PRODUCTION CODE

		echo '</div>';

	}

}

?>
