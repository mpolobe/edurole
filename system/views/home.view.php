<?php
class home {

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

	public function buildView($core) {
		$this->core = $core;
	}

	public function showHome($item) {
		require_once "system/components.inc.php";
		include $this->core->conf['conf']['viewPath'] . "item.view.php";
		include $this->core->conf['conf']['viewPath'] . "proxy.view.php";

		$items = new item();
		$items->buildView($this->core);
		$proxy = new proxy();
		$proxy->buildView($this->core);
	
		if($item == "internet"){
			echo'<div class="successpopup">You have logged in succesfuly, you can now browse the internet. <br>Please keep in mind your data limit for today is <u>700MB</u>.</div>';
		}

		$proxy->statusProxy(FALSE);	

		$this->dataBar(FALSE);

		$userid =$this->core->userID;
		$sql = "SELECT * FROM `basic-information`WHERE ID = '$userid'";
		$run = $this->core->database->doSelectQuery($sql);

		while ($fetch = $run->fetch_assoc()) {
			$status = $fetch['Status'];
		}

		if($status == "Locked"){
			echo'<div class="errorpopup">Your account is locked because you have not fully registered. Complete your registration first.</div>';
			return;
		}


		$this->infoSheet();
		$items->overviewItem('news', "News and updates");
		$items->overviewItem('memo', "Memorandums relevant to you");
		$this->easyMenu();


		if ($this->core->role > 100) {
			$this->globalStatistics();
		}

	
		$items->showItem(1,false);
		

	
	}

	private function dataBar($item){

		$limit = 734003200;   // MOVE DATA LIMIT TO CONF

		$current = $this->personalData(FALSE);
		$bw = $this->formatBytes($current,0);

		if($bw == 'NAN' || $bw == 'NANB'){
			$bw = '0MB';
		}

		$data =  'PERSONAL DATA USED: '. $bw . ' / 700MB';

		$percent = $current / $limit * 100;


		if($percent > 100){ $percent = 100; }
		if($percent < 50){ $color = 'progress-bar-success'; }
		if($percent > 50){ $color = 'progress-bar-warning'; }
		if($percent > 80){ $color = 'progress-bar-danger'; }



		echo'<div class="progress" style="height: 30px; font-size: 16px; color: #333;  font-weight: bold; text-align: center; line-height: 35px; position: relative;">
			
			<div class="progress-bar '.$color.'" role="progressbar" aria-valuenow="'.$percent.'" aria-valuemin="0" aria-valuemax="100" style="width:'.$percent.'%;  padding: 5px; position: absolute; float: left;">
			
		  </div><div style="position: absolute; top: 0px; left: 230px;  text-align: center;">'.$data.'</div> 
		</div>';

	}

	private function formatBytes($size, $precision = 2) { 
		$base = log($size, 1024);
		$suffixes = array('B', 'KB', 'MB', 'GB', 'TB');   

		return round(pow(1024, $base - floor($base)), $precision) .''. $suffixes[floor($base)];
	}

	private function personalData($item){
		$user = $this->core->username;
		$sql = "SELECT SUM(`data`) as data FROM `acl` WHERE `user` LIKE '$user' AND `date` = CURDATE()";

		$run = $this->core->database->doSelectQuery($sql);
		while ($ds = $run->fetch_assoc()) {
			$data = $ds['data'];
		}

		return $data;
	}


