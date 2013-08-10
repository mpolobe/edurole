<?php
class error(){

    public $core;
	public $view;
	
	public function configView(){
		$this->view->header		= TRUE;
		$this->view->footer		= TRUE;
		$this->view->menu		= FALSE;
		$this->view->javascript = array(3);
		$this->view->css 		= array(4);
		
		return $this->view;
	}
	
	public function buildView($core){
	
		$this->core = $core;
		
		echo'<div class="menucontainer"></div><div class="breadcrumb"><a href=".">home</a> > error</div>
		<div class="contentpadfull">
		<p class="title2">'.$pagename.'</p>
		<div class="errorpopup">'.$error.'</div>
		</div>';
	}
?>