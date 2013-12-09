<script type="text/javascript">

jQuery(document).ready(function(){

	jQuery(function() {
		jQuery('.datepicker').datepicker({
			dateFormat : 'yy-mm-dd'
		});
	});	

	jQuery('.select').ddslick({width:280, height:300,
	    onSelected: function(selectedData){
	        console.log(selectedData.selectedData.text);
	    }
	});

});

</script>

<form id="addstudy" name="addstudy" method="post" action="<? echo $this->core->conf['conf']['path'] . "/studies/save/" . $this->core->item; ?>">
	<p>You are editing:<b> <? echo $fetch[6]; ?></b>  </p>
	<p>

	<table width="768" border="0" cellpadding="5" cellspacing="0">
        <tr>
                <td width="205" height="28" bgcolor="#EEEEEE"><strong>Information</strong></td>
                <td width="200" bgcolor="#EEEEEE"><strong>Input field</strong></td>
                <td  bgcolor="#EEEEEE"><strong>Description</strong></td>
        </tr>

	<tr><td width="150">Full name of study</td>
	<td><input name="fullname" type="text" value="<? echo $fetch[6]; ?>"></b></td>
	<td></td>
	</tr>

	<tr>
	<td>Short menu name for study</td>
	<td><input name="shortname" type="text" value="<? echo $fetch[7]; ?>" maxlength="15"></b></td>
	<td>Max. 15 characters</td>
	</tr>

	<tr><td>School</td>
	<td>
		<select name="school" id="school" class="select">
			<? echo $schools; ?>
        </select>
	</td>
	<td></td>
	</tr>

	<tr><td>Method of Delivery</td>
	<td>
		<select name="delivery"  class="select">

		<?php
		echo '<option value="0" ';	if ($fetch[4] == "0") {	echo 'selected=""';	}	echo '>-choose-</option> '; 
		echo '<option value="1" ';	if ($fetch[4] == "1") {	echo 'selected=""';	}	echo '>Distance learning</option>';
		echo '<option value="2" ';	if ($fetch[4] == "2") {	echo 'selected=""';	}	echo '>Parallel programme</option>';
		echo '<option value="3" ';	if ($fetch[4] == "3") {	echo 'selected=""';	}	echo'>Regular programme</option>';
		echo '<option value="4" ';	if ($fetch[4] == "4") {	echo 'selected=""';	}	echo'>Various forms</option>';
		?>

		</select>
	</td>
	<td></td>
	</tr>

	<tr><td>Study Type</td>
	<td>
	<select name="studytype" class="select">
	
	<?php
		echo '<option value="0" ';	if ($fetch[9] == "0") {	echo 'selected=""';	}	echo '>-choose-</option> '; 
		echo '<option value="14"';	if ($fetch[9] == "14"){	echo 'selected=""';	}	echo '>Bachelor of Education</option>';
		echo '<option value="1" ';	if ($fetch[9] == "1") {	echo 'selected=""';	}	echo '>Bachelor of art</option>';
		echo '<option value="2" ';	if ($fetch[9] == "2") {	echo 'selected=""';	}	echo '>Bachelor of Engineering</option>';
		echo '<option value="3" ';	if ($fetch[9] == "3") {	echo 'selected=""';	}	echo '>Diploma maths and science</option>';
		echo '<option value="4" ';	if ($fetch[9] == "4") {	echo 'selected=""';	}	echo '>Diploma other than maths and science</option>';
		echo '<option value="5" ';	if ($fetch[9] == "5") {	echo 'selected=""';	}	echo '>Doctor</option>';
		echo '<option value="6" ';	if ($fetch[9] == "6") {	echo 'selected=""';	}	echo '>Licentiate</option>';
		echo '<option value="7" ';	if ($fetch[9] == "7") {	echo 'selected=""';	}	echo '>Master of Arts</option>';
		echo '<option value="8" ';	if ($fetch[9] == "8") {	echo 'selected=""';	}	echo '>Master of Business Administration</option>';
		echo '<option value="9" ';	if ($fetch[9] == "9") {	echo 'selected=""';	}	echo '>Master of Engineering Science</option>';
		echo '<option value="10" ';	if ($fetch[9] == "10"){	echo 'selected=""';	}	echo '>Master of Science</option>';
		echo '<option value="11" ';	if ($fetch[9] == "11"){	echo 'selected=""';	}	echo '>Doctor of Philosophy</option>';
		echo '<option value="12" ';	if ($fetch[9] == "12"){	echo 'selected=""';	}	echo '>Secondary school</option>';
	?>

	</select>
	</td>
	<td></td>
	</tr>

	<tr><td>Currenty on offer</td>
	<td><select name="active" class="select">

		<?php
			echo '<option value="0" ';	if ($fetch[8] == "0") {	echo 'selected=""';	}	echo '>No</option> '; 
			echo '<option value="1" ';	if ($fetch[8] == "1") {	echo 'selected=""';	}	echo '>Yes</option>';
		?>

	</select></td>
	<td></td>
	</tr>

	<tr><td>Intensity of program</td>
	<td><select name="active" class="select">

	<?php
		echo '<option value="0" ';	if ($fetch[11] == "0") {	echo 'selected=""';	}	echo '>Part-time</option> '; 
		echo '<option value="1" ';	if ($fetch[11] == "1") {	echo 'selected=""';	}	echo '>Full-time</option>';
	?>

	</select></td>
	<td></td>
	</tr>

	<tr><td>Start of Intake</td>
	<td><input name="startintake" type="text" class="datepicker" value="<? echo $fetch[2]; ?>"></td>
	<td></td>
	</tr>
	<tr><td>End of Intake</td>
	<td><input name="endintake" type="text" class="datepicker"  value="<? echo $fetch[3]; ?>"></td>
	<td></td>
	</tr>


	<tr><td>Total duration of study</td>
	<td>
	<select name="duration" class="select">

