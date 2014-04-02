<?php
class checkValue {

	public $core;
	public $service;
	public $mail;

	public function configService() {
		$this->service->output = FALSE;

		return $this->service;
	}

	public function runService($core) {
		$this->core = $core;

		include $this->core->conf['conf']['classPath'] . "mailcount.inc.php";
		$this->mail = new mailOperations();
	}

	function newMail($mailTemplate, $recipient = NULL, $name = NULL, $study = NULL){
		$mailTemplate = $this->core->conf['conf']['templatePath'] . "mail/" . $mailTemplate . ".json";

		if (file_exists($mailTemplate)) {
			$file = file_get_contents($mailTemplate);
			$mailTemplate = json_decode($file);
		}

		$mailTemplate = parseMailTemplate($mailTemplate);
		$this->mail->sendMail($mailTemplate, $recipient);
	}

	function parseMailTemplate($mailTemplate){
		$data = array(
			"INSTITUTION" => $this->core->conf['conf']['institutionName'],
			"NAME"		  => $this->name,
			"STUDY" 	  => $this->study,
			"STUDY" 	  => $this->study
		);

		foreach ($data as $var => $value){
			$mailTemplate = str_replace('%'.$var.'%', $value, $mailTemplate);
		}

		return $mailTemplate;
	}

}

?>