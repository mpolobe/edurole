<?php	
class assignments{
    
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

        include"includes/classes/files.inc.php";


        if ($this->core->cleanGet["action"]=="manage" || !isset($this->core->cleanGet["action"])  && $access > 100) { 

		echo breadcrumb::generate(get_class());

                echo'<div class="contentpadfull">
                <p class="title2">Overview of all assignments</p> <p><b>Manage assignments for students to view</b>';

                $this->manageAssignments();

        } elseif ($this->core->cleanGet["action"]=="overview" || !isset($this->core->cleanGet["action"]) || !isset($this->core->cleanGet["action"]) && $access >= 10 ) { 

		echo breadcrumb::generate(get_class());

                echo'<div class="contentpadfull">
                <p class="title2">Overview of personal assignments</p> <p><b>Your assignments currently active in your courses and programmes</b>';

                $this->allAssignments();

        } elseif ($this->core->cleanGet["action"]=="edit") {
                $this->editFile();
        } elseif ($this->core->cleanGet["action"]=="saveFile") {
                $this->saveFile();
        } elseif ($this->core->cleanGet["action"]=="renameFile") {
                $this->renameFile();
        } elseif ($this->core->cleanGet["action"]=="delete") {
                $this->deleteFile();
        } elseif ($this->core->cleanGet["action"]=="uploadFile") { 
                $this->uploadFile();
        } elseif ($this->core->cleanGet["action"]=="newFile") { 
                $this->newFile();
        }
    }

    function allAssignments(){

            global $connection;


            $sql="SELECT * FROM `assignments`, `courses`, `basic-information` WHERE  `courses`.ID = CourseID AND `assignments`.CreatorID = `basic-information`.ID ORDER BY DateCreated";

            $run = $this->core->database->doSelectQuery($sql);

            $init = TRUE;

             while ($fetch = $run->fetch_row()) {

                    $grade = $fetch[3];			$firstname = $fetch[16];
                    $lastname = $fetch[18];		$studentno = $fetch[8];
                    $assignmentid = $fetch[0];		$assignmentname = $fetch[2];
                    $assignmentfile = $fetch[9];	$batchdescription = $fetch[3];
                    $uid = $fetch[20];			$date = $fetch[11];


                            echo'<div style="border:solid 1px #ccc; padding-left: 10px; margin-bottom: 4px;">
                            <table width="756">'.
                            '<tr>'. 
                            '<td width="200px"><b>Assignment name:</b></td>'.
                            '<td width="150px"><b>Assigned by</b></td>'.
                            '<td width="200px"><b>Deadline for submission</b></td>'.
                            '<td width="100px"><b></td>'.
                            '</tr>';
                            echo '<tr>'.
                            '<td><a href="?id=courses&action=view&item='.$assignmentid .'"><b>'. $assignmentname .'</b></a></td>'.
                            '<td><a href="?id=view-information&uid='.$uid.'">'. $firstname .' '. $lastname .'</a></td>'.
                            '<td>'. $date .'</td>'.
                            '<td>
                            <b><a href="?id=studies&action=edit&item='. $fetch[0] .'"> Submit result </a></b>
                            </td>'.
                            '</tr></table></div>';	


            }

    }

    function manageAssignments(){

            global $connection;


            $sql="SELECT * FROM `assignments`, `courses`, `basic-information` WHERE  `courses`.ID = CourseID AND `assignments`.CreatorID = `basic-information`.ID ORDER BY DateCreated";

            $run = $this->core->database->doSelectQuery($sql);
            
            $init = TRUE;

             while ($fetch = $run->fetch_row()) {

                    $grade = $fetch[3];			$firstname = $fetch[16];
                    $lastname = $fetch[18];		$studentno = $fetch[8];
                    $assignmentid = $fetch[0];		$assignmentname = $fetch[2];
                    $assignmentfile = $fetch[9];	$batchdescription = $fetch[3];
                    $uid = $fetch[20];			$date = $fetch[11];


                            echo'<div style="border:solid 1px #ccc; padding-left: 10px; margin-bottom: 4px;">
                            <table width="756">'.
                            '<tr>'. 
                            '<td width="200px"><b>Assignment name:</b></td>'.
                            '<td width="150px"><b>Assigned by</b></td>'.
                            '<td width="200px"><b>Deadline for submission</b></td>'.
                            '<td width="100px"><b></td>'.
                            '</tr>';
                            echo '<tr>'.
                            '<td><a href="?id=courses&action=view&item='.$assignmentid .'"><b>'. $assignmentname .'</b></a></td>'.
                            '<td><a href="?id=view-information&uid='.$uid.'">'. $firstname .' '. $lastname .'</a></td>'.
                            '<td>'. $date .'</td>'.
                            '<td>
                            <b><a href="?id=studies&action=edit&item='. $fetch[0] .'"> Submit result </a></b>
                            </td>'.
                            '</tr></table></div>';	


            }

    }
}
?>