<?php
class exams {
	public $core;
	public $view;

	public function configView() {
		$this->view->open = TRUE;
		$this->view->header = TRUE;
		$this->view->footer = TRUE;
		$this->view->menu = FALSE;
		$this->view->javascript = array();
		$this->view->css = array('login');
		
		return $this->view;
	}

	public function buildView($core) {
		$this->core = $core;
	}

	public function viewMenu(){
		echo'<div class="toolbar"><a href="' . $this->core->conf['conf']['path'] . '/exams/add">Create new exam</a></div>';
	}

	public function openExams() {
		$sql = "SELECT * FROM `exams` WHERE CURRENT_TIMESTAMP <= `exams`.ExamEnd ORDER BY `exams`.ExamName";

		echo '<div class="table-responsive">
		<table class="table table-bordered table-striped" cellspacing="0" cellpadding="5" >
		<thead><tr><th> <b>Exam name</b></th><th><b>Exam description</b></th><th><b>Exam date</b></th></thead><tbody>';

		$run = $this->core->database->doSelectQuery($sql);

		while ($row = $run->fetch_row()) {
			echo '<tr><th><b><a href="' . $this->core->conf['conf']['path'] . '/exams/start/' . $row[0] . '"> 
				<span class="glyphicon glyphicon-play" aria-hidden="true"></span> ' . $row[1] . '</a></b></th>' .
				'<td>' . $row[3] . '</td>' .
				'<td>' . $row[4] . '</td></tr>';
		}

		echo '</tbody></table>
		</div>';
	}


	public function showExams($item) {
		if($this->core->role > 100){
			$this->core->redirect("exams", "manage", NULL);
		}else{
			$this->core->redirect("exams", "open", NULL);
		}
	}

	public function submitExams($item) {
		$cid = $_SESSION["cid"];
		$sql = 'SELECT COUNT(ID), SUM(Points), CID, Attempt FROM `answered` WHERE CID = "'.$cid.'" GROUP BY Attempt';

		$run = $this->core->database->doSelectQuery($sql);

		while ($row = $run->fetch_row()) {
			$total = $row[0];
			$points = $row[1];
			$attempt = $row[3];
		}
	
		$passrate = $total/2;
		if($points >= $passrate){
			$color = 'alert-success';
			$message = 'Congatulations, you have passed your exam!<br>';
		} else {
			$color = 'alert-warning';
			$message = 'Unfortunatly you did not pass your exam<br>';
		}

		echo'<div class="alert '.$color.'" role="alert" style="font-size: 12pt;">
		Your exam has been submitted<br><br> <b>your score was: '.$points.' out of '.$total.' points. <br>This was exam attempt number '.$attempt.'<br><br> '.$message.'</b>
		</div>';
	}

	public function deleteExams($item) {
		$sql = "DELETE FROM `exams` WHERE `ID` = '$item'";
		$this->core->database->doInsertQuery($sql);

		$this->manageExams();
		$this->core->showAlert("The exam has been deleted");
	}

	public function editExams($item) {
                $sql = "SELECT * FROM `exams` WHERE ID = $item";
                $run = $this->core->database->doSelectQuery($sql);

                while ($row = $run->fetch_row()) {
                        $name = $row[1];
                        $description = $row[3];
                        $start = $row[4];
                        $end = $row[5];
                        $duration = $row[6];
                        include $this->core->conf['conf']['formPath'] . "addexam.form.php";
                }
        }

	public function startExams($item) {
		include $this->core->conf['conf']['formPath'] . "startexam.form.php";
	}

