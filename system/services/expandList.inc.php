<?php
class expandList {

	public $core;
	public $service;

	public function configService() {
		$this->service->output = TRUE;
		return $this->service;
	}

	public function runService($core) {
		$this->core = $core;
		error_reporting(E_ALL);
		$this->getList();
	}

       public function configView() {
                $this->view->configurable = FALSE;
                $this->view->header = FALSE;
                $this->view->footer = FALSE;
                $this->view->title = FALSE;
                $this->view->description = FALSE;
                $this->view->breadcrumb = FALSE;
                $this->view->menu = FALSE;
                $this->view->javascript = array();
                $this->view->css = array();

                return $this->view;
	}

	function getList(){
		$this->core->action = $this->core->subitem;
		$builder = new viewBuilder($this->core);

		$builder->viewConfig->menu = false;
		$builder->viewConfig->header = false;
		$builder->viewConfig->footer = false;
		$builder->viewConfig->breadcrumb = false;
		$builder->viewConfig->title = false;
		$builder->viewConfig->description = false;

		$builder->buildView($this->core->item, TRUE);

		return TRUE;
	}
}
?>
