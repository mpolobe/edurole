<?php
class viewBuilder {

	public $id, $core;

	public function __construct($core) {
		$this->core = $core;
		$this->pageSwitch($this->core->route);
	}

	public function pageSwitch($route) {

		$this->core->logEvent("Starting viewBuilder for " . $route, "3");

		$route = explode('/', $route);

		if (count($route) > 0) {
			$page = $route[0];
		} elseif (count($route) > 1) {
			$action = $route[1];
		}


		if (!isset($this->core->role)) {

			// User is not authenticated

			if ($page == "") {
				$this->showView("login");
			} elseif ($page == "login") {
				$auth = new auth($this->core);
				$auth->login();
			} elseif ($page == "template") {
				$this->setTemplate();
			} elseif ($page) {
				$this->showView($page);
			}

		} else {

			// User is authenticated

			if ($page == "") {
				$this->showView("home");
			} elseif ($page == "logout") {
				auth::logout();
			} elseif ($page == "download") {
				$filename = $this->core->cleanGet['file'];
				include "includes/classes/files.inc.php";
				downloadFile($filename);
			} elseif ($page == "template") {
				$this->setTemplate();
			} elseif ($page) {
				$this->showView($page);
			}
		}
	}

	public function showView($view) {

		$viewinclude = "includes/views/" . $view . ".view.php";

		if (file_exists($viewinclude)) {
			$this->core->logEvent("Initializing view $view", "3");

			require_once $viewinclude;

			$this->view = new $view();
			$viewconfig = $this->view->configView();

		} else {
			$this->core->throwError("Required view missing $viewinclude");
		}

		$this->jsFiles = $this->core->conf['javascript'][0] . "\n"; //include default JS

		foreach ($viewconfig->javascript as $file) {
			$this->jsFiles .= $this->core->conf['javascript'][$file]; //include JS files required by page
		}

		$this->cssFiles = $this->core->conf['css'][0] . "\n"; //include default CSS

		foreach ($viewconfig->css as $file) {
			$this->cssFiles .= $this->core->conf['css'][$file] . "\n"; //include CSS required by page
		}

		$this->cssFiles = str_replace("%TEMPLATE%", $this->core->template, $this->cssFiles);

		if ($viewconfig->header == TRUE) {
			$this->viewPageHeader($this->core->template);
		}

		if (isset($this->core->role) && $viewconfig->menu != TRUE) {
			$this->viewMenu();
		}

		$this->view->buildview($this->core);

		if ($viewconfig->footer == TRUE) {
			$this->viewPageFooter($this->core->template);
		}

	}

	public function viewMenu() {
		$menu = new menuConstruct($this->core);
		$menu->buildMainMenu();
	}

	public function setTemplate() {
		$_SESSION['template'] = $this->core->cleanPost['template'];
		header('Location: .'); // Reload page
	}

	public function viewPageHeader($template) {
		require_once "templates/" . $template . "/header.inc.php";
	}

	public function viewPageFooter($template) {
		if ($this->core->conf['conf']['debugging'] == TRUE) {
			$this->core->showDebugger();
		}
		include "templates/" . $template . "/footer.inc.php";
	}
}

?>
