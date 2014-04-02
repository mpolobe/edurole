<?php
class payments {

	public $core;
	public $view;

	public function configView() {
		$this->view->header = TRUE;
		$this->view->footer = TRUE;
		$this->view->menu = TRUE;
		$this->view->javascript = array();
		$this->view->css = array();

		return $this->view;
	}

	private function viewMenu(){
		echo '<div class="toolbar">'.
		'<a href="' . $this->core->conf['conf']['path'] . '/payments/unknown">Show list of unknown payments</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/payments/manage">Show list of known payments</a></div>';
	}

	public function buildView($core) {
		$this->core = $core;
	}

	public function unknownPayments($item=NULL, $linked = TRUE) {
		$this->managePayments(NULL, FALSE);
	}

	public function approvePayments($item=NULL, $linked = TRUE) {

	}

	public function rejectPayments($item=NULL, $linked = TRUE) {

	}

	public function managePayments($item=NULL, $linked = TRUE) {
		if($linked==TRUE){
			if($item==NULL){
				$sql = "SELECT * FROM `transactions` 
				LEFT JOIN `basic-information`
				ON `transactions`.StudentID = `basic-information`.ID 
				ORDER BY `transactions`.TransactionDate";
			}else{
				$sql = "SELECT * FROM `transactions` 
				LEFT JOIN `basic-information`
				ON `transactions`.StudentID = `basic-information`.ID 
				WHERE `basic-information`.ID = '$item'";
			}
		} else {

			$sql = "SELECT * FROM `transactions` 
			WHERE `transactions`.StudentID NOT IN (SELECT `basic-information`.ID FROM `basic-information`) 
			ORDER BY `transactions`.TransactionDate";

		}

		$run = $this->core->database->doSelectQuery($sql);

		if($this->core->role > 10){
			$this->viewMenu();
		}

		echo'<table width="768" height="" border="0" cellpadding="3" cellspacing="0">'.
		'<tr class="tableheader"><td><b>Transaction ID</b></td>' .
		'<td width="60px"><b>Time</b></td>' .
		'<td width="65px"><b>Amount</b></td>' .
		'<td width="60px"><b>Student ID</b></td>' .
		'<td><b>Deposit Name</b></td>' .
		'<td><b>Successfully linked to</b></td>' .
		'<td><b>Match</b></td>' .
		'<td><b>Management</b></td>' .
		'</tr>';

		$i = 0;
		$percent = 0;
		$percenttwo = 0;
		$color = "";
		$name = "";
		$userid = "";
		while ($fetch = $run->fetch_row()) {
			if($this->core->action != "unknown"){
			$percenttwo = 0;
			$name1 = ucwords(strtolower($fetch[8]));
			$name2 = ucwords(strtolower($fetch[18] . " " . $fetch[16]));
			similar_text($name1, $name2, $percent);
			$percent = floor($percent);

			$name1 = ucwords(strtolower($fetch[8]));
			$name2 = ucwords(strtolower($fetch[18] . " " . $fetch[17] . " " .  $fetch[16]));
			similar_text($name1, $name2, $percenttwo);
			$percenttwo = floor($percenttwo);

			$name1 = ucwords(strtolower($fetch[8]));
			$name2 = ucwords(strtolower($fetch[16] . " " . $fetch[17] . " " .  $fetch[18]));
			similar_text($name1, $name2, $percentthree);
			$percentthree = floor($percentthree);

			if($percenttwo>$percent){
				$percent = $percenttwo;
			}

			if($percentthree>$percent){
				$percent = $percentthree;
			}

			if($percent<70){
				$color = 'style="color: #FF0000;"';
			} else {
				$color = '';
			}

			if(!empty($name1)){
				$percent = '<b>('. $percent .'%)</b>';
			}

			$name = $fetch[16] .'  '. $fetch[18];
			$userid = $fetch[20];
			}

			echo '<tr ' . $color . '>
			<td><b><a href="' . $this->core->conf['conf']['path'] . '/payments/show/' . $fetch[0] . '"> ' . $fetch[3] . '</a></b></td>
			<td>' . $fetch[6] . '</td>
			<td><b>' . $fetch[7] . ' '.$this->core->conf['conf']['currency'].'</b></td>
			<td>' . $fetch[4] . '</td>
			<td>' . ucwords(strtolower($fetch[8])) . ' </td>
			<td><a href="' . $this->core->conf['conf']['path'] . '/information/show/'. $userid .'">'.$name.'</a></td>
			<td>'. $percent .'</td>
			<td>
			<a href="' . $this->core->conf['conf']['path'] . '/payments/edit/' . $fetch[0] . '"> <img src="' . $this->core->fullTemplatePath . '/images/edi.png"> edit</a>
 			</td>
			</tr>';
		}

		echo '</table>';
	}

