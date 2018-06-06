 <?php
class logout {

	public $core;
	public $view;

	public function configView() {
		$this->view->open = TRUE;
		$this->view->header = TRUE;
		$this->view->footer = TRUE;
		$this->view->menu = FALSE;
		$this->view->javascript = array();
		$this->view->css = array('login');
		
		return $this->view;
	}

	public function buildView($core) {
		$this->core = $core;
	}



	public function doLogout() {
	}
}
?>