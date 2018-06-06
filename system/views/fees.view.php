<?php
class fees {

	public $core;
	public $view;
	public $item = NULL;

	public function configView() {
		$this->view->header = TRUE;
		$this->view->footer = TRUE;
		$this->view->menu = TRUE;
		$this->view->javascript = array('jquery.ui.datepicker');
		$this->view->css = array();

		return $this->view;
	}

	public function buildView($core) {
		$this->core = $core;
	}
	
	public function editFees($item) {
		$sql = "SELECT * FROM `fees` WHERE `fees`.ID = '$item'";

		$run = $this->core->database->doSelectQuery($sql);

		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
		
		$select = new optionBuilder($this->core);

		if ($fetch = $run->fetch_row()) {
			$owner = $select->showUsers("100", $fetch[6]);
			$name = $fetch[2]; 
			$description = $fetch[3]; 
			$amount = $fetch[4]; 
			$packageid = $fetch[1]; 

			include $this->core->conf['conf']['formPath'] . "editfee.form.php";
		}
	}

	public function addFees() {
		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
		
		$select = new optionBuilder($this->core);
		$owner = $select->showUsers("100", null);

		include $this->core->conf['conf']['formPath'] . "addfee.form.php";
	}


	public function deleteFees($item) {
		$sql = 'DELETE FROM `fees` WHERE `ID` = "' . $item . '"';
		$run = $this->core->database->doInsertQuery($sql);

		$this->core->redirect("fees", "show", $this->core->subitem);
	}

	public function saveFees($item) {
		$name = $this->core->cleanPost['name'];
		$description = $this->core->cleanPost['description'];
		$owner = $this->core->cleanPost['owner'];
		$amount = $this->core->cleanPost['amount'];
		$packageid = $this->core->cleanPost['packageid'];

		if (!empty($item)) {
			$sql = "UPDATE `fees` SET `Name` = '$name', `Description` = '$description', `Owner` = '$owner', `Amount` = '$amount' WHERE `ID` = $item;";
			$run = $this->core->database->doInsertQuery($sql);
		} else {
			$sql = "INSERT INTO `fees` (`ID`, `PackageID`, `Name`, `Description`, `Amount`, `Date`, `Owner`) VALUES (NULL, '$packageid', '$name', '$description', '$amount', NOW(), '$owner');";
			$run = $this->core->database->doInsertQuery($sql);
		}

		$this->core->redirect("fees", "show", $packageid);
	}

	public function showFees($item, $admin = FALSE) {
		if($admin == FALSE){
			$sql = "SELECT * FROM `fees` LEFT JOIN `fee-package` ON `fees`.PackageID = `fee-package`.ID WHERE `fee-package`.ID = '$item'";
		}else{
			$sql = "SELECT * FROM `fees` LEFT JOIN `fee-package` ON `fees`.PackageID = `fee-package`.ID WHERE `fee-package`.Name = '$item'";
		}

		$run = $this->core->database->doSelectQuery($sql);

		if($admin != TRUE){ 
			$output .=  '<div class="toolbar">
			<a href="' . $this->core->conf['conf']['path'] . '/fees/add/'.$item.'">Add Fee</a>
			</div>';
		}

		$feeset = FALSE;
		

		$i = 0;
		$total = 0;
		while ($fetch = $run->fetch_row()) {
			$feeset = TRUE;
			if ($i == 0) {
				$method = $fetch[4];
				$output.= '<table border="0" cellpadding="5" cellspacing="0">

					  <tr>
						<td  width="200px"><strong>Fee package</strong></td>
						<td width="200px">' . $fetch[8] . '</td>
						<td></td>
					  </tr>
					  <tr>
						<td><strong>Package description</strong></td>
						<td>' . $fetch[9] . ' </td>
					  </tr>
					</table>';

				$i++;

				$output .= '<br /><table width="600px" border="0" cellpadding="5" cellspacing="0">
					    <tr>
					    <td width="200px" height="28" bgcolor="#EEEEEE"><strong>Fee information</strong></td>
					    <td width="300px" bgcolor="#EEEEEE">Description</td>
					    <td width="100px" bgcolor="#EEEEEE">Cost</td>';

				if($admin != TRUE){ $output .= '<td bgcolor="#EEEEEE">Options</td>'; }

				$output .= '</tr>
					    <tr>
					    <td><strong>' . $fetch[2] . '</strong></td>
					    <td>' . $fetch[3] . ' </td>
					    <td><b>' . $fetch[4] . '</b></td>';

				if($admin != TRUE){
					$output .= '<td>
					<a href="' . $this->core->conf['conf']['path'] . '/fees/edit/'.$fetch[0].'"> <img src="'.$this->core->fullTemplatePath.'/images/edi.png"> edit</a>
					<a href="' . $this->core->conf['conf']['path'] . '/fees/delete/' . $fetch[0] . '/'. $item .'" onclick="return confirm(\'Are you sure?\')"> <img src="'.$this->core->fullTemplatePath.'/images/del.png"> delete </a>
					</td>';
				}

				$output.= '</tr>';

				$total = $fetch[4] + $total;
		
			 }else if(!empty($fetch[5])){


				$output.= ' <tr>
					<td><strong>' . $fetch[2] . '</strong></td>
					<td>' . $fetch[3] . ' </td>
					<td><b>' . $fetch[4] . '</b></td>';

					if($admin != TRUE){
						$output.= '<td>
						<a href="' . $this->core->conf['conf']['path'] . '/fees/edit/'.$fetch[0].'"> <img src="'.$this->core->fullTemplatePath.'/images/edi.png"> edit</a>
						<a href="' . $this->core->conf['conf']['path'] . '/fees/delete/' . $fetch[0] . '" onclick="return confirm(\'Are you sure?\')"> <img src="'.$this->core->fullTemplatePath.'/images/del.png"> delete </a>
						</td>';
					}
				$output.= ' </tr>';

				$total = $fetch[4] + $total;
			
			}



		}

		if($feeset == TRUE){
			$output.= ' <tr>
				<td width="205" height="28" bgcolor="#EEEEEE"><strong>Total fees in package</strong></td>
				<td width="200" bgcolor="#EEEEEE"></td>
				<td bgcolor="#EEEEEE"><strong>' . $total . '</strong></td>
				<td width="200" bgcolor="#EEEEEE"></td>
		 	 </tr>
			</table>';
		}

		if($admin == FALSE){
			echo $output;
		} else { 
			return $output;
		}

	}

