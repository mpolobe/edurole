<script type="text/javascript">
	Aloha.ready( function() {
		var $ = Aloha.jQuery;
		$('.editable').aloha();
	});
</script>

<form id="edithostels" name="edithostels" method="post" action="<?php echo $this->core->conf['conf']['path'] . "/hostels/save/" . $this->core->item; ?>">
	<p></p>
	<table width="768" border="0" cellpadding="5" cellspacing="0">
		<tr>
			<td width="205" height="28" bgcolor="#EEEEEE"><strong>Information</strong></td>
			<td width="200" bgcolor="#EEEEEE"><strong>Input field</strong></td>
			<td  bgcolor="#EEEEEE"><strong>Description</strong></td>
		</tr>
		 <tr>
                        <td><strong><?php echo $this->core->translate("Hostel name"); ?> </strong></td>
                        <td>
                                <input type="text" name="name" value ="<?php echo $fetch[1]; ?>"  /></td>
                        <td><?php echo $this->core->translate("Name of hostel"); ?></td>
                </tr>

		 <tr>
                        <td><strong><?php echo $this->core->translate("Hostel location"); ?> </strong></td>
                        <td>
                                <input type="text" name="location" value ="<?php echo $fetch[2]; ?>"  /></td>
                        <td><?php echo $this->core->translate("Location of the hostel"); ?></td>
                </tr>
                <tr>
                        <td><strong><?php echo $this->core->translate("Number of rooms"); ?> </strong></td>
                        <td>
                                <input type="text" name="room" value="<?php echo $fetch[3]; ?>"/></td>
                        <td><?php echo $this->core->translate("Number of rooms in the hostel");?></td>
                </tr>
                 <tr>
                        <td><strong><?php echo $this->core->translate("Hostel capacity"); ?> </strong></td>
                        <td>
                                <input type="text" name="capacity" value = "<?php echo $fetch[4]; ?>"/></td>
                        <td><?php echo $this->core->translate("Number of students the hostel can hold");?></td>
                </tr>


                <tr>
                        <td><strong><?php echo $this->core->translate("hostels manager"); ?></strong></td>
                        <td>
                                <select name="dean" id="dean">
                                        <?php echo $this->core->translate($dean); ?>
                                </select></td>
                        <td></td>
                </tr>
                <tr>
                        <td><strong><?php echo $this->core->translate("Optional description"); ?></strong></td>
                        <td>
                                <textarea rows="4" cols="37" class="editable" name="description" ><?php echo $fetch[5]?> </textarea>
                        </td>
                        <td></td><tr>
                        <td><strong><?php $this->core->translate("hostels location"); ?></strong>
                        <td></td>
                </tr>

	</table>
	<br />

	<input type="submit" class="submit" name="submit" id="submit" value="Save hostels" />
	<p>&nbsp;</p>

</form>
