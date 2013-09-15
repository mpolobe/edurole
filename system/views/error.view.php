<?php
class error {

	public $core;
	public $view;

	public function configView() {
		$this->view->header = TRUE;
		$this->view->footer = TRUE;
		$this->view->menu = TRUE;
		$this->view->javascript = array(3);
		$this->view->css = array(4);

		return $this->view;
	}

	public function buildView($core) {
		$this->core = $core;

		$error = $this->core->getViewError();
		
		if($error){
			$this->showError($error->message, $error->description);		
		}
		
	}
	
	public function showError($message, $description){
	
		$function = __FUNCTION__;

		echo $this->core->breadcrumb->generate(get_class(), $function);
		echo component::generateTitle($message);

		$this->core->throwError($description);
	
	}
}

?>