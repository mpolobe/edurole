<?php
$name = $this->core->cleanPost['name'];

if (!isset($name)) {

	echo '<form action="parse.php" method="post" enctype="multipart/form-data">
	Name: <input type="text" name="name" id="name" /> 
	File: <input type="file" name="file" id="file" /> 
	<input type="submit" name="submit" value="Submit" />
	</form>';

} else {

	//iCal class
	require 'class.iCalReader.php';

	//File Import
	$location = "cal/" . $_FILES["file"]["name"];
	move_uploaded_file($_FILES["file"]["tmp_name"], $location);
	$ical = new ICal($location);
	$events = $ical->events();
	unlink($location);


	$sql = "INSERT INTO `coursecal` (`CourseName`) VALUES ('$name')";
	$run = $this->core->database->doSelectQuery($sql);

	$sql = "SELECT * FROM `coursecal` WHERE `CourseName` LIKE '$name'";
	$run = $this->core->database->doSelectQuery($sql);

	while ($fetch = mysqli_fetch_row($pep)) {
		$calid = $fetch[0];
	}

	foreach ($events as $event) {

		$start = $ical->iCalDateToUnixTimestamp($event['DTSTART']);
		$end = $ical->iCalDateToUnixTimestamp($event['DTEND']);
		$description = $event['DESCRIPTION'];
		$location = $event['LOCATION'];
		$summary = $event['SUMMARY'];

		$sql = "INSERT INTO `calendar` (`CoursecalID`,`StartTime`,`EndTime`,`Description`,`Location`,`Summary`) VALUES ('$calid','$start','$end','$description','$location','$summary')";
		doInsertQuery($sql);

	}

	echo "Calendar uploaded";

}


function doInsertQuery($sql) {

	global $connection;
	if (!mysql_query($sql, $connection)) {
		die('DATABASE ERROR ' . mysql_error());
	} else {
		return true;
	}

}

?>