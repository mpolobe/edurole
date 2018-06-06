<?php
class students{

	public $core;

	public function __construct($core) {
		$this->core = $core;
	}
	
	public function registerStudent(){		
		//print_r($_REQUEST);

		$firstname = $this->core->cleanPost["firstname"];
		$middlename = $this->core->cleanPost["middlename"];
		$surname = $this->core->cleanPost["surname"];
		$sex = $this->core->cleanPost["sex"];
		$id = $this->core->cleanPost["studentid"];
		$studentid = $this->core->cleanPost["studentno"];
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
		$disytype = $this->core->cleanPost["disytype"];
		$studytype = $this->core->cleanPost["studytype"];
		$email = $this->core->cleanPost["email"];
		$distype = $this->core->cleanPost["dissabilitytype"];
		$hostel = $this->core->cleanPost["hostel"];
		$payment = $this->core->cleanPost["payment"];

		$examcenter = $this->core->cleanPost["examcenter"];
		$yearofstudy = $this->core->cleanPost["yearofstudy"];
		$placementname = $this->core->cleanPost["placementname"];
		$placementdistrict = $this->core->cleanPost["placementdistrict"];
		$placementprovince = $this->core->cleanPost["placementprovince"];
		$districtresource = $this->core->cleanPost["districtresource"];
		$provincialresource = $this->core->cleanPost["provincialresource"];
		$studygroup = $this->core->cleanPost["studygroup"];
		$studygrouptwo = $this->core->cleanPost["studygrouptwo"];

		
		$i = 0;
		$n = 0;
		
		function password($length, $charset = '123456789abc') {
			$str = '';
			$count = strlen($charset);
			while ($length--) {
				$str .= $charset[mt_rand(0, $count - 1)];
			}
			return $str;
		}
		
		$sqlinsert = "INSERT INTO `basic-information` (`FirstName`, `MiddleName`, `Surname`, `Sex`, `ID`, `GovernmentID`, `DateOfBirth`, `PlaceOfBirth`, `Nationality`, `StreetName`, `PostalCode`, `Town`, `Country`, `HomePhone`, `MobilePhone`, `Disability`, `DissabilityType`, `PrivateEmail`, `MaritalStatus`, `StudyType`, `Status`) 
			 	VALUES ('$firstname', '$middlename', '$surname', '$sex', NULL, '$id', '$year-$month-$day', '$pob', '$nationality', '$streetname', '$postalcode', '$town', '$country', '$homephone', '$celphone', '$dissability', '$disytype', '$email', '$mstatus', '$studytype', 'Requesting');";

	

		$idw = stripslashes($id);

		if(empty($studentid)){
			$sql = "SELECT * FROM `basic-information` 
			WHERE `GovernmentID` = '$id' AND `ID` = '$studentid' 
			OR `GovernmentID` = '$idw'";
		} else {
			$sql = "SELECT * FROM `basic-information` 
			WHERE `GovernmentID` = '$id' AND `ID` = '$studentid' 
			OR `GovernmentID` = '$idw' AND `ID` = '$studentid'";
		}


		$dms = $this->core->database->doSelectQuery($sql);
		$rereg = FALSE;




		//CHECK IF EXISTING STUDENT

		while ($fetch = $dms->fetch_row()) {
			$rereg = TRUE;
			
			$sql = "INSERT INTO `basic-information-previous` SELECT * FROM `basic-information` WHERE `basic-information`.GovernmentID = '$id';";
			$this->core->database->doInsertQuery($sql);

			$sql = "DELETE FROM `basic-information` WHERE `basic-information`.GovernmentID = '$id';";
			$this->core->database->doInsertQuery($sql);

			echo '<div class="successpopup">You succesfully matched with your old student records</div>';

			$sqlinsert = "INSERT INTO `basic-information` (`FirstName`, `MiddleName`, `Surname`, `Sex`, `ID`, `GovernmentID`, `DateOfBirth`, `PlaceOfBirth`, `Nationality`, `StreetName`, `PostalCode`, `Town`, `Country`, `HomePhone`, `MobilePhone`, `Disability`, `DissabilityType`, `PrivateEmail`, `MaritalStatus`, `StudyType`, `Status`) 
		 	VALUES ('$firstname', '$middlename', '$surname', '$sex', '$studentid', '$id', '$year-$month-$day', '$pob', '$nationality', '$streetname', '$postalcode', '$town', '$country', '$homephone', '$celphone', '$dissability', '$disytype', '$email', '$mstatus', '$studytype', 'Requesting');";
		}
		


		if ($this->core->database->doInsertQuery($sqlinsert)) {

			$sql = "SELECT * FROM `basic-information` WHERE `GovernmentID` = '$id'";

			$dms = $this->core->database->doSelectQuery($sql);

			while ($fetch = $dms->fetch_row()) {
				$n=0;
				$userID = $fetch[4];
		
				// OTHER DATA
				if(empty($yearofstudy)){ $yearofstudy = 1; }

				$sql = "INSERT INTO `student-data-other` (`ID`, `StudentID`, `YearOfStudy`, `ExamCentre`, `PlacementName`,  `PlacementProvince`, `PlacementDistrict`, `DistrictResourceCentre`, `ProvincialResourceCentre`, `StudyGroupOne`, `StudyGroupTwo`) 
				VALUES (NULL, '$userID', '$yearofstudy', '$examcenter', '$placementname', '$placementprovince', '$placementdistrict', '$districtresource', '$provincialresource', '$studygroup', '$studygrouptwo');";


				$this->core->database->doInsertQuery($sql);
		
				// EMERGENCY CONTACTS
				if (isset($this->core->cleanPost["econtact"])) {
					foreach ($this->core->cleanPost["econtact"] as $econ) {
					
						$fullname = $econ["fullname"];
						$relationship = $econ["relationship"];
						$phonenumber = $econ["phonenumber"];
						$street = $econ["street"];
						$town = $econ["town"];
						$postalcode = $econ["postalcode"];
		
						$sql = "INSERT INTO `emergency-contact` (`ID` ,`StudentID` ,`FullName`, `Relationship` ,`PhoneNumber` ,`Street` ,`Town` ,`Postalcode`)VALUES (NULL , '$userID', '$fullname', '$relationship', '$phonenumber', '$street', '$town', '$postalcode')";
					
						$this->core->database->doInsertQuery($sql);
					}
				}
		
				// PREVIOUS EDUCATION RECORDS
				if (isset($this->core->cleanPost["education"])) {
					foreach ($this->core->cleanPost["education"] as $education) {

						$type = $education["type"];
						$institution = $education["institution"];
						$name = $education["name"];
		
						if (isset($_FILES["education"][$n]["upload"])) {

							$file = $_FILES["education"][$n]["upload"];
		
							$this->fileUpload($file);
		
						}
		
						$sql = "INSERT INTO `education-background` (`ID` ,`StudentID` ,`CertificateName` ,`TypeOfCertificate` ,`InstitutionName`, `DocumentName`) 
							VALUES (NULL , '$id', '$name', '$type', '$institution', '$name.$extension')";
						$this->core->database->doInsertQuery($sql);
						$this->core->logEvent("Query executed: $sql", "3");
		
					}
				}

		
				// MAJOR AND MINOR 
				$major = $this->core->cleanPost["major"];
				$minor = $this->core->cleanPost["minor"];
				$dateofenrollment = date("Y-m-d");
				$studyid = $this->core->cleanPost["studyid"];

				$sql = "INSERT INTO `student-program-link` (`ID` ,`StudentID` ,`Major` ,`Minor` ,`DateOfEnrollment`) VALUES (NULL , '$userID', '$major', '$minor', '$dateofenrollment')";
				$this->core->database->doInsertQuery($sql);
				$this->core->logEvent("Query executed: $sql", "3");

	
				// STUDY
				$sql = "INSERT INTO `student-study-link` (`ID` ,`StudentID` ,`StudyID`, `Status`) VALUES (NULL , '$userID', '$studyid', '1')";
				$this->core->database->doInsertQuery($sql);
				$this->core->logEvent("Query executed: $sql", "3");

				
				// ACCESS PASSWORD
				$password = password(5);
				$passenc = hash('sha512', $password . $this->core->conf['conf']['hash'] . $userID);
				if($rereg == TRUE){
					$sql = "REPLACE INTO `access` (`ID`, `Username`, `RoleID`, `Password`) VALUES ('$studentid', '$studentid', '9', '$passenc');";
					$sqls = "REPLACE INTO `accesspass` (`ID`, `Username`, `RoleID`, `Password`) VALUES ('$userID', '$userID', '9', '$password');";
					$this->core->database->doInsertQuery($sql);

					$this->core->database->doInsertQuery($sqls);

					$this->core->logEvent("Query executed: $sql", "3");

				}else{
					$sql = "INSERT INTO `access` (`ID`, `Username`, `RoleID`, `Password`) VALUES ('$userID', '$userID', '1', '$passenc');";
					$this->core->database->doInsertQuery($sql);
					$this->core->logEvent("Query executed: $sql", "3");
				}

				// PAYMENT TYPE
				if($rereg == FALSE){ $new = "-New"; }

				$sqls = "SELECT * FROM `study` WHERE `ID` = $studyid";
				$smi = $this->core->database->doSelectQuery($sqls);

				while ($fetchi = $smi->fetch_assoc()) {
					$progamtype = $fetchi["StudyType"];
				}

				$payment = "$studytype-$progamtype-$payment$new";
				$sql = "INSERT INTO `fee-package-charge-link` (`ID`, `StudentID`, `ChargeType`, `ChargedTerm`) VALUES (NULL, '$userID', '$payment', '20163');";


				$this->core->database->doInsertQuery($sql);
				$this->core->logEvent("Query executed: $sql", "3");

				// FINISHED
				echo '<h2>Student record registration completed</h2>';
				if($rereg == FALSE){
					echo'<div class="successpopup">Your request for has been submitted to the registrar. You are able to monitor your enrollment progress with the following login information. WRITE THIS INFORMATION DOWN OR REMEMBER IT!</div>';
				} else {
					echo'<div class="successpopup">Your information has been processed, please WRITE DOWN THE PASSWORD below and verify your username matches your student number. Please continue to have your picture taken.</div>';
				}
				echo'<div class="successpopup">Username:  <b>' . $userID . '</b><br>Password:  <b>' . $password . '</b> <br><br> You can log in on the <a href="' . $this->core->conf['conf']['path'] .'">home page</a> to view your admission and payment status</div>';
				break;

			}

		} else {
			$this->core->throwError("An error occurred with the information you have entered. Possible causes are:<br> - The ID number you have entered is incorrect");
			$this->core->throwError('Please return to the form and verify your information. <a a href="javascript:" onclick="history.go(-1); return false">Go back</a>');

		}
	}