	public function assignedFees($item) {	
		$sql = "SELECT * FROM `basic-information`,
			WHERE `basic-information`.ID = '$item'
			LEFT JOIN `student-study-link` as ss ON ss.`StudentID` = `basic-information`.ID
			LEFT JOIN `student-program-link` as sp ON sp.`StudentID` = `basic-information`.ID
			LEFT JOIN `fee-package-study-link` as fpsl ON fpsl.StudyID = `StudyID`.ID
			LEFT JOIN `fee-package-program-link` as fppl ON fppl.ProgramID = `ProgrammeID`.ID";

		$sql = "SELECT * FROM `fee-package` LEFT JOIN `fees` ON `fees`.PackageID = `fee-package`.ID";

		$run = $this->core->database->doSelectQuery($sql);

		echo '<div class="toolbar"><a href="' . $this->core->conf['conf']['path'] . '/fees/add">Add Fee</a></div>';

		$i = 0;
		while ($fetch = $run->fetch_row()) {
			if ($i == 0) {
				$method = $fetch[4];
				echo '<table width="768" border="0" cellpadding="5" cellspacing="0">
					 <tr>
						<td width="205" height="28" bgcolor="#EEEEEE"><strong>Fee package</strong></td>
						<td width="500" bgcolor="#EEEEEE"></td>
						<td  bgcolor="#EEEEEE"></td>
					  </tr>
					  <tr>
						<td><strong>Fee package</strong></td>
						<td>' . $fetch[1] . '</td>
						<td></td>
					  </tr>
					  <tr>
						<td><strong>Package description</strong></td>
						<td>' . $fetch[2] . ' </td>
					  </tr>'; 

					echo'</table>';

				$i++;


				echo '<br /><table width="768" border="0" cellpadding="5" cellspacing="0">
					 <tr>
						<td width="205" height="28" bgcolor="#EEEEEE"><strong>Fee information</strong></td>
						<td width="200" bgcolor="#EEEEEE">Description</td>
						<td bgcolor="#EEEEEE">Cost</td>
						<td bgcolor="#EEEEEE">Options</td>
					  </tr>

					 <tr>
						<td><strong>' . $fetch[7] . '</strong></td>
						<td>' . $fetch[8] . ' </td>
						<td><b>' . $fetch[9] . '</b></td>
						<td>
							<a href="' . $this->core->conf['conf']['path'] . '/fees/editfee/'.$fetch[5].'"> <img src="'.$this->core->fullTemplatePath.'/images/edi.png"> edit</a>
							<a href="' . $this->core->conf['conf']['path'] . '/fees/deletefee/' . $row[5] . '" onclick="return confirm(\'Are you sure?\')"> <img src="'.$this->core->fullTemplatePath.'/images/del.png"> delete </a>
						</td>
					  </tr>';

					$total = $fetch[9] + $total;

			 }else if(!empty($fetch[5])){


					 echo' <tr>
						<td><strong>' . $fetch[7] . '</strong></td>
						<td>' . $fetch[8] . ' </td>
						<td><b>' . $fetch[9] . '</b></td>
						<td>
							<a href="' . $this->core->conf['conf']['path'] . '/fees/editfee/'.$fetch[5].'"> <img src="'.$this->core->fullTemplatePath.'/images/edi.png"> edit</a>
							<a href="' . $this->core->conf['conf']['path'] . '/fees/deletefee/' . $row[5] . '" onclick="return confirm(\'Are you sure?\')"> <img src="'.$this->core->fullTemplatePath.'/images/del.png"> delete </a>
						</td>
					  </tr>';

					$total = $fetch[9] + $total;
			}


		}


		echo' <tr>
			<td width="205" height="28" bgcolor="#EEEEEE"><strong>Total fees in package</strong></td>
			<td width="200" bgcolor="#EEEEEE"></td>
			<td bgcolor="#EEEEEE"><strong>' . $total . '</strong></td>
			<td width="200" bgcolor="#EEEEEE"></td>
		  </tr>
		</table>';


	}
}
?>
