<?php

class itemManager {

	function showItem($id) {

		$sql = "SELECT * FROM `content` WHERE `ContentID` = $id";
		$run = $this->core->database->doSelectQuery($sql);

		echo '<div class="welcomecontainers">';

		if ($this->core->role == 1000) {
			echo '<div style="float: right;"><a href="' . $this->core->conf['conf']['path'] . 'item/edit/' . $id . '">edit</a></div>';
		}

		while ($row = $run->fetch_row()) {
			echo ' <h2>' . $row[1] . '</h2>';
			echo ' <p>' . $row[2] . '</p>';
		}

		echo '</div>';

	}

}

?>
