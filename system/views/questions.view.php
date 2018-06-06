<?php
class questions {

	public $core;
	public $view;

	public function configView() {
		$this->view->open = TRUE;
		$this->view->header = TRUE;
		$this->view->footer = TRUE;
		$this->view->menu = TRUE;
		$this->view->javascript = array();
		$this->view->css = array('login');
		
		return $this->view;
	}

	public function buildView($core) {
		$this->core = $core;
	}

	public function viewMenu($item){
		echo'<div class="toolbar"><a href="' . $this->core->conf['conf']['path'] . '/questions/add/'.$item.'">Add new question</a></div>';
	}

	public function manageQuestions($item) {
		$sql = "SELECT * FROM `questions` WHERE `questions`.ExamID = '$item'";
		$run = $this->core->database->doSelectQuery($sql);
		$count = 1;

		$this->viewMenu($item);

		echo '<div class="table-responsive">
		<table class="table table-bordered table-striped" cellspacing="0" cellpadding="5" >
		<thead><tr><th><b>#</b></th><th><b>Question</b></th><th><b>Question type</b></th><th><b>Manage</b></th></thead><tbody>';

		while ($row = $run->fetch_row()) {
			$question = $row[3];
			$input = '<b>...........</b>';
			$question = str_replace("#", $input, $question);

			$type = $row[2];
			if($type == 1) { 		$type = "Multiple Choice";		
			}else if($type == 2) {	$type = "Match Question";		
			}else if($type == 3) {	$type = "Complete Sentence"; }

			echo '<tr>
				<td>' . $count++ . '</td>
				<th> '. $question . '</th>			
				<td>' . $type . '</td>
				<td>
				    <a href="' . $this->core->conf['conf']['path'] . '/questions/delete/'.$row[0].'?e='.$item.'">Delete</a></td>
				</tr>';
		}
		
		echo '</tbody></table>
		</div>';
	}

	public function addQuestions($item) {
		if(isset($this->core->cleanGet['type'])){
			$item = $this->core->cleanGet['type'];
		} else {
			$item = 0;
		}

		if($item == 1){
			include $this->core->conf['conf']['formPath'] . "addmultichoicequestion.form.php";
		}else if($item == 2){
			include $this->core->conf['conf']['formPath'] . "addmatchquestion.form.php";
		}else if($item == 3){
			include $this->core->conf['conf']['formPath'] . "addcompletequestion.form.php";
		} else {

			echo'<form id="questiontype" method="GET" action="">

			<div style="float: left; font-size: 10pt; font-weight: bold; margin-right: 30px; padding: 3px;">Question type: </div>
			<select name="type" class="submit" style="width: 250px" onchange="this.form.submit()">
				 <option value="" selected> </option>
				 <option value="1">Multiple Choice</option>
				 <option value="2">Match Question</option>
				 <option value="3">Complete the Sentence</option>
			</select>

			</form>';

		}
	}

	public function editQuestions($item) {

	}


	public function deleteQuestions($item) {
		
		$sql = "DELETE FROM `questions` WHERE `questions`.ID = $item";
		$exam = $this->core->cleanGet['e'];

		$this->core->database->doInsertQuery($sql);

		$this->core->redirect("questions", "manage", $exam);

	}

	public function saveQuestions($item) {
		$question = $this->core->cleanPost['q'];
		$questionType = $this->core->cleanPost['qt'];

		$sql = "INSERT INTO `questions` (`ID`, `ExamID`, `QuestionType`, `Question`, `QuestionPriority`) 
		 	 VALUES (NULL, '$item', '$questionType', '$question', '1');";

		$this->core->database->doInsertQuery($sql);

		$sql = "SELECT LAST_INSERT_ID()";
		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_row()) {
			$questionid = $fetch[0];
		}



