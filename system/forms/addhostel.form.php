<div class="heading"><?php echo $this->core->translate("Add Hostel"); ?></div>
<form id="idsearch" name="paymentadd" method="get" action="<?php echo $this->core->conf['conf']['path']; ?>/accommodation/hostel/save">

<div class="label"><?php echo $this->core->translate("Gender"); ?></div><select name="gender" style="width: 260px">
		<option value="">-choose gender-</option>
		<option value="Female">Female</option>
		<option value="Male">Male</option>
	</select><br />

	<div class="label"><?php echo $this->core->translate("Hostel Name"); ?>:</div><input type="text" name="name" id="name" class="submit" style="width: 260px" /><br>

	<div class="label">&nbsp;</div><input type="submit" name="submit" id="submit" class="submit" style="width: 260px"/>
</form>
