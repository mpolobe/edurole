<div class="bodycontainer">
	<div class="contentpadfull">
		<h2>&nbsp;</h2>
		<ol>
			<p class="title2">Create User Account</p>

			<form id="form1" name="form1" method="post" action="/">
				<table width="813" height="146" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td width="138" valign="top">First / Last name</td>
						<td width="669" valign="top"><input type="text" name="first" id="first"/>
							<input type="text" name="last" id="last"/>
						</td>
					</tr>
					<tr>
						<td valign="top">Username/StudentNo</td>
						<td valign="top">
							<label for="username"></label>
							<input type="text" name="username" id="username"/>

							<select name="ou" id="ou">
								<option value="STAFF">STAFF</option>
								<option value="STUDENT">STUDENT</option>
							</select>
						</td>
					</tr>
					<tr>
						<td height="43" valign="top">&nbsp;</td>
						<td valign="top">
							<input type="submit" name="button" id="button" value="Create User"/>
						</td>
					</tr>
				</table>
			</form>

			<?php
			$ou = $this->core->cleanPost["ou"];
			$username = $this->core->cleanPost["username"];
			$first = $this->core->cleanPost["first"];
			$last = $this->core->cleanPost["last"];
			$uid = rand(10000, 999999);
			$password = rand(10000, 999999);

			if (isset($username) && isset($ou)) {

				if ($ou == "STAFF") {
					//STAFF ACTION
					$file = "secure/staff.txt";
					$fh = fopen($file, 'a') or die("Error please contact ICT department");
					$stringData = "$uid $username $username $password $first $last \n";
					fwrite($fh, $stringData);

					echo "<br><p><b>USER ACCOUNT CREATED</b><br>Username: $username<br>Password: <b>$password</b></p>";

				} else {
					//STUDENT ACTION
					$file = "secure/student.txt";
					$fh = fopen($file, 'a') or die("Error please contact ICT department");
					$stringData = "$uid $username $password $first $last \n";
					fwrite($fh, $stringData);

					echo "<br><p><b>USER ACCOUNT CREATED</b><br>Username: $username<br>Password: <b>$password</b></p>";

				}

			} else {
				echo "<br><br><h2>Please enter all fields</h2>";
			}
			?>