		if($this->core->cleanPost['qt'] == 1){
			if(!empty($this->core->cleanPost['a'])){
				$sql = "INSERT INTO `answers` (`ID`, `QuestionID`, `Answer`, `AnswerCorrect`) VALUES (NULL, '$questionid', '".$this->core->cleanPost['a']."', '0')";
				$this->core->database->doInsertQuery($sql);
			}
			if(!empty($this->core->cleanPost['b'])){
				$sql = "INSERT INTO `answers` (`ID`, `QuestionID`, `Answer`, `AnswerCorrect`) VALUES (NULL, '$questionid', '".$this->core->cleanPost['b']."', '0')";
				$this->core->database->doInsertQuery($sql);
			}
			if(!empty($this->core->cleanPost['c'])){
				$sql = "INSERT INTO `answers` (`ID`, `QuestionID`, `Answer`, `AnswerCorrect`) VALUES (NULL, '$questionid', '".$this->core->cleanPost['c']."', '0')";
				$this->core->database->doInsertQuery($sql);
			}
			if(!empty($this->core->cleanPost['d'])){
				$sql = "INSERT INTO `answers` (`ID`, `QuestionID`, `Answer`, `AnswerCorrect`) VALUES (NULL, '$questionid', '".$this->core->cleanPost['d']."', '0')";
				$this->core->database->doInsertQuery($sql);
			}
			if(!empty($this->core->cleanPost['e'])){
				$sql = "INSERT INTO `answers` (`ID`, `QuestionID`, `Answer`, `AnswerCorrect`) VALUES (NULL, '$questionid', '".$this->core->cleanPost['e']."', '0')";
				$this->core->database->doInsertQuery($sql);
			}
			if(!empty($this->core->cleanPost['f'])){
				$sql = "INSERT INTO `answers` (`ID`, `QuestionID`, `Answer`, `AnswerCorrect`) VALUES (NULL, '$questionid', '".$this->core->cleanPost['f']."', '0')";
				$this->core->database->doInsertQuery($sql);
			}
			if(!empty($this->core->cleanPost['g'])){
				$sql = "INSERT INTO `answers` (`ID`, `QuestionID`, `Answer`, `AnswerCorrect`) VALUES (NULL, '$questionid', '".$this->core->cleanPost['g']."', '0')";
				$this->core->database->doInsertQuery($sql);
			}
		}else if($this->core->cleanPost['qt'] == 2){

			if(!empty($this->core->cleanPost['1'])){
				$sql = "INSERT INTO `answers` (`ID`, `QuestionID`, `Answer`, `AnswerCorrect`) VALUES (NULL, '$questionid', '".$this->core->cleanPost['1']."', '".$this->core->cleanPost['1c']."')";
				$this->core->database->doInsertQuery($sql);
			}
			if(!empty($this->core->cleanPost['2'])){
				$sql = "INSERT INTO `answers` (`ID`, `QuestionID`, `Answer`, `AnswerCorrect`) VALUES (NULL, '$questionid', '".$this->core->cleanPost['2']."', '".$this->core->cleanPost['2c']."')";
				$this->core->database->doInsertQuery($sql);
			}
			if(!empty($this->core->cleanPost['3'])){
				$sql = "INSERT INTO `answers` (`ID`, `QuestionID`, `Answer`, `AnswerCorrect`) VALUES (NULL, '$questionid', '".$this->core->cleanPost['3']."', '".$this->core->cleanPost['3c']."')";
				$this->core->database->doInsertQuery($sql);
			}
			if(!empty($this->core->cleanPost['4'])){
				$sql = "INSERT INTO `answers` (`ID`, `QuestionID`, `Answer`, `AnswerCorrect`) VALUES (NULL, '$questionid', '".$this->core->cleanPost['4']."', '".$this->core->cleanPost['4c']."')";				$this->core->database->doInsertQuery($sql);
				$this->core->database->doInsertQuery($sql);
			}
			if(!empty($this->core->cleanPost['5'])){
				$sql = "INSERT INTO `answers` (`ID`, `QuestionID`, `Answer`, `AnswerCorrect`) VALUES (NULL, '$questionid', '".$this->core->cleanPost['5']."', '".$this->core->cleanPost['5c']."')";
				$this->core->database->doInsertQuery($sql);
			}
			if(!empty($this->core->cleanPost['6'])){
				$sql = "INSERT INTO `answers` (`ID`, `QuestionID`, `Answer`, `AnswerCorrect`) VALUES (NULL, '$questionid', '".$this->core->cleanPost['6']."', '".$this->core->cleanPost['6c']."')";
				$this->core->database->doInsertQuery($sql);
			}
			if(!empty($this->core->cleanPost['7'])){
				$sql = "INSERT INTO `answers` (`ID`, `QuestionID`, `Answer`, `AnswerCorrect`) VALUES (NULL, '$questionid', '".$this->core->cleanPost['7']."', '".$this->core->cleanPost['7c']."')";
				$this->core->database->doInsertQuery($sql);
			}
			if(!empty($this->core->cleanPost['8'])){
				$sql = "INSERT INTO `answers` (`ID`, `QuestionID`, `Answer`, `AnswerCorrect`) VALUES (NULL, '$questionid', '".$this->core->cleanPost['8']."', '".$this->core->cleanPost['7c']."')";
				$this->core->database->doInsertQuery($sql);
			}



			if(!empty($this->core->cleanPost['a'])){
				$sql = "INSERT INTO `answers` (`ID`, `QuestionID`, `Answer`, `AnswerCorrect`) VALUES (NULL, '$questionid', '".$this->core->cleanPost['a']."', '1')";
				$this->core->database->doInsertQuery($sql);
			}
			if(!empty($this->core->cleanPost['b'])){
				$sql = "INSERT INTO `answers` (`ID`, `QuestionID`, `Answer`, `AnswerCorrect`) VALUES (NULL, '$questionid', '".$this->core->cleanPost['b']."', '1')";
				$this->core->database->doInsertQuery($sql);
			}
			if(!empty($this->core->cleanPost['c'])){
				$sql = "INSERT INTO `answers` (`ID`, `QuestionID`, `Answer`, `AnswerCorrect`) VALUES (NULL, '$questionid', '".$this->core->cleanPost['c']."', '1')";
				$this->core->database->doInsertQuery($sql);
			}
			if(!empty($this->core->cleanPost['d'])){
				$sql = "INSERT INTO `answers` (`ID`, `QuestionID`, `Answer`, `AnswerCorrect`) VALUES (NULL, '$questionid', '".$this->core->cleanPost['d']."', '1')";
				$this->core->database->doInsertQuery($sql);
			}
			if(!empty($this->core->cleanPost['e'])){
				$sql = "INSERT INTO `answers` (`ID`, `QuestionID`, `Answer`, `AnswerCorrect`) VALUES (NULL, '$questionid', '".$this->core->cleanPost['e']."', '1')";
				$this->core->database->doInsertQuery($sql);
			}
			if(!empty($this->core->cleanPost['f'])){
				$sql = "INSERT INTO `answers` (`ID`, `QuestionID`, `Answer`, `AnswerCorrect`) VALUES (NULL, '$questionid', '".$this->core->cleanPost['f']."', '1')";
				$this->core->database->doInsertQuery($sql);
			}
			if(!empty($this->core->cleanPost['g'])){
				$sql = "INSERT INTO `answers` (`ID`, `QuestionID`, `Answer`, `AnswerCorrect`) VALUES (NULL, '$questionid', '".$this->core->cleanPost['g']."', '1')";
				$this->core->database->doInsertQuery($sql);
			}
			if(!empty($this->core->cleanPost['h'])){
				$sql = "INSERT INTO `answers` (`ID`, `QuestionID`, `Answer`, `AnswerCorrect`) VALUES (NULL, '$questionid', '".$this->core->cleanPost['h']."', '1')";
				$this->core->database->doInsertQuery($sql);
			}


		}else if($this->core->cleanPost['qt'] == 3){
			$answer = $this->core->cleanPost['answer'];
			$sql = "INSERT INTO `answers` (`ID`, `QuestionID`, `Answer`, `AnswerCorrect`) VALUES (NULL, '$questionid', '$answer', '0')";
			$this->core->database->doInsertQuery($sql);
		}

		$this->core->redirect("questions", "manage", $item);
	}
}