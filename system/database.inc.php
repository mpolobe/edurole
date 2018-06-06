<?php
class database {

	public $core;

	function __construct($core) {
		$this->core = $core;
		$this->connectDatabase();
	}

	function connectDatabase() {
		$this->mysqli = new mysqli($this->core->conf['mysql']['server'],
			$this->core->conf['mysql']['user'],
			$this->core->conf['mysql']['password'],
			$this->core->conf['mysql']['db']);

		if ($this->mysqli->connect_errno) {
			$this->core->logEvent("Error: " . $this->mysqli->connect_errno, "1");
			$this->core->throwError("Failed to connect to the database, please contact the administrator");
		} else {
			$this->core->logEvent("Database connection initialized", "3");
		}

	}

	public function fetch_all($run){
		for ($res = array(); $tmp = $run->fetch_array();) $res[] = $tmp;
		return $res;
	}

	public function prepareQuery($sql){
		$run = $this->mysqli->prepare($sql);
		return $run;
	}

	public function escape($data){
		$run = $this->mysqli->real_escape_string($data);
		return $run;
	}

	public function id(){
		$run = $this->mysqli->insert_id;
		return $run;
	}

	public function doInsertQuery($sql, $quiet=FALSE) {
		if($this->core->roleName == "Auditor"){
			$this->core->throwError("You are an auditor, therefore you are not able to edit, save or modify.");
			return;
		}

		if (!$run = $this->mysqli->query($sql)) {
			$this->core->logEvent("Query error: " . $this->mysqli->error, "1");

			if ($this->mysqli->error == "Duplicate entry '0' for key 'PRIMARY'") {
				return ("duplicate");
			} else {
				if($quiet == FALSE){
					$this->core->logEvent("Query error: " . $this->mysqli->error, "1");
					$this->core->throwError("An error occurred with the information you have entered.");
				}
				return FALSE;
			}
		} else {
			$this->core->logEvent("Query executed: $sql", "3");
			return TRUE;
		}
	}

	public function doSelectQuery($sql) {

		if (!$run = $this->mysqli->query($sql)) {
			$this->core->logEvent("Query error SQL: <span style=\"font-weight: normal;\">" . $sql . "</span>" . $this->mysqli->error, "1");
			$this->core->throwError("An error occurred with the database information retrieval system query failed: <br /> " . $sql);
			return false;
		}

		$this->core->logEvent("Query executed: $sql", "3");
		return $run;
	}

	public function closeConnection() {
		mysqli_close($this->connection);
		$this->core->logEvent("Database connection closed", "3");
	}

}

?>