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

<form id="addstudy" name="addstudy" method="post" action="<? echo $this->core->conf['conf']['path'] . "/studies/save/" . $this->core->item; ?>">
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
			<td><input name="fullname" type="text" value=""></b></td>
			<td></td>
		</tr>
		<tr>
			<td>Short menu name for study</td>
			<td><input name="shortname" type="text" value="" maxlength="15"></b></td>
			<td>Max. 15 characters</td>
		</tr>


		<tr>
			<td>School</td>
			<td><select name="school" id="school">
					<?php echo $schools; ?>
				</select></td>
			<td></td>
		</tr>


		<tr>
			<td>Method of Delivery</td>
			<td><select name="delivery">

					<option value="3">Distance learning</option>
					<option value="2">Parallel programme</option>
					<option value="1">Regular programme</option>
					<option value="4">Various forms</option>

				</select></td>
			<td></td>
		</tr>

		<tr>
			<td>Type of study</td>
			<td><select name="studytype">

				<option value="14">Bachelor of Education</option>
				<option value="1">Bachelor of Arts</option>
				<option value="2">Bachelor of Engineering</option>
				<option value="3">Bachelor of Science</option>
				<option value="3">Bachelor of Business Studies</option>
				<option value="4">Diploma maths and science</option>
				<option value="5">Diploma other than maths and science</option>
				<option value="6">Doctor</option>
				<option value="7">Licentiate</option>
				<option value="8">Master of Art</option>
				<option value="9">Master of Business Administration</option>
				<option value="10">Master of Engineering Science</option>
				<option value="11">Master of Science</option>
				<option value="12">Master of Science Engineering</option>
				<option value="13">Secondary school</option>

				</select></td>
			<td></td>
		</tr>

		<tr>
			<td>Total duration of study</td>
			<td>
				<select name="duration">
					<option value="12"
					'; if($fetch[10]=="12"){ echo'selected=""'; } echo'>1 Year</option>
					<option value="24"
					'; if($fetch[10]=="24"){ echo'selected=""'; } echo'>2 Years</option>
					<option value="36"
					'; if($fetch[10]=="36"){ echo'selected=""'; } echo'>3 Years</option>
					<option value="48"
					'; if($fetch[10]=="48"){ echo'selected=""'; } echo'>4 Years</option>
					<option value="60"
					'; if($fetch[10]=="60"){ echo'selected=""'; } echo'>5 Years</option>
					<option value="72"
					'; if($fetch[10]=="72"){ echo'selected=""'; } echo'>6 Years</option>
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
			<td>Intensity of program</td>
			<td><select name="intensity">

					<option value="0">Part-time</option>
					<option value="1">Fulltime</option>


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
