<?php
$school = $fetch[1];
function showSchools($school) {
	global $connection;

	$sql = "SELECT `ID`, `Name` FROM `schools`";
	$run = doSelectQuery($sql);

	while ($fetch = mysql_fetch_row($run)) {

		$name = $fetch[1];
		$uid = $fetch[0];
		if ($uid == $school) {
			$sel = 'selected="selected"';
		} else {
			$sel = "";
		}

		$out = $out . '<option value="' . $uid . '" ' . $sel . '>' . $name . '</option>';

	}

	return ($out);
}

$select = showSchools($school);

echo '<form id="addstudy" name="addstudy" method="post" action="?id=studies&action=save">
	<p>You are editing:<b> ' . $fetch[6] . '</b>  </p>
	<p>

	<table width="768" border="0" cellpadding="5" cellspacing="0">
        <tr>
                <td width="205" height="28" bgcolor="#EEEEEE"><strong>Information</strong></td>
                <td width="200" bgcolor="#EEEEEE"><strong>Input field</strong></td>
                <td  bgcolor="#EEEEEE"><strong>Description</strong></td>
        </tr>

	<tr><td width="150">Full name of study</td>' .
	'<td><input name="fullname" type="text" value="' . $fetch[6] . '"></b></td>' .
	'<td></td>' .
	'</tr>

	<tr>
	<td>Short menu name for study</td>
	<td><input name="shortname" type="text" value="' . $fetch[7] . '" maxlength="15"></b></td>
	<td>Max. 15 characters</td>
	</tr>

	<tr><td>School</td>
	<td>  <select name="school" id="school">
			' . $select . '
                  </select></td>
	<td></td>
	</tr>

	<tr><td>Method of Delivery</td>' .
	'<td><select name="delivery">

	<option value="0" ';
if ($fetch[4] == "3") {
	echo 'selected=""';
}
echo '>-choose-</option>
	<option value="3" ';
if ($fetch[4] == "3") {
	echo 'selected=""';
}
echo '>Distance learning</option>
	<option value="2" ';
if ($fetch[4] == "2") {
	echo 'selected=""';
}
echo '>Parallel programme</option>
	<option value="1" ';
if ($fetch[4] == "1") {
	echo 'selected=""';
}
echo '>Regular programme</option>
	<option value="4" ';
if ($fetch[4] == "4") {
	echo 'selected=""';
}
echo '>Various forms</option>

	</select></td>' .
	'<td></td>' .
	'</tr>

	<tr><td>Study Type</td>' .
	'<td><select name="studytype">

	<option value="0" ';
if ($fetch[9] == "0") {
	echo 'selected=""';
}
echo '>-choose-</option>
	<option value="1" ';
if ($fetch[9] == "1") {
	echo 'selected=""';
}
echo '>Bachelor of art</option>
	<option value="2" ';
if ($fetch[9] == "2") {
	echo 'selected=""';
}
echo '>Bachelor of Engineering</option>
	<option value="3" ';
if ($fetch[9] == "3") {
	echo 'selected=""';
}
echo '>Bachelor of science</option>
	<option value="4" ';
if ($fetch[9] == "4") {
	echo 'selected=""';
}
echo '>Diploma maths and science</option>
	<option value="5" ';
if ($fetch[9] == "5") {
	echo 'selected=""';
}
echo '>Diploma other than maths and science</option>
	<option value="6" ';
if ($fetch[9] == "6") {
	echo 'selected=""';
}
echo '>Doctor</option>
	<option value="7" ';
if ($fetch[9] == "7") {
	echo 'selected=""';
}
echo '>Licentiate</option>
	<option value="8" ';
if ($fetch[9] == "8") {
	echo 'selected=""';
}
echo '>Master of art</option>
	<option value="9" ';
if ($fetch[9] == "9") {
	echo 'selected=""';
}
echo '>Master of Business Administration</option>
	<option value="10" ';
if ($fetch[9] == "10") {
	echo 'selected=""';
}
echo '>Master of Engineering Science</option>
	<option value="11" ';
if ($fetch[9] == "11") {
	echo 'selected=""';
}
echo '>Master of science</option>
	<option value="12" ';
if ($fetch[9] == "12") {
	echo 'selected=""';
}
echo '>Master of Science Engineering </option>
	<option value="13" ';
if ($fetch[9] == "13") {
	echo 'selected=""';
}
echo '>Secondary school</option>

	</select></td>' .
	'<td></td>' .
	'</tr>

	<tr><td>Currenty on offer</td>' .
	'<td><select name="active">

	<option value="0" ';
if ($fetch[8] == "0") {
	echo 'selected=""';
}
echo '>No</option>
	<option value="1" ';
if ($fetch[8] == "1") {
	echo 'selected=""';
}
echo '>Yes</option>


	</select></td>' .
	'<td></td>' .
	'</tr>

	<tr><td>Intensity of program</td>' .
	'<td><select name="active">

	<option value="0" ';
if ($fetch[11] == "0") {
	echo 'selected=""';
}
echo '>Part-time</option>
	<option value="1" ';
if ($fetch[11] == "1") {
	echo 'selected=""';
}
echo '>Fulltime</option>


	</select></td>' .
	'<td></td>' .
	'</tr>' .

	'<tr><td>Start of Intake</td>' .
	'<td><input name="startintake" type="text" class="datepicker" value="' . $fetch[2] . '"></td>' .
	'<td></td>' .
	'</tr>' .

	'<tr><td>End of Intake</td>' .
	'<td><input name="endintake" type="text" class="datepicker"  value="' . $fetch[3] . '"></td>' .
	'<td></td>' .
	'</tr>' .


	'<tr><td>Total duration of study</td>
	<td>
	<select name="duration">
	<option value="12" ';
if ($fetch[10] == "12") {
	echo 'selected=""';
}
echo '>1 Year</option>
	<option value="24" ';
if ($fetch[10] == "24") {
	echo 'selected=""';
}
echo '>2 Years</option>
	<option value="36" ';
if ($fetch[10] == "36") {
	echo 'selected=""';
}
echo '>3 Years</option>
	<option value="48" ';
if ($fetch[10] == "48") {
	echo 'selected=""';
}
echo '>4 Years</option>
	<option value="60" ';
if ($fetch[10] == "60") {
	echo 'selected=""';
}
echo '>5 Years</option>
	<option value="72" ';
if ($fetch[10] == "72") {
	echo 'selected=""';
}
echo '>6 Years</option>
	</select>
	</td>
	<td></td>
	</tr>
	</table>
	</p> 
	<input type="hidden" name="item" value="' . $this->core->cleanGet['item'] . '" />
	<input type="submit" class="submit" name="submit" id="submit" value="Save changes to study" />
</form>';
?>