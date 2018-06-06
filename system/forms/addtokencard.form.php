<div class="heading"><?php echo $this->core->translate("Add Tokens to EduCard"); ?></div>
<form id="idsearch" name="tokenadd" method="get" action="">
	<div class="label"><?php echo $this->core->translate("Token Amount"); ?>:</div>
	<input type="text" name="token" id="token" class="submit" style="width: 125px"/>
	<input type="hidden" name="card" id="cardh" value="<?php echo $card ?>"/>
	<input type="submit" class="submit" value="<?php echo $this->core->translate("Add Tokens"); ?>" style="width: 125px"/>
</form>

