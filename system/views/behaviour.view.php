<?php
class behaviour {

	public $core;
	public $view;
	public $item = NULL;

	public $database;

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


	public function showBehaviour($item){

	}



	function connectDatabase() {
		$this->mysqli = new mysqli($this->core->conf['mysql']['server'],
			$this->core->conf['mysql']['user'],
			$this->core->conf['mysql']['password'],
			$this->core->conf['mysql']['db']);

		if ($this->mysqli->connect_errno) {
			echo $this->mysqli->connect_errno;
			$this->core->throwError("Failed to connect to the database, please contact the administrator");
		} else {
			$this->core->logEvent("Database connection initialized", "3");
		}

	}

	public function doSelectQuery($sql) {

		if (!$run = $this->mysqli->query($sql)) {
			$this->core->logEvent("Query error SQL: <span style=\"font-weight: normal;\">" . $sql . "</span>" . $this->mysqli->error, "1");
			$this->core->throwError("An error occurred with the database information retrieval system query failed: <br /> " . $sql);
			return false;
		}

		$this->core->logEvent("Query executed: $sql", "3");
		return $run;
	}


	function getdomain($url){
		$pieces = parse_url($url);
		$domain = isset($pieces['host']) ? $pieces['host'] : '';
		if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
			return $regs['domain'];
		}
		return false;
	}



	private function formatBytes($size, $precision = 2) { 
		$base = log($size, 1024);
		$suffixes = array('B', 'KB', 'MB', 'GB', 'TB');   

		return round(pow(1024, $base - floor($base)), $precision) .''. $suffixes[floor($base)];
	}

	public function historyBehaviour($item){

		$date = $this->core->subitem;

		$this->core->conf['mysql']['server'] = '41.63.36.140';
		$this->core->conf['mysql']['user'] = 'squid';
		$this->core->conf['mysql']['password'] = 'NCE2017pass';
		$this->core->conf['mysql']['db'] = 'proxy';

	
		$this->connectDatabase();

 
		$sql = "SELECT * FROM  `logs` WHERE `IP` = '$item' AND `DateTime` LIKE '$date%'";

		$run = $this->doSelectQuery($sql);


		echo'<table style="width: 100%">
			<tr class="heading" >
				<td>DateTime</td>
				<td>Domain</td>
				<td>Received</td>
			</tr>';

		$olddomain = '';
		while ($data = $run->fetch_assoc()) {
			$url = $data['URL'];
			$domain = substr($data['URL'],7,75);
			$time = $data['DateTime'];
			$received = $this->formatBytes($data['Received']);

			echo '<tr>
				<td>'.$time.'</td>
				<td><a href="'.$url.'">'.$domain.'</a></td>
				<td>'.$received.'</td>
				</tr>';

			$olddomain = $domain;
		}
		echo'</table>';


	}



}
?>