<div class="heading"><?php echo $this->core->translate("Apply for accommodation online"); ?></div>
<form id="idsearch" name="paymentadd" method="get" action="<?php echo $this->core->conf['conf']['path']; ?>/accommodation/apply/save">

	<div class="label"><?php echo $this->core->translate("Select your province"); ?>:</div><select name="province" id="province">
					<option selected> SELECT YOUR PROVINCE HERE </option>
					<option value="Central">Central</option>
					<option value="Copperbelt">Copperbelt</option>
					<option value="Eastern">Eastern</option>
					<option value="Luapula">Luapula</option>
					<option value="Lusaka">Lusaka</option>
					<option value="Muchinga">Muchinga</option>
					<option value="North-Western">North-Western</option>
					<option value="Northern">Northern</option>
					<option value="Southern">Southern</option>
					<option value="Western">Western</option>

	</select> <br>


	<input type="hidden" name="number" value="<?php echo $item; ?>"/>

	<div class="label"><?php echo $this->core->translate("What district do you live in?"); ?> </div><input type="text" name="district" value="" class="submit" style="width: 260px" style="width: 260px" /><br>

	<div class="label"><?php echo $this->core->translate("Do you have a disability"); ?> </div><select name="disability" id="disability">
					<option selected  value="No"> NO</option>
					<option value="Yes">Yes</option>

	</select> <br>
	<div class="label">&nbsp;</div><input type="submit" name="submit" id="submit" value="Apply now" class="submit" style="width: 260px"/>
</form>