	public function confirmExams($item) {
		$number = $this->core->cleanPost['number'];
		$day = $this->core->cleanPost['day'];
		$month = $this->core->cleanPost['month'];
		$year = $this->core->cleanPost['year'];

		$dob = "$year-$month-$day";
		$attempt = 0;

		$sql = "SELECT * FROM `basic-information` WHERE `ID` = $number AND `DateOfBirth` = '$dob'";
		$run = $this->core->database->doSelectQuery($sql);

		if($run->num_rows > 0) {
			while ($row = $run->fetch_row()) {
				$fname = $row[0];
				$lname = $row[2];

				$sql = "SELECT `ExamDuration` FROM `exams` WHERE `exams`.`ID` = $item";
				$run = $this->core->database->doSelectQuery($sql);

				while ($fetch = $run->fetch_row()) {
					$duration = $fetch[0]; 
				}

				$sql = "INSERT INTO `starttimes` (`ID`, `CID`, `Starttime`, `Attempt`) VALUES (NULL, '$row[4]', NOW(), '1');";
				$runx = $this->core->database->doInsertQuery($sql);

				//$_SESSION['cont'] = 1;
				//$_SESSION['mcc'] = FALSE;
				//$_SESSION['mqc'] = FALSE;
				//$_SESSION['bqc'] = FALSE;
				$_SESSION["cid"] = $row[4];
				
				
				$sql = 'SELECT Attempt FROM `answered` WHERE CID = "'.$row[4].'" LIMIT 1';
				$run = $this->core->database->doSelectQuery($sql);
				while ($row = $run->fetch_row()) {
					$attempt = $row[0];
				}
				$attempt = $attempt+1;

				$_SESSION['attempt'] = $attempt;

				echo'<div style="clear:both; margin-top: 30px"><h1>Good luck '. $fname .' '. $lname .'</h1><h2>You have exactly '.$duration.' minutes for your exam. <br><br> This is attempt number '.$attempt.' out of 3 total possible attempts.</h2><p></p>
				<a href="' . $this->core->conf['conf']['path'] . '/exams/take/'.$item.'"><button type="submit" class="btn btn-lg btn-info" aria-label="Left Align">
				<span class="glyphicon glyphicon-play" aria-hidden="true"> </span>
				Start your exam
				</button></a>';

				
			}

		} else {
			echo'<div class="errorpopup">The information you have entered is incorrect</div>';
			$this->startExams($item);
		}
	}


	public function takeExams($item) {
		$bcount = 0;

		if(isset($_SESSION['cont'])){
			$cont = $_SESSION['cont'];
		} else {
			$cont = 1;
		}

 		echo '<div class="questions"><form method="post"  action="'.$this->core->conf['conf']['path'].'/exams/take/'.$item.'">';

		if($cont != "done"){
			$timed = $this->examTimer($item);
		}

		echo $this->submitQuestions($item);
		
		if($_SESSION['mcc'] == FALSE){
			$echocount = $this->multiChoice($item, $cont);
			$cont = $echocount + $cont;
		}

   		if($echocount == 0 && $_SESSION['mqc'] == FALSE && $cont != "done"){
			$_SESSION['mcc'] = TRUE;
			$mcount = $this->matchingQuestions($item, $cont);

			$ncount = $mcount[0];
			echo'<input type="hidden" name="mq" value="'.$ncount.'">';
			$cont = $mcount[1];
		}
		
		if($ncount==0 && $_SESSION['mcc'] == TRUE && $cont != "done"){
			$_SESSION['mcc'] = TRUE;
			$bcount = $this->blankQuestions($item, $cont);
			$cont = $bcount + $cont;
			$cont = "done";
		}

		$_SESSION['cont'] = $cont;
		
		if($cont == "done" && $bcount == 0){
			$this->submitExams();
		} else {

			echo'<input type="hidden" name="cont" value="'.$cont.'">';
			echo '<div style="clear:both; margin-top: 30px">
				<button type="submit" class="btn btn-default" aria-label="Left Align">
                                <span class="glyphicon glyphicon-play" aria-hidden="true"> </span>
                                Continue with exam
				</button>
			</form></div></div>';
		}
        }

