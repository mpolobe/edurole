<?php
class password {

	public $core;
	public $view;

	public function buildView($core) {
		$this->core = $core;

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

	public function recoverPassword() {

		if(!isset($this->core->cleanPost['captcha_code'])){
			include $this->core->conf['conf']['formPath'] . 'recoverpassword.form.php';
		}else{

			include_once $this->core->conf['conf']['path'] . '/' . $this->core->conf['conf']['libPath'] . 'secureimage/securimage.php';
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


	public function recoveredPassword(){
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
                $run = $this->core->database->doSelectQuery($sql);

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
