<?php
class menuConstruct {

	public $core;

	function __construct($core) {
		$this->core = $core;
	}

	public function buildMainMenu($menudata = FALSE) {

		if($menudata == FALSE){
			$menu = NULL;
		} else if(isset($this->core->role)){
			$menu = $this->fillMainMenu();
		}

		$menu = $this->menuContainer($menu);
	
		return $menu;
	}

	public function menuContainer($menu) {

		$container = '<div class="menu">
			<ul class="nav side-nav">
					<li class="userinfo">Current user: <strong>' . $this->core->username . '</strong></li>';

		$container .= $menu;
		$container .= '</ul>
				</div>';

		$container .= '<div id="page-wrapper">';
		return $container;
	}

	private function fillMainMenu() {

		$menu = NULL;

		if($this->core->role != 1000){
			$sql = "SELECT *
			FROM `functions-permissions`, `functions`, `roles`
			WHERE `functions`.`FunctionMenuVisible` > 0
			AND `functions-permissions`.RoleID = " . $this->core->role ."
			AND `functions-permissions`.FunctionID = `functions`.ID
			AND `roles`.ID = " . $this->core->role ."
			ORDER BY `functions`.`FunctionMenuVisible` ASC";
		}else{
			$sql = "SELECT *, `PermissionDescription` as RoleName  
			FROM `permissions`, `functions`
			WHERE `functions`.`FunctionRequiredPermissions` = `permissions`.`ID`
			AND `permissions`.`RequiredRoleMin` LIKE '%'
			AND `permissions`.`RequiredRoleMax` NOT IN (2,3,4,5,7,8,9,10)
			AND `functions`.`FunctionMenuVisible` > 0
			ORDER BY `permissions`.`RequiredRoleMin`, `functions`.`FunctionRequiredPermissions`,  `functions`.`FunctionMenuVisible`";
		}

		$run = $this->core->database->doSelectQuery($sql);

		if ($run->num_rows == 0) {
			return $menu;
		}


		$currentSegment = NULL;
		$i=0;

		while ($fetch = $run->fetch_assoc()) {

			$segmentName = $fetch['RoleName'];
			$pageRoute = $fetch['Class'] . '/' . $fetch['Function'];
			$pageName = $fetch['FunctionTitle'];

			if (!isset($currentSegment)) {

				$menu .= $this->segmentHeader($segmentName);

			} else if ($segmentName != $currentSegment) {

				$i++;
				$menu .= '</ul></div>';
				$menu .= $this->segmentHeader($segmentName, $i);

			}


			if ($pageName == "Message Inbox") {
				$uid = $this->core->userID;
				$sql = "SELECT `helpdesk`.ID as MID FROM `helpdesk`
				WHERE `RecipientID` LIKE '$uid' AND `Read` = 0
				OR `RecipientID` LIKE 'ALL'
				ORDER BY `MID` DESC";

				$runx = $this->core->database->doSelectQuery($sql);
				$countm = $runx->num_rows;

				$menu .= '<li class="menu" '.$style.'><a href="' . $this->core->conf['conf']['path'] . '/' . $pageRoute . '">' . $pageName . '<div class="mailcount"><b>'.$countm.'</b></div> </a></li>';
			} else {
				$menu .= $this->pageItem($pageRoute, $pageName);
			}

			$currentSegment = $segmentName;

		}

		$menu .= '</div>';

		return $menu;

	}

	public function segmentHeader($segmentName, $count) {
		if(strlen($segmentName) > 25){
			$segmentName = substr($segmentName, 0, 25) . "...";
		}
		$id =  rand(1000, 9999);

		if($count == 0 || $count == 1){
			$expand = 'open';
		} 

		if($this->core->role < 1000){

		}

		$menu = '<div class="dropdown  '.$expand.'" ><button class="btn btn-default dropdown-toggle" style="border-radius: 0px; margin-left: 10px; width: 100%; text-align: left;" type="button" id="dropdownMenu'.$id.'" data-toggle="dropdown" aria-haspopup="true" ><strong>' . $segmentName . '</strong> <span class="caret"></span>
			</button><ul class="dropdown-menu" aria-labelledby="dropdownMenu'.$id.'" style="margin-left: 10px; width: 100%; text-align: left; position: relative;">';

		return $menu;
	}

	public function pageItem($pageRoute, $pageName) {

		if($pageName == 'Logout'){ 
			if($this->core->role == 1000){
				$menu .= '<li class="menu" '.$style.' id="chatopen"><a href="#">Direct Chat</a></li>';
			}
			$style='class="bold"';
			$menu .= '<li role="separator" class="divider"></li>';
		}
		$menu .= '<li class="menu" '.$style.'><a href="' . $this->core->conf['conf']['path'] . '/' . $pageRoute . '">' . $pageName . '</a></li>';
		return $menu;
	}

}
?>