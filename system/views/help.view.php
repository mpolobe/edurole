<?php
class help {
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


	function viewHelp($item, $full = TRUE) {

		if(empty($item)){
			$item = "1";
		}

		if($full == TRUE){
			$class = "col-lg-12";
		} else {
			$class = "col-lg-6";
		}

		$sql = "SELECT * FROM `content` WHERE `ContentID` LIKE '$item'";
		$run = $this->core->database->doSelectQuery($sql);

		while ($row = $run->fetch_row()) {

			echo '<div class="'.$class.'"><div class="panel panel-default fixedheightpanel">';

			if ($this->core->role == 1000) {
				echo '<div style="float: right;"><a href="' . $this->core->conf['conf']['path'] . '/item/edit/' . $item . '">edit</a></div>';
			}

			echo ' <h2>' . $row[1] . '</h2>';
			echo ' <p>' . $row[2] . '</p>';

			echo '</div></div>';
		}
	}

	function showHelp($item) {

		if(empty($item)){
			$item = "1";
		}

		$sql = "SELECT * FROM `content` WHERE `ContentCat` = 'help'";
		$run = $this->core->database->doSelectQuery($sql);

		echo '<div class="newscontainers">	 ';
		if ($this->core->role == 1000) {
			echo '<div style="float: right;"><a href="' . $this->core->conf['conf']['path'] . '/item/add/help">add</a></div>';
		}

		echo "<h2>Help articles</h2><p>";

		while ($row = $run->fetch_row()) {
			echo ' <li> <b><a href="' . $this->core->conf['conf']['path'] . '/help/show/' . $row[0] . '">' . $row[1] . '</a></b></li>';
		}

		echo '</p></div>';

		$this->viewHelp($item, FALSE);
	}

}
?>
