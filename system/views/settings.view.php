<?php
class settings {

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

	function viewMenu() {
		if($this->core->role == 1000){
			echo '<div class="toolbar">
			<a href="' . $this->core->conf['conf']['path'] . '/settings/permissions">Access Management</a>
			<a href="' . $this->core->conf['conf']['path'] . '/settings/functions">Block Management</a>
			<a href="' . $this->core->conf['conf']['path'] . '/settings/translation">Translation Management</a>
			<a href="' . $this->core->conf['conf']['path'] . '/settings/update">Update system</a>
			</div>';
		}
	}

	public function deleteSettings($item) {
		$sql = 'DELETE FROM `functions` WHERE `ID` = "' . $item . '"';
                $run = $this->core->database->doInsertQuery($sql);

                $this->core->redirect("settings", "functions", NULL);
	}

	public function translateSettings($language) {

		$this->viewMenu();

		if (isset($language)) {

                        $sql = 'SELECT ts.ID, ts.LanguageID, ts.Phrase, tr.TranslatedPhrase FROM `translation` as `ts`
				LEFT JOIN `translation` as `tr` ON `tr`.`LanguageID` = ?
                                WHERE `ts`.`LanguageID` = 0';

                        $run = $this->core->database->prepareQuery($sql);
                        $run->bind_param('i', $language);
                        $run->execute();

                        $run->bind_result($id, $languageid, $phrase, $translatedphrase);
                        $run->store_result();

			echo'<form id="save" name="savetranslation" method="POST" action="'.$this->core->conf['conf']['path'].'/settings/save/translation/'.$language.'">';
			echo'<div class="easymencontainer"><input type="submit" class="submit" value="Save settings" /></div>';

			echo'<div class="easymencontainer"><div class="label" style="width:50px;"> </div>
			<div class="label" style="width:300px;"><b>Original</b></div>
			<div class="label" style="width:300px;">Your translation</div>';
			$i=1;

			if($run->num_rows == 0){
                                $run->close();
                        } else {

                                while ($run->fetch()) {
					echo'<div class="linecontainer">
					<div class="label" style="width:30px;"><b> '.$i.' </b></div><div class="label" style="width:300px;"> <input type="text" class="submit" name="original[' . $id . ']" style="width: 300px;" value="' . $phrase . '"></div>
					<div class="label" style="width:300px;"><input type="text" class="submit" name="translation[' . $id . ']" style="width: 300px;" value="' . $translatedphrase . '"> </div>
					</div>';
					$i++;
				}
			}

			echo'</div>';

			echo'<div class="easymencontainer"><input type="submit" class="submit" value="Save settings" /></div>';
		}
	}

	public function translationSettings() {
		$this->viewMenu();

		$sql = 'SELECT ID, Language FROM `languages`';

		$run = $this->core->database->prepareQuery($sql);
                        $run->execute();
                        $run->bind_result($id, $language);
                        $run->store_result();


                if($run->num_rows == 0){
                	$run->close();
                } else {
			echo '<div class="toolbar">';

 			while ($run->fetch()) {
				echo'<a href="' . $this->core->conf['conf']['path'] . '/settings/translate/'.$id.'">'.$language.'</a>';
                        }

			echo'</div>';
		}
	}

	public function updateSettings() {
		$this->viewMenu();

		include $this->core->conf['conf']['classPath'] . "update.inc.php";

		$update = new update($this->core);

		$update->updateClient();
	}

