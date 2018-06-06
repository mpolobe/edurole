<form id="sendmessage" name="sendmessage" method="post" action="<?php echo $this->core->conf['conf']['path'] . "/helpdesk/send/" . $this->core->item; ?>">
	<p>Please enter the following information</p>
	<table width="100%" border="0" cellpadding="5" cellspacing="0">
		<tr class="heading">
			<td colspan="2"><strong>Message</strong></td>
		</tr>
		<tr>
			<td width="120px"><strong>Recipient</strong><br> </td>
			<td>
				<input type="input" id="recipients" name="recipients" style="width: 100%;" value="<?php echo $name; ?>"/>
				<input type="hidden" id="recipient"  name="recipient" value="<?php echo $uid; ?>"/>
			</td>
			
		</tr>
		<tr>
			<td><strong>Subject</strong><br> </td>
			<td>
				<input type="input" name="title" style="width: 100%;" value="<?php echo $item; ?>" />
			</td>
			
		</tr>
		<tr>
			<td><strong>Message</strong><br></td>
			<td>
				<textarea rows="4" cols="37" style="width: 100%;" class="editable" name="message"><?php echo $message; ?></textarea>
			</td>
			
		</tr>
		<tr>
			<td><strong>SUBMIT </strong></td>
			<td>
				<input type="submit" class="submit" name="submit" id="submit" value="Send message" style="font-weight: bold; font-size: 14px;" />
			</td>
		</tr>
	</table>
	<br />

	
	<p>&nbsp;</p>

</form>


<script>

jQuery('#recipients').autocomplete({
    serviceUrl: '<?php echo $this->core->conf['conf']['path']; ?>/api/checkvalue/student',
    onSelect: function (suggestion) {
    	$(recipient).val(suggestion.data);
    }
});

</script>