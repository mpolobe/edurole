<?php
class password {

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

		if ($this->core->action == "recover") {
			$this->recoverPassword();
		}elseif ($this->core->action == "change" && $core->role > 0) {
			$this->changePassword();
		}
	}

	public function recoverPassword(){
		echo '<div class="menucontainer">
		<div class="menubar">
		<div class="menuhdr"><strong>Home menu</strong></div>
		<div class="menu">
		<a href=".">Home</a>
		<a href="admission?id=info">Overview of all studies</a>
		<a href="admission">Studies open for intake</a>
		<a href="password">Recover lost password</a>
		</div>
		</div>
		</div>';

		$function = __FUNCTION__;
		$title = 'Recover password';
		$description = 'Recover password your password using your email';

		echo component::generateBreadcrumb(get_class(), $function);
		echo component::generateTitle($title, $description);

		include $this->core->classPath . "changepassword.form.php";
	}

	public function changePassword(){
		$function = __FUNCTION__;

		$oldpass = $this->core->cleanPost["oldpass"];
		$newpass = $this->core->cleanPost["newpass"];
		$newpasscheck = $this->core->cleanPost["newpasscheck"];

		$title = 'Overview of personal assignments';
		$description = 'Your assignments currently active in your courses and programmes';

		echo component::generateBreadcrumb(get_class(), $function);
		echo component::generateTitle($title, $description);

		if (isset($newpass) && isset($oldpass)) {

			if ($newpass == $newpasscheck) {

				if (auth::ldapChangePass($this->username, $oldpass, $newpass)) {

					if (auth::mysqlChangePass($this->username, $oldpass, $newpass)) {
						eduroleCore::throwError("The information you have entered is incorrect.");
					}
				}

			} else {

				echo "<h2>The entered passwords do not match</h2>";
			}

		} else {

			echo "<p>Please remember to enter all fields!</p>";
			include $this->core->formPath . "changepass.form.php";

		}
	}
}

?>
