<form id="sendmessage" name="sendmessage" method="post" action="<?php echo $this->core->conf['conf']['path'] . "/helpdesk/send/" . $this->core->item; ?>">
	<p>Please enter the following information</p>
	<table width="100%" border="0" cellpadding="5" cellspacing="0">
		<tr>
			<td width="" height="28" bgcolor="#EEEEEE"><strong>Information</strong></td>
			<td width="" bgcolor="#EEEEEE"><strong>Input field</strong></td>
		</tr>
		<tr>
			<td><strong>Title for your ticket</strong><br>Example: Missing Results </td>
			<td>
				<input type="input" name="title" style="width: 100%;" />
					<input type="hidden" name="recipient" value="1" />
			</td>
			
		</tr>
		<tr>
			<td><strong>Ticket information</strong><br>Write a description on what the problem is.</td>
			<td>
				<textarea rows="4" cols="37" style="width: 100%;" class="editable" name="message"></textarea>
			</td>
			
		</tr>
		<tr>
			<td><strong>SUBMIT </strong></td>
			<td>
				<input type="submit" class="submit" name="submit" id="submit" value="Create ticket" style="font-weight: bold; font-size: 14px;" />
			</td>
		</tr>
	</table>
	<br />

	
	<p>&nbsp;</p>

</form>