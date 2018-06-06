<script type="text/javascript">

jQuery(document).ready(function(){


  jQuery( function() {
    $( ".datepicker" ).datepicker();
  } );


	jQuery('.select').ddslick({width:280, height:300,
	    onSelected: function(selectedData){
	        console.log(selectedData.selectedData.text);
	    }
	});

});

</script>

<form id="addstudy" name="addstudy" method="post" action="<?php echo $this->core->conf['conf']['path'] . "/studies/save/" . $this->core->item; ?>">
	<p>You are editing:<b> <?php echo $fetch[6]; ?></b>  </p>
	<p>

	<table width="768" border="0" cellpadding="5" cellspacing="0">
        <tr>
                <td width="205" height="28" bgcolor="#EEEEEE"><strong>Information</strong></td>
                <td width="200" bgcolor="#EEEEEE"><strong>Input field</strong></td>
                <td  bgcolor="#EEEEEE"><strong>Description</strong></td>
        </tr>

	<tr><td width="150">Full name of study</td>
	<td><input name="fullname" type="text" value="<?php echo $fetch[6]; ?>"></b></td>
	<td></td>
	</tr>

	<tr>
	<td>Short menu name for study</td>
	<td><input name="shortname" type="text" value="<?php echo $fetch[7]; ?>" maxlength="15"></b></td>
	<td>Max. 15 characters</td>
	</tr>

	<tr><td>School</td>
	<td>
		<select name="school" id="school" class="select">
			<?php echo $schools; ?>
        </select>
	</td>
	<td></td>
	</tr>

	<tr>
		<td>Maximum size of intake</td>
		<td><b><input name="maxintake" type="text" value="<?php echo $fetch[5]; ?>" style="width:100px"> students</b></td>
		<td></td>
	</tr>

	<tr><td>Method of Delivery</td>
	<td>
		<select name="delivery"  class="select">

		<?php
		if ($fetch[4] == "0") {	 echo '<option value="0">-choose-</option> '; } else { 	echo '<option value="'.$fetch[4].'">'.$fetch[4].'</option>'; }

		echo '<option value="Distance">Distance learning</option>';
		echo '<option value="Block">Block Release</option>';
		echo '<option value="Parallel">Parallel programme</option>';
		echo '<option value="Fulltime">Fulltime</option>';
		?>

		</select>
	</td>
	<td></td>
	</tr>

	<tr><td>Study Type</td>
	<td>
	<select name="studytype" class="select">
	
	<?php
		if ($fetch[9] == "0") {	 echo '<option value="0">-choose-</option> '; } else { 	echo '<option value="'.$fetch[9].'">'.$fetch[9].'</option>'; }
	?>
		<option value="Certificate">Certificate</option>
		<option value="Diploma" >Diploma</option>
		<option value="Undergraduate">Udergraduate study</option>
		<option value="Postgraduate">Postgraduate study</option>
		<option value="Doctorate">Doctorate</option>
	</select>
	</td>
	<td></td>
	</tr>

	<tr><td>Currenty on offer</td>
	<td><select name="active" class="select">

		<?php
			if ($fetch[8] == "0") {	 
				echo '<option value="0" selected="" >No</option> <option value="1">Yes</option> ';	
			}  else {
 				 echo '<option value="1" selected="" >Yes</option> <option value="0">No</option> ';	
			} 
		?>

	</select></td>
	<td></td>
	</tr>


	<tr><td>Start of Intake</td>
	<td><input name="startintake" type="text" class="datepicker" value="<?php echo $fetch[2]; ?>"></td>
	<td></td>
	</tr>
	<tr><td>End of Intake</td>
	<td><input name="endintake" type="text" class="datepicker"  value="<?php echo $fetch[3]; ?>"></td>
	<td></td>
	</tr>


	<tr><td>Total duration of study</td>
	<td>
	<select name="duration" class="select">

	<?php	
		echo '<option value="'.$fetch[10].'" elected="">'.$fetch[10].' Year</option>';
	?>
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
                <td  width="230" bgcolor="#EEEEEE"><strong>Currently selected</strong></td>
                <td  width="230" bgcolor="#EEEEEE">Currently not selected</td>
              </tr>
 	<tr >
	<td>Select which programmes should be offered as part of this study</td>
	<td width="100"> 
	<form id="selected" name="selectedfr" method="post" action="<?php echo $this->core->conf['conf']['path'] . "/studies/save/" . $this->core->item; ?>">
	<input type="hidden" name="item" value="<?php echo $item; ?>" />
		<select name="selected[]" multiple="multiple" size="10" style="width: 230px">
			<?php echo $selectedprogrammes;  ?> 
		</select><br>
		<input type="submit" class="submit" name="submit" id="submit" value="Remove Selected" style="width: 130px" />
	</form>
	</td> 
	
	<td  width="100">
	<form id="nselected" name="nselectedfr" method="post" action="<?php echo $this->core->conf['conf']['path'] . "/studies/save/" . $this->core->item; ?>">
	<input type="hidden" name="item" value="<?php echo $item; ?>" />
	<select name="nselected[]" multiple="multiple" size="10" style="width: 230px">';
		<?php echo $notselectedprogrammes;  ?> 
	</select> <br>
	<input type="submit" class="submit" name="submit" id="submit" value="Add Selected" style="width: 130px" /></form>
	</td>
	<td></td>
	</tr>

	</table>
	</p> 




<br /><br /> <p class="title">Manage fee packages assigned to study</p><p>Please select the required fee packages to be paid</p>


	 <table width="700" border="0" cellpadding="5" cellspacing="0">
              <tr>
                <td width="205" height="28" bgcolor="#EEEEEE"><strong>Information</strong></td>
                <td  width="230" bgcolor="#EEEEEE"><strong>Currently selected</strong></td>
                <td  width="230" bgcolor="#EEEEEE">Currently not selected</td>
              </tr>
 	<tr >
	<td>Select which fee packages should be paid</td>
	<td width="100"> 
	<form id="feepackagesselected" name="feepackagesselected" method="post" action="<?php echo $this->core->conf['conf']['path'] . "/studies/save/" . $this->core->item; ?>">
	<input type="hidden" name="item" value="<?php echo $item; ?>" />
		<select name="selectedf[]" multiple="multiple" size="10" style="width: 230px">
			<?php echo $selectedfeepackages;  ?> 
		</select><br>
		<input type="submit" class="submit" name="submit" id="submit" value="Remove Selected" style="width: 130px" />
	</form>
	</td> 
	
	<td  width="100">
	<form id="feepackagesnselected" name="feepackagesnselected" method="post" action="<?php echo $this->core->conf['conf']['path'] . "/studies/save/" . $this->core->item; ?>">
	<input type="hidden" name="item" value="<?php echo $item; ?>" />
	<select name="nselectedf[]" multiple="multiple" size="10" style="width: 230px">';
		<?php echo $notselectedfeepackages;  ?> 
	</select> <br>
	<input type="submit" class="submit" name="submit" id="submit" value="Add Selected" style="width: 130px" /></form>
	</td>
	<td></td>
	</tr>

	</table>
	</p> 

