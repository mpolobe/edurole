<?php
session_start();

class installer {

	public function install(){
		$configFile = "../system/config.inc.php";
		$core = "../system/core.inc.php";

		if(file_exists($configFile)){
			require_once $configFile;
		} else {
			echo "Please restore the base configuration file";
		}

		if(file_exists($core)){
			require_once $core;
		} else {
			echo "The EduRole core could not be loaded";
		}

		$this->core = new eduroleCore($conf, FALSE);

		$this->cssFiles = '<link href="../templates/edurole/css/style.css" rel="stylesheet" type="text/css" />';
		require_once "../templates/edurole/header.inc.php";

		echo'<div class="contentpadfull"> <form action="?save=true">
		<div class="title">Welcome to EduRole</div>
		<ul>
			<li>PHP dependencies: php5-ldap, php5-imap, php5-gd</li>
			<li>Apache dependencies: mod_rewrite</li>
		</ul>';
		
		if (!extension_loaded('gd', 'gd2')) {
			$this->core->throwError("The php5-gd extension is either not installed or not loaded, try installing it!");
		}
		if (!extension_loaded('ldap')) {
			$this->core->throwError("The php5-ldap extension is either not installed or not loaded, try installing it!");
		}
		if (!extension_loaded('mysql')) {
			$this->core->throwError("The php5-mysql extension is either not installed or not loaded, try installing it!");
		}
		if (!extension_loaded('imap')) {
			$this->core->throwError("The php5-imap extension is either not installed or not loaded, try installing it!");
		}

		$this->core->throwSuccess("Please enter the following information");

		echo'<div>';
		require_once "configNamer.inc.php";

		$this->core->throwSuccess("General configuration");
		foreach($conf["conf"] as $name=>$value){
			if($fullname["conf"][$name][1]=="text"){ $input = '<input type="text" name="'.$name.'" value="'.$value.'">'; 
			}elseif($fullname["conf"][$name][1]=="select"){ $input = '<select name="'.$name.'"> <option value="ON">ON</option> <option value="OFF">OFF</option> </select>'; }

			echo'<label for="'.$name.'">'.$fullname["conf"][$name][0].'</label>'.$input.'<br/>';
			if($name == "path"){ break; }
		}
		
		$this->core->throwSuccess("MySQL server configuration");
		foreach($conf["mysql"] as $name=>$value){
			if($fullname["mysql"][$name][1]=="text"){ $input = '<input type="text" name="'.$name.'" value="'.$value.'">'; 
			}elseif($fullname["mysql"][$name][1]=="select"){ $input = '<select name="'.$name.'"> <option value="ON">ON</option> <option value="OFF">OFF</option> </select>'; }

			echo'<label for="'.$name.'">'.$fullname["mysql"][$name][0].'</label>'.$input.'<br/>';
		}
		
		$this->core->throwSuccess("LDAP server configuration");
		foreach($conf["ldap"] as $name=>$value){
			if($fullname["ldap"][$name][1]=="text"){ $input = '<input type="text" name="'.$name.'" value="'.$value.'">'; 
			}elseif($fullname["ldap"][$name][1]=="select"){ $input = '<select name="'.$name.'"> <option value="ON">ON</option> <option value="OFF">OFF</option> </select>'; }
			
			echo'<label for="'.$name.'">'.$fullname["ldap"][$name][0].'</label>'.$input.'<br/>';
		}
		
		$this->core->throwSuccess("Mail configuration");
		foreach($conf["mail"] as $name=>$value){
			if($fullname["mail"][$name][1]=="text"){ $input = '<input type="text" name="'.$name.'" value="'.$value.'">'; 
			}elseif($fullname["mail"][$name][1]=="select"){ $input = '<select name="'.$name.'"> <option value="ON">ON</option> <option value="OFF">OFF</option> </select>'; }
			
			echo'<label for="'.$name.'">'.$fullname["mail"][$name][0].'</label>'.$input.'<br/>';
		}

		$this->core->throwSuccess("Write your configuration to file!");
		echo'<label for="submit"> </label><input type="submit" value="Save settings"> </div></form>';

		clearstatcache();
		require_once "../templates/edurole/footer.inc.php";
	}
	
	public function save(){
		// Write changes to configuration
		$config = "config.inc.php";
		$fh = fopen($config, 'w+') or die("can't open configuration file");
		foreach ($_POST as $key => $value){
			$rule = '$conf[\'conf\']['.$key.'] = "'.$value.'";';
			fwrite($fh, $rule);
		}
		
		fclose($fh);
		
		// Base configuration completed, starting core
		require_once "../system/database.inc.php";
		require_once "../system/core.inc.php";
		$this->core = new eduroleCore($conf, FALSE);

		// Loading database
		$fp = file('sql/edurole.sql', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		$query = '';
		foreach ($fp as $line) {
			if ($line != '' && strpos($line, '--') === false) {
				$query .= $line;
				if (substr($query, -1) == ';') {
					$this->core->database->doSelectQuery($query);
					$query = '';
				}
			}
		}

		// Set users password
		$username = "admin";
		$hash = $conf['conf']['hash'];
		$password = $password;
		$passenc =  hash('sha512', $password . $hash . $username);
		$sql = "INSERT INTO `access` (`ID`, `Username`, `RoleID`, `Password`) VALUES (100001, 'admin', 1000, '');";
		$this->core->database->doSelectQuery($sql);
		
		
		// Finished
		$this->core->throwSuccess("System has been installed. Your kingdom <a href=\"../\">awaits!</a>");
		
	}
}

$installer = new installer();
if(isset($_GET['save'])){
	$installer->save();
} else {
	$installer->install();
}
?>