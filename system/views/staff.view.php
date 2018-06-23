<?php
class staff{

	public $core;
	public $view;

	public function configView() {
		$this->view->header = FALSE;
		$this->view->footer = FALSE;
		$this->view->menu = FALSE;
		$this->view->javascript = array();
		$this->view->css = array();

		return $this->view;
	}

	public function buildView($core) {
		$this->core = $core;
	}

	private function viewMenu($item, $phone){
		if($this->core->role != 107 && $this->core->role != 104 && $this->core->role != 1000 ){
			echo '<div class="toolbar">'.
			'<a href="' . $this->core->conf['conf']['path'] . '/staff/request">Request Leave</a>'.
			'<a href="' . $this->core->conf['conf']['path'] . '/staff/approval">Leave Approval</a>'.
			'</div>';
		}else{
			echo'<div class="toolbar">'.
			'<a href="' . $this->core->conf['conf']['path'] . '/staff/request">Request Leave</a>'.
			'<a href="' . $this->core->conf['conf']['path'] . '/staff/approval">Leave Approval</a>'.
			'<a href="' . $this->core->conf['conf']['path'] . '/staff/show">Manage Staff</a>'.
			'<a href="' . $this->core->conf['conf']['path'] . '/staff/show/expiring">Expiring Contracts</a>'.
			'<a href="' . $this->core->conf['conf']['path'] . '/sms/new/'.$phone.'">Message staff</a>'.
			'</div>';
		}
	}

	public function approvalStaff() {

		$this->viewMenu();

		$item = $this->core->userID;
		$role = $this->core->role;

		$sql = "SELECT * FROM `roles` WHERE `RoleManager` = '$item'";
		$run = $this->core->database->doSelectQuery($sql);
		if($run->num_rows > 0){
			echo "<h2>HEAD OF DEPARTMENT - LEAVE MANAGEMENT</h2><br/>";
			$hod = TRUE;
		}

		if($this->core->role != 107 && $this->core->role != 104 || $hod == TRUE){

			if($hod == TRUE){
				// HOD DEPARTMENT
				$sql = "SELECT `basic-information`.FirstName,`basic-information`.Surname,`leave`.EmployeeNo,`leave`.Description, `leave`.StartDate,`leave`.EndDate,`leave`.Status,`leave`.ID AS ID 
				FROM `leave`, `basic-information`, `roles`, `access`
				WHERE `basic-information`.ID = `leave`.`EmployeeNo`
				AND `leave`.`Status` != '100'
				AND `basic-information`.ID = `access`.ID
				AND `basic-information`.ID != '$item'
				AND `access`.`RoleID` = `roles`.ID
				AND `roles`.`RoleManager` = '$item'";
			}else{
				$sql = "SELECT `basic-information`.FirstName,`basic-information`.Surname,`leave`.EmployeeNo,`leave`.Description,
		   		`leave`.StartDate,`leave`.EndDate,`leave`.Status,`leave`.ID AS ID 
				FROM `leave`, `basic-information` 
				WHERE `basic-information`.ID = `leave`.EmployeeNo 
				AND `leave`.`Status` != '100'
				AND `basic-information`.ID = '$item'";
			}

		}else{

			$sql = "SELECT `basic-information`.FirstName,`basic-information`.Surname,`leave`.EmployeeNo,`leave`.Description,
		    	`leave`.StartDate,`leave`.EndDate,`leave`.Status,`leave`.ID AS ID 
			FROM `leave`, `basic-information`
			WHERE `basic-information`.ID = `leave`.EmployeeNo 
			AND `basic-information`.ID != '$item'
			AND `leave`.`Status` != '100'";
		}



		$run = $this->core->database->doSelectQuery($sql);

		echo'<table id="messages" class="table table-bordered  table-hover">
			<thead>
				<tr>
					<th bgcolor="#EEEEEE" width="30px"><b>Emp</b></th>
					<th bgcolor="#EEEEEE" width=""><b>Name</b></th>
					<th bgcolor="#EEEEEE" width="100px"><b>Start leave</b></th>
					<th bgcolor="#EEEEEE" width="100px"><b>End leave</b></th>
					<th bgcolor="#EEEEEE" width="150px">Description</th>
					<th bgcolor="#EEEEEE" width="150px">Status</th>
				</tr>
			</thead>
			<tbody>';

		if($run->num_rows == 0){
			echo'<div class="warningpopup">No leave requests currently made, PLEASE CLICK ON REQUEST LEAVE TO START A NEW REQUEST</div>';
		}

		while ($fetch = $run->fetch_assoc()) {
			$name = $fetch['FirstName'] . ' ' . $fetch['Surname'];
			$employeeid = $fetch['EmployeeNo'];
			$description = $fetch['Description'];
			$start = $fetch['StartDate'];
			$end = $fetch['EndDate'];
			$status = $fetch['Status'];
			$id = $fetch['ID'];
				
		
        if($status==0){
				$status= '<b><a href="' . $this->core->conf['conf']['path'] . '/staff/process/?ID='.$id.'&Status=approved"> Approve</a></b> |'.
				
                          '<b><a href="' . $this->core->conf['conf']['path'] . '/staff/process/?ID='.$id.'&Status=rejected"> Reject</a></b>';
              
		} elseif($status ==1){
			
			$status= 'Approved';
			}
            elseif($status ==2){
				
				$status= 'Rejected';
			}    

			echo'<tr>
				<td>'.$employeeid.'</td>
				<td><b><a href="' . $this->core->conf['conf']['path'] . '/information/show">'.$name.'</a></b></td>
				<td>'.$start.'</td>
				<td>'.$end.'</td>
				<td>'.$description.'</td>
				<td>'.$status.'</td>
			</tr>';
		}

		echo'</table>';
		

	}
	

