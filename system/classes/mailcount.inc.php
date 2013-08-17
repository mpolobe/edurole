<?php
function mailcount() {

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

	return ($out);

}

?>