<?php	
	echo'<option value="12" ';
if ($fetch[10] == "12") {
	echo 'selected=""';
}
echo '>1 Year</option>	<option value="24" ';
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
echo '>6 Years</option>';
?>

	</select>
	</td>
	<td></td>
	</tr>
	<tr>
	<td></td>
	<td>
		<input type="submit" class="submit" name="submit" id="submit" value="Save changes to study" />
	</td>
	<td></td>
	</tr>

	</table>

</form>

<br /><br /> <p class="title">Manage programmes on offer in study</p><p>Please enter the following information</p>


	 <table width="700" border="0" cellpadding="5" cellspacing="0">
              <tr>
                <td width="205" height="28" bgcolor="#EEEEEE"><strong>Information</strong></td>
                <td  width="130" bgcolor="#EEEEEE"><strong>Currently selected</strong></td>
                <td  width="130" bgcolor="#EEEEEE">Currently not selected</td>
                <td bgcolor="#EEEEEE"><strong>Description</strong></td>
              </tr>
 	<tr >
	<td>Select which programmes should be offered as part of this study</td>
	<td width="100"> 
	<form id="selected" name="selectedfr" method="post" action="<? echo $this->core->conf['conf']['path'] . "/studies/save/" . $this->core->item; ?>">
	<input type="hidden" name="item" value="<? echo $item; ?>" />
		<select name="selected[]" multiple="multiple" size="10" style="width: 130px">
			<?php echo $selectedprogrammes;  ?> 
		</select><br>
		<input type="submit" class="submit" name="submit" id="submit" value="Remove Selected" style="width: 130px" />
	</form>
	</td> 
	
	<td  width="100">
	<form id="nselected" name="nselectedfr" method="post" action="<? echo $this->core->conf['conf']['path'] . "/studies/save/" . $this->core->item; ?>">
	<input type="hidden" name="item" value="<? echo $item; ?>" />
	<select name="nselected[]" multiple="multiple" size="10" style="width: 130px">';
		<?php echo $notselectedprogrammes;  ?> 
	</select> <br>
	<input type="submit" class="submit" name="submit" id="submit" value="Add Selected" style="width: 130px" /></form>
	</td>
	<td></td>
	</tr>

	</table>
	</p> 
