<script type="text/javascript">

jQuery(document).ready(function(){

	jQuery('.ddsel').ddslick({width:280, height:300,
	    onSelected: function(selectedData){
	        console.log(selectedData.selectedData.text);
	    }
	});

});

</script>


<form id="editprogramme" name="editprogramme" method="post" action="<? echo $this->core->conf['conf']['path'] . "/programmes/save/" . $this->core->item; ?>">
	<p>Please enter the following information</p>
	<table width="768" border="0" cellpadding="5" cellspacing="0">
              <tr>
                <td width="205" height="28" bgcolor="#EEEEEE"><strong>Information</strong></td>
                <td  colspan="2" width="200"  bgcolor="#EEEEEE"><strong>Currently selected</strong></td>
                <td bgcolor="#EEEEEE"><strong>Description</strong></td>
              </tr>
              <tr>
		<td width="150"><b>Name of Programme</b></td>
                <td colspan="2">
                  <input type="text" name="name" value="<?php echo $fetch[1]; ?>" /></td>
                <td></td>
              </tr>
              <tr>
		<td width="150"><b>Programme Coordinator</b></td>
                <td colspan="2">
                  <select name="coordinator" class="ddsel" id="coordinator">
					<? echo $users; ?>
                  </select></td>
                <td>Functional course coordinator</td>
              </tr>
		<tr><td>Programme Type</td>
		<td colspan="2"><select name="programtype" class="ddsel">
				
		<?php
			echo '<option value="0" ';	if ($fetch[3] == "0") {	echo 'selected=""';	}	echo '>-choose-</option> '; 
			echo '<option value="1" ';	if ($fetch[3] == "1") {	echo 'selected=""';	}	echo '>Minor</option>';
			echo '<option value="2" ';	if ($fetch[3] == "2") {	echo 'selected=""';	}	echo '>Major</option>';
			echo '<option value="5" ';	if ($fetch[3] == "5") {	echo 'selected=""';	}	echo '>Diploma</option>';
			echo '<option value="4" ';	if ($fetch[3] == "4") {	echo 'selected=""';	}	echo '>Compulsory</option>';
			echo '<option value="3" ';	if ($fetch[3] == "3") {	echo 'selected=""';	}	echo'>Available as both</option>';
		?>

		</select></td>
		<td></td>
		</tr>

		<tr>
		<td width="150"></td>
		<td colspan="2">
		<input type="hidden" name="item" value="<? echo $item; ?>" />
	 		 <input type="submit" class="submit" name="submit" id="submit" value="Save changes to programme" /></td>
                <td></td>
              </tr></table>

		</form><br /><br /> <p class="title2">Manage courses in programme</p><p>Please enter the following information</p>

	 <table width="700" border="0" cellpadding="5" cellspacing="0">
              <tr>
                <td width="205" height="28" bgcolor="#EEEEEE"><strong>Information</strong></td>
                <td  width="130" bgcolor="#EEEEEE"><strong>Currently selected</strong></td>
                <td  width="130" bgcolor="#EEEEEE">Currently not selected</td>
                <td bgcolor="#EEEEEE"><strong>Description</strong></td>
              </tr>

 	<tr >
	<td>Select which courses should be part of this programme</td>
	<td width="100"> 
	<form id="selected" name="selectedfr" method="post" action="<? echo $this->core->conf['conf']['path'] . "/programmes/save/" . $this->core->item; ?>">
	<input type="hidden" name="item" value="<? echo $item; ?>" />
		<select name="selected[]" multiple="multiple" size="10" style="width: 130px">
			<?php echo $selectedcourses;  ?> 
		</select>
		<input type="submit" class="submit" name="submit" id="submit" value="Remove Selected" style="width: 130px" />
	</form>
	</td>
	
	<td  width="100">
	<form id="nselected" name="nselectedfr" method="post" action="<? echo $this->core->conf['conf']['path'] . "/programmes/save/" . $this->core->item; ?>">
	<input type="hidden" name="item" value="<? echo $item; ?>" />
	<select name="nselected[]" multiple="multiple" size="10" style="width: 130px">';
		<?php echo $notselectedcourses;  ?> 
	</select>  
	<input type="submit" class="submit" name="submit" id="submit" value="Add Selected" style="width: 130px" /></form>
	</td>
	<td></td>
	</tr></table>
