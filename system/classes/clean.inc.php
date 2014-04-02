<?php
class dataCleaner {

	public $core;

	public function __construct($core) {
		$this->core = $core;
	}

	private function indexAccess($item=FALSE){
		if($item == FALSE){

			$sql = 'INSERT INTO `access` (Username, RoleID, Password)
					SELECT `basic-information`.ID AS Username, 4 AS RoleID, 0 AS Password FROM `basic-information` WHERE `basic-information`.ID 
					NOT IN (SELECT `access`.Username FROM `access`)';

		} else {

			$sql = 'INSERT INTO `access` (Username, RoleID, Password)
					SELECT `basic-information`.ID AS Username, 4 AS RoleID, 0 AS Password FROM `basic-information` WHERE `basic-information`.ID = "'.$item.'"';

		}

		$run = $this->core->database->doInsertQuery($sql);

	}

}

?>