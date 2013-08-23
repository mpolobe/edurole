<?php
class checkValue {

	public $core;
	public $view;
	public $item = NULL;

	public function configView() {
		$this->view->header = TRUE;
		$this->view->footer = TRUE;
		$this->view->javascript = array(3);
		$this->view->css = array(1, 4);

		return $this->view;
	}

	public function runService($core) {
		$this->core = $core;
	}

	function newMail($mailTemplate, $recipient = NULL, $name = NULL, $study = NULL){
		$mailTemplate = $this->core->conf['conf']['templatePath'] . "mail/" . $mailTemplate . ".json";

		if (file_exists($mailTemplate)) {
			$file = file_get_contents($mailTemplate);
			$mailTemplate = json_decode($file);
		}

		$mailTemplate = parseMailTemplate($mailTemplate);

		sendMail($mailTemplate, $recipient);
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

	function sendMail($mailTemplate, $recipient){
		$to      = 	$recipient;
		$subject = 	$mailTemplate->Subject;
		$content = 	$mailTemplate->Content;
		$headers =	'From: '. $this->core->conf['conf']['systemMail'] ."\r\n" .
		'Reply-To: '. $this->core->conf['conf']['systemMail'] . "\r\n" .
		'X-Mailer: EduRole SIS';

		try{
			mail($to, $subject, $content, $headers);
		} catch (Exception $e){
			$this->core->throwError("Mail could not be sent.");
		}
	}
}

?>