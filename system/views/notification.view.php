<?php
class notification{

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

	private function viewMenu(){
		echo '<div class="toolbar">'.
		'<a href="' . $this->core->conf['conf']['path'] . '/notification/manage">View notifications</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/notification/upload">Upload new notifications</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/notification/push">Push all notifications</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/notification/clear">Clear sent notifications</a>'.
		'</div>';
	}

	public function manageNotification($item){

		$this->viewMenu();

		$sql = "SELECT * FROM  `basic-information`, `notifications` WHERE `notifications`.StudentID = `basic-information`.ID AND `notifications`.Status = 0";
		$run = $this->core->database->doSelectQuery($sql);


		echo'<div class="heading">' . $this->core->translate("Not sent") . ' </div>';

		echo'<table id="results" class="table table-bordered  table-hover">
		<thead>
			<tr>
				<th bgcolor="#EEEEEE" width="30px"></th>
				<th bgcolor="#EEEEEE" width="40px">#</th>
				<th bgcolor="#EEEEEE" width="300px" data-sort"string"=""><b>Student Name</b></th>
				<th bgcolor="#EEEEEE"><b>Student Number</b></th>
				<th bgcolor="#EEEEEE"><b>Phone number</b></th>
				<th bgcolor="#EEEEEE"><b>Message</b></th>
			</tr>
		</thead>
		<tbody>';


		$count = $this->offset+1;

		while ($row = $run->fetch_row()) {
			$results == TRUE;

			$id = $row[4];
			$NID = $row[5];
			$firstname = $row[0];
			$middlename = $row[1];
			$surname = $row[2];
			$celphone = $row[14];
			$mode = $row[19];
			$message = $row[23];
			$status = $row[24];

			echo'<tr style="background-color:  #FFF;">
				<td><img src="/edurole/templates/edurole/images/user.png"></td><td>'.$count.'</td>
				<td><a href="' . $this->core->conf['conf']['path'] . '/information/show/' . $id . '"><b> '.$firstname.' '.$middlename.' '.$surname.'</b></a></td>
				<td> '.$id.'</td>
				<td> '.$celphone.'</td>
				<td> '.$message.'</td>
				</tr>';

			$count++;
			$results = TRUE;
		}

		echo'</tbody>
		</table>';



		$sql = "SELECT * FROM  `basic-information`, `notifications` WHERE `notifications`.StudentID = `basic-information`.ID AND `notifications`.Status = 1";
		$run = $this->core->database->doSelectQuery($sql);

		echo'<div class="heading">' . $this->core->translate("Sent messages ") . ' </div>';

		echo'<table id="results" class="table table-bordered  table-hover">
		<thead>
			<tr>
				<th bgcolor="#EEEEEE" width="30px"></th>
				<th bgcolor="#EEEEEE" width="40px">#</th>
				<th bgcolor="#EEEEEE" width="300px" data-sort"string"=""><b>Student Name</b></th>
				<th bgcolor="#EEEEEE"><b>Student Number</b></th>
				<th bgcolor="#EEEEEE"><b>Phone number</b></th>
				<th bgcolor="#EEEEEE"><b>Message</b></th>
			</tr>
		</thead>
		<tbody>';


		$count = $this->offset+1;

		while ($row = $run->fetch_row()) {
			$results == TRUE;

			$id = $row[4];
			$NID = $row[5];
			$firstname = $row[0];
			$middlename = $row[1];
			$surname = $row[2];
			$celphone = $row[14];
			$mode = $row[19];
			$message = $row[23];
			$status = $row[24];

			echo'<tr style="background-color:  #FFF;">
				<td><img src="/edurole/templates/edurole/images/user.png"></td><td>'.$count.'</td>
				<td><a href="' . $this->core->conf['conf']['path'] . '/information/show/' . $id . '"><b> '.$firstname.' '.$middlename.' '.$surname.'</b></a></td>
				<td> '.$id.'</td>
				<td> '.$celphone.'</td>
				<td> '.$message.'</td>
				</tr>';

			$count++;
			$results = TRUE;
		}

		echo'</tbody>
		</table>';

	}