	public function requestStaff($item) {
		include $this->core->conf['conf']['formPath'] . "requestleave.form.php";
	}


	public function leaverequestStaff($item){

		$employeeno = $this->core->userID;
		$start = $this->core->cleanPost['start'];
		$end = $this->core->cleanPost['end'];
		$description = $this->core->cleanPost['description'];

		$total = $this->getWorkdays($start, $end);

		$sql = "INSERT INTO `leave` (`ID`, `EmployeeNo`, `StartDate`, `EndDate`, `Description`, `Total`, `Status`, `Comment`) 
			VALUES ('', '$employeeno', '$start', '$end', '$description', '$total', '0', '');";
			
        	$run = $this->core->database->doInsertQuery($sql);

		echo '<div class="successpopup">Your leave request was submitted to your HOD for approval</div>';
		$this->approvalStaff();

		
       		$this->core->redirect("approval", "show", $item);
	}

	
	public function processStaff($item){
			
	if(isset($_GET['Status'] )) {
        $id = $this->core->userID;
        $id = $this->core->cleanGet['ID'];
	    $status = $this->core->cleanGet['Status'];

		if ($status == 'approved'){

			$sql = "UPDATE `leave` SET `Status` = 1 WHERE `leave`.`ID` =".$id;
			$run = $this->core->database->doInsertQuery($sql);
			approvalStaff();
			
		}elseif($status == 'rejected'){

			$sql = "UPDATE `leave` SET `Status` = 2 WHERE leave.ID =". $id;
			$run = $this->core->database->doInsertQuery($sql);
			
			approvalStaff();
		}
				$this->core->redirect("process", "app", $item);

	 }
			
}
	 
	 
	 
	public function showStaff($item) {

		$this->viewMenu();


		if($this->core->item == 'expiring'){
			$sql = "SELECT * FROM `basic-information`
			LEFT JOIN `staff` ON `basic-information`.`ID` = `staff`.`EmployeeNo`
			WHERE `Status` = 'Employed'
			AND `EndDate`>= NOW() AND `EndDate` <= NOW() + INTERVAL 6 MONTH
			AND `EndDate` LIKE '2018%'
			ORDER BY `staff`.`EndDate`  ASC";
		} else {
			$sql = "SELECT * FROM `basic-information`
			LEFT JOIN `staff` ON `basic-information`.`ID` = `staff`.`EmployeeNo`
			WHERE `Status` = 'Employed'";
		}


		$run = $this->core->database->doSelectQuery($sql);

		echo'<table id="messages" class="table table-bordered  table-hover">
			<thead>
				<tr>
					<th bgcolor="#EEEEEE" width="30px"><b>Emp</b></th>
					<th bgcolor="#EEEEEE" width=""><b>Name</b></th>
					<th bgcolor="#EEEEEE" width="70px"><b>Date of Employment</b></th>
					<th bgcolor="#EEEEEE" width="100px"><b>End of contract</b></th>
					<th bgcolor="#EEEEEE" width="50px">Grade</th>
					<th bgcolor="#EEEEEE" width="50px">Leave</th>
				</tr>
			</thead>
			<tbody>';

		while ($fetch = $run->fetch_assoc()) {
			$name = $fetch['FirstName'] . ' ' . $fetch['Surname'];
			$employeeid = $fetch['EmployeeNo'];
			$grade = $fetch['Grade'];
			$doe = $fetch['EmploymentDate'];
			$eoe = $fetch['EndDate'];
			$leave = $fetch['Leavedays'];

			echo'<tr>
				<td>'.$employeeid.'</td>
				<td><b><a href="#">'.$name.'</a></b></td>
				<td>'.$doe.'</td>
				<td>'.$eoe.'</td>
				<td>'.$grade.'</td>
				<td>'.$leave.'</td>
			</tr>';
		}

		echo'</table>';

	}
	

	private function getWorkdays($date1, $date2, $workSat = FALSE, $patron = NULL) {
	  if (!defined('SATURDAY')) define('SATURDAY', 6);
	  if (!defined('SUNDAY')) define('SUNDAY', 0);


	  // DEFINE HOLIDAYS HERE!!!!
	  $publicHolidays = array('01-01', '01-06', '04-25', '05-01', '06-02', '08-15', '11-01', '12-08', '12-25', '12-26');

	  if ($patron) {
	    $publicHolidays[] = $patron;
	  }
	  $yearStart = date('Y', strtotime($date1));
	  $yearEnd   = date('Y', strtotime($date2));
	  for ($i = $yearStart; $i <= $yearEnd; $i++) {
	    $easter = date('Y-m-d', easter_date($i));
	    list($y, $m, $g) = explode("-", $easter);
	    $monday = mktime(0,0,0, date($m), date($g)+1, date($y));
	    $easterMondays[] = $monday;
	  }
	  $start = strtotime($date1);
	  $end   = strtotime($date2);
	  $workdays = 0;
	  for ($i = $start; $i <= $end; $i = strtotime("+1 day", $i)) {
	    $day = date("w", $i);  // 0=sun, 1=mon, ..., 6=sat
	    $mmgg = date('m-d', $i);
	    if ($day != SUNDAY &&
	      !in_array($mmgg, $publicHolidays) &&
	      !in_array($i, $easterMondays) &&
	      !($day == SATURDAY && $workSat == FALSE)) {
	        $workdays++;
	    }
	  }
	  return intval($workdays);
	}
}
?>