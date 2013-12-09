<?php
class accounting {

	public $core;

	function __construct($core){
		$this->core = $core;
	}

	public function sysLog($action, $level){

		$username = $this->core->username;
		$userid = $this->core->userID;
		$role = $this->core->role;

		$sql = "INSERT INTO `system-accounting` (`ID`, `UserID`, `Role`, `Action`, `Level`, `DateTime`) 
			VALUES (NULL, '$userid', '$role', '$action', '$level', NOW());";

		$run = $this->core->database->doInsertQuery($sql);

		return $results;
	}
}
?>
