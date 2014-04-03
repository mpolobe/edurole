<?php
class eduroleCore {

	public $conf;
	
	public $route;
	public $page;
	public $action;
	public $item;
	public $subitem;
	
	public $username;
	public $userID;
	public $role = 0;
	public $roleName;
	
	public $template;
	public $fullTemplatePath;
	
	public $database;
	
	public $cleanGet;
	public $cleanPost;

	public $limit = 50;
	public $offset = 0;
	public $pager = FALSE;
	
	public $log;

	public $builder;
	public $component;

	public $accounting;

	public $core;

	public function __construct($conf, $initialize = TRUE) {
		$this->conf = $conf;

		$this->core = $this;

		$this->logEvent("Initializing EduRole core", "3");

		if (class_exists('database')) {
			$this->database = new database($this);
			$this->cleanInput();
		}

		if (class_exists('accounting')) {
			$this->accounting = new accounting($this);
		}

		if (class_exists('component')) {
			$this->component = new component($this);
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

                if(isset($this->cleanGet['offset'])){
                        $this->offset = $this->cleanGet['offset'];
                        $this->pager = TRUE;
                }

                if(isset($this->cleanGet['limit'])){
                        $this->limit = $this->cleanGet['limit'];
                }

		if ($this->page == "api") {
			$this->builder = new serviceBuilder($this);
		} else {
			$this->builder = new viewBuilder($this);
			$this->builder($this->page);
		}
	}

	public function translate($phrase, $output = TRUE) {

		$this->language = 2;

		if (isset($phrase)) {

			$sql = 'SELECT ID, Phrase, TranslatedPhrase FROM `translation`
				WHERE `translation`.`Phrase` = ?';

			$run = $this->database->prepareQuery($sql);
			$run->bind_param('s', $phrase);
			$run->execute();

			$run->bind_result($id, $phrase, $translatedphrase);
			$run->store_result();

			if($run->num_rows == 0){
				$run->close();

				$sql = "INSERT INTO `translation` (`ID`, `LanguageID`, `Phrase`, `TranslatedPhrase`) VALUES (NULL, 0, ?, '');";
				$run = $this->database->prepareQuery($sql);
				$run->bind_param('s', $phrase);
				$run->execute();

				return $phrase;

			} else {
				while ($run->fetch()) {
					if(empty($translatedphrase)){
						return $phrase;
					}

					return $translatedphrase;
				}
			}
		}
	}

	public function redirect($page, $action, $item = NULL) {
		$base = $this->conf['conf']['path'];

		$page = $page == NULL ? "" : $page . "/"; 
		$item = $item == NULL ? "" : "/".$item; 

		$url = $base ."/". $page . $action . $item;

		header("Location: " . $url);
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
		if (isset($this->route[3])) {
			$this->subitem = $this->route[3];
		}
	}
	
	public function builder($page) {
		$this->builder->buildView($page);
	}

	private function cleanInput() {
		$this->cleanGet   = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
		$this->cleanPost  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
	}

	public function getFunctionSettings($page, $action, $viewsettings){
		$settings = new StdClass;

		$viewsettings = json_encode($viewsettings);
		
		if (isset($page) && isset($action)) {
			$sql = 'SELECT * FROM `functions`, `permissions` 
				WHERE `functions`.`FunctionRequiredPermissions` = `permissions`.`ID` 
				AND `Class` = "' . $page . '" AND `Function` = "' . $action . '" 
				OR `functions`.`FunctionRequiredPermissions` = `permissions`.`ID` 
				AND `Class` = "' . $page . '" AND `FunctionRoutes` 
				LIKE "%/' . $action . '/%"';

			$run = $this->database->doSelectQuery($sql);

			if($run->num_rows == 0){

				$sql = "INSERT INTO `functions` (`ID`, `Class`, `Function`, `FunctionRoutes`, `FunctionRequiredPermissions`, `Status`, `FunctionRequiredElements`) VALUES (NULL, '". $page ."', '". $action ."', '/".$action."/', '100', '1', '".$viewsettings."');";
				$this->database->doInsertQuery($sql);

				$settings = $this->getFunctionSettings($page, $action, NULL);

			} else {

				while ($fetch = $run->fetch_assoc()) {
					$settings->class = $fetch['Class'];
					$settings->function = $fetch['Function'];
					$settings->minRole = $fetch['RequiredRoleMin'];
					$settings->maxRole = $fetch['RequiredRoleMax'];
					$settings->status = $fetch['Status'];
					$settings->routes = $fetch['FunctionRoutes'];
					$settings->functionRequiredElements = json_decode($fetch['FunctionRequiredElements']);
					$settings->title = $fetch['FunctionTitle'];
					$settings->description = $fetch['FunctionDescription'];
				}	
			}
		}

		return $settings;
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
			$template = $this->conf['conf']['templates'][$template];
		}

		if (!isset($template)) {
			$template = "0";
			$template = $this->conf['conf']['templates']['0'];
		}


		$this->template = $template;
		$this->fullTemplatePath = $this->conf['conf']['path'] . '/' . $this->conf['conf']['templatePath'] . $this->template;

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
		echo '</div><div class=""><h2>Debugging is activated in the config:</h2><p>';
		if(isset($this->log)){			echo $this->log;			}
		if(isset($this->database->log)){	echo $this->database->log;		}
		if(isset($this->view->log)){		echo $this->view->log;			}
		if(isset($this->view->database->log)){	echo $this->view->database->log;	}
		echo"</p></div>";
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