	private function submitQuestions($item){
		$cid = $_SESSION['cid'];
		$attempt = $_SESSION['attempt'];
		$answers = $_POST['answers'];
		$points = 0;

		foreach($answers as $questionid=>$answer){
			if(is_array($answer)){
				foreach($answer as $answerid=>$answer){
					$sql = "SELECT * FROM `questions`, `answers` WHERE `answers`.ID = '$answerid' AND `questions`.ID = `answers`.QuestionID";
					$run = $this->core->database->doSelectQuery($sql);

					while ($row = $run->fetch_row()) {
						$qtype = $row[2];
						$qcorrect = $row[8];
						if($qtype == 3){
							$correctanswer = $row[7];
						} else {
							$correctanswer = $qcorrect;
							$answer = strtoupper($answer);
						}
					}

					if($answer == "0"){
						$points = 0;
					}else if($answer == $correctanswer){
						$points = 1;
					} else {
						$points = 0;
					}

					$sql = "INSERT INTO `answered` (`ID`, `EID`, `CID`, `QID`, `Answer`, `Points`, `Attempt`) 
					VALUES (NULL, '$item', '$cid', '$questionid', '$answer', '$points', '$attempt')";
					$this->core->database->doInsertQuery($sql);
				}
			} else {
				$sql = "SELECT * FROM `questions`, `answers` WHERE `answers`.ID = '$answer' AND `questions`.ID = `answers`.QuestionID";
				$run = $this->core->database->doSelectQuery($sql);

				while ($row = $run->fetch_row()) {
					$aid = $row[5];
					$qcorrect = $row[8];
					if($qcorrect == 1){
						$points = 1;
					} else {
						$points = 0;
					}
				}
	
				$sql = "INSERT INTO `answered` (`ID`, `EID`, `CID`, `QID`, `Answer`, `Points`, `Attempt`)  
					VALUES (NULL, '$item', '$cid', '$questionid', '$answer', '$points', '$attempt')";
				$this->core->database->doInsertQuery($sql);
	
			}
		}

		
	}


	private function examTimer($item){
		$cid = $_SESSION['cid'];
		$sql = "SELECT `ExamDuration`, `Starttime` FROM `exams`, `starttimes` WHERE `exams`.`ID` = $item AND `starttimes`.CID = '$cid'";
		$run = $this->core->database->doSelectQuery($sql);

		while ($row = $run->fetch_row()) {
			$duration = $row[0]-60;

			$dt = strtotime($row[1]);
			$time= date('H:i:s', $dt);
			$timed= date('H:i:s');
			$td = strtotime($time)-strtotime($timed);
			$duration = date('H:i:s', strtotime("+$duration minutes", $td));
			$date = date('Y/m/d');
		} 

		$duration = $duration;

		echo' <div style="left: 1002px; top: 30px; background-color: #FFF; position:fixed; z-index:999; border: solid 1px #ccc; width: 180px; padding: 20px;"> TIME REMAINING: <div id="countdown" style="font-size: 25pt;">'.$duration.'</div></div>';


			echo'<script>
				history.pushState({ page: 1 }, "title 1", "#nbb");
				window.onhashchange = function (event) {
				window.location.hash = "nbb";

				$(document.body).keydown(function (e) {
					var elm = e.target.nodeName.toLowerCase();
					if (e.which == 8 && elm !== \'input\' && elm  !== \'textarea\') {
						e.preventDefault();
					}
					e.stopPropagation();
				});

			};
			</script> ';

	}

	private function blankQuestions($item, $cont){

                $sql = "SELECT * FROM `questions`, `answers` WHERE `questions`.ExamID = '$item' AND `questions`.QuestionType = 3 AND `questions`.ID = `answers`.QuestionID";
                $run = $this->core->database->doSelectQuery($sql);
                echo '<div class=" panel panel-default" style="padding-bottom: 30px;">
                        <div class="panel-heading">
                                <h3 class="panel-title">Fill-in-the-blank Questions</h3>
                        </div>
                        <div class="panel-body">';

		   $count = $cont;

                while ($row = $run->fetch_row()) {
			$aid = $row[5];

                        if($start || $row[3] != $question){
				    $answer = $row[7];
				    $qid = $row[0];
                                $question = $row[3];
                                $input = '<input type="text" name="answers['.$qid .']['.$aid.']" value="" style="width:200px;">';
                                $question = str_replace("#", $input, $question);
                                $start = FALSE;
                                $count++;
                                echo '</p><p><div class="question" style="margin-bottom: 20px;"><b>'.$count.'</b> - '. $question . '</div>';
                        }

                }
                echo'</div></div>';

		return $count;
	}

