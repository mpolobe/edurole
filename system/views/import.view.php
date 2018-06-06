<?php
class import {

	public $core;
	public $view;
	public $item = NULL;

	public function configView() {
		$this->view->header = TRUE;
		$this->view->footer = TRUE;
		$this->view->menu = TRUE;
		$this->view->javascript = array('jquery.ui.datepicker');
		$this->view->css = array();

		return $this->view;
	}

	public function buildView($core) {
		$this->core = $core;
	}
	

	private function hashPassword($username, $password){
		$passwordHashed = hash('sha512', $password . $this->core->conf['conf']['hash'] . $username);
		return $passwordHashed;
	}

	public function completeImport($item) {
		$sql = "SELECT * FROM `access` WHERE CHAR_LENGTH(Password) < 64";
		
		$run = $this->core->database->doSelectQuery($sql);

		echo '<div class="toolbar"><a href="' . $this->core->conf['conf']['path'] . '/fees/add/'.$item.'">Import staff</a></div>';

		echo '<table width="768" height="" border="0" cellpadding="5" cellspacing="0">
		<tr>
		<td bgcolor="#EEEEEE" width="180px"><b>Username</b></td>
		<td bgcolor="#EEEEEE">Password</td>
		</tr>';

		$i = 0;
		while ($fetch = $run->fetch_row()) {
			$username = $fetch[1];
			$password = $fetch[3];

			echo' <tr>
			<td><strong>' . $username . '</strong></td>
			<td>
			' . $password . '
			</td>
			</tr>';
			
			$hash = $this->hashPassword($username, $password);
			$sql = "UPDATE `access` SET  `Password` =  '$hash' WHERE  `Username` = '$username'";
			$this->core->database->doInsertQuery($sql);
		}

		echo'</table>';
	}
}
?>
