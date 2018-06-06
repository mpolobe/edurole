<?php
class transcript {

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


	public function footerTranscript($item){


		echo'<h2 class="break">Key to Understanding Grades</h2>
		<h3>Pass Grades</h3> 
		<TABLE>
		<TR>
		  <TD width="100">A+</TD>
		  <TD width="800">Distinction</TD>
		 </TR>
		<TR>
		  <TD>A</TD>
		  <TD>Distinction</TD>
		 </TR>
		 <TR>
		  <TD>B+</TD>
		  <TD>Meritorious</TD>
		 </TR>
		 <TR>
		  <TD>B</TD>
		  <TD>Very Satisfactory</TD>
		 </TR>
		 <TR>
		  <TD>C+</TD>
		  <TD>Clear Pass</TD>
		 </TR>
		 <TR>
		  <TD>C</TD>
		  <TD>Bare Pass</TD>
 </TR>
 <TR>
  <TD>S</TD>
  <TD>Satisfactory, Pass in a Practical Course or Oral Examinations</TD>
 </TR>
 <TR>
  <TD>P</TD>
  <TD>Pass in a Supplementary Examination</TD>
 </TR>
</TABLE>

<h3>Fail Grades</h3> 
<TABLE>
<TR>
  <TD width="100">D+</TD>
  <TD width="800">Bare Fail</TD>
 </TR>
<TR>
  <TD>D</TD>
  <TD>Definite Fail</TD>
 </TR>
 <TR>
  <TD>F</TD>
  <TD>Fail in a Supplementary Examination</TD>
 </TR>
 <TR>
  <TD>U</TD>
  <TD>Unsatisfactory, Fail in a practical Course/  Thesis or Oral Examinations</TD>
 </TR>
 <TR>
  <TD>NE</TD>
  <TD>No Examination Taken</TD>
 </TR>
 <TR>
  <TD>RS</TD>
  <TD>Re-sit course examination only</TD>
 </TR>
</TABLE>

<h3> Other Grades</h3> 
<TABLE>
<TR>
  <TD width="100">WP</TD>
  <TD width="800">Withdrawn from course with permission</TD>
 </TR>
<TR>
  <TD>DC</TD>
  <TD>Deceased during course</TD>
 </TR>
 <TR>
  <TD>EX</TD>
  <TD>Exempted</TD>
 </TR>
 <TR>
  <TD>IN</TD>
  <TD>Incomplete</TD>
 </TR>
 <TR>
  <TD>DEF</TD>
  <TD>Deferred Examination</TD>
 </TR>
 <TR>
  <TD>SP</TD>
  <TD>Supplementary Examination</TD>
 </TR>
  <TR>
  <TD>DISQ</TD>
  <TD>Disqualified</TD>
 </TR>
 </TABLE>

<p>This transcript is not valid if it does not bear the '.$this->core->conf['conf']['organization'].' <b>date Stamp</b> or if it has <b>alterations.</b></p>
<h2>&nbsp;</h2>
<h2>&nbsp;</h2>
</div>
</div>';

		echo'<script type="text/javascript">
			window.print();
		</script>';

	}




	
	public function resultsTranscript($item){

		if(!isset($item) || $this->core->role <= 10){
			$item = $this->core->userID;
		}



		$studentID = $_GET['uid'];
		$studentNo = $studentID;

		$start = substr($studentID, 0, 4);

		$sql = "SELECT Firstname, MiddleName, Surname, Status, Sex, Status, ProgramNo FROM `basic-information`, `programmes-link`, `student-program-link` 
		WHERE `basic-information`.ID = '$studentID' AND	 `student-program-link`.`StudentID` = `basic-information`.ID AND `student-program-link`.`ProgrammeID` = `programmes-link`.ID";

		$run = $this->core->database->doSelectQuery($sql);


			echo'	<center><div style="width: 200px;"><a href="'. $this->core->conf['conf']['path'] .'"><img height="100px" src="'. $this->core->fullTemplatePath .'/images/header.png" /></a></div>
			<div style=" font-size: 22pt; color: #333; margin-top: 15px; margin-left: -30px; ">'.$this->core->conf['conf']['organization'].'<div style="font-size: 13pt">TRANSCRIPT OF RESULTS</div></div>
			</center>

			<div align="right">
			<br>
			P O Box 80404<br>
			<b> KABWE<br>
			TEL/FAX. 260 5 223223<br></b></p>
			<hr size=2>
			</div>';

			echo"<span style=\"font-size: 12px\"><br> This to certify that: <b>$studentname</b> - Student No.: <b>$studentNo</b><br>
			was a registered student of <b> '.$this->core->conf['conf']['organization'].'</b><br>
			studying: <b>$programme</b> from the academic session: <b>$start</b>.<br>
			His/her academic performance was as follows:<br></span>";



		while ($fetch = $run->fetch_row()){

			$firstname = $fetch[0];
			$middlename = $fetch[1];
			$surname = $fetch[2];
			$remark=$fetch[5];
			$sex=$fetch[4]; 
			$studentname = $firstname . " " . $middlename . " " . $surname;

			$school=$fetch[7];
			$programme=$fetch[6];

			$graduation=$fetch[6]; 
			$grad=$fetch[7];

			switch ($sex) {
				case "M":
					$title="He";
					break;
				case "F":
					$title="She";
					break;
				default:
					$title="He/She";
			}

			$this->academicyear($studentNo);


			echo "<br>";

			switch ($remark) {
				case "DECEASED":
					Print "$title was Deceased<br><br>";
					break;
				case "EXCLUDE":
					Print "$title was Excluded<br><br>";
					break;
				case "WP":
					Print "$title withdrew with permission<br><br>";
					break;
				case "Enrolled":
					print "$title will be awarded the specified degree upon completion of studies<br><br>";
					break;
				default:
					if ($grad>0){
						$graduation1=date('d M Y',strtotime($graduation));
						echo "$title will be awarded a <b>$programme</b> 
						degree with <b>$remark</b> at the graduation ceremony to be held on $graduation1<br><br>";
					} else {
						$graduation1=date('d M Y',strtotime($graduation));
						echo "$title was awarded a <b>$programme</b>
						degree with <b>$remark</b> at the graduation ceremony held on $graduation1<br><br>";
					}
			}

			echo "<br><br><br> <b>". $this->core->conf[institution][head] ."<br>". $this->core->conf[institution][title] ."</b><br> <br> <b>A key to understanding of the grades is on the reverse side of this statement</b><br><br>";

		}

		echo'</p>';


	}