	private function globalStatistics() {

		include $this->core->conf['conf']['viewPath'] . "sms.view.php";

		$sms = new sms();
		$sms->buildView($this->core);
		
		

		$this->core->logEvent("Initializing global user statistics count", "3");

		$sql = "SELECT  
		(SELECT count(ID) FROM `basic-information` WHERE `StudyType` = 'Fulltime' AND `Status` = 'Approved') AS fulltimestudents, 
		(SELECT count(ID) FROM `basic-information` WHERE `StudyType` = 'Distance' AND `Status` = 'Approved') AS block, 
		(SELECT count(ID) FROM `basic-information` WHERE `Status` = 'New') AS oldRecords,
		(SELECT count(ID) FROM `basic-information` WHERE `Status` = 'Employed') AS users";

		$run = $this->core->database->doSelectQuery($sql);

		while ($row = $run->fetch_row()) {
			$fulltime = $row[0];
			$block= $row[1];
			$old = $row[2];
			$users = $row[3];
			$total = $fulltime + $block ;
		}

		$sql = "SELECT SUM(`data`) as data FROM `acl` WHERE `date` = CURDATE()";

		$run = $this->core->database->doSelectQuery($sql);
		while ($ds = $run->fetch_assoc()) {
			$data = $ds['data'];
			$data = $this->formatBytes($data,0);
		}

		echo '<div style="width: 100%">
        	<div class="statistics">'.$this->core->translate("Total Data Use Today").': <div class="statistic">' . $data . '</div></div>
        	<div class="statistics">'.$this->core->translate("Fulltime Registered").': <div class="statistic">' . $fulltime . '</div></div>
         	<div class="statistics">'.$this->core->translate("Distance Registered").': <div class="statistic">' . $block . '</div></div>
        	<div class="statistics">'.$this->core->translate("Total population").': <div class="statistic"  style="color: #2C89D4;">' . $total . '</div></div>
        	<div class="statistics">'.$this->core->translate("Staff users").': <div class="statistic">' . $users . '</div></div>
        	<div class="statistics">'.$this->core->translate("SMS available").': <div class="statistic">';
		$sms->unitsSms();
		  echo'</div></div>
        	</div>';

	}

