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

<form id="changeprogramme" name="changeprogramme" method="post" action="<?php echo $this->core->conf['conf']['path'] . "/programmes/change/" . $this->core->item; ?>">
	<p>You are editing:<b> <?php echo  $this->core->item; ?></b>  </p>
	<p>

	<table width="768" border="0" cellpadding="5" cellspacing="0">
        <tr>
                <td width="205" height="28" bgcolor="#EEEEEE"><strong>Information</strong></td>
                <td width="200" bgcolor="#EEEEEE"><strong>Input field</strong></td>
                <td  bgcolor="#EEEEEE"><strong>Description</strong></td>
        </tr>


	<tr><td width="150">Select Study</td>
	<td>

		<select name="study" style="width: 230px">
			<?php echo $study;  ?> 
		</select><br>

	</td>
	<td></td>
	</tr>



	<tr><td width="150">New Major</td>
	<td>

		<select name="major"   style="width: 230px">
			<?php echo $major;  ?> 
		</select><br>

	</td>
	<td></td>
	</tr>

	<tr>
	<td>New Minor</td>
	<td>

		<select name="minor" style="width: 230px">
			<?php echo $minor;  ?> 
		</select><br>

	</td>
	<td></td>
	</tr>




	</select>
	</td>
	<td></td>
	</tr>
	<tr>
	<td></td>
	<td>
		<input type="submit" class="submit" name="submit" id="submit" value="Change programmes" />
	</td>
	<td></td>
	</tr>

	</table>

</form>