	public function functionsSettings() {
		$this->viewMenu();

		$out = array();
		$modules = 0;
		$actions = 0;

		$dir = 'system/views';
		if ($handle = opendir($dir)) {

			$files = scandir($dir);

			foreach ($files as $file) {

				if ($file != "." && $file != "..") {
					include_once $file;
					$name = explode(".",$file);
					$class = $name[0];

					$functions = get_class_methods($class);

					echo'<form name="savesettings" method="POST" action="'.$this->core->conf['conf']['path'].'/settings/save/settings">';
					echo '<div class="easymencontainer"><h3>' . ucwords($class) . ' ('.$file.')</h3>';
	

					$uclass = ucwords($class);
					$sql = "SELECT * FROM `functions` WHERE `Class` = '$uclass'";
					$run = $this->core->database->doSelectQuery($sql);

					while ($fetch = $run->fetch_row()) {
						$curset = FALSE;

						$func = $fetch[2];
						$funcID = $fetch[0];

						foreach ($functions as $i => $function) {
							$view = str_replace(ucwords($class), "", $function);

							if($func == $view){

								if($function == "configView" || $function == "buildView" || $function == "viewMenu"){
									continue;
								}

								$url = $this->core->conf['conf']['path'] . '/' . $class . '/' . $view;
								$uview = ucwords($view);

								if($run->num_rows > 0){
									$cache = "<b>CACHED</b>";
								} else {
									$cache = "UNKNOWN";
								}

								$funcSettings = json_decode($fetch[6]);
								$setlist = "";

								$setVars = array();
								foreach($funcSettings as $key => $val){
									$setVars[$key] = $val;
								}

								echo '<input type="hidden" name="functions['.$funcID.']" value="'.$funcID.'">';

								$checkboxes = array("header", "menu", "breadcrumb", "title", "description", "footer");

								foreach($checkboxes as $set){

									if(isset($setVars[$set])){
										$val = $setVars[$set];
									}else{
										$val = FALSE;
									}

									if($val == TRUE){
										$check = "checked";
									} else {
										$check = "";
									}

									$setlist .= '<input class="submit" type="checkbox" name="'.$set.'['.$funcID.']" value="true" '.$check.'> ' . ucwords($set);
								}


								$hiddensettings = array("javascript", "css");

								foreach($hiddensettings as $set){
									if(isset($setVars[$set])){
                                                                                $val = urlencode(json_encode($setVars[$set]));
                                                                        }

									echo '<input type="hidden" name="'.$set.'['.$funcID.']" value="'.$val.'">';
								}


                                                                echo    '<div  style="width:50px; float:left; clear:left;">&nbsp;</div>
                                                                <div style="width:150px; float:left;"><a href="'.$url.'"><b>' . $uview . '</b></a></div>
                                                                <div  style="width:430px; float:left;"> '.$setlist.'</div>';

								unset($functions[$i]);

								$curset = TRUE;
								$actions++;
							}else {
								continue;
							}

						}

						if($curset == FALSE){
							echo	'<div  style="width:50px; float:left; clear:left;">&nbsp;</div>
							<div style="width:150px; float:left;"><a href="'.$url.'"><b>' . $uview . '</b></a></div>
							<div  style="width:430px; float:left;"> ORPHANED CONFIG <a href="'.$this->core->conf['conf']['path'].'/settings/delete/'.$funcID.'">PLEASE DELETE</a> </div>';
						}

						$current = $fetch[1];
						$actions++;
					}

					foreach ($functions as $function) {

							if($function == "configView" || $function == "buildView" || $function == "viewMenu"){
								continue;
							}

							if(is_callable(array($class, $function))){
								$pub =  '<b>PUBLIC</b>';
							} else {
								$pub =  '';
							}

							$view = str_replace(ucwords($class), "", $function);
							$url = $this->core->conf['conf']['path'] . '/' . $class . '/' . $view;



							$uview = ucwords($view);


							if($run->num_rows > 0){
								$cache = "<b>CACHED</b>";
							} else {
								$cache = "UNKNOWN";
							}

								echo	'<div  style="width:50px; float:left; clear:left;">&nbsp;</div>
								<div style="width:150px; float:left;"><a href="'.$url.'"><b>' . $uview . '</b></a></div>
								<div  style="width:230px; float:left;"> NO CONFIGURATION YET - RUN FIRST</div>';

							$actions++;
					}


					echo '</div>';
					echo'<div class="easymencontainer" style="margin-top: 0px; border-top: 0px;"><input type="submit" class="submit" value="Save settings" /></div></form>';
					$modules++;
				}
				

			}
			closedir($handle);

		}
		echo'<div class="easymencontainer"><div class="label"  style="width:170px;">Installed modules: <b>' . $modules . '</b> <br>Total actions: <b>' . $actions . '</b></div></div>';


	}