	private function infoSheet() {

		$this->core->logEvent("Initializing info-sheet", "3");

		$sql = "SELECT * FROM `basic-information` as bi, `access` as ac WHERE ac.`ID` = '" . $this->core->userID . "' AND ac.`ID` = bi.`ID`";
		$run = $this->core->database->doSelectQuery($sql);

		if ($run->num_rows == 0) {
			$this->core->throwSuccess($this->core->translate("Please take the time to enter your profile information first, you can do this <a href='". $this->core->conf['conf']['path'] ."/information/edit/personal'>here</a>."));
		}

		while ($row = $run->fetch_row()) {

			$id = $this->core->userID;
			$firstname = $row[0];
			$middlename = $row[1];
			$surname = $row[2];
			$sex = $row[3];
			$idnumber = $row[4];
			$nrc = $row[5];
			$studytype = $row[22];
			$username = $row[22];

			if(empty($firstname) && empty($lastname)){
				$this->core->throwSuccess($this->core->translate("Please take the time to enter your profile information first, you can do this <a href='". $this->core->conf['conf']['path'] ."/information/edit/personal'>here</a>."));
			}

			$sql = "SELECT * FROM `content` WHERE `ContentCat` = 'news' ORDER BY `ContentID` DESC LIMIT 1";
			$runx = $this->core->database->doSelectQuery($sql);


echo"<script>
document.addEventListener('DOMContentLoaded', function () {
  if (Notification.permission !== 'granted')
    Notification.requestPermission();
});


  if (Notification.permission !== 'granted')
    Notification.requestPermission();
  else {";


    


			while ($row = $runx->fetch_assoc()) {
				$name =  $row['Name'];
				$content =  strip_tags($row['Content']);
				$url =  $row['URL'];

				if(empty($url)){
					$url =  $row['Files'];
				}

				echo"    var notification = new Notification('$name', {
      icon: 'https://www.nkrumah.edu.zm/edurole/templates/edurole/images/apple-touch-icon-144x144-precomposed.png',
      body: '$content',
    });

    notification.onclick = function () {
      window.open('$url');      
    };";
			}

  echo'}
</script>';

			echo '<div class="col-lg-12 greeter">'.$this->core->translate("Hi").' ' . $firstname . ' ' . $surname . '</div>
			<div class="col-lg-12 panel panel-default">

               	 	<table width="600" border="0" cellpadding="2"><tr>';

			if ($this->core->role < 100) {
				echo'<td  width="117">';
				echo $this->core->translate("Student number");
				echo'</td>';
			} else {
				echo '<td  width="200">';
				echo $this->core->translate("Employee number");
				echo '</td>';
			}

			echo '<td><b>' . $idnumber . '</a></td></tr>';
			echo '<tr><td>';
			echo $this->core->translate("Your privileges");
			echo'</td> <td>' . $this->core->roleName . '</td></tr>';
			echo '<tr><td>';

			$sql = "SELECT * FROM `access` as ac, `study` as st
				LEFT JOIN  `student-study-link` as ss ON ss.`StudentID` = ''
				LEFT JOIN  `student-program-link` as pl ON pl.`StudentID` = '$id'
				WHERE ac.`ID` = ''
				AND ss.`StudyID` = st.`ID`";

			$run = $this->core->database->doSelectQuery($sql);

			while ($row = $run->fetch_row()) {

				$status = $row[7];
				$study = $row[14];

				echo '<tr><td>';
				echo $this->core->translate("Selected Study");
                        	echo'</td><td><b><a href="' . $this->core->conf['conf']['path'] . '/studies/show/' . $row[8] . '">' . $study . '</a></b></td>
                        	</tr>';

				$sql = "SELECT * FROM `programmes` as pr WHERE pr.`ID` = '$row[23]' OR pr.`ID` = '$row[24]'";
				$run = $this->core->database->doSelectQuery($sql);

				$i = 0;

				while ($row = $run->fetch_row()) {

					$programme = $row[2];

					if ($i == 0) {
						$majmin = "major";
						$i++;
					} else {
						$majmin = "minor";
						$n = 1;
					}

				echo '<tr><td>';
				echo $this->core->translate("Selected");
                                echo ' ' . $majmin . '</td>
                                <td>' . $programme . '</td>
                                </tr>';
				}

				if ($majmin == "major") {
					echo '<tr><td>';
					echo $this->core->translate("Selected Minor");
					echo'</td><td>' . $programme . '</td></tr>';
				}
			}

			echo '</table></div>';

		}

	}

	private function easyMenu() {
		if ($this->core->role > 10) {
			echo '<div style="float:left; margin-bottom: 5px; width: 100%">
                 	<div class="easymen"><a href="http://library.nkrumah.edu.zm/"><img src="' . $this->core->fullTemplatePath . '/images/books.png"> <br>  Library</a></div>
                 	<div class="easymen"><a href="' . $this->core->conf['conf']['path'] . '/grades"><img src="' . $this->core->fullTemplatePath . '/images/chart.png"> <br> Grades</a></div>
                 	<div class="easymen"><a href="' . $this->core->conf['conf']['path'] . '/calendar"><img src="' . $this->core->fullTemplatePath . '/images/calendar.png"> <br> Calendar</a></div>
		 	<div class="easymen"><a href="' . $this->core->conf['conf']['path'] . '/files/personal"><img src="' . $this->core->fullTemplatePath . '/images/box.png"> <br>  Files</a></div>
                 	<div class="easymen"><a href="' . $this->core->conf['conf']['path'] . '/assignments"><img src="' . $this->core->fullTemplatePath . '/images/clipboard.png">  <br> Assignments</a></div>
                 	<div class="easymen"><a href="' . $this->core->conf['conf']['path'] . '/payments/show"><img src="' . $this->core->fullTemplatePath . '/images/money.png"> <br> Payments</a></div>
                 	<div class="easymen"><a href="' . $this->core->conf['conf']['path'] . '/helpdesk/message"><img src="' . $this->core->fullTemplatePath . '/images/info.png">  <br> Help</a></div>
                	</div>';
		} else {
			echo '<div style="float:left; margin-bottom: 5px; width: 100%">
                 	<div class="easymen"><a href="http://library.nkrumah.edu.zm/"><img src="' . $this->core->fullTemplatePath . '/images/books.png"> <br>  Library</a></div>
                 	<div class="easymen"><a href="' . $this->core->conf['conf']['path'] . '/grades/personal"><img src="' . $this->core->fullTemplatePath . '/images/chart.png"> <br> Grades</a></div>
                 	<div class="easymen"><a href="' . $this->core->conf['conf']['path'] . '/calendar/personal"><img src="' . $this->core->fullTemplatePath . '/images/calendar.png"> <br> Calendar</a></div>
		 	<div class="easymen"><a href="' . $this->core->conf['conf']['path'] . '/files/personal"><img src="' . $this->core->fullTemplatePath . '/images/box.png"> <br>  Files</a></div>
                 	<div class="easymen"><a href="' . $this->core->conf['conf']['path'] . '/assignments/show"><img src="' . $this->core->fullTemplatePath . '/images/clipboard.png">  <br> Assignments</a></div>
                 	<div class="easymen"><a href="' . $this->core->conf['conf']['path'] . '/payments/personal"><img src="' . $this->core->fullTemplatePath . '/images/money.png"> <br> Payments</a></div>
                 	<div class="easymen"><a href="' . $this->core->conf['conf']['path'] . '/helpdesk/message"><img src="' . $this->core->fullTemplatePath . '/images/info.png">  <br> Help</a></div>
                	</div>';
		}

	}
}

?>
