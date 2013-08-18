<?php
class component {

	public static function generateBreadcrumb($classname, $functionname) {

		$pathArray = classNamespace::getNamespace($classname);
		$crumb = "";

		foreach ($pathArray['executionpath'] as $class => $name) {

			if (!$name == "home") {
				$functionalName = classNamespace::getNamespace($name);
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

	public static function generateTitle($title, $description = NULL) {
		$title = '<div class="contentpadfull"> <p class="title">' . $title . '</p>';

		if (!empty($description)) {
			$title .= '<p><b>' . $description . '</b> </b></p>';
		}

		return ($title);
	}
}

?>