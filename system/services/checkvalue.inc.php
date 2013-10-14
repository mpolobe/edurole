<?php
class checkValue {

	public $core;
	public $service;
	public $item = NULL;

	public function configService() {
		$this->service->output = TRUE;

		return $this->service;
	}

	/*
	 * Government ID taken check in forms
	 */
	public function runService($core) {
		$this->core = $core;
	
		if (isset($this->core->item)) {

			$check = $this->core->item;
			$sql = "SELECT * FROM `basic-information` WHERE `GovernmentID` = '$check'";

			$run = $this->core->database->doSelectQuery($sql);

			$out = "FALSE";
			
			while ($fetch = $run->fetch_row()) {
				$out = "TRUE";
			}
			
			echo $out;

		}

	}

}

?>