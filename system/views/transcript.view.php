<?php
class transcript {

	public $core;
	public $view;
	public $item = NULL;


	public function configView() {
		$this->view->header = TRUE;
		$this->view->footer = TRUE;
		$this->view->menu = TRUE;

		if($_GET['print'] == TRUE){
			$this->iew->menu = FALSE;
			$this->view->header = FALSE;
			$this->view->footer = FALSE;
		}

		$this->view->javascript = array();
		$this->view->css = array();

		return $this->view;
	}


	public function buildView($core) {
		$this->core = $core;
		
		if(!empty($this->core->cleanGet['uid'])){
			$this->core->item = $this->core->cleanGet['uid'];
		}

		if ($this->core->action == "results" && $this->core->role > 104) {
			$this->resultsTranscript($this->core->item);
		}

	}

	function academicyear($studentNo) {
		global $remark, $count, $count1;
	

		$sql = "SELECT distinct academicyear, semester FROM `nkrumah-grades` WHERE StudentNo = '$studentNo' order by academicyear";
		$run = $this->core->database->doSelectQuery($sql);


		while ($fetch = $run->fetch_array()){

			$acyr = $fetch[0];
			$semester = $fetch[1];

			$this->detail($studentNo, $acyr, $semester);

		}

	}

	function detail($studentNo, $acyr, $semester) {

		echo "<p> <span style=\"font-size: 14px; font-weight: bold;\"> $acyr &nbsp ($semester) </span></p>
		<table width=\"100%\" border= \"1\" style=\"font-size: 12px\">\n <tr >\n <th >COURSENO</th>\n <th >COURSE NAME</th>\n  <th >GRADE</th>\n </tr>\n\n";

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
		
		while ($fetch = $run->fetch_array()){
			print "<tr>\n";
			print "<td width=10%>$fetch[0]</td>\n";
			print "<td width=80%>$fetch[1]</td>\n";
			print "<td width=10%>$fetch[2]</td>\n";
			print "</tr>\n\n";
		}

		print "</table>\n";
	}

	public function resultsTranscript($item){

		echo'<div id="exhibition" style="left:20px; top:5px; width:auto; z-index:90;">
		<center>
		<font size="5">NKRUMAH UNIVERSITY</font><br>
		<font size="4">ACADEMIC OFFICE </font></center>
		<p align="right">
		<br>
		P O Box 80404<br>
		<b> KABWE<br>
		TEL/FAX. 260 5 223223<br></b></p>
		<hr size=2>
		<p>';


		$studentID = $item;
		$studentNo = $studentID;

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
				`programmes-link` as pr,
				`nkrumah-student-program-link` as npl 
			WHERE  bi.`ID` = '$studentID' 
			AND	npl.`StudentID` = bi.`ID`
			AND	npl.`ProgrammeID` = pr.`ID`";

		$run = $this->core->database->doSelectQuery($sql);

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

			echo"<span style=\"font-size: 12px\"><br> This to certify that: <b>$studentname</b> - Student No.:<b>$studentNo</b><br>
			was a registered student of <b>Nkrumah University College</b><br>
			studying: <b>$programme</b> from the academic session: <b>$start</b>.<br>
			His/her academic performance was as follows:<br></span>";


			$this->academicyear($studentNo);


			echo "<br>";

			switch ($remark) {
				case "DECEASED":
					Print "$title was Deceased<br><br>";
					break;
				case "EXCLUDE":
					Print "$title was excluded from Nkrumah University College<br><br>";
					break;
				case "WP":
					Print "$title withdrew with permission from Nkrumah University College<br><br>";
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

<p>This transcript is not valid if it does not bear the Nkrumah University College<b>Date Stamp</b> or if it has <b>alterations.</b></p>
<h2>&nbsp;</h2>
<h2>&nbsp;</h2>
</div>
</div>';

	}
}
?>