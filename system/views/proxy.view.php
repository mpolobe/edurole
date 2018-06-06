<?php
class proxy {

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
		$today = date("Y-m-d");

		if(isset($_GET['date'])){
			$today = $_GET['date'];
		}

		echo '<div class="toolbar">'.
		'<a href="' . $this->core->conf['conf']['path'] . '/proxy/add">Add Static Host</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/proxy/blocked">Blocked Clients</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/proxy/banned">Banned Computers</a>'.
		'<a href="' . $this->core->conf['conf']['path'] . '/proxy/log">Access Logs</a>
		</div>';
	}


	function statusProxy($item) {

		$username = $this->core->username;

		if($username == "auditor"){
			return;
		}

		$ip = $_SERVER['REMOTE_ADDR'];
		if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
			$ip = array_pop(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']));
		}

		if(substr( $ip, 0, 2) == '10'){
			#LAN USER PLEASE PROCEED
		} else {
			return false;
		}

		$current = time();
		$mac = $this->getMac($ip);
		$today = date("Y-m-d");


		$sql = 'UPDATE acl SET `status`="EXPIRED" WHERE `date`< CURDATE() AND `status` != "PROTECTED";';
		$run = $this->core->database->doInsertQuery($sql);

		$sqla = "SELECT * FROM `acl` WHERE `user` = '$username' AND `date` = CURDATE() AND `status` != 'LOGOUT'";
		$run = $this->core->database->doSelectQuery($sqla);


	
		if($run->num_rows < 2 || !is_numeric($username) || $username == "admin"){

			$sqlb = "SELECT * FROM `acl` WHERE `user` = '$username' AND `status` = 'BANNED'";
			$run = $this->core->database->doSelectQuery($sqlb);
			if($run->num_rows == 0){

				$sqlc = "SELECT * FROM `acl` WHERE `user` = '$username' AND `status` = 'KICKED' AND `date` = CURDATE()";
				$run = $this->core->database->doSelectQuery($sqlc);
				if($run->num_rows == 0){
				
					$sqld = "SELECT * FROM `acl` WHERE `user` = '$username' AND `status` = 'DATA' AND `date` = CURDATE()";
					$run = $this->core->database->doSelectQuery($sqld);
					if($run->num_rows == 0){

						$sqle = "SELECT * FROM `acl` WHERE `user` = '$username' AND `status` = 'PROTECTED' AND `ip` = '$ip'";
						$run = $this->core->database->doSelectQuery($sqle);
						if($run->num_rows == 0){
							$sql = 'INSERT INTO acl (`ip`, `user`, `start_time`, `mac`, `date`, `status`) VALUES ("'.$ip.'", "'.$username.'", "'.$current.'", "'.$mac.'", CURDATE(), "ACTIVE") ON DUPLICATE KEY UPDATE `status` = "ACTIVE", `user` = "'.$username.'";';
							$run = $this->core->database->doInsertQuery($sql);
						} else {
						}

					} else {
						$this->core->redirect("proxy", "error", "data");
					}


				} else {
					$this->core->redirect("proxy", "error", "kicked");
				}

			}else{
				$this->core->redirect("proxy", "error", "banned");
			}


		} else {
			$this->core->redirect("proxy", "error", "session");
		}
	}

	function authenticateProxy($item) {

		$ip = $_SERVER['REMOTE_ADDR'];
		if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
			$ip = array_pop(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']));
		}

		$username = $_POST['username'];
		$password = $_POST['password'];

		$auth = new auth($this->core);
		$login = $auth->login();

		$current = time();

		// GET MAC TO IDENTIFY COMPUTER
		$mac = $this->getMac($ip);

		$today = date("Y-m-d");




		if($login == FALSE){
			echo'FAILED TO LOGIN, PLEASE USE YOUR EDUROLE PASSWORD';
		}else{

			$sql = 'UPDATE acl SET `status`="EXPIRED" WHERE `date`< CURDATE() AND `status` != "PROTECTED";';
			$run = $this->core->database->doInsertQuery($sql);

			$sqla = "SELECT * FROM `acl` WHERE `user` = '$username' AND `date` = CURDATE() AND `status` != 'LOGOUT'";
			$run = $this->core->database->doSelectQuery($sqla);
			if($run->num_rows < 2 || $username == 'admin'){


				$sqlb = "SELECT * FROM `acl` WHERE `user` = '$username' AND `status` = 'BANNED'";
				$run = $this->core->database->doSelectQuery($sqlb);
				if($run->num_rows == 0){


					$sqlc = "SELECT * FROM `acl` WHERE `user` = '$username' AND `status` = 'KICKED' AND `date` = CURDATE()";
					$run = $this->core->database->doSelectQuery($sqlc);
					if($run->num_rows == 0){

				
						$sqld = "SELECT * FROM `acl` WHERE `user` = '$username' AND `status` = 'DATA' AND `date` = CURDATE()";
						$run = $this->core->database->doSelectQuery($sqld);
						if($run->num_rows == 0){

							$sqle = "SELECT * FROM `acl` WHERE `user` = '$username' AND `status` = 'PROTECTED' AND `ip` = '$ip'";
							$run = $this->core->database->doSelectQuery($sqle);
							if($run->num_rows == 0){

								$sql = 'INSERT INTO acl (`ip`, `user`, `start_time`, `mac`, `date`, `status`) VALUES ("'.$ip.'", "'.$username.'", "'.$current.'", "'.$mac.'", CURDATE(), "ACTIVE") ON DUPLICATE KEY UPDATE `status` = "ACTIVE", `user` = "'.$username.'";';
								$run = $this->core->database->doInsertQuery($sql);
								$this->core->redirect("home", "show", "internet");
							} else {
							}

						} else {
							$this->core->redirect("proxy", "error", "data");
						}


					} else {
						$this->core->redirect("proxy", "error", "kicked");
					}


				}else{
					$this->core->redirect("proxy", "error", "banned");
				}


			} else {
				$this->core->redirect("proxy", "error", "session");
			}
		}
	}

	private function getMac($ip){

		// GET MAC TO IDENTIFY COMPUTER
		$arp = "arp -n $ip";
		$arp = exec($arp);
		$arp = explode('ether', $arp);

		$mac=substr($arp[1], 0, strrpos($arp[1], ' C '));

		return trim($mac);

	}

	function errorProxy($item) {

		if($item == "session"){
			echo '<div class="errorpopup">You are already logged in on TWO other devices</div>';
		}else if($item == "kicked"){
			echo '<div class="errorpopup">You have been banned from the internet for rest of the day</div>';
		}else if($item == "banned"){
			echo '<div class="errorpopup">You have been permanently banned from internet use. Please report to ICT.</div>';
		}else if($item == "data"){
			echo '<div class="errorpopup">You have used up your daily 500 megabytes of data for today. Please feel free to log in again tomorrow.</div>';
		}else{
			echo "AN UNKNOWN ERROR";
		}


	}

	private function formatBytes($size, $precision = 2) { 
		$base = log($size, 1024);
		$suffixes = array('B', 'KB', 'MB', 'GB', 'TB');   

		return round(pow(1024, $base - floor($base)), $precision) .''. $suffixes[floor($base)];
	}


	function deamonProxy($item){
		$limit = 99734003200;			// MAXIMUM DATA USE PUT IN SETTINGS FILE 


		if($item == "data"){

			$today = date("Y-m-d");


			$sqlc = "SELECT `user`, `ip` FROM `acl` WHERE `date` = CURDATE();";
			$run = $this->core->database->doSelectQuery($sqlc);
			while ($data = $run->fetch_assoc()) {
				$users[$data['ip']] = $data['user'];
			}

			$sqlc = "SELECT `ip` FROM `acl` WHERE `status` = 'PROTECTED'";
			$runc = $this->core->database->doSelectQuery($sqlc);
			while ($data = $runc->fetch_assoc()) {
				$protected[$data['ip']] = $data['ip'];
			}

			$log = file_get_contents("http://10.0.0.1/$today/data.log");
			$lines = explode("\n", $log);
			echo '#!/bin/bash' . "\n";

			foreach($lines as $line){
				$line = preg_replace('/\s+/', ' ',$line);
				$data = explode(' ', $line);


				$ip = $data[0];
				if($ip == '#'){
					continue;
				}else{
					$ip = $data[0];
					$total = $data[3];

					#echo $ip . ' - ' . $total; 
					
					$usage[$ip] = $total;
					$sql = "UPDATE `acl` SET `data` = '$total' WHERE `ip` = '$ip' AND `date` = CURDATE() AND `data` < '$total';";
					$this->core->database->doInsertQuery($sql, TRUE);
					
					if($protected[$ip] == $ip){
						continue;
					}


					
					if($total > $limit && !is_numeric($users[$ip])){
						$sqlk = "UPDATE `acl` SET `status` = 'DATA' WHERE `user` = '$users[$ip]' AND `date` = CURDATE() AND `status` != 'PROTECTED'";
						$this->core->database->doInsertQuery($sqlk, TRUE);
						echo '/sbin/iptables -A FORWARD -s '.$ip.' -j DROP  #user '. $users[$ip] . "\n";
					}

					
					if(empty($users[$ip])){
						$mac = $this->getMac($ip);
						
						if(!empty($mac)){
							$sqle = "INSERT INTO `acl`(`ID`, `ip`, `user`, `start_time`, `mac`, `date`, `status`, `data`) VALUES (NULL, '$ip', '', '0', '$mac', CURDATE(), 'NONE', '$total') ON DUPLICATE KEY UPDATE `data` = '$total';"; 
							$this->core->database->doInsertQuery($sqlk, TRUE);
						

							//echo '/sbin/iptables -A FORWARD -s '.$ip.' -j DROP  #user '. $users[$ip] . "\n";
						}
					}

				}
			}


		} else {
			echo 'NO DEAMON SELECTED';
		}
	}


	public function aclProxy($item){

		echo "#!/bin/bash \n";

		$sql = "SELECT * FROM  `basic-information`, `access`, `acl` 
			WHERE `user` != '' AND `user` = access.`username` AND `access`.ID = `basic-information`.ID AND `date` = CURDATE() AND `acl`.`status` IN ('ACTIVE', 'PROTECTED')
			ORDER BY `user`";
		

		$run = $this->core->database->doSelectQuery($sql);
		while ($data = $run->fetch_assoc()) {
			$user = $data['user'];	
			$ip = $data['ip'];

			$output = $output. '/sbin/iptables -A FORWARD  -p tcp --match multiport --dport 80:443 -s  '.$ip.' -o eth1 #ALLOW '. $user."\n";
			$output = $output. '/sbin/iptables -t nat -A POSTROUTING -p tcp --match multiport --dport 80:443 -s  '.$ip.' -o eth1 -j MASQUERADE  #ALLOW '. $user."\n";

		} 

		echo $output;

		$file = fopen("/tmp/allowed.list", "w") or die("Unable to open file!");
		fwrite($myfile, $output);
		fclose($file);
	}


	function manageProxy($item) {

		$limit = 734003200;   // MOVE DATA LIMIT TO CONF

		$today = date("Y-m-d");
		if(isset($_GET['date'])){
			$today = $_GET['date'];
		}

		$date = new DateTime($today);
		$yesterday = new DateTime($today);
		$yesterday = $yesterday->sub(new DateInterval('P5D'))->format('Y-m-d');
		$todayp = new DateTime($today);


		if($this->core->role > 10){
			$this->viewMenu();
		}

		$todays = date("Y-m-d");

		$datea = new DateTime($todays);
		$dateb = new DateTime($todays);
		$datec = new DateTime($todays);
		$dated = new DateTime($todays);
		$datee = new DateTime($todays);
		$datef = new DateTime($todays);
		$dateg = new DateTime($todays);

		echo'<nav>
			<ul class="pagination">
				
				<li><a href="?date='. $dateb->sub(new DateInterval('P6D'))->format('Y-m-d').'">'. $dateb->format('d-m-Y').'</a></li>
				<li><a href="?date='. $datec->sub(new DateInterval('P5D'))->format('Y-m-d').'">'. $datec->format('d-m-Y').'</a></li>
 				<li><a href="?date='. $dated->sub(new DateInterval('P4D'))->format('Y-m-d').'">'. $dated->format('d-m-Y').'</a></li>
 		   		<li><a href="?date='. $datee->sub(new DateInterval('P3D'))->format('Y-m-d').'">'. $datee->format('d-m-Y').'</a></li>
 		   		<li><a href="?date='. $datef->sub(new DateInterval('P2D'))->format('Y-m-d').'">'. $datef->format('d-m-Y').'</a></li>
 		   		<li><a href="?date='. $dateg->sub(new DateInterval('P1D'))->format('Y-m-d').'">'. $dateg->format('d-m-Y').'</a></li>
				<li><a href="?date='. $dateg->add(new DateInterval('P1D'))->format('Y-m-d').'">'. $dateg->format('d-m-Y').'</a></li>';
				echo'<li><a href="' . $this->core->conf['conf']['path'] . '/proxy/manage"><b>CURRENT USERS</b></a></li>';
				
 		   		echo'
 		 	</ul>
		</nav>';

		echo'<table id="proxy" class="table table-bordered table-striped table-hover">
			<thead>
				<tr>
					<th bgcolor="#EEEEEE" width="35px">#</th>
					<th bgcolor="#EEEEEE" width="30px"></th>
					<th bgcolor="#EEEEEE" data-sort"string"=""><b>Student Name</b></th>
					<th bgcolor="#EEEEEE"><b>ID Number</b></th>
					<th bgcolor="#EEEEEE"><b>Daily Data Used</b></th>
					<th bgcolor="#EEEEEE"><b>IP address</b></th>
					<th bgcolor="#EEEEEE"><b>Login</b></th>
					<th bgcolor="#EEEEEE"><b>Status</b></th>
					<th bgcolor="#EEEEEE" width="80px"><b>Manage</b></th>
				</tr>
			</thead>
			<tbody>';

		
		if(isset($_GET['date'])){
			$today = $this->core->cleanGet['date'];
			$sql = 'SELECT * FROM `basic-information`, `access`,  `acl` WHERE `user` = access.`username` AND `access`.ID = `basic-information`.ID AND `date` = "'.$today.'" ORDER BY `user`';
		}else{
			$sql = 'SELECT * FROM  `basic-information`, `access`, `acl` 
				WHERE `user` != "" AND `user` = access.`username` AND `access`.ID = `basic-information`.ID AND `date` = CURDATE()
				ORDER BY `user`';
		}

		$run = $this->core->database->doSelectQuery($sql);
		while ($data = $run->fetch_assoc()) {
			$user = $data['user'];



			$ip = $data['ip'];
			$start= date('H:i:s', $data['start_time']);
			$end = $data['end_time'];

			$name = $data['FirstName'] . ' ' . $data['MiddleName'] . ' ' . $data['Surname'];
			$phone = $data['MobilePhone'];
			$studentid = $data['Username'];
			$mac = $data['mac'];
			$status = $data['status'];
			$data = $data['data'];

			if(empty($data)){
				$use = "-";
			}else{


				if($data > $limit && $studentid != 'admin'){
					$sqlk = "UPDATE `acl` SET `status` = 'DATA' WHERE `user` = '$studentid' AND `date` = CURDATE() AND `status` != 'PROTECTED'";
					$this->core->database->doInsertQuery($sqlk);
				}

				
				if($data > $limit  && $studentid != 'admin'){
					$warn = '<img src="' . $this->core->fullTemplatePath . '/images/warning.png">';
				} else {
					$warn = "";
				}

				$use = $this->formatBytes($data,0) . $warn;

			}

			if($status == "KICKED"){
				$kick = '<a href="' . $this->core->conf['conf']['path'] . '/proxy/allow/'.$studentid.'"><img src="' . $this->core->fullTemplatePath . '/images/check.png"> allow</a>';
			}else if($status == "BANNED"){
				$status = "<b>BANNED</b>";
				$kick = '<a href="' . $this->core->conf['conf']['path'] . '/proxy/allow/'.$studentid.'"><img src="' . $this->core->fullTemplatePath . '/images/check.png"> allow</a>';
			}else{
				$kick = '<a href="' . $this->core->conf['conf']['path'] . '/proxy/kick/'.$studentid.'">kick</a> / 
					<a href="' . $this->core->conf['conf']['path'] . '/proxy/ban/'.$studentid.'">ban</a>';
			}

			

			if($olduser == $user){
				$o++;
				echo '<tr>
				<td><img src="' . $this->core->fullTemplatePath . '/images/bullet_user.png"></td>
				<td></td>
				<td> <i>'.$name.'  ('.$o.')</i></td>
				<td>'.$studentid.'</td>
				<td><b>'.$use.'</b> </td>
				<td><a href="' . $this->core->conf['conf']['path'] . '/behaviour/history/'.urlencode($ip).'/'.urlencode($today).'"><div title="'.$mac.'">'.$ip.'</div></a></td>
				<td>'.$start.'</td>
				<td>'.$status.'</td>
				<td>'.$kick.'</td>
				</tr>';
			} else {
				$o=1; $i++;
				echo '<tr >
				<td ><img src="' . $this->core->fullTemplatePath . '/images/bullet_user.png"> </td>
				<td > '.$i.'</td>
				<td><a href="' . $this->core->conf['conf']['path'] . '/information/show/'.$studentid.'"><b>'.$name.' </b></a>(1)</td>
				<td>'.$studentid.'</td>
				<td><b>'.$use.'</b> </td>
				<td><a href="' . $this->core->conf['conf']['path'] . '/behaviour/history/'.urlencode($ip).'/'.urlencode($today).'"><div title="'.$mac.'">'.$ip.'</div></a></td>
				<td>'.$start.'</td>
				<td>'.$status.'</td>
				<td>'.$kick.'</td>
				</tr>';
			}



			$olduser=$user;

		}

		echo'</tbody></table>';

	}

	function banProxy($item) {
		
		$sql = 'UPDATE acl SET `status`="BANNED" WHERE `user` = "'.$item.'" AND `date` = CURDATE()';

		$run = $this->core->database->doInsertQuery($sql);
		$this->core->redirect("proxy", "manage", NULL);

	}


	function kickProxy($item) {
		
		$sql = 'UPDATE acl SET `status`="KICKED" WHERE `user` = "'.$item.'" AND `date` = CURDATE()';

		$run = $this->core->database->doInsertQuery($sql);
		$this->core->redirect("proxy", "manage", NULL);

	}

	function allowProxy($item) {
		
		$sql = 'UPDATE acl SET `status`="ACTIVE" WHERE `user` = "'.$item.'" AND `date` = CURDATE()';

		$run = $this->core->database->doInsertQuery($sql);
		$this->core->redirect("proxy", "manage", NULL);

	}


	function logoutProxy($item) {
		$ip = $_GET['ip'];
		$sql = 'UPDATE acl SET `status`="LOGOUT" WHERE `user` = "'.$item.'" AND `ip` = "$ip" AND `date` = CURDATE()';
		$run = $this->core->database->doInsertQuery($sql);

		$this->core->redirect("proxy", "manage", NULL);
	}


}
?>