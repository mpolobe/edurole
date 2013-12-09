<?php
class optionBuilder {

	public $core;

	public function __construct($core) {

		$this->core = $core;
	}

	public function buildSelect($sql, $selected = NULL) {

		$out = "";
		$run = $this->core->database->doSelectQuery($sql);

		if ($run->num_rows > 0) {

		while ($row = $run->fetch_row()) {

			$name = $row[1];
			$uid = $row[0];
			if ($uid == $selected) {
				$sel = 'selected="selected"';
			} else {
				$sel = "";
			}

			if ($uid == $selected) {
				$begin = '<option value="' . $uid . '" ' . $sel . '>' . $name . '</option>';
			}else{
				$out = $out . '<option value="' . $uid . '" ' . $sel . '>' . $name . '</option>';
			}

		}

		} else {

			$out = $out . '<option value="">No information available</option>';

		}

		$out = $begin . $out;
		return $out;

	}

	function showPrograms($study, $selected = null) {

		if ($study != null) {
			$sql = "SELECT `programmes`.ID, `programmes`.ProgramName FROM `programmes`, `study-program-link` WHERE `study-program-link`.StudyID = '$study' AND `study-program-link`.ProgramID = `programmes`.ID";
		} else {
			$sql = "SELECT `ID`, `ProgramName` FROM `programmes`";
		}

		$out = $this->buildSelect($sql, $selected);

		return ($out);
	}

	function showCourses($program, $selected = null) {

		if ($program != null) {
			$sql = "SELECT * FROM `courses`, `programmes`, `program-course-link` WHERE `program-course-link`.CourseID = `courses`.ID AND `program-course-link`.ProgramID = `programmes`.ID AND `program-course-link`.ProgramID = $program";
		} else {
			$sql = "SELECT `ID`, `Name` FROM `courses`";
		}

		$out = $this->buildSelect($sql, $selected);

		return ($out);
	}

	function showUsers($role, $selected = null) {

		$sql = "SELECT `basic-information`.`ID`, CONCAT(`FirstName`, ' ', `Surname`) FROM `basic-information`, `access`, `roles` WHERE `access`.`ID` = `basic-information`.`ID` AND `access`.`RoleID` = `roles`.`ID` AND `access`.`RoleID` >= '$role'";
		$out = $this->buildSelect($sql, $selected);

		return ($out);
	}

	function showPaymentTypes($selected = null) {

		$sql = "SELECT `ID`, `Value`, `Name` FROM `settings` WHERE `Name` LIKE 'PaymentType%' ORDER BY `Name`";
		$out = $this->buildSelect($sql, $selected);

		return ($out);
	}

	function showSchools($selected = null) {

		$sql = "SELECT `ID`, `Name` FROM `schools`";
		$out = $this->buildSelect($sql, $selected);

		return ($out);
	}

	function showStudies($selected = null) {

		$sql = "SELECT `ID`, `Name` FROM `study`";
		$out = $this->buildSelect($sql, $selected);

		return ($out);
	}

	function showRoles($selected = null) {

		$sql = "SELECT * FROM `roles`";
		$out = $this->buildSelect($sql, $selected);

		return ($out);
	}

	function showAccommodation($selected = null) {

		$sql = "SELECT `ID`, `Name` FROM `accommodation`";
		$out = $this->buildSelect($sql, $selected);

		return ($out);
	}


}

?>