	private function matchingQuestions($item, $cont){

		if(isset($_POST['mq'])){
			$mq = $_POST['mq'];
		}

		
		$sql = "SELECT * FROM `questions`, `answers` WHERE `questions`.ExamID = '$item' 
		AND `questions`.QuestionType = 2 AND `questions`.ID = `answers`.QuestionID ORDER BY `answers`.ID";


		$run = $this->core->database->doSelectQuery($sql);

		$question = TRUE;
		$start = TRUE;
		$close = TRUE;
  		$letter = 'A';

		if($cont == 1){ $cd = 6; } else { $cd = $cont+6; }

		$echocount = 0;
		$question = "";
		$count = 1;

		$countd = $cont;
		$cont = 1;
		

              while ($row = $run->fetch_row()) {
			$q = $row[3];
			$qid = $row[0];
			$aid = $row[5];
			$answer = $row[7];
			$cde = $row[8];
			
			if($qid != $question){
				if(isset($mq)){
					if($qid <= $mq){
						continue;
					}
				}

				if($start == FALSE) {
					continue;
				}
			
				if(isset($cont) && $count > $cont && $count < $cd || $cont == 1 && $count == 1){
					
					if($start){ 
						echo '<div class=" panel panel-default" style="padding-bottom: 30px; height: 400px;">
						<div class="panel-heading">
						<h3 class="panel-title">Matching Questions</h3>
						</div>
						<div class="panel-body">';
					}


				  
				   $questiond = "";
				   $echocount++;
			
				} 
				


				$questiond = '</p><p><div class="question" style="margin-bottom: 20px; padding-top: 20px;  height: 30px;">'. $row[3] . '</div>
						<div style="width: 300px; height: 300px;  float: left; background color: #ccc;">';

				$eid = $qid;

				echo $questiond;
				$question = $qid;
				$start = FALSE;
				$letter = 'A';
			}

			if($cde == 1){
				

					if($close == "TRUE"){
					echo '</div>
					<div style="width: 600px; height: 300px;  float: left; background color: #ccc;">';
					$close = FALSE;
					}

                                echo '<div style=" width: 600px;">
                                <div class="answer"><b>'.$letter .' - ' . $row[7] . '</b></div>
                                </div>';
                                $letter++;
                              
			} else {
				$countd++;
				echo '<div style=" width: 300px;">
				<b>'.$countd.'</b> - <input type="text" name="answers['.$qid .']['.$aid .']" value="" size="1" style="width:45px;">
				<span class="answer">' . $answer . '</span></div>';
				
			}
		}

		if($letter!="A"){
			echo'</div></div></div>';
		}

		$ret = array($eid, $countd);

		return $ret;
	}


