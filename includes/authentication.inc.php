<?php
class auth {

	public $core;

	public function __construct($core) {

		$this->core = $core;
	}

	public function login() {

		$username = $this->core->cleanPost['username'];
		$password = $this->core->cleanPost['password'];

		if (isset($username) && isset($password)) {

			if (!$this->authenticateLDAP($username, $password)) {

				if (!$this->authenticateSQL($username, $password)) {

					$this->core->throwError('<p>Please <a href=".">return to the login page</a> and try again. If you forgot your password please request a new one <a href="password.php">here</a>.</p>', $pagename);

				}

			}

		} else {
			echo "<h2>Please enter all fields</h2>";
		}

	}

	private function authenticateLDAP($username, $password) {

		if ($this->core->conf['conf']['ldapenabled'] == TRUE) {

			if (is_numeric($username)) { //Student usernames are numeric
				$ou = $this->core->conf['ldap']['studentou']; //Use student OU
			} else {
				$ou = $this->core->conf['ldap']['staffou'];
			}

			if (function_exists('ldap_connect')) {

				$ldapconn = ldap_connect($this->core->conf['ldap']['server'], $this->core->conf['ldap']['port']);
				ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);

				$ldapbind = @ldap_bind($ldapconn, "uid=" . $username . "," . $ou, $password);

				if ($ldapbind) { //successful login
					$this->core->logEvent("User '$username' authenticated succesfuly", "3");
					auth::authorize($username, $password);
				} else {
					$this->core->logEvent("User '$username' authentication failed", "2");
					return false;
				}

			} else {
				$this->core->logEvent("PHP-LDAP module missing or not enabled", "1");
			}

		}
		return false;
	}

	private function authenticateSQL($username, $password) {

		$password = sha512($password . $this->core->conf['conf']['hash'] . $username);

		$sql = "SELECT ID FROM `access` WHERE `username` = '$username' AND `password` = '$password'";
		$run = $this->core->database->doSelectQuery($sql);

		if ($run->num_rows > 0) { //successful login

			$this->core->logEvent("User '$username' authenticated succesfuly", "3");
			auth::authorize($username, $password);

		} else {

			$this->core->logEvent("User '$username' authentication failed", "2");
			return false;
		}

	}

	public function authorize($username, $password) {

		$passwordHashed = sha512($password . $this->core->conf['conf']['hash'] . $username);

		if (is_numeric($username)) {

			$sql = "SELECT * FROM `basic-information` WHERE ID LIKE '$username'";
			$run = $this->core->database->doSelectQuery($sql);

			if ($run->num_rows > 0) {

				$_SESSION['username'] = $username;

				$sql = "SELECT * FROM `access` WHERE Username LIKE '$username'";
				$run = $this->core->database->doSelectQuery($sql);

				while ($row = $run->fetch_row()) {

					$_SESSION['access'] = $row[2];
					$_SESSION['userid'] = $row[0];
					$_SESSION['username'] = $username;
					$_SESSION['password'] = $password;
					auth::role($row[2]);
				}

				$sql = "SELECT `st`.Name,  `st`.ShortName, ProgramName, `sc`.Name FROM `access` as ac, `student-study-link` as ss, `study` as st, `student-program-link` as pl, `programmes` as pr, `schools` as sc, `basic-information` as bi
				WHERE ac.`ID` = '$id' AND ac.`ID` = bi.`ID` AND bi.`GovernmentID` = ss.`StudentID` AND ss.`StudyID` = st.`ID`  AND bi.`GovernmentID` = pl.`StudentID` AND st.`ParentID` = sc.`ID` AND pl.`major` = pr.`id`
				OR  ac.`ID` = '$id'  AND ac.`ID` = bi.`ID` AND bi.`GovernmentID` = ss.`StudentID` AND ss.`StudyID` = st.`ID`  AND bi.`GovernmentID` = pl.`StudentID` AND st.`ParentID` = sc.`ID` AND pl.`minor` = pr.`id`";

				$run = $this->core->database->doSelectQuery($sql);

				$_SESSION['saobjects'] = $run->fetch_array(MYSQLI_NUM);

				if (!isset($_SESSION['access'])) {

					// student authenticated succesfuly but doesn't have a role assigned (old migration data from Edurole v1.1)

					$this->core->logEvent("Partial user in database: user $username only present in 'basic-information' table", "1");

					$sql = "INSERT INTO `access` (`ID`, `Username`, `RoleID`, `Password`) VALUES ('$username', '$username', '10', '$passwordHashed');";
					$run = $this->core->database->doSelectQuery($sql);

					$_SESSION['access'] = "10";
				}

			} else {

				$this->core->logEvent("Partial user in database: user $username only present in 'access' table", "1");

			}

		} else {

			$sql = "SELECT ID, RoleID FROM `access` WHERE Username LIKE '$username'";
			$run = $this->core->database->doSelectQuery($sql);

			while ($row = $run->fetch_row()) {

				$_SESSION['username'] = $username;
				$_SESSION['password'] = $password;
				$_SESSION['userid'] = $row[0];
				$_SESSION['access'] = $row[1];
				$this->role($row[1]);

				$this->core->logEvent("User $username authorized level $row[1]", "3");

			}

			if (!isset($_SESSION['access'])) {

				$sql = "INSERT INTO `access` (`ID`, `Username`, `RoleID`, `Password`) VALUES (NULL, '$username', '4', '$passwordHashed');";
				$run = $this->core->database->doSelectQuery($sql);

			}

		}

		if (!isset($_SESSION['access'])) {

			$this->core->logEvent("Unauthorized access by $username.", "3");
			$this->core->exitError("You do not have permissions to access this system, please contact the academic office", $pagename);

		} else {

			header("location: .");

		}

	}

	public function ldapChangePass($username, $oldpass, $newpass) {

		// Select correct organizational unit from LDAP tree configuration
		if ($this->core->role > 1) {
			$ou = $this->core->conf['ldap']['staffou'];
		} elseif ($this->core->role > 10) {
			$ou = $this->core->conf['ldap']['adminou'];
		} else {
			$ou = $this->core->conf['ldap']['studentou'];
		}

		try {
			$ldapconn = ldap_connect($this->core->conf['ldap']['server'] . "s", $this->core->conf['ldap']['port']);
			ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);

		} catch (Exception $e) {

			$this->core->logEvent("Could not connect to LDAP server.", "1");
			$this->core->throwError("Could not connect to LDAP server.");
		}

		try {
			ldap_bind($ldapconn, "uid=" . $username . "," . $ou, $oldpass);

			$userpassword = "{SHA}" . base64_encode(pack("H*", sha1($newpass)));
			if (ldap_mod_replace($ldapconn, "uid=" . $username . "," . $ou, array('userpassword' => $userpassword))) {
				echo "<p><h2>YOUR PASSWORD IS NOW CHANGED</h2></p>";
			} else {
				return (FALSE);
			}

		} catch (Exception $e) {
			$this->core->logEvent("Could not bind to LDAP server using user credentials", "1");
			$this->core->throwError("Could not bind to LDAP server using user credentials.");
		}


	}

	public function mysqlChangePass($username, $oldpass, $newpass) {

		$newpass = sha1($newpass);
		$oldpass = sha1($oldpass);

		$sql = "UPDATE `access` SET `Password` = '$newpass' WHERE `Username` = '$username' AND `Password` = '$oldpass'";

		if ($this->core->database->doSelectQuery($sql)) {
			$this->core->throwSuccess("YOUR PASSWORD IS NOW CHANGED");
		} else {
			return (1);
		}

	}

	private function role($access) {

		$sql = "SELECT * FROM `roles` WHERE RoleLevel LIKE '$access'";
		$run = $this->core->database->doSelectQuery($sql);

		while ($row = $run->fetch_row()) {
			$_SESSION['rolename'] = $row[1];
		}
	}

	function logout() {
		session_destroy();
		header("location: .");
	}
}

?>