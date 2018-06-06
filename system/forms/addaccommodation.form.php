<script type="text/javascript">
	Aloha.ready( function() {
		var $ = Aloha.jQuery;
		$('.editable').aloha();
	});
</script>

<form id="addaccommodation" name="addaccommodation" method="post" action="<?php echo $this->core->conf['conf']['path'] . "/accommodation/save/" . $this->core->item; ?>">
	<p>Please enter the following information</p>
	<table width="768" border="0" cellpadding="5" cellspacing="0">
		<tr>
			<td width="205" height="28" bgcolor="#EEEEEE"><strong>Information</strong></td>
			<td width="200" bgcolor="#EEEEEE"><strong>Input field</strong></td>
			<td  bgcolor="#EEEEEE"><strong>Description</strong></td>
		</tr>
		<tr>
			<td><strong>Accommodation name </strong></td>
			<td>
				<input type="text" name="name" /></td>
			<td>Name of accommodation</td>
		</tr>
		<tr>
			<td><strong>Accommodation location </strong></td>
			<td>
				<input type="location" name="location" /></td>
			<td>Name of accommodation</td>
		</tr>
		<tr>
			<td><strong>Accommodation manager</strong></td>
			<td>
				<select name="dean" id="dean">
					<?php echo $dean; ?>
				</select></td>
			<td></td>
		</tr>
		<tr>
			<td><strong>Optional description</strong></td>
			<td>
				<textarea rows="4" cols="37" class="editable" name="description"></textarea>
			</td>
			<td></td>
		</tr>
	</table>
	<br />

	<input type="submit" class="submit" name="submit" id="submit" value="Create accommodation" />
	<p>&nbsp;</p>

</form>