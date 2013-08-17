<?php
class classNamespace {

	public function getNamespace($classname) {

		$namespace = NULL;
		include $this->core->classPath . "classnamespace.conf.php";
		return ($namespace[$classname]);

	}

}

?>