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
			<a href="' . $this->core->conf['conf']['path'] . '/intake/register">Current student registration</a>
			<a href="' . $this->core->conf['conf']['path'] . '/password">Recover lost password</a>
			</div>
			</div>
			</div><div class="contentpadfull">';

		$function = __FUNCTION__;
		$title = 'Recover password';
		$description = 'Recover your password using your email';

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($title, $description);

		if(!isset($this->core->cleanPost['captcha_code'])){
			include $this->core->conf['conf']['formPath'] . 'recoverpassword.form.php';
		}else{

			include_once $this->core->conf['conf']['libPath'] . '/secureimage/securimage.php';
			$securimage = new Securimage();

			if ($securimage->check($this->core->cleanPost['captcha_code']) == false) {
				echo "The security code entered was incorrect.<br /><br />";
				echo "Please go <a href='javascript:history.go(-1)'>back</a> and try again.";
				exit;
			}

			$uid = $this->core->cleanPost['uid'];
			$studentid = $this->core->cleanPost['studentid'];

			$sql = "SELECT * FROM `basic-information` WHERE `ID` = '$studentid' OR `GovernmentID` = '$uid'";
			$run = $this->core->database->doSelectQuery($sql);
	
			while ($fetch = $run->fetch_assoc()) {
				$this->core->throwSuccess("Your password was sent to your email or the administrator");
			}
		}
	}


}

?>
