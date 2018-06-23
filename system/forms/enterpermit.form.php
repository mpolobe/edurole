
<div class="heading"><?php echo $this->core->translate("Enter your One Time Password (OTP) you have been given after been allowed to register (Please dont enter your login Password here, this password is only given to you if you have been given permission to register after registration has Closed"); ?></div>
<form id="otp" name="otp" method="POST" action="<?php echo $this->core->conf['conf']['path']; ?>/permits/use">


	<div class="label"><?php echo $this->core->translate("OTP code"); ?>:</div>
	<input type="text" name="otp" id="otp" class="submit" style="width: 260px" value=""/> <br />

	<br>
	<div class="label"><?php echo $this->core->translate("Submit"); ?></div>
	<input type="submit" class="submit" value="<?php echo $this->core->translate("Proceed"); ?>" style="width: 260px"/>
	<br>


</form>
