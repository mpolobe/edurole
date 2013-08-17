<?php
class optionBuilder {

	public $core;

	public function __construct($core) {

		$this->core = $core;
	}

	public function buildSelect($sql) {

		$run = $this->core->database->doSelectQuery($sql);

		while ($row = $run->fetch_row()) {

			$name = $row[1];
			$uid = $row[0];

			if ($uid == $selected) {
				$sel = 'selected="selected"';
			} else {
				$sel = "";
			}

			$out = $out . '<option value="' . $uid . '" ' . $sel . '>' . $name . '</option>';

		}

		return $out;

	}

	function showPrograms($study, $type, $selected) {

		if ($study != null) {
			$sql = "SELECT `programmes`.ID, `programmes`.ProgramName FROM `programmes`, `study-program-link` WHERE `study-program-link`.StudyID = '$study' AND `study-program-link`.ProgramID = `programmes`.ID";
		} else {
			$sql = "SELECT `ID`, `ProgramName` FROM `programmes`";
		}

		$out = $this->buildSelect($sql);

		return ($out);
	}

	function showCourses($program, $selected) {

		if ($program != null) {
			$sql = "SELECT `courses`.`ID`, `courses`.`Name` FROM `courses`, `program-course-link` WHERE CourseID = `courses`.ID AND `ProgramID` = $program";
		} else {
			$sql = "SELECT `ID`, `Name` FROM `courses`";
		}

		$out = $this->buildSelect($sql);

		return ($out);
	}

	function showUsers($role, $selected) {

		$sql = "SELECT `ID`, CONCAT(`FirstName`, ' ', `Surname`) FROM `basic-information`, `access`, `roles` WHERE `access`.`ID` = `basic-information`.`ID` AND `access`.`RoleID` = `roles`.`ID` AND `access`.`RoleID` >= $role";

		$out = $this->buildSelect($sql);

		return ($out);
	}

	function showPaymentTypes() {

		$sql = "SELECT `ID`, `Name` FROM `settings` WHERE `Name` LIKE 'PaymentType%' ORDER BY `Name`";

		$out = $this->buildSelect($sql);

		return ($out);
	}

	function showSchools($selected) {

		$sql = "SELECT `ID`, `Name` FROM `schools`";

		$out = $this->buildSelect($sql);

		return ($out);
	}

	function showStudies($selected) {

		$sql = "SELECT `ID`, `Name` FROM `study`";

		$out = $this->buildSelect($sql);

		return ($out);
	}

	function showRoles($role) {

		$sql = "SELECT * FROM `roles`";

		$out = $this->buildSelect($sql);

		return ($out);
	}

}

?>