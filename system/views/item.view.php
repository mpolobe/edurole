<?php
class item {

	public $core;
	public $view;

	public function configView() {
		$this->view->header = TRUE;
		$this->view->footer = TRUE;
		$this->view->menu = TRUE;
		$this->view->javascript = array('require', 'aloha');
		$this->view->css = array('aloha');

		return $this->view;
	}

	public function buildView($core) {
		$this->core = $core;
	}

	private function showNewsOverview() {

		$sql = "SELECT * FROM `content` WHERE `ContentCat` = 'news'";
		$run = $this->core->database->doInsertQuery($sql);

		echo '<div class="newscontainers">	<h2>News and updates</h2> <p>';

		while ($row = $run->fetch_row()) {
			echo ' <li> <b><a href="' . $this->core->conf['conf']['path'] . '/item/show/' . $row[0] . '">' . $row[1] . '</a></b></li>';
		}

		echo '</p></div>';
	}

        public function saveItem($item) {
		$id = $this->core->cleanPost['itemid'];
		$name = $this->core->cleanPost['name'];
		$content = $this->core->cleanPost['content'];

		if(!empty($id)){
	                $sql = "UPDATE `content` SET `Content` = '".$content."', `Name` = '".$name."' WHERE `ContentID` = '".$id."';";
		}else{
	                $sql = "INSERT INTO `content` (`ContentID`, `Name`, `Content`, `ContentCat`) VALUES (NULL, '".$name."', '".$content."', '".$this->core->item."');";
		}		
echo $sql;
                $run = $this->core->database->doInsertQuery($sql);

		$this->core->redirect($this->core->item, "manage", NULL);
	}

	function showItem($id) {

		$sql = "SELECT * FROM `content` WHERE `ContentID` = $id";
		$run = $this->core->database->doSelectQuery($sql);

		echo '<div class="welcomecontainers">';

		if ($this->core->role == 1000) {
			echo '<div style="float: right;"><a href="' . $this->core->conf['conf']['path'] . '/item/edit/' . $id . '">edit</a></div>';
		}

		while ($row = $run->fetch_row()) {
			echo ' <h2>' . $row[1] . '</h2>';
			echo ' <p>' . $row[2] . '</p>';
		}

		echo '</div>';

	}


	function addItem($item) {

		if ($this->core->role == 1000) {
			echo "<script type=\"text/javascript\">
				Aloha.ready( function() {
					var $ = Aloha.jQuery;
					$('.editable').aloha();
				});
			</script>";
		}

		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
		$select = new optionBuilder($this->core);
		$manager = $select->showUsers("100", null);
				
		include $this->core->conf['conf']['formPath'] . "additem.form.php";

		echo '</div>';

	}

	function editItem($id) {
		$sql = "SELECT * FROM `content` WHERE `ContentID` = $id";

		$run = $this->core->database->doSelectQuery($sql);

		if ($this->core->role == 1000) {
			echo "<script type=\"text/javascript\">
				Aloha.ready( function() {
					var $ = Aloha.jQuery;
					$('.editable').aloha();
				});
			</script>";
		}

		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
		$select = new optionBuilder($this->core);
		$manager = $select->showUsers("100", null);
		
		while ($row = $run->fetch_row()) {
			$name =  $row[1];
			$content = $row[2];

			include $this->core->conf['conf']['formPath'] . "edititem.form.php";
		}

		echo '</div>';

	}
}

?>