	public function saveSettings($item) {
		if($item == "permissions"){

			$functions = array();

			foreach(array_keys($_POST['titles']) as $id){
				$functions["$id"]["id"] = $id;
				$functions["$id"]["title"] = $_POST['titles'][$id];
				$functions["$id"]["description"] = $_POST['descriptions'][$id];
				$functions["$id"]["role"] = $_POST['roles'][$id];
				$functions["$id"]["menu"] = $_POST['menu'][$id];
			}

			$sql = "";

			foreach($functions as $setting){
				
				$menu = $setting["menu"];
				if($menu == ""){
						$menu = "0";
				}
				
				$sql = "UPDATE `functions`
					  SET  `FunctionTitle` =  '". $setting["title"] ."',
					  `FunctionDescription` =  '". $setting["description"] ."',
					  `FunctionRequiredPermissions` =  '". $setting["role"] ."',
					  `FunctionMenuVisible` =  '". $menu ."'
					  WHERE  `functions`.`ID` = ". $setting["id"] ."";
			echo $sql;
				$this->core->database->doInsertQuery($sql);
			}

			$this->core->redirect("settings", "permissions", NULL);

		}else if($item == "settings"){

			$functions = array();
			$checkboxes = array("header", "menu", "breadcrumb", "title", "description", "footer");

			foreach(array_keys($_POST['functions']) as $id){
				foreach($checkboxes as $set){
					if(isset($_POST[$set][$id])){
						$data = $_POST[$set][$id];
					} else {
						$data = FALSE;
					}

					if($data == "true"){
						$functions[$id]["$set"] = TRUE; 
					}else{
						$functions[$id]["$set"] = FALSE; 
					}
				}
			}


			$checkboxes = array("javascript", "css");

			foreach(array_keys($_POST['functions']) as $id){
				foreach($checkboxes as $set){
					$functions[$id]["$set"] = json_decode(urldecode($_POST[$set][$id])); 
				}
			}


			$keys = array_keys($functions);

			foreach($keys as $id){
				$json[$id] = json_encode($functions[$id]);
			}

			$sql = "";

			foreach($json as $id => $setting){
				$sql = "UPDATE `functions` 
			 	 SET `FunctionRequiredElements` =  '". $setting ."'
				 WHERE  `functions`.`ID` = ". $id ."; ";
				echo $sql;
				$this->core->database->doInsertQuery($sql);
			}


			$this->core->redirect("settings", "functions", NULL);

		}else if($item == "institution"){
			$institutionname = $this->core->cleanPost["institutionname"];
			$institutionwebsite = $this->core->cleanPost["institutionwebsite"];

			$sql = "UPDATE `settings` SET `Value` = '".$institutionname."' WHERE `settings`.`Name` = 'InstitutionName';";
			$sql .= "UPDATE `settings` SET `Value` = '".$institutionwebsite."' WHERE `settings`.`Name` = 'InstitutionWebsite';";

			$this->core->database->mysqli->multi_query($sql);

	                $this->core->redirect("settings", "manage", NULL);

		}else if($item == "payments"){
			$sql ="";
			$i=1;

			foreach($this->core->cleanPost["paymenttypes"] as $paymenttype){
				$sql .= "UPDATE `settings` SET `Value` = '".$paymenttype."' WHERE `settings`.`Name` = 'PaymentType".$i."';";
				$i++;
			}

			$this->core->database->mysqli->multi_query($sql);

	                $this->core->redirect("settings", "manage", NULL);

		}else if($item == "admission"){
			$sql ="";
			$i=1;

			foreach($this->core->cleanPost["admissionlevels"] as $admissionlevel){
				$sql .= "UPDATE `settings` SET `Value` = '".$admissionlevel."' WHERE `settings`.`Name` = 'AdmissionLevel".$i."';";
				$i++;
			}

			$this->core->database->mysqli->multi_query($sql);
	                $this->core->redirect("settings", "manage", NULL);

		}
	}

