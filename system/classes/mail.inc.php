<?php
class mailOperations{

	function mailCount() {
		global $conf;

		imap_timeout(IMAP_READTIMEOUT, 1);
		imap_timeout(IMAP_OPENTIMEOUT, 1);
		$mbox = @imap_open("{" . $conf['mail']['server'] . ":143/novalidate-cert}", $_SESSION['username'], $_SESSION['password'], OP_HALFOPEN);

		if (!$mbox) {
			return;
		}

		$status = @imap_status($mbox, "{" . $conf['mail']['server'] . "}INBOX", SA_ALL);

		if ($status) {
			$out = $status->unseen;
		}

		@imap_close($mbox);

		if (empty($out)) {
			$out = "0";
		}

		return ($out);
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