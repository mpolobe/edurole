<?php
class breadcrumb {
	public function __construct($core) {
		$this->core = $core;
	}

	public function generate($classname) {

		$pathArray = $this->core->getNamespace($classname);
		$crumb = "";

		foreach ($pathArray['executionpath'] as $class => $name) {

			if (!$name == "home") {
				$functionalName = getNamespace($name);
			} else {
				$functionalName['functionalname'] = "home";
			}

			$crumb .= '<a href="' . $name . '">' . $functionalName['functionalname'] . '</a> > ';
		}

		$crumbstart = '<div class="breadcrumb">';
		$crumbend = '</div>';

		$currentClass = $pathArray['functionalname'];

		$completecrumb = $crumbstart . $crumb . $currentClass . $crumbend;

		return ($completecrumb);
	}

}

?>