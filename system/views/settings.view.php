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

			echo'<div class="easymencontainer">
			<div class="label" style="width:300px;"><b>Original</b></div>
			<div class="label" style="width:300px;">Your translation</div>';

			if($run->num_rows == 0){
                                $run->close();
                        } else {

                                while ($run->fetch()) {
					echo'<div class="linecontainer">
					<div class="label" style="width:300px;"><b><input type="text" class="submit" name="original[' . $id . ']" style="width: 300px;" value="' . $phrase . '"></b></div>
					<div class="label" style="width:300px;"><input type="text" class="submit" name="translation[' . $id . ']" style="width: 300px;" value="' . $translatedphrase . '"> </div>
					</div>';
				}
			}

			echo'</div>';

			echo'<div class="easymencontainer"><input type="submit" class="submit" value="Save settings" /></div>';
		}		
	}

	public function translationSettings() {
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

		echo'<form id="save" name="savesettings" method="POST" action="'.$this->core->conf['conf']['path'].'/settings/save/settings">';
		echo'<div class="easymencontainer"><input type="submit" class="submit" value="Save settings" /></div>';

		$dir = 'system/views';
		if ($handle = opendir($dir)) {
			
			$files = scandir($dir);

			foreach ($files as $file) {

				if ($file != "." && $file != ".." && $file != "settings.view.php") {
					include $file;
					$name = explode(".",$file);
					$class = $name[0];

					$functions = get_class_methods($class);

					echo '<div class="easymencontainer"><h3>' . ucwords($class) . '</h3>';

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

								echo	'<div  style="width:50px; float:left; clear:left;">&nbsp;</div>
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
					$modules++;
				}
			}
			closedir($handle);
		}

		echo'<div class="easymencontainer"><input type="submit" class="submit" value="Save settings" /></div>';
		echo'<div class="easymencontainer"><div class="label"  style="width:170px;">Installed modules: <b>' . $modules . '</b> <br>Total actions: <b>' . $actions . '</b></div></div></form>';
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
				$sql .= "UPDATE `functions` 
					  SET  `FunctionTitle` =  '". $setting["title"] ."',  
					  `FunctionDescription` =  '". $setting["description"] ."',
					  `FunctionRequiredPermissions` =  '". $setting["role"] ."',
					  `FunctionMenuVisible` =  '". $setting["menu"] ."'
					  WHERE  `functions`.`ID` = ". $setting["id"] ."; ";
			}

			$this->core->database->mysqli->multi_query($sql);

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
				$sql .= "UPDATE `functions` 
			 	 SET `FunctionRequiredElements` =  '". $setting ."'
				 WHERE  `functions`.`ID` = ". $id ."; ";
			}

			$this->core->database->mysqli->multi_query($sql);

			$this->core->redirect("settings", "functions", NULL);

		}
	}

	public function permissionsSettings() {
		$this->viewMenu();

		include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";

		$select = new optionBuilder($this->core);

		$sql = "SELECT * FROM `functions` ORDER BY `Class`, `Function`";
		$run = $this->core->database->doSelectQuery($sql);

		$i = 0;

		echo'<form id="save" name="institutionname" method="POST" action="'.$this->core->conf['conf']['path'].'/settings/save/permissions">
		<div class="easymencontainer">
		<input type="submit" class="submit" value="Save settings" />';

		$modules = 0;
		$actions = 0;

		$current = NULL;

		$rolesList = $select->showMultipleRoles();

		while ($fetch = $run->fetch_row()) {

			$roles = $select->buildSelect($rolesList, $fetch[4]);

			if($current != $fetch[1]){
				$class=ucwords($fetch[1]);
				echo '</div><div class="easymencontainer">';

				echo'<div class="label"  style="width:70px;"><h3>' . $class . '</h3></div>
				<div class="label"  style="width:120px;"><i>Title</i></div>
				<div class="label" style="width:190px;"><i>Description</i></div>
				<div class="label" style="width:130px;"><i>Permission required</i></div>
				<div class="label" style="width:40px;"><i>Menu #</i></div>';

				$modules++;
			} else {
				$class="";
			}

			$menu = $fetch[9];
			if(empty($menu)){
				$menu = "";
			}

			echo'  <div class="label"  style="width:70px;"><b>' . ucwords($fetch[2]) . '</b></div>
				<div class="label" style="width:120px;"><input type="text" class="submit" name="titles[' . $fetch[0] . ']" style="width: 120px;" value="' . $fetch[7] . '"> </div>
				<div class="label" style="width:190px;"><input type="text" class="submit" name="descriptions[' . $fetch[0] . ']" style="width: 180px;" value="' . $fetch[8] . '"> </div>
				<div class="label" style="width:130px;"><select style="width:150px;" class="submit" name="roles[' . $fetch[0] . ']"> ' . $roles . ' </select></div>
				<div class="label" style="width:30px;"><input type="text" class="submit" name="menu[' . $fetch[0] . ']" style="width: 20px;" value="' . $menu . '"> </div>';

			$current = $fetch[1];
			$actions++;
		}

			echo'</div><div class="easymencontainer"><input type="submit" class="submit" value="Save settings" /><div class="label"  style="width:170px;">Installed modules: <b>' . $modules . '</b> <br>Total actions: <b>' . $actions . '</b></div>
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
			<form id="save" name="institutionname" method="POST" action="/settings/save">
			<h2>Institutional identity</h2>
			<p><input type="hidden" name="id" value="view-information">
			<div class="padding"><div class="label">Name of institution</div> <input type="text" name="institutionname" class="submit" value="' . $fetch[2] . '"/><br>
			</div>';
				$i++;
			} else {
				echo '<div class="padding"><div class="label">Website of institution</div> <input type="text" name="institutionwebsite" class="submit" value="' . $fetch[2] . '" />
			</div>
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
                            <form id="save" name="paymenttypes" method="POST" action="/institution/save">
                            <h2>Payment types</h2>
                            <p><input type="hidden" name="id" value="view-information">
                            <div class="padding"><div class="label">Payment Type ' . $i . '</div> <input type="text" name="paymenttype' . $i . '" class="submit" value="' . $fetch[2] . '"/><br>
                            </div>';
				$i++;
			} else {
				echo '<div class="padding"><div class="label">Payment Type ' . $i . '</div> <input type="text" name="paymenttype' . $i . '" class="submit" value="' . $fetch[2] . '"/><br>
                            </div>';
				$i++;
			}

		}
		echo '<div class="label"> </div>
            <input type="submit" class="submit" value="Save settings" />
            </form></p>
            </div>';
	}

	private function admissionFlow() {

		$sql = "SELECT * FROM `settings` WHERE  `Name` LIKE 'AdmissionLevel%' ORDER BY `Name` ASC";

		$run = $this->core->database->doSelectQuery($sql);

		$n = 1;
		$i = 0;

		while ($fetch = $run->fetch_row()) {

			if ($i == 0) {
				echo '<div class="easymencontainer">
                            <form id="save" name="save" method="get" action="">
                            <h2>Admission flow</h2>
                            <p><input type="hidden" name="id" value="view-information">
                            <div class="padding"><div class="label">Admission step ' . $n . '</div>
                            <input type="text" name="admissionsteps' . $n . '" class="submit"  value="' . $fetch[2] . '" size="40"/><br></div>';
				$i++;
			} else {
				echo '<div class="padding"><div class="label">Admission step ' . $n . '</div>
                            <input type="text" name="admissionsteps' . $n . '" class="submit"  value="' . $fetch[2] . '" size="40"/><br></div>';
				$n++;
			}
		}

		echo '<div class="label"> </div>
            <input type="submit" class="submit" value="Save settings" />
            </form></p>
            </div>';

	}

}

?>