	public function uploadNotification($item){
		$this->viewMenu();

		if (isset($_FILES["notification"])) {
			$notification = $_FILES["notification"];
			$home = getcwd();

			$path = $this->core->conf['conf']['dataStorePath'] . 'tmp/notifications';		

			if (!is_dir($path)) {
				mkdir($path, 0755, true);
			}
		
			$notallowedExts = array("exe", "EXE", "cmd", "CMD", "sh", "SH", "vb", "VB", "app", "APP", "com", "COM", "bat", "BAT", "php", "PHP", "html", "HTML", "cgi", "CGI", "htm", "HTM", "htaccess");
			$extension = end(explode(".", $file["notification"]["name"]));
			
			if (($file["grades"]["size"] < 50000000) && !in_array($extension, $notallowedExts)) {
		
				if ($file["notification"]["error"] > 0) {
					echo "Error: " . $file["notification"]["error"] . "<br>";
				} else {
					$rand = mt_rand(10000,100000);
					$name =  $rand.'-'. $_FILES["notification"]["name"];

					while (file_exists("$path/$name." . $extension)) {
						$rand = mt_rand(10000,100000);
						$name =  $rand.'-'. $_FILES["notification"]["name"];
					}

					if (file_exists("$path/$name")) {
						echo "ERROR THIS FILE ALREADY EXISTS: $path/$name";
					} else{
						move_uploaded_file($_FILES["notification"]["tmp_name"], "$path/$name");
						$this->core->logEvent("File upload completed: $path/$name", "3");
						
						echo'<h2>Upload of file: "'.$name.'" succeeded</h2><br>';
					}
				}

			} else {
				$this->core->logEvent('Warning: File upload failed, invalid file', '2');
				$this->core->throwWarning('Error: File upload failed, invalid file');
			}
		

			$path = $this->core->conf['conf']['dataStorePath'] . 'tmp/notifications';

			foreach (glob("$path/*") as $filename) {
				echo "<br><h2>IMPORTING $filename</h2>";

				$file = file_get_contents($filename);
				$document = explode("\n", $file);

				foreach($document as $line){

					$linearray = explode(",", $line);

					$linearray = str_getcsv($line,',','"');

					if(!isset($linearray[1])){
						continue;
					}

					$studentID = $linearray[0];
					$message = $linearray[1];
	
					$sql = "INSERT INTO `notifications` (`ID`, `StudentID`, `Message`, `Status`) VALUES (NULL, '$studentID', '$message', '0');";

					if( $this->core->database->doInsertQuery($sql) ){	
						echo " - Notification added <br>";	
					}else{
						echo " - Failed to add <br>";
						continue;
					}
				
				}
			}

			unlink($filename); 

		} else {

			echo'<div class="heading">' . $this->core->translate("Upload notifications by CSV file") . ' </div>';
			echo '<p><form id="upload" name="upload" method="POST" action="'.$this->core->conf['conf']['path'].'/notification/upload" enctype="multipart/form-data">
			<div class="label" style="float:left;">Upload CSV file: </div>
			<div style="float:left; width:200px"><input type="file" name="notification" id="notification" class="submit" /></div>
			<br><br><br><div class="label" style="clear:both;"> </div>
			<input type="submit" value="Begin upload" class="submit" />
			</form></p>';

		}
	}

	public function pushNotification($item){
		include $this->core->conf['conf']['viewPath'] . "sms.view.php";
		$sms = new sms();
		$sms->buildView($this->core);

		$sql = "SELECT * FROM  `notifications` WHERE `Status` = 0;";

		$run = $this->core->database->doSelectQuery($sql);

		while ($row = $run->fetch_row()) {
			$mid = $row[0];
			$studentID = $row[1];
			$message = $row[2];

			echo "$studentID - $message<br>";
			$sms->parseMessage(0, $studentID, $message);

			$sql = "UPDATE `notifications` SET `Status` = '1' WHERE `ID` = $mid";
			$this->core->database->doInsertQuery($sql);
		}
	}
}
?>