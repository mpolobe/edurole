<script type="text/javascript">

jQuery(document).ready(function(){

	jQuery(function() {
		jQuery('.datepicker').datepicker({
			dateFormat : 'yy-mm-dd'
		});
	});	

	jQuery('select').ddslick({width:280, height:300,
	    onSelected: function(selectedData){
	        console.log(selectedData.selectedData.text);
	    }
	});

});

</script>

<form id="addstudy" name="addstudy" method="post" action="<?php echo $this->core->conf['conf']['path'] . "/studies/save/" . $this->core->item; ?>">
	<p>This form creates a new study, the start and end of intake determine the days online registration will be open
	</p>

	<p>
	<table width="768" border="0" cellpadding="5" cellspacing="0">
		<tr>
			<td width="205" height="28" bgcolor="#EEEEEE"><strong>Information</strong></td>
			<td width="200" bgcolor="#EEEEEE"><strong>Input field</strong></td>
			<td bgcolor="#EEEEEE"><strong>Description</strong></td>
		</tr>

		<tr>
			<td>Full name of study</td>
			<td><input name="fullname" type="text" value=""></td>
			<td></td>
		</tr>
		<tr>
			<td>Short menu name for study</td>
			<td><input name="shortname" type="text" value="" maxlength="15"></td>
			<td>Max. 15 characters</td>
		</tr>
		<tr>
			<td>Maximum size of intake</td>
			<td><b><input name="maxintake" type="text" value="" style="width:100px"> students</b></td>
			<td></td>
		</tr>
		<tr>
			<td>School</td>
			<td><select name="school" id="school">
					<?php echo $schools; ?>
				</select></td>
			<td></td>
		</tr>


	<tr><td>Method of Delivery</td>
	<td>
		<select name="delivery"  class="select">

		<?php
		echo '<option value="0" ';		if ($fetch[4] == "0") {	echo 'selected=""';	}	echo '>-choose-</option> '; 
		echo '<option value="Distance" ';	if ($fetch[4] == "Distance") {	echo 'selected=""';	}	echo '>Distance learning</option>';
		echo '<option value="Block" ';		if ($fetch[4] == "Block") {	echo 'selected=""';	}	echo '>Block Release</option>';
		echo '<option value="Parallel" ';	if ($fetch[4] == "Parallel") {	echo 'selected=""';	}	echo '>Parallel programme</option>';
		echo '<option value="Fulltime" ';	if ($fetch[4] == "Fulltime") {	echo 'selected=""';	}	echo'>Fulltime</option>';
		?>

		</select>
	</td>
	<td></td>
	</tr>

	<tr><td>Study Type</td>
	<td>
	<select name="studytype" class="select">
		<option value="Undergraduate">-choose-</option> 
		<option value="Certificate">Certificate</option>
		<option value="Diploma" >Diploma</option>
		<option value="Undergraduate">Udergraduate study</option>
		<option value="Postgraduate">Postgraduate study</option>
		<option value="Doctorate">Doctorate</option>
	</select>

		<tr>
			<td>Total duration of study</td>
			<td>
				<select name="duration">
				<option value="1">1 Year</option>
				<option value="2">2 Year</option>
				<option value="3">3 Year</option>
				<option value="4">4 Year</option>
				<option value="5">5 Year</option>
				</select>
			</td>
			<td></td>
		</tr>

		<tr>
			<td>Currenty on offer</td>
			<td><select name="active">
					<option value="1">Yes</option>
					<option value="0">No</option>
				</select></td>
			<td></td>
		</tr>

		<tr>
			<td>Start of Intake</td>
			<td><input name="startintake" type="text" class="datepicker" value=""></td>
			<td></td>
		</tr>

		<tr>
			<td>End of Intake</td>
			<td><input name="endintake" type="text" class="datepicker" value=""></td>
			<td></td>
		</tr>


		<tr>
			<td></td>
			<td>
			</td>
			<td></td>
		</tr>
	</table>
	</p><input type="submit" class="submit" name="submit" id="submit" value="Create study"/>
</form>