	private function academicyear($studentNo) {
		global $remark, $count, $count1;
	

		$sql = "SELECT distinct academicyear, semester FROM `grades` WHERE StudentNo = '$studentNo' order by academicyear";
		$run = $this->core->database->doSelectQuery($sql);


		while ($fetch = $run->fetch_array()){

			$acyr = $fetch[0];
			$semester = $fetch[1];

			

			$this->detail($studentNo, $acyr, $semester);

		}

	}

	private function detail($studentNo, $acyr, $semester) {

		echo "<p> <span style=\"font-size: 14px; font-weight: bold;\"> $acyr &nbsp ($semester) </span></p>
		<table width=\"100%\"  style=\"font-size: 12px; text-align: left;\">\n <tr >\n <th >COURSE</th>\n <th >COURSE NAME</th>\n  <th >GRADE</th>\n </tr>\n\n";

		$sql = "SELECT 
				p1.CourseNo,
				p2.CourseDescription,
				p1.Grade
			FROM 
				`grades` as p1,
				`courses` as p2
			WHERE 	p1.StudentNo = '$studentNo'
			AND	p1.AcademicYear = '$acyr'
			AND	p1.CourseNo = p2.Name  
			AND	p1.Semester = '$semester' 
			ORDER BY p1.courseNo";

		$run = $this->core->database->doSelectQuery($sql);
		
		while ($fetch = $run->fetch_array()){
			print "<tr>\n";
			print "<td width=10%>$fetch[0]</td>\n";
			print "<td width=80%>$fetch[1]</td>\n";
			print "<td width=10%>$fetch[2]</td>\n";
			print "</tr>\n\n";
		}

		print "</table>\n";
	}

}
?>