	function listPayments($item){
		$this->managePayments($this->core->userID);
	}

	function showPayments($item){
		$sql = "SELECT * FROM `transactions` 
			WHERE `transactions`.ID = '$item'";

		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_row()) {

		echo '<table width="768" border="0" cellpadding="5" cellspacing="0">
                  <tr>
                    <td width="205" height="28" bgcolor="#EEEEEE"><strong>Information</strong></td>
                    <td width="200" bgcolor="#EEEEEE"></td>
                    <td  bgcolor="#EEEEEE"></td>
                  </tr>
                  <tr>
                    <td><strong>Transaction ID</strong></td>
                    <td> <b>' . $fetch[3] . '</b></td>
                    <td></td>
                  </tr>
                  <tr>
                    <td><strong>Student Number</strong></td>
                    <td><a href="' . $this->core->conf['conf']['path'] . '/information/show/' . $fetch[4] . '">' . $fetch[4] . '</a></td>
                    <td></td>
                  </tr>
                   <tr>
                    <td><strong>NRC</strong></td>
                    <td>' . $fetch[5] . '</td>
                    <td></td>
                  </tr>
                  <tr>
                    <td><strong>Transaction Date</strong></td>
                    <td>' . $fetch[6] . '</td>
                    <td></td>
                  </tr>
                  <tr>
                    <td><strong>Amount</strong></td>
                    <td>' . $fetch[7] . '</td>
                    <td></td>
                  </tr>
                  <tr>
                    <td><strong>Name</strong></td>
                    <td>' . $fetch[8] . '</td>
                    <td></td>
                  </tr>
                  <tr>
                    <td><strong>Type</strong></td>
                    <td>' . $fetch[9] . '</td>
                    <td></td>
                  </tr>
                  <tr>
                    <td><strong>Status</strong></td>
                    <td>' . $fetch[14] . '</td>
                    <td></td>
                  </tr>
		 </table>';

		}		
	}

	function balancePayments($item) {

		if(empty($item)){
			$item = $this->core->userID;
		}

		$sql = "SELECT * FROM `fees`
			LEFT JOIN `fee-package` ON `fee-package`.ID = `fees`.PackageID

			LEFT JOIN `basic-information` ON `basic-information`.ID = '$item'
			LEFT JOIN `nkrumah-student-program-link` ON `nkrumah-student-program-link`.StudentID = `basic-information`.ID
			LEFT JOIN `programmes-link` ON `programmes-link`.ID = `nkrumah-student-program-link`.ProgrammeID

			LEFT JOIN `student-study-link` ON `student-study-link`.StudentID = `basic-information`.ID
			LEFT JOIN `study` ON `study`.ID = `student-study-link`.StudyId 

			LEFT JOIN `fee-package-study-link` ON `fee-package-study-link`.StudyID = `study`.ID
			LEFT JOIN `fee-package-program-link` ON `fee-package-program-link`.ProgramID = `programmes-link`.ID";

		$sqlp = "SELECT * FROM `transactions` 
				LEFT JOIN `basic-information`
				ON `transactions`.StudentID = `basic-information`.ID 
				WHERE `basic-information`.ID = '$item'";

		$run = $this->core->database->doSelectQuery($sql);
		$runp = $this->core->database->doSelectQuery($sqlp);

		$i = 0;
		$total = 0;

		while ($fetch = $run->fetch_row()) {

			if ($i == 0) {
				$method = $fetch[4];
				echo '<h2>Due Fees</h2><br><table width="768" border="0" cellpadding="5" cellspacing="0">
					 <tr>
						<td width="205" height="28" bgcolor="#EEEEEE"><strong>Fee package</strong></td>
						<td width="500" bgcolor="#EEEEEE"></td>
						<td  bgcolor="#EEEEEE"></td>
					  </tr>
					  <tr>
						<td><strong>Fee package</strong></td>
						<td>' . $fetch[8] . '</td>
						<td></td>
					  </tr>
					  <tr>
						<td><strong>Package description</strong></td>
						<td>' . $fetch[9] . ' </td>
					  </tr>'; 

					echo'</table>';

				$i++;


				echo '<br /><table width="768" border="0" cellpadding="5" cellspacing="0">
					 <tr>
						<td width="205" height="28" bgcolor="#EEEEEE"><strong>Fee information</strong></td>
						<td width="200" bgcolor="#EEEEEE">Description</td>
						<td bgcolor="#EEEEEE" >Cost</td>
					  </tr>'; 
				}


					$total = $fetch[4] + $total;


					echo' <tr>
						<td><strong>' . $fetch[2] . '</strong></td>
						<td>' . $fetch[3] . ' </td>
						<td><b>' . $fetch[4] . '</b></td>
					  </tr>';


					$total = $fetch[9] + $total;
			


		}


		echo' <tr>
			<td width="205" height="28" bgcolor="#EEEEEE"><strong>Total fees in package</strong></td>
			<td width="400" bgcolor="#EEEEEE"></td>
			<td bgcolor="#EEEEEE"><strong>' . $total . '</strong></td>

		  </tr>
		</table> <br/>';

		$totalpayed = 0;

		echo 
		'<h2>Received payments </h2><br><table width="768" height="" border="0" cellpadding="3" cellspacing="0">'.
		'<tr class="tableheader"><td><b>Transaction ID</b></td>' .
		'<td width="60px"><b>Time</b></td>' .
		'<td width="65px"><b>Amount</b></td>' .
		'<td width="60px"><b>Student ID</b></td>' .
		'<td><b>Deposit Name</b></td>' .
		'<td><b>Successfully linked to</b></td>' .
		'<td><b>Match</b></td>' .
		'<td><b>Management</b></td>' .
		'</tr>';

		$i = 0;
		while ($fetch = $runp->fetch_row()) {

			$name1 = ucwords(strtolower($fetch[8]));
			$name2 = ucwords(strtolower($fetch[18] . " " . $fetch[16]));
			similar_text($name1, $name2, $percent);
			$percent = floor($percent);

			$name1 = ucwords(strtolower($fetch[8]));
			$name2 = ucwords(strtolower($fetch[18] . " " . $fetch[17] . " " .  $fetch[16]));
			similar_text($name1, $name2, $percenttwo);
			$percenttwo = floor($percenttwo);

			$name1 = ucwords(strtolower($fetch[8]));
			$name2 = ucwords(strtolower($fetch[16] . " " . $fetch[17] . " " .  $fetch[18]));
			similar_text($name1, $name2, $percentthree);
			$percentthree = floor($percentthree);

			if($percentwo>$percent){
				$percent = $percenttwo;
			}

			if($percentthree>$percent){
				$percent = $percentthree;
			}

			if($percent<70){
				$color = 'style="color: #FF0000;"';
			} else {
				$color = '';
			}

			if(!empty($name1)){
				$percent = '<b>('. $percent .'%)</b>';
			}

			echo '<tr ' . $color . '>
			<td><b><a href="' . $this->core->conf['conf']['path'] . '/payments/details/' . $fetch[0] . '"> ' . $fetch[3] . '</a></b></td>
			<td>' . $fetch[6] . '</td>
			<td><b>' . $fetch[7] . ' '.$this->core->conf['conf']['currency'].'</b></td>
			<td>' . $fetch[4] . '</td>
			<td>' . ucwords(strtolower($fetch[8])) . ' </td>
			<td><a href="' . $this->core->conf['conf']['path'] . '/information/show/'. $fetch[20] .'">'. $fetch[16] .'  '. $fetch[18] .'</a></td>
			<td>'. $percent .'</td>
			<td>
			<a href="' . $this->core->conf['conf']['path'] . '/payments/edit/' . $fetch[0] . '"> <img src="' . $this->core->fullTemplatePath . '/images/edi.png"> edit</a>
 			</td>
			</tr>';

			$totalpayed = $fetch[7] + $totalpayed;
		}


		echo '<tr class="tableheader"><td><b>Total payed</b></td>' .
		'<td width="60px"></td>' .
		'<td width="65px"><b>'. $totalpayed .' '.$this->core->conf['conf']['currency'].'</b></td>' .
		'<td width="60px"></td>' .
		'<td></td>' .
		'<td></td>' .
		'<td></td>' .
		'<td></td>' .
		'</tr>';

		echo '</table>';

		$balance = $total-$totalpayed;

		echo'<br> <h2>Oustanding balance </h2><br> <table width="768" border="0" cellpadding="5" cellspacing="0">
			 <tr>
			<td width="205" height="28" bgcolor="#D4ABA0"><h2>Outstanding fees</h2></td>
			<td width="200" bgcolor="#D4ABA0"></td>
			<td bgcolor="#D4ABA0"><h2>' . $balance . ' '.$this->core->conf['conf']['currency'].'</h2></td>
			<td width="200" bgcolor="#D4ABA0"></td>
		  </tr>
		</table> <br/>';
	}
}

?>