	public function permissionsSettings() {
		$this->viewMenu();

		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";
		$select = new optionBuilder($this->core);
		$rolesList = $select->showPermissions();

		$sql = "SELECT * FROM `functions` ORDER BY `Class`, `Function`";
		$run = $this->core->database->doSelectQuery($sql);

		$i = 0;

		echo'<form id="save" name="institutionname" method="POST" action="'.$this->core->conf['conf']['path'].'/settings/save/permissions">
		<div class="easymencontainer"><input type="submit" class="submit" value="Save settings" />';

		$modules = 0;
		$actions = 0;
		$cc = 0;

		$current = NULL;

		$classes = array();
		$functions = array();

		$dir = 'system/views';
		if ($handle = opendir($dir)) {

			$files = scandir($dir);

			foreach ($files as $file) {

				if ($file != "." && $file != ".." && $file != "settings.view.php" && $file != "error.view.php" && $file != "menu.inc.php") {

					include_once $file;
					$name = explode(".",$file);
					$classes[] = ucwords($name[0]);

					$functionlist = get_class_methods($name[0]);

					$functionarray = array();
					foreach($functionlist as $function){
						if($function == "configView" || $function == "buildView" || $function == "viewMenu"){
							continue;
						}

						$functionarray[] = str_replace(ucwords($name[0]), "", $function);
					}

					$functions[$name[0]] = $functionarray;
					$cc++;

				}
			}

					$functionlist = get_class_methods("settings");
					foreach($functionlist as $function){
						if($function == "configView" || $function == "buildView" || $function == "viewMenu"){
							continue;
						}

						$functionarray[] = str_replace(ucwords("settings"), "", $function);
					}
					$classes[] = "Settings";
					$functions["settings"] = $functionarray;

		}

		while ($fetch = $run->fetch_row()) {

			$roles = $select->buildSelect($rolesList, $fetch[4]);

			if($current != $fetch[1]){
				$class=ucwords($fetch[1]);

				if(!in_array($class, $classes)){
					continue;
				} else {
					$functionlist = $functions[$fetch[1]];
				}

				echo '</div><div class="easymencontainer">';

                                echo'<div style="clear:both"><div class="label"  style="width:70px;"><h3>' . $class . '</h3></div>
                                <div class="label"  style="width:120px;"><i>Title</i></div>
                                <div class="label" style="width:290px;"><i>Description</i></div>
                                <div class="label" style="width:130px;"><i>Permission required</i></div>
                                <div class="label" style="width:40px;"><i>Menu #</i></div></div>';

				$modules++;
			} else {
				$class="";
			}

			$menu = $fetch[9];
			if(empty($menu)){
				$menu = "";
			}

			$function = $fetch[2];
			$exists = FALSE;

			foreach($functionlist as $functionfromdb){
				if($function == $functionfromdb){
					$exists = TRUE;
				}
			}

			if ($exists){
				echo'  <div style="clear:both"><div class="label"  style="width:70px;"><b>' . ucwords($fetch[2]) . '</b></div>
                                <div class="label" style="width:120px;"><input type="text" class="submit" name="titles[' . $fetch[0] . ']" style="width: 120px;" value="' . $fetch[7] . '"> </div>
                                <div class="label" style="width:290px;"><input type="text" class="submit"name="descriptions[' . $fetch[0] . ']" style="width: 280px;" value="' . $fetch[8] . '"> </div>
                                <div class="label" style="width:130px;"><select style="width:130px;" class="submit" name="roles[' . $fetch[0] . ']"> ' . $roles . ' </select></div>
                                <div class="label" style="width:50px;"><input type="text" class="submit" name="menu[' . $fetch[0] . ']" style="width: 50px;" value="' . $menu . '"> </div> </div>';
			} else{
				continue;
			}

			$current = $fetch[1];
			$actions++;
		}

		echo'</div><div class="easymencontainer"><input type="submit" class="submit" value="Save settings" />
		<div class="label"  style="width:170px;">Installed modules: <b>' . $modules . '</b> 
		<br>Total number of functions: <b>' . $actions . '</b></div>
		</form></div>';

	}

