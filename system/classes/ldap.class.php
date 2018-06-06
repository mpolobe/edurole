<?php

class ldap {

	function addAccount($username, $password, $firstname, $lastname) {

		$ldapconn = ldap_connect($this->core->conf['ldap']['server'], $this->core->conf['ldap']['port']);
		ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);

		$ldapbind = ldap_bind($ldapconn, "uid=rvos"," . "ou=staff,dc=nkrumah,dc=edu,dc=zm", "halo8335");

		if ($ldapbind) {
			$this->core->logEvent("Admin LDAP authenticated successfully", "4");
		} else {	
			$this->core->logEvent("Admin LDAP authentication failed", "1");
			return FALSE;
		}

		$group = 505;

		$info["dn"] = 'uid='.$username.',ou=students,'.$this->core->conf['ldap']['domain'];
		$info["uid"] = '.$username.';
		$info["uidNumber"] = $username;
		$info["gidNumber"] = (int) $group;
		$info["givenName"] = $firstname;
		$info["cn"] = $firstname .' '. $lastname;
		$info["sn"] = $lastname;
		$info["objectClass"][0] = "top";
		$info["objectClass"][1] = "person";
		$info["objectClass"][2] = "inetOrgPerson";
		$info["objectClass"][3] = "posixAccount";
		$info["objectClass"][4] = "organizationalPerson";
		$info["objectClass"][5] = "shadowAccount";
		$info["loginShell"] = "/bin/bash";
		$info["mail"] = $username.'@'.$this->core->conf['conf']['domain'];
		$info["homeDirectory"] = '/home/'.$username;

  		$r = ldap_add($ds, $info["dn"], $info);

		if(ldap_error($ldap_conn) == "Success"){
			ldap_close($ds);
			return true;
		} else {
			ldap_close($ds);
			return false;
		}
	}

}

$ldap = new ldap;
$ldap->addAccount("bobs", "123", "Bob", "Sachet");

?>