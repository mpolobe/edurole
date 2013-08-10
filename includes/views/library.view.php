<?php
class library (){

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
		$access = $_SESSION['access'];
		include"includes/functions/files.inc.php";


		if ($this->core->cleanGet["action"]=="manage" || !isset($this->core->cleanGet["action"])  && $access > 100) { 

			echo'<div class="breadcrumb"><a href=".">home</a> > <a href="?id=users">library management</a></div>
			<div class="contentpadfull">
			<p class="title2">Overview of all assignments</p> <p><b>Manage assignments for students to view</b>';

			manageBooks();

		} elseif ($this->core->cleanGet["action"]=="overview" || !isset($this->core->cleanGet["action"]) || !isset($this->core->cleanGet["action"]) && $access >= 10 ) { 

			echo'<div class="breadcrumb"><a href=".">home</a> > <a href="?id=users">books on loan</a></div>
			<div class="contentpadfull">
			<p class="title2">Overview of personal assignments</p> <p><b>View your books on loan from the library</b>';

			viewLoans();

		} elseif ($this->core->cleanGet["action"]=="edit") {
				editFile();
			} elseif ($this->core->cleanGet["action"]=="saveFile") {
				saveFile();
			} elseif ($this->core->cleanGet["action"]=="renameFile") {
				renameFile();
			} elseif ($this->core->cleanGet["action"]=="delete") {
				deleteFile();
			} elseif ($this->core->cleanGet["action"]=="uploadFile") { 
				uploadFile();
			} elseif ($this->core->cleanGet["action"]=="newFile") { 
				newFile();
			}
	}
	
	function viewLoans(){

		global $connection;

		
		$sql="SELECT * FROM `assignments`, `courses`, `basic-information` WHERE  `courses`.ID = CourseID AND `assignments`.CreatorID = `basic-information`.ID ORDER BY DateCreated";
		
		if (!$pep= mysql_query($sql,$connection)) {
			die('Error: ' . mysql_error());
		}

		$init = TRUE;

		while($fetch = mysql_fetch_row($pep)){

			$grade = $fetch[3];			$firstname = $fetch[16];
			$lastname = $fetch[18];		$studentno = $fetch[8];
			$assignmentid = $fetch[0];		$assignmentname = $fetch[2];
			$assignmentfile = $fetch[9];	$batchdescription = $fetch[3];
			$uid = $fetch[20];			$date = $fetch[11];


				echo'<div style="border:solid 1px #ccc; padding-left: 10px; margin-bottom: 4px;">
				<table width="756">'.
				'<tr>'. 
				'<td width="200px"><b>Book name:</b></td>'.
				'<td width="150px"><b>Written by</b></td>'.
				'<td width="200px"><b>Return book before</b></td>'.
				'<td width="100px"><b></td>'.
				'</tr>';
				echo '<tr>'.
				'<td><a href="?id=courses&action=view&item='.$assignmentid .'"><b>'. $assignmentname .'</b></a></td>'.
				'<td>'. $firstname .' '. $lastname .'</a>'.
				'<td>'. $date .'</td>'.
				'<td>
				<b><a href="?id=studies&action=edit&item='. $fetch[0] .'"> Submit result </a></b>
				</td>'.
				'</tr></table></div>';	


		}

		echo'</table></div></p>';

	}

	function manageBooks(){

		global $connection;

		
		$sql="SELECT * FROM `assignments`, `courses`, `basic-information` WHERE  `courses`.ID = CourseID AND `assignments`.CreatorID = `basic-information`.ID ORDER BY DateCreated";
		
		if (!$pep= mysql_query($sql,$connection)) {
			die('Error: ' . mysql_error());
		}

		$init = TRUE;

		while($fetch = mysql_fetch_row($pep)){

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

		echo'</table></div></p>';

	}
}
?>