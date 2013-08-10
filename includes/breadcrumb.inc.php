<?php
class breadcrumb {

	public static function generate($classname){

		$pathArray = classNamespace::getNamespace($classname);

		foreach ($pathArray['executionpath'] as $class => $name){
			
			if(!$name == "home"){
				$functionalName = classNamespace::getNamespace($name);
			} else {
				$functionalName['functionalname'] = "home";
			}

			$crumb .= '<a href="'.$name.'">'.$functionalName['functionalname'].'</a> > ';
		}

		$crumbstart	= '<div class="breadcrumb">';
		$crumbend	= '</div>';

		$currentClass = $pathArray['functionalname'];

		$completecrumb = $crumbstart . $crumb . $currentClass . $crumbend;

		return($completecrumb);
	}

}
?>