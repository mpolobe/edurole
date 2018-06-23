<?php
class permits {

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

	public function registerPermits($uid){
		$uid = $this->core->item;
		$otp = rand(11111,99999);
		$function = $this->core->subitem;

		$sql = "INSERT INTO `permits` (`ID`, `UserID`, `FunctionID`, `OTP`, `Handout`, `Expire`) VALUES (NULL, '$uid', '$function', '$otp', NOW(), '0');";

		$this->core->database->doInsertQuery($sql);

		echo '<span class="successpopup">The One Time Password is '.$otp.'</span>';

	}


	public function enterPermits() {
			
		include $this->core->conf['conf']['formPath'] . "enterpermit.form.php";

	}


	public function usePermits() {
		$uid = $this->core->userID;
		$otp = $this->core->cleanPost['otp'];


		$sql = "SELECT * FROM `functions`, `permits` WHERE `permits`.FunctionID = `functions`.ID AND `permits`.OTP = '$otp' AND `UserID` = '$uid' AND `Expire` = '0';";
		$rund = $this->core->database->doSelectQuery($sql);

		$sql = "UPDATE `permits` SET `Expire` = '1' WHERE `UserID` = '$userid' AND `OTP` = '$otp';";
		$this->core->database->doInsertQuery($sql);

		
		while ($row = $rund->fetch_assoc()) {
			$function = $row['Function'];
			$classname = $row['Class'];
			$uclass = ucfirst($classname);

			

			$ffunction = $function.$uclass;

			include $this->core->conf['conf']['viewPath'] . "$classname.view.php";
			$class = new $classname();
			$class->buildView($this->core);
			$class->$ffunction($uid, TRUE);
		}



	}
}
?>
