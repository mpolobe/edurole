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

	function resultsStatement($item) {

		if(!isset($item) || $this->core->role <= 10){
			$item = $this->core->userID;
		}

		echo'<div id="print_div1">';

		if($this->core->action != "view-grades"){
			echo'<center><img src="'.$this->core->fullTemplatePath.'/images/nkrumah-small.jpg"><br>
			<font size=5>Nkrumah University </font><br>
			<font size=4>ACADEMIC OFFICE</font><br>
			<font size=3> STATEMENT OF RESULTS</font></center>
			<p><font size=2>';
		}

		$studentID = $item;

		$sql = "SELECT 
				bi.Firstname, 
				bi.MiddleName, 
				bi.Surname, 
				bi.Status,
				bi.Sex,
				bi.Status,
				pr.ProgramNo
			FROM 
				`basic-information` as bi,
				`access` as ac,
				`programmes-link` as pr,
				`nkrumah-student-program-link` as npl 
			WHERE  ac.`ID` = '$studentID' 
			AND 	ac.`ID` = bi.`ID` 
			AND	npl.`StudentID` = ac.`ID`
			AND	npl.`ProgrammeID` = pr.`ID`";

		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_array()){

			$firstname = $fetch[0];
			$middlename = $fetch[1];
			$surname = $fetch[2];
			$remark=$fetch[5];
			$sex=$fetch[4]; 
			$studentname = $firstname . " " . $middlename . " " . $surname;


			$school=$fetch[7];
			$programme=$fetch[6];

			$session=$fetch[9];
			$remark=$fetch[8];
	
			echo "Statement of Results for <b>$studentname</b> - Examination No.:<b>$studentID</b><br> in the Department of <b>$school</b><br> Studying <b>$programme</b> from the academic session: <b>$session</b>.<br>";

			$overallremark= $this->academicyear($studentNo);

			if ($overallremark=="EXCLUDE" or $overallremark=="DISQUALIFIED" or $overallremark=="SUSPENDED" or $overallremark=="WITHHELD"){
				print "<br>OVERALL REMARK: $overallremark";
			} else { 
				print "<br>OVERALL REMARK: PROCEED";
			}
		}

		print "<br><br><br>Digital result overview provided by EduRole";

	}


	private function academicyear($studentNo) {

		print "<br>Courses, Grades and Comment";
		print "<table>\n";

		$sql = "SELECT distinct academicyear, semester FROM `nkrumah-grades` WHERE StudentNo = '$studentNo' order by academicyear";
		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_array()){
			print "<tr>\n";
			$acyr = $fetch[0];
			$semester = $fetch[1];
			$count = 0;
			$count1 = 0;
			$overallremark= $this->detail($studentNo, $acyr, $semester, $remark, $dbPass);
			print "</tr>\n\n";
		}

		print "</table>\n";

		return $overallremark;
	}

	private function detail($studentNo, $acyr, $semester, $remark) {

		print "<td>";
		print "$acyr";
		print "&nbsp";
		print "($semester)</td>";
		print "<td>&nbsp&nbsp</td>";

		$sql = "SELECT p1.courseNo, p1.grade, p1.semester FROM grades as p1, courses as p2 WHERE p1.StudentNo = '$studentNo' and p1.academicyear = '$acyr' and p1.courseNo=p2.courseNo and semester='$semester' ORDER BY p1.courseNo";

		$sql = "SELECT 
				p1.CourseNo,
				p2.CourseDescription,
				p1.Grade
			FROM 
				`nkrumah-grades` as p1,
				`courses` as p2
			WHERE 	p1.StudentNo = '$studentNo'
			AND	p1.AcademicYear = '$acyr'
			AND	p1.CourseNo = p2.Name  
			AND	p1.Semester = '$semester' 
			ORDER BY p1.courseNo";

		$run = $this->core->database->doSelectQuery($sql);

		$output = "";
		$count2 = 0;
		$countwp=0;
		$suppoutput1="";
		$suppoutput2="";
		$suppoutput3="";
		$countb = 0;
		$i=0;

		while ($fetch = $run->fetch_array()){
			$i++;
	
			print "<td>$row[0]</td>\n";
			print "<td>$row[1]</td>\n";
			print "<td>&nbsp&nbsp</td>";
			$count2 = $count2 + 3;

			if ($row[1] == "IN" or $row[1] == "D" or $row[1]=="F" or $row[1]=="NE") {

				$output .= "REPEAT $row[0];";
				if (substr($row[0], -1) =='1'){
					$count=$count + 0.5;
				}else{
					$count=$count + 1;
				}

				$courseno=$row[0];
				$countb=$countb + 1;

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

				$suppoutput1 .= "SUPP IN $row[0]; ";
				$suppoutput2 .= "REPEAT $row[0]; ";

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
				print "<td>&nbsp&nbsp</td>";
				$count2 = $count2 + 1;
			}

			$calcount=$count1/($count+$count1)*100;
			
			if ($year=='YEAR I') {
		
				if ($calcount < 50) {
					print "<td>EXCLUDE</td>";
					$overallremark="EXCLUDE";
				}else {
					if ($countb == 0) {
						if ($suppoutput3=="") {
							print "<td>CLEAR PASS</td>";
						} else { 
							print "$countwp<br> $suppoutput3<br>";
						}
	
						if ($countwp>2){
							print "2$countwp<br> $suppoutput3<br>";
							print "<td>WITHDRAWN WITH PERMISSION</td>";
						} else {
							print "<td>$suppoutput3</td>"; 
						}
	
					}else {
						if ($count1 > 1) {
							$output .= $suppoutput1;
							print "<td>$output</td>";
						}else {
							$output .= $suppoutput2;
							print "<td>$output</td>";
						}
					}
				}
	
			} else {

				if ($calcount < 75) {
					print "<td>EXCLUDE</td>";
					$overallremark="EXCLUDE";
				} else {
					if ($countb == 0) {
						if ($suppoutput3=="") {
							print "<td>CLEAR PASS</td>";
						} else { 
							if ($countwp>2){
								print "<td>WITHDRAWN WITH PERMISSION</td>"; 
							}else{
								print "<td>$suppoutput3</td>"; 
							}
						}
					} else {
						if ($count1 > 1) {
							$output .= $suppoutput1;
							print "<td>$output</td>";
						} else {
							$output .= $suppoutput2;
							print "<td>$output</td>";
						}
					}
				}
			}

	

		if(!empty($upfail)){
			$overallremark="FAILED";
		}

		$ocount=$ocount + $count;
		return $overallremark;
	}	

}
?>
