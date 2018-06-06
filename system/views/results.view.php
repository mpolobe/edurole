<?php
class results {

	public $core;
	public $view;
	public $item = NULL;

	public function buildView($core) {
		$this->core = $core;
	}

	function academicyear($studentNo) {
		global $remark, $count, $count1;
	
		$sql = "SELECT distinct academicyear, semester from grades WHERE StudentNo = '$studentNo' order by academicyear";
		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_array()){
			print "<tr>\n";
			$acyr = $fetch[0];
			$semester = $fetch[1];

			detail($studentNo, $acyr, $semester);
		}
	}

	function detail($studentNo, $acyr, $semester) {

		echo "<br> $acyr &nbsp ($semester)<br>
		<table width=100% border = \"1\">\n <tr >\n <th >COURSENO</th>\n <th >COURSE NAME</th>\n  <th >GRADE</th>\n </tr>\n\n";

		$sql = "SELECT p1.courseNo, p2.CourseDescription, p1.grade FROM grades as p1, courses as p2 WHERE p1.StudentNo = '$studentNo' and p1.academicyear = '$acyr' and p1.courseNo=p2.courseNo and semester='$semester' ORDER BY p1.courseNo";

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

		echo'<div id="exhibition" style="position:absolute; left:20px; top:5px; width:auto; height:38px; z-index:90;">
		<center>
		<font size=5>'.$this->core->conf['conf']['organization'].'</font><br>
		<font size=4>ACADEMIC OFFICE </font></center>
		<p align="right">
		<br>
		P O Box 80404<br>
		<b> KABWE<br>
		TEL/FAX. 260 5 223223<br></b></p>
		<hr size=2>
		<p>';


		$studentID = $item;
		$studentNo = $studentID;

		$sql = "SELECT * FROM `basic-information`, as bi, `access` as ac, `student-study-link` as ss, `study` as st, `student-program-link` as pl 
			WHERE ac.`ID` = '" . $item. "' 
			AND ac.`ID` = bi.`ID` 
			AND ss.`StudyID` = st.`ID` 
			AND pl.`StudentID` = $studentID 
			AND ss.`StudentID` = $studentID";

		$sql = "SELECT p1.studentname, p2.school, p1.repyear, p2.programname, p1.remark, p1.sex, p1.graduation, datediff(p1.graduation,curdate()) FROM students as p1, programmes as p2 WHERE p1.ProgramNo = p2.ProgramNo AND StudentNo = '$studentNo'";
		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_array()){

			$studentname = $fetch[0];
			$school=$fetch[1];
			$programme=$fetch[3];
			$remark=$fetch[4];
			$sex=$fetch[5]; 
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

			echo'<br> This to certify that: <b>$studentname</b> - Student No.:<b>$studentNo</b><br>
			was a registered student of <b>'.$this->core->conf['conf']['organization'].'</b><br>
			studying: <b>$programme</b> from the academic session: <b>$session</b>.<br>
			His/her academic performance was as follows:<br>';


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
				case "Student":
					//print "$title will be awarded a <b>$programme</b> ";
					//print "upon completion of studies<br><br>";
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

echo'</p>
<h2 class="break">Key to Understanding Grades</h2>
            <h3>Pass Grades</h3> 
<TABLE width=100%>
<TR>
  <TD>A+</TD>
  <TD>Distinction</TD>
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
<TABLE width=100%>
<TR>
  <TD>D+</TD>
  <TD>Bare Fail</TD>
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
<TABLE width=100%>
<TR>
  <TD>WP</TD>
  <TD>Withdrawn from course with permission</TD>
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
This transcript is not valid if it does not bear the '.$this->core->conf['conf']['organization'].' <b>Date Stamp</b> or if it has <b>alterations.</b>
<h2>&nbsp;</h2>
<h2>&nbsp;</h2>
</div>
</div>';

	}



}
?>
