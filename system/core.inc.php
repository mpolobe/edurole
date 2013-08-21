<?php
class eduroleCore {

	public $conf, $page, $action, $item, $username, $userID, $template, $database, $role, $roleName, $cleanPost, $log, $cleanGet, $route, $fullTemplatePath;

	public function __construct($conf) {
		$this->conf = $conf;

		$this->logEvent("Initializing EduRole core", "3");

		$this->database = new database($this);
		$this->breadcrumb = new breadcrumb($this);

		$this->setTemplate();
		$this->getSessions();
		$this->cleanInput();
		$this->processRoute();

		$this->initializer();
	}

	public function initializer(){
		if($this->page == "api"){
			new serviceBuilder($this);		// All service calls are processed in the service builder
		}else {
			new viewBuilder($this);			// All views are processed in the view builder
		}
	}

	public function processRoute(){
		$this->route = $this->cleanGet['id'];

		$this->logEvent("Processing route: $this->route", "3");

		$this->route = explode('/', $this->route);

		if (count($this->route) > 0) {
			$this->page = $this->route[0];
		} elseif (count($this->route) > 1) {
			$this->action = $this->route[1];
		} elseif (count($this->route) > 2) {
			$this->item = $this->route[2];
		}
	}

	private function cleanInput(){
		foreach (array_keys($_POST) as $key) {
			$this->cleanPost[$key] = $this->database->mysqli->real_escape_string($_POST[$key]);
		}

		foreach (array_keys($_GET) as $key) {
			$this->cleanGet[$key] = $this->database->mysqli->real_escape_string($_GET[$key]);
		}
	}

	public function getSessions(){
		if (isset($_SESSION['username'])) {
			$this->username = $_SESSION['username'];
		}
		if (isset($_SESSION['rolename'])) {
			$this->rolename = $_SESSION['rolename'];
		}
		if (isset($_SESSION['access'])) {
			$this->role = $_SESSION['access'];
		}
		if (isset($_SESSION['userid'])) {
			$this->userid = $_SESSION['userid'];
		}
	}

	public function setTemplate() {
		if(isset($this->core->cleanPost['template'])){
			$_SESSION['template'] = $this->core->cleanPost['template'];
			header('Location: .'); // Reload page
		}

		if (isset($_SESSION['template'])) {
			$template = $_SESSION['template'];
		}

		if (!isset($template)) {
			$template = 0;
		}

		$template = $this->conf['conf']['templates'][$template];

		$this->template = $template;
		$this->fullTemplatePath = $this->conf['conf']['path'] .'/'. $this->conf['conf']['templatePath'] . $this->template;

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
		echo '<script>
                        alert("' . $message . '");
              </script>';
	}

	public function throwError($error) {
		echo '<div class="errorpopup">' . $error . '</div>';
		$this->core->logEvent("ERROR: $error", "1");
	}

	public function throwSuccess($error) {
		echo '<div class="successpopup">' . $error . '</div>';
	}

	function getUsername() {
		$username = $_SESSION['username'];
		return $username;
	}

}

?>
