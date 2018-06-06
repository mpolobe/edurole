<?php
class recover {
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

		include_once $this->core->conf['conf']['libPath'] . '/secureimage/securimage.php';

		$securimage = new Securimage();

		if ($securimage->check($this->core->cleanPost['captcha_code']) == false) {
			echo "The security code entered was incorrect.<br /><br />";
			echo "Please go <a href='javascript:history.go(-1)'>back</a> and try again.";
			exit;
		}

		$uid = $this->core->cleanPost['uid'];
		$studentid = $this->core->cleanPost['studentid'];

		echo $uid . $studentid;

		$sql = "SELECT * FROM `basic-information` WHERE `ID` = '" . $studentid . "'";
		$run = doSelectQuery($sql);

		while ($fetch = mysql_fetch_row($run)) {

			$ID = $fetch[4];
			$firstname = $fetch[0];
			$middlename = $fetch[1];
			$surname = $fetch[2];
			$phonea = $fetch[13];
			$phoneb = $fetch[14];
			$email = $fetch[17];
		}
	}
}

?>
