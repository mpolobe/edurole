<div class="easymencontainer">

	<form id="changepass" name="changepass" method="post" action="">
		<table width="768" height="146" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td width="138" valign="top">&nbsp;</td>
				<td width="669" valign="top">&nbsp;</td>
			</tr>
			<?php
			if($admin!=TRUE){
			echo'<tr>
				<td height="33" valign="top">Verify old password</td>
				<td valign="top"><input type="password" name="oldpass" class="submit"/></td>
			</tr>';
			}
			?>
			<tr>
				<td valign="top"><strong>New password</strong></td>
				<td valign="top"><input type="password" name="newpass" class="submit"/></td>
			</tr>
			<tr>
				<td valign="top"><strong>Verify new password</strong></td>
				<td valign="top"><input type="password" name="newpasscheck" class="submit"/></td>
			</tr>
			<tr>
				<td height="43" valign="top">&nbsp;</td>
				<td valign="top"><br>
					<input type="submit" name="button" class="submit" value="Change Password"/>
				</td>
			</tr>
		</table>
	</form>

</div>