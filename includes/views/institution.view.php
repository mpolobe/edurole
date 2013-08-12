<?php	
class institution {
    
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
        
	echo breadcrumb::generate(get_class());

        echo'<div class="contentpadfull">';
        echo'<p class="title2">Institution wide application settings</p> <br />';

        $this->institutionName();
        $this->paymentTypes();
        $this->admissionFlow();
        
    }

    function institutionName(){
        
	$sql="SELECT * FROM `settings` WHERE `Name` = 'InstitutionName' OR `Name` = 'InstitutionWebsite'  ORDER BY `Name`";

        $run = $this->core->database->doSelectQuery($sql);

	while ($fetch = $run->fetch_row()) {
		
		if($i==0){
			echo'<div class="easymencontainer">
			<form id="login" name="institutionname" method="POST" action="?id=institution&action=save">
			<p class="title2">Institutional identity</p>
			<p><input type="hidden" name="id" value="view-information">
			<div class="padding"><div class="label">Name of institution</div> <input type="text" name="institutionname" class="submit" value="'.$fetch[2].'"/><br>
			</div>'; 
			$i++;
		}else{
			echo'<div class="padding"><div class="label">Website of institution</div> <input type="text" name="institutionwebsite" class="submit" value="'.$fetch[2].'" />
			</div>
			<div class="label"> </div>
			<input type="submit" class="submit" value="Save settings" />
			</form></p>
			</div>'; 
		}
	}

    }

    function paymentTypes(){

            $sql="SELECT * FROM `settings` WHERE `Name` LIKE 'PaymentType%' ORDER BY `Name`";

            $run = $this->core->database->doSelectQuery($sql);

            $i=1;
            while ($fetch = $run->fetch_row()) {

                    if($i==1){
                            echo'<div class="easymencontainer">
                            <form id="login" name="paymenttypes" method="POST" action="?id=institution&action=save">
                            <p class="title2">Payment types</p>
                            <p><input type="hidden" name="id" value="view-information">
                            <div class="padding"><div class="label">Payment Type '.$i. '</div> <input type="text" name="paymenttype'.$i.'" class="submit" value="'.$fetch[2].'"/><br>
                            </div>'; 
                            $i++;
                    }else{
                            echo'<div class="padding"><div class="label">Payment Type '.$i. '</div> <input type="text" name="paymenttype'.$i.'" class="submit" value="'.$fetch[2].'"/><br>
                            </div>'; 
                            $i++;
                    }

            }
            echo'<div class="label"> </div>
            <input type="submit" class="submit" value="Save settings" />
            </form></p>
            </div>';
    }

    function admissionFlow(){

            $sql="SELECT * FROM `settings` WHERE  `Name` LIKE 'AdmissionLevel%' ORDER BY `Name` ASC";

            $run = $this->core->database->doSelectQuery($sql);

            $n=1;
            while ($fetch = $run->fetch_row()) {

                    if($i==0){
                            echo'<div class="easymencontainer">
                            <form id="login" name="login" method="get" action="">
                            <p class="title2">Admission flow</p>
                            <p><input type="hidden" name="id" value="view-information">
                            <div class="padding"><div class="label">Admission step '.$n.'</div>
                            <input type="text" name="admissionsteps'.$n.'" class="submit"  value="'.$fetch[2].'" size="40"/><br></div>';
                            $i++;
                    }else{
                            echo'<div class="padding"><div class="label">Admission step '.$n.'</div>
                            <input type="text" name="admissionsteps'.$n.'" class="submit"  value="'.$fetch[2].'" size="40"/><br></div>';
                            $n++;
                    }
            }

            echo'<div class="label"> </div>
            <input type="submit" class="submit" value="Save settings" />
            </form></p>
            </div>';

    }

    function newsoverview(){

            $sql="SELECT * FROM `content` WHERE `ContentCat` = 'news'";

            $run = $this->core->database->doSelectQuery($sql);

            echo'<div class="newscontainers">	<h2>News and updates</h2> <p>';

            while ($fetch = $run->fetch_row()) {
                    echo ' <li> <b><a href="?id=item&item=' . $fetch[0] . '">' . $fetch[1] . '</a></b></li>';
            }

            echo'</p></div>';
    }
}
?>

