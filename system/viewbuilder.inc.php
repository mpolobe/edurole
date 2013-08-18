<?php
class viewBuilder {

	public $id, $core;

	public function __construct($core) {
		$this->core = $core;
		$this->viewBuilder($this->core->page);
	}

	public function viewBuilder($page) {
		$this->core->logEvent("Starting view builder for page: " . $this->core->page . " action: " . $this->core->action, "3");

		if (!isset($this->core->role)) {
			/*
			 * User is not authenticated
			 * Services available without authorization are listed here
			 */

			if (empty($page)) {
				$this->initView("login");
			} elseif ($page == "login") {
				$auth = new auth($this->core);
				$auth->login();
			} elseif ($page == "template") {
				$this->setTemplate();
			} elseif ($page) {
				$this->initView($page);
			}

		} else {
			/*
			 * User is authenticated
			 * Services available with authorization are listed here
			 */

			if (empty($page)) {
				$this->initView("home");
			} elseif ($page == "logout") {
				auth::logout();
			} elseif ($page == "download") {
				$filename = $this->core->cleanGet['file'];
				include $this->core->classPath . "files.inc.php";
				downloadFile($filename);
			} elseif ($page == "template") {
				$this->setTemplate();
			} elseif ($page) {
				$this->initView($page);
			}
		}
	}

	public function initView($view) {
		$viewInclude = $this->core->viewPath . $view . ".view.php";

		if (file_exists($viewInclude)) {
			$this->core->logEvent("Initializing view $view", "3");

			require_once $viewInclude;

			$this->view = new $view();
			$viewConfig = $this->view->configView();
		} else {
			$this->core->throwError("Required view missing $viewInclude");
		}

		$this->jsFiles = $this->core->conf['javascript'][0] . "\n"; //include default JS

		foreach ($viewConfig->javascript as $file) {
			$this->jsFiles .= $this->core->conf['javascript'][$file]; //include JS files required by page
		}

		$this->cssFiles = $this->core->conf['css'][0] . "\n"; //include default CSS

		foreach ($viewConfig->css as $file) {
			$this->cssFiles .= $this->core->conf['css'][$file] . "\n"; //include CSS required by page
		}

		$this->cssFiles = str_replace("%TEMPLATE%", $this->core->template, $this->cssFiles);
		$this->cssFiles = str_replace("%BASE%", $this->core->conf['path'], $this->cssFiles);

		if ($viewConfig->header == TRUE) {
			$this->viewPageHeader($this->core->template);
		}

		if (isset($this->core->role) && $viewConfig->menu != TRUE) {
			$this->viewMenu();
		}

		$this->view->buildview($this->core);

		if ($viewConfig->footer == TRUE) {
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
		require_once $this->core->templatePath . $template . "/header.inc.php";
	}

	public function viewPageFooter($template) {
		if ($this->core->conf['conf']['debugging'] == TRUE) {
			$this->core->showDebugger();
		}
		include $this->core->templatePath . $template . "/footer.inc.php";
	}
}

?>