	function manageSettings() {
		$this->viewMenu();

		$this->institutionName();
		$this->paymentTypes();
		$this->admissionFlow();
	}

	private function institutionName() {
		$sql = "SELECT * FROM `settings` WHERE `Name` = 'InstitutionName' OR `Name` = 'InstitutionWebsite'  ORDER BY `Name`";

		$run = $this->core->database->doSelectQuery($sql);
		$i = 0;

		while ($fetch = $run->fetch_row()) {

			if ($i == 0) {
				echo '<div class="easymencontainer">
				<form id="save" name="institutionname" method="POST" action="'.$this->core->conf['conf']['path'].'/settings/save/institution/">
				<h2>Institutional identity</h2>
				<p><input type="hidden" name="id" value="view-information">
				<div class="padding"><div class="label">Name of institution</div> <input type="text" name="institutionname" class="submit" value="' . $fetch[2] . '"/><br>
				</div>';
				$i++;
			} else {
				echo '<div class="padding"><div class="label">Website of institution</div> <input type="text" name="institutionwebsite" class="submit" value="' . $fetch[2] . '"></div>
				<div class="label"> </div>
				<input type="submit" class="submit" value="Save settings" />
				</form></p>
				</div>';
			}
		}

	}

	private function paymentTypes() {

		$sql = "SELECT * FROM `settings` WHERE `Name` LIKE 'PaymentType%' ORDER BY `Name`";

		$run = $this->core->database->doSelectQuery($sql);

		$i = 1;
		while ($fetch = $run->fetch_row()) {

			if ($i == 1) {
				echo '<div class="easymencontainer">
                         	<form id="save" name="paymenttypes" method="POST" action="'.$this->core->conf['conf']['path'].'/settings/save/payments/">
                            	<h2>Payment types</h2>
                            	<p><input type="hidden" name="id" value="view-information">';
			}

			echo'<div class="padding"><div class="label">Payment Type ' . $i . '</div> <input type="text" name="paymenttypes[]" class="submit" value="' . $fetch[2] . '"/><br></div>';
			$i++;
		}
		echo '<div class="label"> </div>
            	<input type="submit" class="submit" value="Save settings" />
            	</form></p>
            	</div>';
	}

	private function admissionFlow() {

		$sql = "SELECT * FROM `settings` WHERE  `Name` LIKE 'AdmissionLevel%' ORDER BY `Name` ASC";

		$run = $this->core->database->doSelectQuery($sql);

		$i = 1;

		while ($fetch = $run->fetch_row()) {

			if ($i == 1) {
				echo '<div class="easymencontainer">
                        	<form id="save" name="save" method="POST" action="'.$this->core->conf['conf']['path'].'/settings/save/admission/">
                        	<h2>Admission flow</h2>
                        	<p><input type="hidden" name="id" value="view-information">';
			}

			echo '<div class="padding">
				<div class="label">Admission step ' . $i . '</div>
				<input type="text" name="admissionlevels[]" class="submit"  value="' . $fetch[2] . '" size="40"/><br>
			      </div>';
			$i++;

		}

		echo '<div class="label"> </div>
            	<input type="submit" class="submit" value="Save settings" />
            	</form></p>
            	</div>';
	}

}

?>
