<?php
class component {

	public $core;

        function __construct($core) {
                $this->core = $core;
	}

	public function generateHeader() {
		$template = $this->core->template;
		require_once $this->core->conf['conf']['templatePath'] . $template . "/header.inc.php";
	}

	public function generateFooter() {
		$template = $this->core->template;
		include $this->core->conf['conf']['templatePath'] . $template . "/footer.inc.php";
	}

        public function generateBreadcrumb() {

                $pathArray[$this->core->page] = ucwords($this->core->page);
                $pathArray[$this->core->page . '/' . $this->core->action] = $this->core->builder->settings->title;

                $crumb = "";

                $crumb .= '<a href="'.$this->core->conf["conf"]["path"].'/">Home</a>';

		if($this->core->page != "home" && $this->core->action != "show"){
                	foreach ($pathArray as $class => $name) {
                	        $crumb .= ' > <a href="'.$this->core->conf["conf"]["path"].'/' . $class . '">' . $name . '</a>';
                	}
		}

                $crumbstart = '<div class="breadcrumb">';
                $crumbend = '</div>';

                $completecrumb = $crumbstart . $crumb .  $crumbend;

                return ($completecrumb);
        }

	public function generateTitle($title = NULL) {
		if (!empty($title)) {
			$title = '<p class="title">' . $title . '</p>';
		}

		return ($title);
	}

	public function generateDescription($description = NULL) {
		if (!empty($description)) {
			$description = '<p><b>' . $description . '</b> </b></p>';
		}
		return ($description);
	}

	public function generateMenu() {
		$menu = new menuConstruct($this->core);
		$menu = $menu->buildMainMenu(TRUE);
		return ($menu);
	}
}
?>