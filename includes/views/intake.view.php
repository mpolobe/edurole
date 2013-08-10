<?php
class intake{

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

		global $nomen;
		$pagename = "Studies open for intake";

		if($nomen==FALSE){
                    
                    echo'<div class="menucontainer">
                    <div class="menubar">
                    <div class="menuhdr"><strong>Home menu</strong></div>
                    <div class="menu">
                    <a href=".">Home</a>
                    <a href="index.php?id=info">Overview of all studies</a>
                    <a href="admission">Studies open for intake</a>
                    <a href="password">Recover lost password</a>
                    </div>
                    </div>
                    </div>';
                    
		}

                echo breadcrumb::generate(get_class());
                
		echo'<div class="contentpadfull">
		<h1>'.$pagename.'</h1> ';
	
		if($this->core->cleanGet['action']=="view"){
			$item = $this->core->cleanGet['item'];
			$sql="SELECT * FROM `study`,`schools` WHERE `study`.ParentID = `schools`.ID AND `study`.ParentID = `schools`.ID AND `study`.ID = $item";
			$this->showitem($sql);
		} else {
			$sql="SELECT * FROM `study`,`schools` WHERE `study`.ParentID = `schools`.ID AND CURRENT_TIMESTAMP <= `study`.IntakeEnd ORDER BY `study`.Name";
			$this->showlist($sql);
		}

	}

	function showlist($sql){

		echo'<p>Overview of all studies for which intake is currently open, click on the study to proceed to filing your request for admission. </p>
		<p>
		<table width="768" cellspacing="0" cellpadding="5" >
		<tr><td bgcolor="#EEEEEE"> <b>Study</b></td>'.
		'<td bgcolor="#EEEEEE"><b>School</b></td>'.
		'<td bgcolor="#EEEEEE"><b>End of intake</b></td>'.
		'</tr>';

		$run = $this->core->database->doSelectQuery($sql);

		while ($row = $run->fetch_row()) {
			echo '<tr>
			<td><b><a href="index.php?id=register&action=view&item='. $row[0] .'"> '. $row[6] .'</a></b></td>'.
			'<td>'. $row[16] .'</td>'.
			'<td>'. $row[3] .'</td>'.
			'</tr>';
		}

		echo'</table>
		</p>';
	}
}

?>