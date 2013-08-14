<?php
class changepass {

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

		$oldpass = $this->core->cleanPost["oldpass"];
		$newpass = $this->core->cleanPost["newpass"];
		$newpasscheck = $this->core->cleanPost["newpasscheck"];

		$function = __FUNCTION__;
		echo breadcrumb::generate(get_class(), $function);

		echo '<div class="contentpadfull">
            <p class="title2">' . $pagename . '</p> ';

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
			include "includes/forms/changepass.form.php";

		}
	}
}

?>
