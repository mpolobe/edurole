<div class="heading"><?php echo $this->core->translate("STUDENT IDENTITY CARD REPLACEMENT FORM"); ?></div>
<form id="idsearch" name="paymentadd" method="get" action="<?php echo $this->core->conf['conf']['path']; ?>/cards/replace">



	<div class="label"><?php echo $this->core->translate("Student ID"); ?>:</div>
	<input type="text" name="uidvisible" id="studentid" class="submit" style="width: 260px" value="<?php echo $this->core->userID; ?>" readonly>
	<input type="hidden" name="uid" id="uid" class="submit" style="width: 260px" value="<?php echo $item; ?>"/><br />

	<div class="label"><?php echo $this->core->translate("Phone number"); ?>:</div>
	<input type="text" name="phone" id="amount" class="submit" style="width: 260px" value="<?php echo $phone; ?>"/><br />

	<div class="warningpopup"><?php echo $this->core->translate("By submitting this form you agree to the payment of 100 Kwacha for the replacement of your student identity card."); ?></div>

	<div class="label"><?php echo $this->core->translate("Submit"); ?></div>
	<input type="submit" class="submit" value="<?php echo $this->core->translate("Confirm replacement request"); ?>" style="width: 260px"/>
	<br>




</form>

