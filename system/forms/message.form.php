
<script type="text/javascript">
	Aloha.ready( function() {
		var $ = Aloha.jQuery;
		$('.editable').aloha();
	});
</script>

<form id="sendmessage" name="sendmessage" method="post" action="<?php echo $this->core->conf['conf']['path'] . "/message/send/" . $this->core->item; ?>">
	<p>Please enter the following information</p>
	<table width="700px" border="0" cellpadding="5" cellspacing="0">
		<tr>
			<td width="200px" height="28" bgcolor="#EEEEEE"><strong>Information</strong></td>
			<td width="" bgcolor="#EEEEEE"><strong>Input field</strong></td>
		</tr>
		<tr>
			<td><strong> Enter your full student number</strong><br>For example: 20140123</td>
			<td>
				<input type="text" name="uid" style="width: 100%;" />
			</td>
			
		</tr>
		<tr>
			<td><strong>Enter your phone number </strong><br>Example: 0961234567</td>
			<td>
				<input type="location" name="phone" style="width: 100%;" />
			</td>
			
		</tr>
		<tr>
			<td><strong>Your message</strong><br>For example: "My missing courses are EDU400 and PHY450"</td>
			<td>
					<textarea rows="4" cols="37" style="width: 100%;" class="editable" name="message"></textarea>
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