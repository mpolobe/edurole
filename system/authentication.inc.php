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
					$this->authenticateSQL($username, $password);
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

		$passwordHashed = hash('sha512', $password . $this->core->conf['conf']['hash'] . $username);
		
		$sql = "SELECT access.ID as UserID, RoleID FROM `access` LEFT JOIN `basic-information` ON `basic-information`.`ID`=`access`.`Username` WHERE `access`.Username = '$username' AND `access`.Password = '$passwordHashed'";
		$run = $this->core->database->doSelectQuery($sql);

		if ($run->num_rows > 0) { //successful login
			$this->core->logEvent("User '$username' authenticated successfully", "3");
			
			while ($row = $run->fetch_assoc()) {
				$userID = $row['UserID'];
				$role = $row['RoleID'];
				$rolename = $this->role($role);
								
				if(empty($role)) {
					// User does not have any permissions
					$this->core->setViewError('Unauthorized access', "You do not have permissions to access this system, please contact the academic office", "LOGIN");
					$this->core->builder->initView("error");
				}
			}
			
		} else {
			$this->core->logEvent("User '$username' authentication failed", "2");
			return FALSE;
		}

		if(isset($username, $password, $userID, $role, $rolename)){
		
			$_SESSION['userid'] = $userID;
			$_SESSION['username'] = $username;
			$_SESSION['password'] = $password;
			$_SESSION['role'] = $role;
			$_SESSION['rolename'] = $rolename;

			$_SESSION['saobjects'] = $this->getStudyInformation($userID);

			$this->core->setUsername($username);
			$this->core->setUserID($userID);
			$this->core->setRoleName($rolename);
			$this->core->setRole($role);
		
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	public function getStudyInformation($userID){
		$sql = "SELECT `st`.Name,  `st`.ShortName, ProgramName, `sc`.Name FROM `access` as ac, `student-study-link` as ss, `study` as st, `student-program-link` as pl, `programmes` as pr, `schools` as sc, `basic-information` as bi
		WHERE ac.`ID` = '$userID' AND ac.`ID` = bi.`ID` AND bi.`GovernmentID` = ss.`StudentID` AND ss.`StudyID` = st.`ID`  AND bi.`GovernmentID` = pl.`StudentID` AND st.`ParentID` = sc.`ID` AND pl.`major` = pr.`id`
		OR  ac.`ID` = '$userID'  AND ac.`ID` = bi.`ID` AND bi.`GovernmentID` = ss.`StudentID` AND ss.`StudyID` = st.`ID`  AND bi.`GovernmentID` = pl.`StudentID` AND st.`ParentID` = sc.`ID` AND pl.`minor` = pr.`id`";

		$run = $this->core->database->doSelectQuery($sql);

		return $run->fetch_array(MYSQLI_NUM);
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

		while ($row = $run->fetch_assoc()) {
			return $row['RoleName'];
		}
	}

	function logout() {
		session_destroy();
		
		$this->core->setUsername(NULL);
		$this->core->setUserID(NULL);
		$this->core->setRoleName(NULL);
		$this->core->setRole(NULL);
			
		$this->core->setPage(NULL);
		$this->core->initializer();
	}
}

?>