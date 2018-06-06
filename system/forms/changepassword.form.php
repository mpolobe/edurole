<form id="recover" name="recover" method="post" action="">

	<p>Please provide the needed information to recover your account password.</p>

	<table width="700" border="0" cellpadding="5" cellspacing="0">

		<tr>
			<td><strong>National ID number</strong></td>
			<td>
				<input type="text" name="uid"/>
			</td>
		</tr>
		<tr>
			<td><strong>Student Number </strong></td>
			<td>
				<input type="text" name="studentid"/></td>
		</tr>
		<tr>
			<td><img id="captcha" src="<?php echo $this->core->conf['conf']['path']; ?>/lib/secureimage/securimage_show.php" alt="CAPTCHA Image"/></td>
			<td><input type="text" name="captcha_code" class="captcha_code" size="10" maxlength="6"/></td>
			<td>Please enter the text in the verification image, if you are having difficulty reading the text please
				click <a href="#" onclick="document.getElementById('captcha').src = '<?php echo $this->core->conf['conf']['path']; ?>/lib/secureimage/securimage_show.php?' + Math.random(); return false">here.</a>
			</td>
		</tr>
	</table>

	<p>Click on the button to complete your request for admission. </p>

	<input type="submit" id="submit" class="submit" value="Recover password"/>

	<p>&nbsp;</p>

</form>