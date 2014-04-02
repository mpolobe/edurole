<?php
class register{
	
	public function register(){
		echo '<div class="breadcrumb"><a href=".">home</a> > <a href="' . $this->core->conf['conf']['path'] . '/admission">registration request</a> </div>
		<div class="contentpadfull"> ';
		
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
		$disytype = $this->core->cleanPost["disytype"];
		$studytype = $this->core->cleanPost["studytype"];
		$email = $this->core->cleanPost["email"];
		$distype = $this->core->cleanPost["dissabilitytype"];
		$hostel = $this->core->cleanPost["hostel"];
		$payment = $this->core->cleanPost["payment"];
		$receiptnr = $this->core->cleanPost["receiptnr"];
		
		$i = 0;
		$n = 0;
		
		function password($length, $charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1') {
			$str = '';
			$count = strlen($charset);
			while ($length--) {
				$str .= $charset[mt_rand(0, $count - 1)];
			}
			return $str;
		}
		
		$sql = "INSERT INTO `basic-information` (`FirstName`, `MiddleName`, `Surname`, `Sex`, `ID`, `GovernmentID`, `DateOfBirth`, `PlaceOfBirth`, `Nationality`, `StreetName`, `PostalCode`, `Town`, `Country`, `HomePhone`, `MobilePhone`, `Disability`, `DissabilityType`, `PrivateEmail`, `MaritalStatus`, `StudyType`, `Status`) VALUES ('$firstname', '$middlename', '$surname', '$sex', NULL, '$id', '$year-$month-$day', '$pob', '$nationality', '$streetname', '$postalcode', '$town', '$country', '$homephone', '$celphone', '$dissability', '$disytype', '$email', '$mstatus', '$studytype', 'Requesting');";
		
		$this->core->throwError("An error occurred with the information you have entered. Possible causes are:<br> - You already have a student account at this institution<br> - The ID number you have entered is incorrect");
		
		if ($this->core->database->doInsertQuery($sql)) {
		
			$sql = "SELECT * FROM `basic-information` WHERE `GovernmentID` = $id";
			$dms = $this->core->database->doSelectQuery($sql);
		
			while ($fetch = mysql_fetch_row($dms)) {
		
				$userID = $fetch[4];
		
				// Emergency contact
				if (isset($this->core->cleanPost["econtact"])) {
					foreach ($this->core->cleanPost["econtact"] as $econ) {
		
						$fullname = $econ["fullname"];
						$relationship = $econ["relationship"];
						$phonenumber = $econ["phonenumber"];
						$street = $econ["street"];
						$town = $econ["town"];
						$postalcode = $econ["postalcode"];
		
						$sql = "INSERT INTO `emergency-contact` (`ID` ,`StudentID` ,`FullName`, `Relationship` ,`PhoneNumber` ,`Street` ,`Town` ,`Postalcode`)VALUES (NULL , '$id', '$fullname', '$relationship', '$phonenumber', '$street', '$town', '$postalcode')";
						doInsertQuery($sql);
		
					}
				}
		
				// Education
				if (isset($this->core->cleanPost["education"])) {
					foreach ($this->core->cleanPost["education"] as $education) {
		
						$type = $education["type"];
						$institution = $education["institution"];
						$name = $education["name"];
		
						if (isset($_FILES["education"][$n]["upload"])) {
		
							$file = $_FILES["education"];
		
							$home = getcwd();
							$path = $this->core->fullTemplatePath . '/datastore/userhomes/" . $userID . "/education-history';
		
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
									eduroleCore::logEvent("File upload completed: $path/$name", "3");
								}
		
							} else {
								eduroleCore::logEvent('Warning: File upload failed, invalid file', '2');
								eduroleCore::throwWarning('Error: File upload failed, invalid file');
							}
		
							$i++;
							$n++;
		
						}
		
						$sql = "INSERT INTO `education-background` (`ID` ,`StudentID` ,`CertificateName` ,`TypeOfCertificate` ,`InstitutionName`, `DocumentName`) VALUES (NULL , '$id', '$name', '$type', '$institution', '$name.$extension')";
						$this->core->database->doInsertQuery($sql);
						eduroleCore::logEvent("Query executed: $sql", "3");
		
					}
				}
		
				$major = $this->core->cleanPost["major"];
				$minor = $this->core->cleanPost["minor"];
				$dateofenrollment = date("Y-m-d");
				$studyid = $this->core->cleanPost["studyid"];
		
				$sql = "INSERT INTO `student-program-link` (`ID` ,`StudentID` ,`Major` ,`Minor` ,`DateOfEnrollment`) VALUES (NULL , '$id', '$major', '$minor', '$dateofenrollment')";
				$this->core->database->doInsertQuery($sql);
				$this->core->logEvent("Query executed: $sql", "3");
		
				$sql = "INSERT INTO `student-study-link` (`ID` ,`StudentID` ,`StudyID`, `Status`) VALUES (NULL , '$userID', '$studyid', '1')";
				$this->core->database->doInsertQuery($sql);
				$this->core->logEvent("Query executed: $sql", "3");
		
				$password = password(6);
				$passenc = hash('sha512', $password . $this->core->conf['conf']['hash'] . $userID);
		
				$sql = "INSERT INTO `access` (`ID`, `Username`, `RoleID`, `Password`) VALUES ('$userID', '$userID', '1', '$passenc');";
				$this->core->database->doInsertQuery($sql);
				$this->core->logEvent("Query executed: $sql", "3");
		
				echo '<h2>Student record registration completed</h2>
				<div class="successpopup">Your request for admission has been submitted to the registrar. You are able to monitor your enrollment progress with the following login information. WRITE THIS INFORMATION DOWN OR REMEMBER IT!</div>
				<div class="successpopup">Username:  <b>' . $fetch[4] . '</b><br>Password:  <b>' . $password . '</b></div> <p>You can log in on the <a href=".">home page</a> to view your admission status</p>';
			}
		
		} else {
			throwError('An error occurred with the information you have entered. Please return to the form and verify your information. <a a href="javascript:" onclick="history.go(-1); return false">Go back</a>');
		}
	}
}
?>
