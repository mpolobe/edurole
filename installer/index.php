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
			require_once "../system/database.inc.php";
			require_once $core;
		} else {
			echo "The EduRole core could not be loaded";
		}

		$this->core = new eduroleCore($conf, FALSE);

		$this->cssFiles = '<link href="../templates/edurole/css/style.css" rel="stylesheet" type="text/css" />';
		require_once "../templates/edurole/header.inc.php";

		echo'<div class="contentpadfull"> <form action=".">
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

		$this->core->throwSuccess("MySQL server configuration");
		foreach($conf["mysql"] as $name=>$value){
			echo'<label for="'.$name.'">'.$name.'</label><input type="text" name="'.$name.'" value="'.$value.'"><br/>';
		}

		$this->core->throwSuccess("General configuration");
		foreach($conf["conf"] as $name=>$value){
			echo'<label for="'.$name.'">'.$name.'</label><input type="text" name="'.$name.'" value="'.$value.'"><br/>';
		}

		echo'<label for="submit"> </label><input type="submit" value="Save settings"> </div></form>';

		clearstatcache();
		require_once "../templates/edurole/footer.inc.php";
	}
}

$installer = new installer();
$installer->install();
?>