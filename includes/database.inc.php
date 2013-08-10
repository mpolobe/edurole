<?php
class database{

	public $core;
        
	function __construct($core){
		$this->core = $core;
	}

	function connectDatabase(){
		$this->mysqli = new mysqli($this->core->conf['mysql']['server'],
								   $this->core->conf['mysql']['user'],
								   $this->core->conf['mysql']['password'],
								   $this->core->conf['mysql']['db']);
		
		if ($this->mysqli->connect_errno) {
			$this->core->logEvent("Error: " . $this->mysqli->connect_errno,"1");
			$this->core->throwError("Failed to connect to the database, please contact the administrator");
		}else{
			$this->core->logEvent("Database connection initialized","3");
		}

	}
	
	public function doInsertQuery($sql){
		
		if (!$run = $this->mysqli->query($sql)) {
			eduroleCore::logEvent("Query error: " . $this->mysqli->error,"1");

			if($this->mysqli->error == "Duplicate entry '0' for key 'PRIMARY'"){
				return("duplicate");
			} else {
				eduroleCore::logEvent("Query error: " . $this->mysqli->error,"1");
				eduroleCore::throwError("An error occurred with the information you have entered.");
				return false;
			}
		} else {
			$this->core->logEvent("Query executed: $sql","3");
			return true;
		}
	}
	
	public function doSelectQuery($sql){
	
		if (!$run = $this->mysqli->query($sql)) {
			eduroleCore::logEvent("Query error SQL: <span style=\"font-weight: normal;\">" . $sql . "</span>". $this->mysqli->error,"1");
			eduroleCore::throwError("An error occured with the database information retrieval");
			return false;
		}
	
		$this->core->logEvent("Query executed: $sql","3");
		return $run; 
	}
	
	public function closeConnection(){

		mysqli_close($this->connection);
		eduroleCore::logEvent("Database connection closed","3");

	}

}
?>
