<?php 
class password{

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

		echo'<div class="menucontainer">
		<div class="menubar">
		<div class="menuhdr"><strong>Home menu</strong></div>
		<div class="menu">
		<a href=".">Home</a>
		<a href="admission?id=info">Overview of all studies</a>
		<a href="admission">Studies open for intake</a>
		<a href="password">Recover lost password</a>
		</div>
		</div>
		</div>'; 

		echo breadcrumb::generate(get_class());

		echo'<div class="contentpadfull">
		<p class="title2">Recover password</p> ';

		include"includes/forms/changepassword.form.php";

	}
}
?>
