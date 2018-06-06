<script type="text/javascript">
	Aloha.ready( function() {
		var $ = Aloha.jQuery;
		$('.editable').aloha();
	});
</script>

<form id="addhousing" name="addhousing" method="post" action="<?php echo $this->core->conf['conf']['path'] . "/housing/save/" . $this->core->item; ?>">
	<p>Please enter the following information</p>
	<table width="768" border="0" cellpadding="5" cellspacing="0">
		<tr>
			<td width="205" height="28" bgcolor="#EEEEEE"><strong>Information</strong></td>
			<td width="200" bgcolor="#EEEEEE"><strong>Input field</strong></td>
			<td  bgcolor="#EEEEEE"><strong>Description</strong></td>
		</tr>
		<tr>
			<td><strong>Student name</strong></td>
			<td>
				<input type="text" name="name" value="<?php echo $studentname; ?>"/>
			</td>
			<td>Name of housing</td>
		</tr>
		<tr>
			<td><strong>Housing status</strong></td>
			<td>
			<select name="status">

				<?php
				echo '<option value="0" ';	if ($fetch[4] == "0") {	echo 'selected=""';	}	echo '>-choose-</option> ';
				echo '<option value="1" ';	if ($fetch[4] == "1") {	echo 'selected=""';	}	echo '>Request for housing</option>';
				echo '<option value="2" ';	if ($fetch[4] == "2") {	echo 'selected=""';	}	echo '>Housing payed</option>';
				echo '<option value="3" ';	if ($fetch[4] == "3") {	echo 'selected=""';	}	echo'>Housing assigned</option>';
				echo '<option value="4" ';	if ($fetch[4] == "4") {	echo 'selected=""';	}	echo'>End of housing</option>';
				?>

				</select>
			</td>
			<td></td>
		</tr>
		<tr>
			<td><strong>Accommodation selection</strong></td>
			<td>
				<select name="accommodation" id="accommodation">
					<?php echo $accommodation; ?>
				</select>
			</td>
			<td></td>
		</tr>
	</table>
	<br />

	<input type="submit" class="submit" name="submit" id="submit" value="Create housing" />
	<p>&nbsp;</p>

</form>