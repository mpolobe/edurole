<?php
class password {

	public $core;
	public $view;

	public function buildView($core) {
		$this->core = $core;
	}

	private function viewMenu(){
		if($this->core->role == 0){
                	echo '<div class="collapse navbar-collapse  navbar-ex1-collapse">
                	<ul class="nav navbar-nav side-nav">
                	<li class="active"><strong>Home menu</strong></li>
                	<li class="menu"><a href="' . $this->core->conf['conf']['path'] . '">Home</a></li>
                	<li class="menu"><a href="' . $this->core->conf['conf']['path'] . '/intake/studies">Overview of all studies</a></li>
                	<li class="menu"><a href="' . $this->core->conf['conf']['path'] . '/intake">Studies open for intake</a></li>
                	<li class="menu"><a href="' . $this->core->conf['conf']['path'] . '/intake/register">Current student registration</a></li>
                	<li class="menu"><a href="' . $this->core->conf['conf']['path'] . '/password/recover">Recover lost password</a></li>
                	</ul><div id="page-wrapper">';
		}
	}

	private function generatePassword($length, $charset = 'abcdefghijklmnopqrstuvwxyz23456789') {
		$str = '';
		$count = strlen($charset);
		while ($length--) {
			$str .= $charset[mt_rand(0, $count - 1)];
		}
		return $str;
	}

	public function changePassword($item) {

		if($this->core->role == 1000){
			if(empty($item)){
				$item = $this->core->username;
			}			
			$admin = TRUE;
		}else {
			$item = $this->core->username;
		}

		$oldpass = $this->core->cleanPost["oldpass"];
		$newpass = $this->core->cleanPost["newpass"];
		$newpasscheck = $this->core->cleanPost["newpasscheck"];

		$auth = new auth($this->core);
		
		if (!empty($newpass)) {

			if ($newpass == $newpasscheck) {

				if ($auth->ldapChangePass($item, $oldpass, $newpass) == false) {
					$ldap = false;
				}
				if ($auth->mysqlChangePass($item, $oldpass, $newpass, $admin) == false && $ldap == false) {
					$this->core->throwError("The information you have entered is incorrect.");
				}

			} else {
				echo "<h2>The entered passwords do not match</h2>";
			}

		} else {

			echo "<p>Please remember to enter all fields!</p>";
			include $this->core->conf['conf']['formPath'] . "changepass.form.php";

		}
	}


	public function resetPassword($item) {
		$admin = TRUE;
		$newpass = "12345";

		$auth = new auth($this->core);

		if ($auth->mysqlChangePass($item, $oldpass, $newpass, $admin) == false) { }

		$this->core->redirect("information", "search");
	}


	public function recoverPassword() {
		if(isset($this->core->cleanPost['uid']) && isset($this->core->cleanPost['studentid'])){
			$uid = $this->core->cleanPost['uid'];
			$studentid = $this->core->cleanPost['studentid'];

			$sql = "SELECT * FROM `basic-information` WHERE `ID` = '$studentid' AND `GovernmentID` = '$uid'";
			$run = $this->core->database->doSelectQuery($sql);

			while ($fetch = $run->fetch_row()) {
				$found = TRUE;
				$this->core->throwSuccess("Your records were found");
				$phone = $fetch[14];
				if(isset($phone)){
					include $this->core->conf['conf']['viewPath'] . "sms.view.php";
					$sms = new sms($this->core);
					$sms->buildView($this->core);

					$password = $this->generatePassword(5);
					$passenc = hash('sha512', $password . $this->core->conf['conf']['hash'] . $studentid);

					$sql = "UPDATE `access` SET `Password` = '$passenc' WHERE `ID` = $studentid;";
					$this->core->database->doInsertQuery($sql);

					$sms->directSms("26".$phone, "Your new password is: $password");
					$this->core->throwSuccess("Your new password was sent by SMS to phone number: $phone");

					$this->core->throwSuccess("Please <a href=\"".$this->core->conf['conf']['path']."/\">log in</a> with the new password");
				} else {
					$this->core->throwError("Report to the helpdesk you do not have a phone number to send a password to.");
				}
			}

			if($found != TRUE){
				$this->core->throwError("NRC or Student number do not match");
				include $this->core->conf['conf']['formPath'] . "recoverpassword.form.php";	
			}
		}else {
			include $this->core->conf['conf']['formPath'] . "recoverpassword.form.php";
		}
	}
}
?>
