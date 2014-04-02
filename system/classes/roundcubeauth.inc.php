<?php

class RoundcubeLogin {

	private $rcPath;


	private $rcSessionID;


	private $rcSessionAuth;


	private $rcLoginStatus;


	private $lastToken;


	private $debugEnabled;


	private $debugStack;

	public function __construct($webmailPath, $enableDebug = false) {
		$this->debugStack = array();
		$this->debugEnabled = $enableDebug;

		$this->rcPath = $webmailPath;
		$this->rcSessionID = false;
		$this->rcSessionAuth = false;
		$this->rcLoginStatus = 0;
	}

	public function login($username, $password) {
		$this->updateLoginStatus();

		if ($this->isLoggedIn())
			$this->logout();

		$data = (($this->lastToken) ? "_token=" . $this->lastToken . "&" : "")
			. "_task=login&_action=login&_timezone=1&_dstactive=1&_url=&_user=" . urlencode($username) . "&_pass=" . urlencode($password);

		$response = $this->sendRequest($this->rcPath, $data);

		if (preg_match('/^Location\:.+_task=/mi', $response)) {
			$this->addDebug("LOGIN SUCCESSFUL", "RC sent a redirection to ./?_task=..., that means we did it!");
			$this->rcLoginStatus = 1;
		} else if (preg_match('/^Set-Cookie:.+sessauth=-del-;/mi', $response)) {
			header($line, false);
			$this->addDebug("LOGIN FAILED", "RC sent 'sessauth=-del-'; User/Pass combination wrong.");
			$this->rcLoginStatus = -1;
		} else {
			$this->addDebug("LOGIN STATUS UNKNOWN", "Neither failure nor success. This maybe the case if no session ID was sent");
			throw new RoundcubeLoginException("Unable to determine login-status due to technical problems.");
		}

		return $this->isLoggedIn();
	}


	public function isLoggedIn() {
		$this->updateLoginStatus();

		if (!$this->rcLoginStatus)
			throw new RoundcubeLoginException("Unable to determine login-status due to technical problems.");

		return ($this->rcLoginStatus > 0) ? true : false;
	}

	public function logout() {
		$data = (($this->lastToken) ? "_token=" . $this->lastToken . "&" : "")
			. "_action=logout&_task=logout";

		$this->sendRequest($this->rcPath, $data);

		return !$this->isLoggedIn();
	}


	public function redirect() {
		header("Location: {$this->rcPath}");
		exit;
	}


	private function updateLoginStatus($forceUpdate = false) {
		if ($this->rcSessionID && $this->rcLoginStatus && !$forceUpdate)
			return;

		if ($_COOKIE['roundcube_sessid'])
			$this->rcSessionID = $_COOKIE['roundcube_sessid'];

		if ($_COOKIE['roundcube_sessauth'])
			$this->rcSessionAuth = $_COOKIE['roundcube_sessauth'];

		$response = $this->sendRequest($this->rcPath);

		if (preg_match('/"request_token":"([^"]+)",/mi', $response, $m))
			$this->lastToken = $m[1];

		if (preg_match('/<input.+name="_token".+value="([^"]+)"/mi', $response, $m))
			$this->lastToken = $m[1]; // override previous token (if this one exists!)

		if (preg_match('/<input.+name="_pass"/mi', $response)) {
			$this->addDebug("NOT LOGGED IN", "Detected that we're NOT logged in.");
			$this->rcLoginStatus = -1;
		} else if (preg_match('/<div.+id="message"/mi', $response)) {
			$this->addDebug("LOGGED IN", "Detected that we're logged in.");
			$this->rcLoginStatus = 1;
		} else {
			$this->addDebug("UNKNOWN LOGIN STATE", "Unable to determine the login status. Did you change the RC version?");
			throw new RoundcubeLoginException("Unable to determine the login status. Unable to continue due to technical problems.");
		}

		if (!$this->rcSessionID) {
			$this->addDebug("NO SESSION ID", "No session ID received. RC version changed?");
			throw new RoundcubeLoginException("No session ID received. Unable to continue due to technical problems.");
		}
	}


	private function sendRequest($path, $postData = false) {
		$method = (!$postData) ? "GET" : "POST";
		$port = ($_SERVER['HTTPS']) ? 443 : 80;
		$host = ($port == 443) ? "ssl://localhost" : "localhost";

		$cookies = array();

		foreach ($_COOKIE as $name => $value)
			$cookies[] = "$name=$value";

		if (!$_COOKIE['roundcube_sessid'] && $this->rcSessionID)
			$cookies[] = "roundcube_sessid={$this->rcSessionID}";

		if (!$_COOKIE['roundcube_sessauth'] && $this->rcSessionAuth)
			$cookies[] = "roundcube_sessauth={$this->rcSessionAuth}";

		$cookies = ($cookies) ? "Cookie: " . join("; ", $cookies) . "\r\n" : "";

		if ($method == "POST") {
			$request =
				"POST " . $path . " HTTP/1.1\r\n"
				. "Host: " . $_SERVER['HTTP_HOST'] . "\r\n"
				. "User-Agent: " . $_SERVER['HTTP_USER_AGENT'] . "\r\n"
				. "Content-Type: application/x-www-form-urlencoded\r\n"
				. "Content-Length: " . strlen($postData) . "\r\n"
				. $cookies
				. "Connection: close\r\n\r\n"

				. $postData;
		} else {
			$request =
				"GET " . $path . " HTTP/1.1\r\n"
				. "Host: " . $_SERVER['HTTP_HOST'] . "\r\n"
				. "User-Agent: " . $_SERVER['HTTP_USER_AGENT'] . "\r\n"
				. $cookies
				. "Connection: close\r\n\r\n";
		}

		$fp = fsockopen($host, $port);

		$this->addDebug("REQUEST", $request);
		fputs($fp, $request);

		$response = "";

		while (!feof($fp)) {
			$line = fgets($fp, 4096);

			if (preg_match('/^HTTP\/1\.\d\s+404\s+/', $line))
				throw new RoundcubeLoginException("No Roundcube installation found at '$path'");

			if (preg_match('/^Set-Cookie:\s*(.+roundcube_sessid=([^;]+);.+)$/i', $line, $match)) {
				header($line, false);

				$this->addDebug("GOT SESSION ID", "New session ID: '$match[2]'.");
				$this->rcSessionID = $match[2];
			}

			if (preg_match('/^Set-Cookie:.+roundcube_sessauth=([^;]+);/i', $line, $match)) {
				header($line, false);

				$this->addDebug("GOT SESSION AUTH", "New session auth: '$match[1]'.");
				$this->rcSessionAuthi = $match[1];
			}

			if (preg_match('/"request_token":"([^"]+)",/mi', $response, $m))
				$this->lastToken = $m[1];

			if (preg_match('/<input.+name="_token".+value="([^"]+)"/mi', $response, $m))
				$this->lastToken = $m[1]; // override previous token (if this one exists!)

			$response .= $line;
		}

		fclose($fp);

		$this->addDebug("RESPONSE", $response);
		return $response;
	}


	private function addDebug($action, $data) {
		if (!$this->debugEnabled)
			return false;

		$this->debugStack[] = sprintf(
			"<b>%s:</b><br /><pre>%s</pre>",
			$action, htmlspecialchars($data)
		);
	}


	public function dumpDebugStack() {
		print "<p>" . join("\n", $this->debugStack) . "</p>";
	}
}


class RoundcubeLoginException extends Exception {
}

?>