<?php
class menuConstruct {

	public $core;

	function __construct($core) {
		$this->core = $core;
	}

	public function buildMainMenu() {

		echo '<div class="menucontainer">';

		$this->fillMainMenu();

		echo '</div><div class="contentpadfull">';

	}

	private function fillMainMenu() {

		$sql = "SELECT * 
		FROM `permission-link`, `permissions`, `pages`, `page-segment` 
		WHERE `pages`.`PageSegmentID` =  `page-segment`.`ID`
		AND `page-segment`.`SegmentRequiredPermission` = `permission-link`.`ID`
		AND `permission-link`.`PermissionsRangeID` =  `permissions`.`ID` 
		AND `permissions`.`RequiredRoleMin` <= " . $this->core->role . "
		AND `permissions`.`RequiredRoleMax` <= " . $this->core->role . "
		ORDER BY `page-segment`.`SegmentPosition`,  `pages`.`PagePosition`";

		$run = $this->core->database->doSelectQuery($sql);
		$currentSegment = NULL;

		while ($fetch = $run->fetch_assoc()) {

			$segmentName = $fetch['SegmentName'];
			$pageRoute = $fetch['PageRoute'];
			$pageName = $fetch['PageName'];

			if ($segmentName == $currentSegment) {

				$this->pageItem($pageRoute, $pageName);

			} else if (!isset($currentSegment)) {

				echo '<div class="menubar">';
				echo '<div class="menuusr"><strong>' . $this->core->username . '</strong> <i>(' . $this->core->rolename . ')</i> </div>';

				$this->segmentHeader($segmentName);
				$this->pageItem($pageRoute, $pageName);

			} else if ($segmentName != $currentSegment) {

				echo '</div>
				<div class="menubar">';

				$this->segmentHeader($segmentName);
				$this->pageItem($pageRoute, $pageName);

			}

			if ($segmentName == "Virtual Learning Environment") {

				$study = NULL;
				while ($_SESSION['saobjects']) {
					$program = $fetch[2];

					if ($study != $fetch[1]) {
						$study = $fetch[1];
						$school = $fetch[3];

						echo '<div class="menu"><a href="' . $this->core->conf['conf']['path'] . '/vle/school/1"> ' . $school . '</a></div>
							<div class="menu"><a href="' . $this->core->conf['conf']['path'] . '/vle/school/1">
								<img src="' . $this->core->fullTemplatePath . '/images/expand.gif"> ' . $study . '</a>
							</div>';
					}

					echo '<div class="menu"><div class="indent"><a href="' . $this->core->conf['conf']['path'] . 'vle&view=school&id=1"><img src="templates/default/images/expand.gif"> ' . $program . '</a></div></div>';
				}

			} else if ($pageName == "mail") {

				if ($this->core->conf['conf']['mailEnabled'] == TRUE) {

					include $this->core->conf['conf']['classPath'] . "mail.inc.php";

					$mail = new mailOperations();
					$mailCount = $mail->mailCount();

					$pageName = 'Personal mail <div class="mailcount"><b>' . $mailCount . '</b></div>';
					$this->pageItem($pageRoute, $pageName);

				}
			}

			$currentSegment = $segmentName;

		}

		echo '</div>';
	}

	public function segmentHeader($segmentName) {
		echo '<div class="menuhdr"><strong>' . $segmentName . '</strong></div>';
	}

	public function pageItem($pageRoute, $pageName) {
		echo '<div class="menu"><a href="' . $this->core->conf['conf']['path'] . '/' . $pageRoute . '">' . $pageName . '</a></div>';
	}

}

?>