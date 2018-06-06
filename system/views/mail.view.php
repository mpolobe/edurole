<?php
class mail {

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

	public function showMail() {

		echo '<div style="margin-top: 15px; height: 100%;">';


		if ( ! filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE) ){
			$url = "http://mail.ru.edu.zm/roundcube";
		}else{
			$url = "http://ru.edu.zm:8080/roundcube";
		}

		// PROOF OF CONCEPT ROUNDCUBE INTEGRATION, NEEDS OVERHAUL TO SHARED SESSION AUTHENTICATION
		echo '<iframe scrolling="no" width="768" height="100%" frameborder="0" src="'.$url.'/?_autologin=1&username=' . $_SESSION['username'] . '&password=' . $_SESSION['password'] . '" seamless="seamless"></iframe>';
		// NOT PRODUCTION CODE

		echo '</div>';

	}

}

?>
