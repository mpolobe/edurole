<?php
class checkValue extends eduroleCore {

	/*
	 * Echo value for javascript "username taken check" in forms
	 */

	public function __construct() {

		if (isset($this->core->cleanPost['username'])) {

			$check = mysql_real_escape_string($this->core->cleanPost['username']);
			$sql = "SELECT * FROM `basic-information` WHERE `GovernmentID` = '$check'";

			$run = $this->core->database->doSelectQuery($sql, $connection);

			if (mysql_num_rows($run) > 0) {
				echo '<img src="error.png"> <b>This ID is already registered please log in instead of completing this registration form again</b>';
			}

		}

	}

}

new checkValue();
?>
