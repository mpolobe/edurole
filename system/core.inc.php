<?php
class eduroleCore {

	public $conf;
	
	public $route;
	public $page;
	public $action;
	public $item;
	
	public $username;
	public $userID;
	public $role;
	public $roleName;
	
	public $template;
	public $fullTemplatePath;
	
	public $database;
	
	public $cleanGet;
	public $cleanPost;
	
	public $log;

	public $builder;

	public $accounting;

	public function __construct($conf, $initialize = TRUE) {
		$this->conf = $conf;


		$this->logEvent("Initializing EduRole core", "3");

		if (class_exists('database')) {
			$this->database = new database($this);
			$this->cleanInput();
		}

		if (class_exists('accounting')) {
			$this->accounting = new accounting($this);
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
			$this->builder($this->page);
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
	
	public function builder($page) {
		$this->builder->buildView($page);
	}

	private function cleanInput() {

		$this->cleanGet   = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
		$this->cleanPost  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

	}

	public function getSessions() {
		if (isset($_SESSION['username'])) {
			$this->username = $_SESSION['username'];
		}
		if (isset($_SESSION['rolename'])) {
			$this->roleName = $_SESSION['rolename'];
		}
		if (isset($_SESSION['role'])) {
			$this->role = $_SESSION['role'];
		}
		if (isset($_SESSION['userid'])) {
			$this->userID = $_SESSION['userid'];
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
		} elseif ($level == 4) {
			$this->log .= "SECURITY: " . $message . "<br>\n";
		}

		if(isset($this->accounting)){
		//	$this->accounting->sysLog($message, $level);
		}
	}

	public function showAlert($message) {
		echo '<script> 
			jQuery(document).ready(function(){
				alert("' . $message . '"); 
			});
		</script>';
	}

	public function throwError($message) {
		echo '<div class="errorpopup">' . $message . '</div>';
		$this->logEvent("ERROR: $message", "1");
	}

	public function throwSuccess($message) {
		echo '<div class="successpopup">' . $message . '</div>';
	}

	public function throwNotice($message) {
		echo '<div class="noticepopup">' . $message . '</div>';
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
	
	public function setPage($page) {
		$this->page = $page;
	}
	
	public function setAction($action) {
		$this->action = $action;
	}
	
	public function setItem($item) {
		$this->item = $item;
	}
	
	/* Getters */
	
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
	
	public function getTitle(){
		if(isset($this->title)){
			return $this->title;
		} else {
			return $this->conf['conf']['titleName'];
		}
	}
}

?>
