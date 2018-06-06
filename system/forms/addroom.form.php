<div class="heading"><?php echo $this->core->translate("Add Hostel"); ?></div>
<form id="idsearch" name="paymentadd" method="get" action="<?php echo $this->core->conf['conf']['path']; ?>/accommodation/rooms/save">

	<div class="label"><?php echo $this->core->translate("Room Type"); ?>:</div><input type="text" name="type" id="type" class="submit" style="width: 260px" /><br>

	<div class="label"><?php echo $this->core->translate("Room Number"); ?>:</div><input type="text" name="number" class="submit" style="width: 260px" style="width: 260px" /><br>

	<div class="label"><?php echo $this->core->translate("Capacity"); ?>:</div><input type="text" name="capacity" value="" class="submit" style="width: 260px" style="width: 260px" /><br>

	<div class="label"><?php echo $this->core->translate("Price"); ?>:</div><input type="text" name="price" value="" class="submit" style="width: 260px" style="width: 260px" /><br>


	<input type="hidden" name="hostel" value="<?php echo $this->core->subitem; ?>">
	<div class="label">&nbsp;</div><input type="submit" name="submit" id="submit" class="submit" style="width: 260px"/>
</form>

