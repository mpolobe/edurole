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
					return FALSE;
				} else {
					return TRUE;
				}
			}else {
				return TRUE;
			}

		} else {
			$this->core->setViewError('Please enter all fields', 'Please <a href=".">return to the login page</a> and try again.');
			var_dump($this->core);
			$this->core->builder->initView("error");
			return FALSE;
		}

		return FALSE;
	}

	private function authenticateLDAP($username, $password) {

		if ($this->core->conf['ldap']['ldapEnabled'] == TRUE) {

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
				
					$this->core->logEvent("User '$username' authenticated successfully", "3");
					$this->authorize($username, $password);
					return TRUE;
					
				} else {
				
					$this->core->logEvent("User '$username' authentication failed", "2");
					return FALSE;
					
				}

			} else {
				$this->core->logEvent("PHP-LDAP module missing or not enabled", "1");
			}

		}
		
		return FALSE;
	}

	private function authenticateSQL($username, $password) {

		$password = hash('sha512', $password . $this->core->conf['conf']['hash'] . $username);

		$sql = "SELECT ID FROM `access` WHERE `username` = '$username' AND `password` = '$password'";
		$run = $this->core->database->doSelectQuery($sql);

		if ($run->num_rows > 0) { //successful login

			$this->core->logEvent("User '$username' authenticated successfully", "3");
			$this->authorize($username, $password);
			return TRUE;

		} else {

			$this->core->logEvent("User '$username' authentication failed", "2");
			return FALSE;
		}
		
		return FALSE;
	}

	public function authorize($username, $password) {

		$passwordHashed = hash('sha512', $password . $this->core->conf['conf']['hash'] . $username);

		if (is_numeric($username)) {

			$sql = "SELECT * FROM `basic-information` WHERE ID LIKE '$username'";
			$run = $this->core->database->doSelectQuery($sql);

			if ($run->num_rows > 0) {

				$_SESSION['username'] = $username;

				$sql = "SELECT * FROM `access` WHERE Username LIKE '$username'";
				$run = $this->core->database->doSelectQuery($sql);

				while ($row = $run->fetch_row()) {
				
					$access = $row[2];
					$userid = $row[0];
					$rolename = $this->role($row[2]);
					
				}

				$sql = "SELECT `st`.Name,  `st`.ShortName, ProgramName, `sc`.Name FROM `access` as ac, `student-study-link` as ss, `study` as st, `student-program-link` as pl, `programmes` as pr, `schools` as sc, `basic-information` as bi
				WHERE ac.`ID` = '$userid' AND ac.`ID` = bi.`ID` AND bi.`GovernmentID` = ss.`StudentID` AND ss.`StudyID` = st.`ID`  AND bi.`GovernmentID` = pl.`StudentID` AND st.`ParentID` = sc.`ID` AND pl.`major` = pr.`id`
				OR  ac.`ID` = '$userid'  AND ac.`ID` = bi.`ID` AND bi.`GovernmentID` = ss.`StudentID` AND ss.`StudyID` = st.`ID`  AND bi.`GovernmentID` = pl.`StudentID` AND st.`ParentID` = sc.`ID` AND pl.`minor` = pr.`id`";

				$run = $this->core->database->doSelectQuery($sql);

				$_SESSION['saobjects'] = $run->fetch_array(MYSQLI_NUM);

				if (!isset($_SESSION['access'])) {

					// student authenticated successfully but doesn't have a role assigned (old migration data from Edurole v1.1)

					$this->core->logEvent("Partial user in database: user $username only present in 'basic-information' table", "1");

					$sql = "INSERT INTO `access` (`ID`, `Username`, `RoleID`, `Password`) VALUES ('$username', '$username', '10', '$passwordHashed');";
					$run = $this->core->database->doSelectQuery($sql);

					$access = "10";
				}

			} else {

				$this->core->logEvent("Partial user in database: user $username only present in 'access' table", "1");

			}

		} else {

			$sql = "SELECT ID, RoleID FROM `access` WHERE Username LIKE '$username'";
			$run = $this->core->database->doSelectQuery($sql);

			while ($row = $run->fetch_row()) {

				$userid = $row[0];
				$access = $row[1];
				$rolename = $this->role($row[1]);

				$this->core->logEvent("User $username authorized level $row[1]", "3");

			}

			if (!isset($_SESSION['access'])) {

				$sql = "INSERT INTO `access` (`ID`, `Username`, `RoleID`, `Password`) VALUES (NULL, '$username', '4', '$passwordHashed');";
				$run = $this->core->database->doSelectQuery($sql);

			}

		}
		
		$_SESSION['access'] = $access;
		$_SESSION['userid'] = $userid;
		$_SESSION['username'] = $username;
		$_SESSION['password'] = $password;
		$_SESSION['rolename'] = $rolename;
		
		$this->core->setUsername($username);
		$this->core->setUserID($userid);
		$this->core->setRoleName($rolename);
		$this->core->setRole($access);
		
		return TRUE;
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
				return TRUE;
			} else {
				return FALSE;
			}

		} catch (Exception $e) {
			$this->core->logEvent("Could not bind to LDAP server using user credentials", "1");
			$this->core->throwError("Could not bind to LDAP server using user credentials.");
		}


	}

	public function mysqlChangePass($username, $oldpass, $newpass) {

		$newpass = hash('sha512', $newpass . $this->core->conf['conf']['hash'] . $username);
		$oldpass = hash('sha512', $oldpass . $this->core->conf['conf']['hash'] . $username);

		$sql = "UPDATE `access` SET `Password` = '$newpass' WHERE `Username` = '$username' AND `Password` = '$oldpass'";

		if ($this->core->database->doSelectQuery($sql)) {
			$this->core->throwSuccess("Your password has been changed! The next time you log-in you will need to use your new password.");
		} else {
			return (1);
		}

	}

	private function role($access) {

		$sql = "SELECT * FROM `roles` WHERE RoleLevel LIKE '$access'";
		$run = $this->core->database->doSelectQuery($sql);

		while ($row = $run->fetch_row()) {
			return $row[1];
		}
	}

	function logout() {
		session_destroy();
		header("location: .");
	}
}

?>