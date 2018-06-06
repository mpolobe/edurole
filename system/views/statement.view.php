<?php
class statement {

	public $core;
	public $view;
	public $item = NULL;

	public function configView() {
		$this->view->header = TRUE;
		$this->view->footer = TRUE;
		$this->view->menu = TRUE;
		$this->view->javascript = array();
		$this->view->css = array();

		return $this->view;
	}

	public function buildView($core) {
		$this->core = $core;
	}

	function overviewStatement($item) {		
		$studentID = $_GET['uid'];
		$start = $_GET['start'];
		$end = $_GET['end'];

		$sql = "SELECT COUNT( DISTINCT  `grades`.StudentNo) FROM `grades`, `basic-information`
			WHERE `grades`.StudentNo LIKE '$studentID%' AND `basic-information`.ID = `grades`.StudentNo ";

		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_array()){
			$count = $fetch[0];

			$pages = $count / 100;
			$count = 1;

			while($count < $pages){
				$show = $count * 100;
				$old  = $show-100;
				echo 'Print results from '.$old.' to '.$show.' - <a href="' . $this->core->conf['conf']['path'] . '/statement/batch/?uid='.$studentID.'&start='.$old.'&end='.$show.'">CLICK HERE</a><br>';
				$count++;
			}
		}
	}


	function batchStatement($item) {
		$studentID = $_GET['uid'];
		$start = $_GET['start'];
		$end = $_GET['end'];

		$sql = "SELECT `grades`.StudentNo FROM `grades`, `basic-information`
			WHERE `grades`.StudentNo LIKE '$studentID%' AND `basic-information`.ID = `grades`.StudentNo GROUP BY `grades`.StudentNo LIMIT $start, $end ";

		$run = $this->core->database->doSelectQuery($sql);
		$first = TRUE;

		while ($fetch = $run->fetch_array()){
			if($x == 5){
				echo'<div style="page-break-after: always;"> </div> ';
				$x=0;
			} else {
				if($first == FALSE){
					echo "<hr noshade>";
				}
			}

			$studentid = $fetch[0];
			$this->resultsStatement($studentid);
			$first = FALSE;
			$i++;
			$x++;
		}

		echo'<script type="text/javascript">
			window.print();
		</script>';

	}
	
	function resultsStatement($item) {

		if(!isset($item) || $this->core->role < 100){
			$item = $this->core->userID;
		}

		if($item == ""){
			$studentID = $_GET['uid'];
		} else {
			$studentID = $item;
		}

		$studentNo = $studentID;
		$start = substr($studentID, 0, 4);

		$sql = "SELECT Firstname, MiddleName, Surname, Status, Sex, Status
			FROM `basic-information`
			WHERE `basic-information`.ID = '$studentID'";
			
		$run = $this->core->database->doSelectQuery($sql);

		$started = FALSE;

		while ($fetch = $run->fetch_array()){

			$started = TRUE;

			$firstname = $fetch[0];
			$middlename = $fetch[1];
			$surname = $fetch[2];
			$remark=$fetch[5];
			$sex=$fetch[4]; 
			$studentname = $firstname . " " . $middlename . " " . $surname;

			$major = $fetch[6];
			$minor = $fetch[7];

			// PAYMENT VERIFICATION FOR GRADES
			require_once $this->core->conf['conf']['viewPath'] . "payments.view.php";
			$payments = new payments();
			$payments->buildView($this->core);
			$actual = $payments->getBalance($item);
	

			if($actual > 100){
				echo ' <h2>OUTSTANDING BALANCE!</h2><div class="errorpopup">According to our financial records you are owing the institution <u>K'.$actual.'</u>. 
					<br>Please check your payments and settle your balance to be able to access your grades
					<br>  <a href="' . $this->core->conf['conf']['path'] . '/payments/show/'.$item.'">View your recent payments</a> </div>';
				if($this->core->role != 1000){
					return;
				}
			}

	
			if($this->core->action == "results"){
				echo'<div style="font-size: 16pt; padding-left: 30px; color: #333; margin-top: 15px;  clear: both; ">STATEMENT OF RESULTS</div><br>';
				echo"<div style=\" width: 660px; padding-left: 30px; margin-top: 15px; height: 40px;\">
						Results for <b>$studentname</b>
						<br> Student No.:<b>$studentID</b>
						<br> Major: <b>$major</b>
						<br> Minor: <b>$minor</b>
						<br>
					</div>
					<div style=\" margin-top: 15px; margin-left: 30px;\">";
			}

			if(isset($year)){
				$overallremark= $this->currentyear($studentNo, $year);
			}else{
				$overallremark= $this->academicyear($studentNo);
			}

			//echo '<div style="  margin-left: 50px; margin-top: 10px;">';

			if ($overallremark=="EXCLUDE" or $overallremark=="DISQUALIFIED" or $overallremark=="SUSPENDED" or $overallremark=="WITHHELD"){
				print "<b>OVERALL REMARK: $overallremark</b>";
			} else { 
				//print "<b>OVERALL REMARK: PROCEED</b>";
			}

			if($this->core->action == "results"){
				echo '<hr>
				<p>
					<b>Deputy Vice Chancellor,</b>
					<br><br><br><br>
					Dr. Judith G. N. Lungu (PhD.)<br>
				</p>';
			}
		}

		

	}

	private function currentyear($studentNo, $year) {

		echo '<table style="font-size: 11px; width: 700px; margin-top:-20px;">';

		$acyr = $year;
		$count = 0;
		$count1 = 0;
	
		$overallremark= $this->detail($studentNo, $acyr, $countyear, $repeat);
		$remark = $overallremark[0];
		$repeat = $overallremark[1];

		print "</table>\n";
		return $remark;
	}



	private function academicyear($studentNo) {

	
		echo '<table style="font-size: 11px; width: 700px; margin-top:30px;">';

		$sql = "SELECT distinct academicyear FROM `grades` WHERE StudentNo = '$studentNo' order by academicyear";

		$run = $this->core->database->doSelectQuery($sql);
		$countyear = 1;
		while ($fetch = $run->fetch_array()){
			$acyr = $fetch[0];
			$count = 0;
			$count1 = 0;
	
			$overallremark= $this->detail($studentNo, $acyr, $countyear, $repeat);
			$remark = $overallremark[0];
			$repeat = $overallremark[1];
			$countyear++;
		}

		print "</table>\n";
		return $remark;
	}

	private function detail($studentNo, $acyr, $countyear, $repeat) {

		$remarked = FALSE;

		echo'<tr class="heading">
			<td><br><b>'.$acyr.' (YEAR '.$countyear.')</b></td>
			<td><br><b>COURSE NAME</td>
			<td><br><b>GRADE</b></td>';
			

		if($this->core->role == "105" || $this->core->role == "1000"){
			echo'<td><br><b>EDIT</b></td>';
		}


		echo'</tr>';

		$sql = "SELECT 
				p1.CourseNo,
				p1.Grade,
				p2.CourseDescription,
				p1.ID
			FROM 
				`grades` as p1,
				`courses` as p2
			WHERE 	p1.StudentNo = '$studentNo'
			AND	p1.AcademicYear = '$acyr'
			AND	p1.CourseNo = p2.Name  
			ORDER BY p1.courseNo";

		$run = $this->core->database->doSelectQuery($sql);

		$output = "";
		$count2 = 0;
		$countwp=0;
		$suppoutput1="";
		$suppoutput2="";
		$suppoutput3="";
		$countb = 0;
		$suppcount = 0;

		$i=0;
		$repeatlist = array();

		while ($row = $run->fetch_array()){

			if($row[1] == ''){
				continue;
			}

			$i++;			
			echo "<tr>
				<td><b>$row[0]</b></td>
				<td>$row[2]</td>
				<td><b>$row[1]</b></td>";
			

			if($this->core->role == "105" || $this->core->role == "1000"){
				echo '<td><a href="' . $this->core->conf['conf']['path'] . '/grades/add/'.$row[3].'"<b>CHANGE</b></a></td>';
			}

			echo'</tr>';

			$count2 = $count2 + 3;

			if ($row[1] == "IN" or $row[1] == "D" or $row[1]=="F" or $row[1]=="NE") {

				$output .= "REPEAT $row[0]; ";
				if (substr($row[0], -1) =='1'){
					$count=$count + 0.5;
				}else{
					$count=$count + 1;
				}

				$courseno=$row[0];
				$countb=$countb + 1;
				$repeatlist[] =  $row[0];

				$upfail[$i] = $courseno;
			}
			

			if ($row[1]== "A+" or $row[1]=="A" or $row[1]=="B+" or $row[1]=="B" or $row[1]=="C+" or $row[1]=="C" or $row[1]=="P") {
				$k=$j-1;

				if (substr($row[0], -1) == 1){
					$count1=$count1 + 0.5;
					$count1before=$count1;

			 		if(count($upfail)>0){
						$count1 = $count1-0.5;
					}

					$checkcount=$count1before-$count1;

					if ($checkcount==1){
						$count=$count-1;
						$count1=$count1+1;
					}

					if ($checkcount==0.5){
						$count=$count-0.5;
						$count1=$count1+0.5;
					}
				} else {
					$count1=$count1 + 1;
					$count1before=$count1;
					if(count($upfail)>0){
						$count1 = $count1-0.5;
					}
					$checkcount=$count1before-$count1;

					if ($checkcount==1){
						$count=$count-1;
						$count1=$count1+1;
					}

					if ($checkcount==0.5){
						$count=$count-0.5;
						$count1=$count1+0.5;
					}
				}
			}

			if ($row[1] == "D+") {

				if($suppcount < 2){
					$suppoutput1 .= "SUPP IN $row[0]; ";
					$suppoutput2 .= "REPEAT $row[0]; ";
				}else{
					$suppoutput1 .= "REPEAT $row[0]; ";
				}

				$suppcount++;

				if (substr($row[0], -1) =='1'){
					$count=$count + 0.5;
				}else{
					$count=$count + 1;}
					$countb=$countb + 1;
					$courseno=$row[0];

					$upfail[$i] = $courseno;
				}

				if ($row[1] == "WP") {
					$suppoutput3 .= "DEF IN $row[0];";
					$countwp=$countwp + 1;
				}
				if ($row[1] == "DEF") {
					$suppoutput3 = "DEFFERED";
				}
				if ($row[1] == "EX") {
					$suppoutput3 .= "EXEMPTED IN $row[0]; ";
				}
				if ($row[1] == "DISQ") {
					$suppoutput3 = "DISQUALIFIED";
					$overallremark=="DISQUALIFIED";
				}
				if ($row[1] == "SP") {
					$suppoutput3 = "SUSPENDED";
					$overallremark=="SUSPENDED";
				}
				if ($row[1] == "LT") {
					$suppoutput3 = "EXCLUDE";
					$overallremark="EXCLUDE";
				}
				if ($row[1] == "WH") {
					$suppoutput3 = "WITHHELD";
					$overallremark="WITHHELD";
					$count = 0;
				}

				$year=$row[2];
			}

			while ($count2 < 27) {
				$count2 = $count2 + 1;
			}

			$calcount=$count1/($count+$count1)*100;

			if ($year=='1') {
		
				if ($calcount < 50) {
					$remarked = TRUE;
					echo '<td class="title"><h2>EXCLUDE</h2></td>';
					$overallremark="EXCLUDE";
				}else {
					if ($countb == 0) {
						if ($suppoutput3=="") {
							$remarked = TRUE;
							echo '<td colspan="3" colspan="3"  class="title"><h2>CLEAR PASS</h2></td>';
						} else {
							$remarked = TRUE;
							echo $countwp .'<br> '.$suppoutput3.'<br>';
						}
	
						if ($countwp>2){
							$remarked = TRUE;
							echo '2'.$countwp.'<br> '.$suppoutput3.'<br>';
							echo '<td colspan="3"  class="title"><h2>WITHDRAWN WITH PERMISSION</h2></td>';
						} else {
							$remarked = TRUE;
							echo '<td colspan="3"  class="title"><h2>$suppoutput3</h2></td>'; 
						}
	
					}else {
						if ($count1 > 1) {
							$remarked = TRUE; 
							$output .= $suppoutput1;
							echo '<td colspan="3"  class="title"><h2>$output</h2></td>';
						}else {
							$remarked = TRUE;
							$output .= $suppoutput2;
							echo '<td colspan="3"  class="title"><h2>'.$output.'</h2></td>';
						}
					}
				}
	
			} else {
				
				if ($calcount < 75) {
					$remarked = TRUE; 
					echo '<td colspan="3"  class="title"><h2>'.$output.'</h2></td>';
					$overallremark="EXCLUDE";
				} else {
					if ($countb == 0) {
						if ($suppoutput3=="") {
							$remarked = TRUE; 
							echo'<td colspan="3"  class="title"><h2>CLEAR PASS</h2></td>';
						} else { 
							if ($countwp>2){
								$remarked = TRUE; 
								echo '<td colspan="3"  class="title"><h2>WITHDRAWN WITH PERMISSION</h2></td>'; 
							}else{
								$remarked = TRUE; 
								echo '<td colspan="3"  class="title"><h2>'.$suppoutput3.'</h2></td>'; 
							}
						}
					} else {
						if ($count1 > 1) {
							$output .= $suppoutput1;
							$remarked = TRUE; 
							echo '<td colspan="3"  class="title"><h2>'.$output.'</h2></td>';
						} else {
							$output .= $suppoutput2;
							$remarked = TRUE; 
							echo '<td colspan="3"  class="title"><h2>'.$output.'</h2></td>';
						}
					}
				}
			}

		if($remarked == TRUE){
			
		} else {
			echo 'WRONG';
		}

		if($i==0){
			$overallremark = "";
		}


		if(!empty($upfail)){
			$overallremark="FAILED";
		}


		$ocount=$ocount + $count;

		$out = array($overallremark, $repeatlist);
		return $out;
	}	

}
?>
