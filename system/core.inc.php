<?php
class eduroleCore {

	public $conf, $page, $action, $item, $username, $userID, $template, $database, $role, $roleName, $cleanPost, $log, $cleanGet, $route, $fullTemplatePath, $builder;

	public function __construct($conf, $initialize = TRUE) {
		$this->conf = $conf;

		$this->logEvent("Initializing EduRole core", "3");

		if (class_exists('database')) {
			$this->database = new database($this);
			$this->cleanInput();
		}

		if (class_exists('breadcrumb')) {
			$this->breadcrumb = new breadcrumb($this);
		}

		$this->setTemplate();
		$this->getSessions();
		$this->processRoute();

		if($initialize){
			$this->initializer();
		}
	}

	public function initializer() {
		if ($this->conf['conf']['installed'] == FALSE) {
			header('Location: installer/');
		}
		
		if ($this->page == "api") {
			$this->builder = new serviceBuilder($this); // All service calls are processed in the service builder
		} else {
			$this->builder = new viewBuilder($this); // All views are processed in the view builder
			$this->builder->buildView($this->page);
		}
	}

	public function processRoute() {
		$this->route = $this->cleanGet['id'];

		$this->logEvent("Processing route: $this->route", "3");

		$this->route = explode('/', $this->route);

		if (isset($this->route[0])) {
			$this->page = $this->route[0];
		}
		if (isset($this->route[1])) {
			$this->action = $this->route[1];
		}
		if (isset($this->route[2])) {
			$this->item = $this->route[2];
		}
	}

	private function cleanInput() {
		foreach (array_keys($_POST) as $key) {
			$this->cleanPost[$key] = $this->database->mysqli->real_escape_string($_POST[$key]);
		}

		foreach (array_keys($_GET) as $key) {
			$this->cleanGet[$key] = $this->database->mysqli->real_escape_string($_GET[$key]);
		}
	}

	public function getSessions() {
		if (isset($_SESSION['username'])) {
			$this->username = $_SESSION['username'];
		}
		if (isset($_SESSION['rolename'])) {
			$this->roleName = $_SESSION['rolename'];
		}
		if (isset($_SESSION['access'])) {
			$this->role = $_SESSION['access'];
		}
		if (isset($_SESSION['userid'])) {
			$this->userid = $_SESSION['userid'];
		}
	}

	public function setTemplate() {
		if (isset($this->cleanPost['template'])) {
			$_SESSION['template'] = $this->cleanPost['template'];
			header('Location: .');
		}

		if (isset($_SESSION['template'])) {
			$template = $_SESSION['template'];
		}

		if (!isset($template)) {
			$template = 0;
		}

		$template = $this->conf['conf']['templates'][$template];

		$this->template = $template;
		$this->fullTemplatePath = $this->conf['conf']['path'] . '/' . $this->conf['conf']['templatePath'] . $this->template;

	}

	public function getNamespace($className) {

		$sql = 'SELECT * FROM `pages` WHERE `PageRoute` = "' . $className . '"';
		$run = $this->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_assoc()) {

			$route = $fetch['PageRoute'];
			$path = explode('/', $route);

			$namespace = array("functionalname" => $fetch['PageName'], "executionpath" => $path);
		}

		return ($namespace);

	}

	public function showTemplate() {
		$count = 0;
		$out = "";

		foreach ($this->conf['conf']['templates'] as $template) {
			$out .= '<option value="' . $count . '">' . $template . '</option>';
			$count++;
		}

		return ($out);
	}

	public function showDebugger() {
		echo '</div><div class="contentpadlog"><p class="title2">System log is active:</p><p>' .
			$this->log .
			$this->database->log .
			$this->view->log .
			$this->view->database->log .
			"</p></div>";
	}

	public function logEvent($message, $level) {
		if ($level == 1) {
			$this->log .= "ERROR: " . $message . "<br>\n";
		} elseif ($level == 2) {
			$this->log .= "WARNING: " . $message . "<br>\n";
		} elseif ($level == 3) {
			$this->log .= "INFO: " . $message . "<br>\n";
		}
	}

	public function showAlert($message) {
		echo '<script> alert("' . $message . '"); </script>';
	}

	public function throwError($message) {
		echo '<div class="errorpopup">' . $message . '</div>';
		$this->logEvent("ERROR: $message", "1");
	}

	public function throwSuccess($message) {
		echo '<div class="successpopup">' . $message . '</div>';
	}
	
	/* Setters */
	
	public function setViewError($error, $description) {
		$this->viewError->message = $error;
		$this->viewError->description = $description;
	}
	
	public function setUsername($username) {
		$this->username = $username;
	}
	
	public function setRole($role) {
		$this->role = $role;
	}
	
	public function setRoleName($roleName) {
		$this->roleName = $roleName;
	}
	
	public function setUserID($userID) {
		$this->userID = $userID;
	}

	/* Setters */
	
	public function getViewError() {
		return $this->viewError;
	}
	
	public function getUsername() {
		return $this->username;
	}
	
	public function getRole() {
		return $this->role;
	}
	
	public function getRoleName() {
		return $this->roleName;
	}
	
	public function getUserID() {
		return $this->userID;
	}
}

?>
