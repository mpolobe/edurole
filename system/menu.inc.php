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

		$container = '<div class="collapse navbar-collapse  navbar-ex1-collapse">
			<ul class="nav navbar-nav side-nav">
					<li class="userinfo">Current user: <strong>' . $this->core->username . '</strong></li>';

		$container .= $menu;
		$container .= '</ul><div id="page-wrapper">';
		
		return $container;
	}

	private function fillMainMenu() {

		$menu = NULL;
		
		if($this->core->role < 1000){
			$sql = "SELECT * 
			FROM `permissions`, `functions` 
			WHERE `functions`.`FunctionRequiredPermissions` = `permissions`.`ID`
			AND `functions`.`FunctionMenuVisible` > 0
			AND `permissions`.`RequiredRoleMin` <= " . $this->core->role . "
			AND " . $this->core->role . " <= `permissions`.`RequiredRoleMax`
			ORDER BY `permissions`.`RequiredRoleMin`, `functions`.`FunctionRequiredPermissions`,  `functions`.`FunctionMenuVisible`";
			
		}else{
			$sql = "SELECT * 
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

		while ($fetch = $run->fetch_assoc()) {

			$segmentName = $fetch['PermissionDescription'];
			$pageRoute = $fetch['Class'] . '/' . $fetch['Function'];
			$pageName = $fetch['FunctionTitle'];

			if (!isset($currentSegment)) {

				$menu .= $this->segmentHeader($segmentName);

			} else if ($segmentName != $currentSegment) {

				$menu .= '</li>
				<li class="menubar">';

				$menu .= $this->segmentHeader($segmentName);

			}


			if ($segmentName == "Virtual Learning Environment") {

				$study = NULL;
				while ($_SESSION['saobjects']) {
					$program = $fetch[2];

					if ($study != $fetch[1]) {
						$study = $fetch[1];
						$school = $fetch[3];

						$menu .= '<li class="menu"><a href="' . $this->core->conf['conf']['path'] . '/vle/school/1"> ' . $school . '</a></li>
							<li class="menu"><a href="' . $this->core->conf['conf']['path'] . '/vle/school/1">
								<img src="' . $this->core->fullTemplatePath . '/images/expand.gif"> ' . $study . '</a>
							</li>';
					}

					$menu .= '<li class="menu"><div class="indent"><a href="' . $this->core->conf['conf']['path'] . 'vle&view=school&id=1"><img src="templates/default/images/expand.gif"> ' . $program . '</a></div></li>';
				}

			} 

			if ($pageRoute == "mail/show") {

				if ($this->core->conf['conf']['mailEnabled'] == TRUE) {

					$pageName = 'Personal Mailbox <div class="mailcount"><b><img src="'.$this->core->fullTemplatePath .'/images/mail.gif"></b></div>'.
					'<script type="text/javascript">' . "\n" .
					'	jQuery(document).ready(function(){' . "\n" .
					'		url = \''.$this->core->conf['conf']['path'].'/api/mailcount/\';' . "\n".
					'		get_mail(url);' . "\n".
					'	});' . "\n".
					'</script>';
					
					$menu .= $this->pageItem($pageRoute, $pageName);
				}
			} else {
				$menu .= $this->pageItem($pageRoute, $pageName);
			}

			$currentSegment = $segmentName;

		}

		$menu .= '</div>';
		
		return $menu;
	}

	public function segmentHeader($segmentName) {
		if(strlen($segmentName) > 25){
			$segmentName = substr($segmentName, 0, 25) . "...";
		}
		$menu = '<li class="active"><strong>' . $segmentName . '</strong></li>';
		return $menu;
	}

	public function pageItem($pageRoute, $pageName) {
		$menu = '<li class="menu"><a href="' . $this->core->conf['conf']['path'] . '/' . $pageRoute . '">' . $pageName . '</a></li>';
		return $menu;	
	}

}
?>