	private function multiChoice($item, $cont){

		$sql = "SELECT * FROM `questions`, `answers` WHERE `questions`.ExamID = '$item' AND `questions`.QuestionType = 1 AND `questions`.ID = `answers`.QuestionID";

		$run = $this->core->database->doSelectQuery($sql);

		$question = TRUE;
		$start = TRUE;
		$close = TRUE;
  		$letter = 'A';

		if($cont == 1){ $cd = 6; $first = TRUE; } else { $cd = $cont+6; $first = FALSE; }

		$echocount = 0;
		$question = "";

              while ($row = $run->fetch_row()) {
			$qid = $row[0];
			$aid = $row[5];

			if($row[3] != $question){
				if(isset($cont) && $count > $cont && $count < $cd || $cont == 1 && $count == 1){
					if($start){ 
						echo '<div class=" panel panel-default" style="padding-bottom: 30px;">
                        			<div class="panel-heading">
                                		<h3 class="panel-title">Multiple Choice Questions</h3>
                       			</div>
                       			<div class="panel-body">';
						$start = FALSE;
					}

				   echo $questiond;
				   $questiond = "";
				   $echocount++;
			
				} 
				$count++;
				
		              $questiond = '</p><p><div class="question"><b>'.$count.'</b> - '. $row[3] . '</div>
							<input type="hidden" name="answers['.$qid.']" value="0">';
                            $question = $row[3];
                            
                            $letter = 'A';
				
                        }

                        $questiond = $questiond . '<div class="radiod"><label style="width: 100%;">
                                <input type="radio" name="answers['.$qid.']" value="'.$aid .'" style="width:30px;">
                                <span class="answer">'.$letter .' - ' . $row[7] . '</span>
                                </label></div>';

			$tc = $count-$cont;
			$letter++;
                }

		if($echocount!=0){
                echo'</div></div>';
		}
		if( $first == TRUE ){
			$echocount--;
		}
		return $echocount;

	}


	public function manageExams($item) {
		
		if($item == null){

			$this->viewMenu();

			$sql = "SELECT * FROM `exams` ORDER BY `exams`.ExamName";

			echo '<div class="table-responsive">
			<table class="table table-bordered table-striped" cellspacing="0" cellpadding="5" >
			<thead><tr><th> <b>Exam name</b></th><th><b>Exam description</b></th><th><b>Exam date</b></th><th><b>Manage</b></th></thead><tbody>';

			$run = $this->core->database->doSelectQuery($sql);

			while ($row = $run->fetch_row()) {
				$date = explode(" ",$row[4]);
				echo '<tr><th><b><a href="' . $this->core->conf['conf']['path'] . '/questions/manage/' . $row[0] . '"> 
					<span class="glyphicon glyphicon-play" aria-hidden="true"></span> ' . $row[1] . '</a></b></th>' .
					'<td>' . $row[3] . '</td>' .
					'<td>' . $date[0] . '</td>
					<td><a href="' . $this->core->conf['conf']['path'] . '/exams/edit/'.$row[0].'"><img src="/gnc/templates/exam/images/edi.png"></a> | 
				  	<a href="' . $this->core->conf['conf']['path'] . '/exams/delete/'.$row[0].'?e='.$item.'"><img src="/gnc/templates/exam/images/delete.gif"></a></td>
					</tr>';
			}
		
			echo '</tbody></table>
			</div>';
		}
	}

	public function addExams() {
		include $this->core->conf['conf']['formPath'] . "addexam.form.php";
	}

        public function saveExams() {

                $item = $this->core->item;


                if(isset($this->core->cleanPost['update'])){
                        $update = $this->core->cleanPost['update'];
                }

                $name = $this->core->cleanPost['name'];
                $start = $this->core->cleanPost['start'];
                $end = $this->core->cleanPost['end'];
                $duration = $this->core->cleanPost['duration'];
                $description = $this->core->cleanPost['description'];
                $owner = $this->core->userID;

                if(isset($update)){
                        $sql = "UPDATE `exams` SET `ExamName` = '$name', `ExamLength` = '100', `ExamDescription` = '$description', `ExamStart` = '$start', `ExamEnd` = '$end', `ExamDuration` = '$duration' WHERE `exams`.`ID` = $item";
                }else{
                        $sql = "INSERT INTO `exams` (`ID`, `ExamName`, `ExamLength`, `ExamDescription`, `ExamStart`, `ExamEnd`, `ExamDuration`, `ExamOwner`)
                        VALUES (NULL, '$name', '100', '$description', '$start', '$end', '$duration', '$owner');";
                }

                $run = $this->core->database->doInsertQuery($sql);
                $this->core->redirect("exams", "manage", NULL);
        }
}
?>
