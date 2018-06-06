<?php
class breadcrumb {
	public function __construct($core) {
		$this->core = $core;
	}

	public function generate() {

		$pathArray[$this->core->page] = "Home";
		$pathArray[$this->core->action] = "Home";

		$crumb = "";

		$crumb .= '<a href="'.$this->core->conf["conf"]["path"].'/">Home</a>';
		foreach ($pathArray as $class => $name) {
			$crumb .= ' > <a href="' . $class . '">' . $name . '</a>';
		}

		$crumbstart = '<div class="breadcrumb">';
		$crumbend = '</div>';

		$currentClass = $pathArray['functionalname'];

		$completecrumb = $crumbstart . $crumb . $currentClass . $crumbend;

		return ($completecrumb);
	}

}

?>