	private function fileUpload($file){

		$home = getcwd();
		$path = $this->core->conf['conf']['dataStorePath'] . 'userhomes/" . $userID . "/education-history';
		
		if (!is_dir($path)) {
			mkdir($path, 0755, true);
		}
		
		$notallowedExts = array("exe", "EXE", "cmd", "CMD", "sh", "SH", "vb", "VB", "app", "APP", "com", "COM", "bat", "BAT", "php", "PHP", "html", "HTML", "cgi", "CGI", "htm", "HTM", "htaccess");
		$extension = end(explode(".", $file["name"][$n]["upload"]));
		
		if (($file["size"][$n]["upload"] < 50000000) && !in_array($extension, $notallowedExts)) {
		
			if ($file["error"][$n]["upload"] > 0) {
				echo "Error: " . $file["error"][$n]["upload"] . "<br>";
			} else {
		
				$name = password(10);
		
				while (file_exists("$path/$name." . $extension)) {
					$name = password(10);
				}
		
				move_uploaded_file($file["tmp_name"][$n]["upload"], "$path/$name." . $extension);
				$this->core->logEvent("File upload completed: $path/$name", "3");
			}
		
		} else {
			$this->core->logEvent('Warning: File upload failed, invalid file', '2');
			$this->core->throwWarning('Error: File upload failed, invalid file');
		}
		
		$i++;
		$n++;
	}
}
?>
