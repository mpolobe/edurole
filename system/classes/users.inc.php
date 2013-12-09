<?php
class users{

	public $core;
	
	function __construct($core){
		return $this->core = $core;
	}
	
	public function password($length, $charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1') {
		$str = '';
		$count = strlen($charset);
		while ($length--) {
			$str .= $charset[mt_rand(0, $count - 1)];
		}
		return $str;
	}

	public function getStudent($id) {
		$sql = "SELECT * FROM `basic-information` WHERE `ID` = $id";

		$run = $this->core->database->doSelectQuery($sql);
		$fetch = $run->fetch_assoc();

		return $fetch;
	}

	public function addUser() {

		$password = password(6);

		if ($this->core->cleanPost["otherdissability"]) {
			$dissabilitytype = $this->core->cleanPost["otherdissability"];
		}


		// ADDUSER QUERY NEEDS PREPARED STATEMENT

		// Fields user account
		$username = $this->core->cleanPost["username"];
		$firstname = $this->core->cleanPost["firstname"];
		$middlename = $this->core->cleanPost["middlename"];
		$surname = $this->core->cleanPost["surname"];
		$sex = $this->core->cleanPost["sex"];
		$id = $this->core->cleanPost["studentid"];
		$day = $this->core->cleanPost["day"];
		$month = $this->core->cleanPost["month"];
		$year = $this->core->cleanPost["year"];
		$pob = $this->core->cleanPost["pob"];
		$nationality = $this->core->cleanPost["nationality"];
		$streetname = $this->core->cleanPost["streetname"];
		$postalcode = $this->core->cleanPost["postalcode"];
		$town = $this->core->cleanPost["town"];
		$country = $this->core->cleanPost["country"];
		$homephone = $this->core->cleanPost["homephone"];
		$celphone = $this->core->cleanPost["celphone"];
		$dissability = $this->core->cleanPost["dissability"];
		$mstatus = $this->core->cleanPost["mstatus"];
		$email = $this->core->cleanPost["email"];
		$dissabilitytype = $this->core->cleanPost["dissabilitytype"];
		$status = $this->core->cleanPost["status"];
		$roleid = $this->core->cleanPost["role"];
		$studytype = $this->core->cleanPost["studytype"];

		if(empty($id)){
			throwError('Please enter an NRC this field is required<a a href="javascript:" onclick="history.go(-1); return false">Go back</a>');
			return false;
		}

		$sql = "INSERT INTO `basic-information` (`FirstName`, `MiddleName`, `Surname`, `Sex`, `ID`, `GovernmentID`, `DateOfBirth`, `PlaceOfBirth`, `Nationality`, `StreetName`, `PostalCode`, `Town`, `Country`, `HomePhone`, `MobilePhone`, `Disability`, `DissabilityType`, `PrivateEmail`, `MaritalStatus`, `StudyType`, `Status`) VALUES ('$firstname', '$middlename', '$surname', '$sex', NULL, '$id', '$year-$month-$day', '$pob', '$nationality', '$streetname', '$postalcode', '$town', '$country', '$homephone', '$celphone', '$dissability', '$dissabilitytype', '$email', '$mstatus', '$studytype', 'Employed');";

		if ($this->core->database->doInsertQuery($sql)) {

			// Provide new user with access information

			$sql = "SELECT * FROM `basic-information` WHERE `GovernmentID` = $id";

			$dms = $this->core->database->doSelectQuery($sql);

			while ($fetch = $dms->fetch_row($dms)) {

				$passenc = sha1($password);
				$sql = "INSERT INTO `access` (`ID`, `Username`, `RoleID`, `Password`) VALUES ('$fetch[4]', '$username', '$roleid', '$passenc');";
				$this->core->database->doInsertQuery($sql);

				echo '<div class="successpopup">The requested user account has been created.<br/> WRITE THE FOLLOWING INFORMATION DOWN OR REMEMBER IT!</div>';

				echo '<div class="successpopup">Username:  <b>' . $username . '</b><br>Password:  <b>' . $password . '</b></div>';

			}

		} else {

			throwError('An error occurred with the information you have entered. Please return to the form and verify your information. <a a href="javascript:" onclick="history.go(-1); return false">Go back</a>');

		}

	}

	public function saveEdit($item, $access = TRUE) {

		$username = $this->core->cleanPost["username"];
		$firstname = $this->core->cleanPost["firstname"];
		$middlename = $this->core->cleanPost["middlename"];
		$surname = $this->core->cleanPost["surname"];
		$sex = $this->core->cleanPost["sex"];
		$day = $this->core->cleanPost["day"];
		$month = $this->core->cleanPost["month"];
		$year = $this->core->cleanPost["year"];
		$pob = $this->core->cleanPost["pob"];
		$nationality = $this->core->cleanPost["nationality"];
		$streetname = $this->core->cleanPost["streetname"];
		$postalcode = $this->core->cleanPost["postalcode"];
		$town = $this->core->cleanPost["town"];
		$country = $this->core->cleanPost["country"];
		$homephone = $this->core->cleanPost["homephone"];
		$celphone = $this->core->cleanPost["celphone"];
		$dissability = $this->core->cleanPost["dissability"];
		$mstatus = $this->core->cleanPost["mstatus"];
		$email = $this->core->cleanPost["email"];
		$dissabilitytype = $this->core->cleanPost["dissabilitytype"];
		$status = $this->core->cleanPost["status"];
		$roleid = $this->core->cleanPost["role"];
		$nrc = $this->core->cleanPost["studentid"];

		$sql = "UPDATE `basic-information` SET  `Surname` = '$surname', `FirstName` = '$firstname', `MiddleName` = '$middlename', `Sex` = '$sex', `GovernmentID` = '$nrc', `Nationality` = '$nationality ', `StreetName` = '$streetname ', `PostalCode` = '$postalcode', `Town` = '$town', `Country` = '$country', `HomePhone` = '$homephone', `MobilePhone` = '$celphone', `Disability` = '$dissability', `DissabilityType` = '$dissabilitytype', `PrivateEmail` = '$email', `MaritalStatus` = '$mstatus', `Status` = '$status' WHERE `ID` = '$item' ";
		$run = $this->core->database->doInsertQuery($sql);

		if($access == TRUE){
			$sql = "UPDATE `access` SET  `RoleID` =  '$roleid' WHERE `access`.`ID` = '$item';";
			$run = $this->core->database->doInsertQuery($sql);
		}

		return true;
	}
}
?>
