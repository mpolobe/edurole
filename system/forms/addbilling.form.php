<div class="heading"><?php echo $this->core->translate("Bill Client"); ?></div>
<form id="idsearch" name="billingadd" method="get" action="<?php echo $this->core->conf['conf']['path']; ?>/payments/save">

	<input type="hidden" value="15" name="paymenttype">

	<div class="label"><?php echo $this->core->translate("Student ID"); ?>:</div>
	<input type="text" name="uidvisible" id="studentid" class="submit" style="width: 260px" value="<?php echo $item; ?>"/>
	<input type="hidden" name="uid" id="uid" class="submit" style="width: 260px" value="<?php echo $item; ?>"/><br />

	<div class="label"><?php echo $this->core->translate("Bill Amount"); ?>:</div>
	<input type="text" name="amount" id="amount" class="submit" style="width: 260px" value="<?php echo $amount; ?>"/> ZMW<br />

	<div class="label"><?php echo $this->core->translate("Billing reference"); ?>:</div>
	<input type="text" name="reference" id="paymentid" class="submit" style="width: 260px" value="<?php echo $paymentid; ?>"/><br />

	<div class="label"><?php echo $this->core->translate("Bill for"); ?>:</div>
	<input type="text" name="description" id="description" class="submit" style="width: 260px" value="<?php echo $description; ?>"/><br />

	<div class="label"><?php echo $this->core->translate("Date of Billing"); ?>:</div>
	<input type="text" name="date" id="date" class="submit" style="width: 260px" value="<?php echo date('Y-m-d'); ?>"/> YYYY-MM-DD<br />

	<div class="label"><?php echo $this->core->translate("Submit"); ?></div>
	<input type="submit" class="submit" value="<?php echo $this->core->translate("Bill Client"); ?>" style="width: 260px"/>
	<br>


</form>

<script>

jQuery('#studentid').autocomplete({
    serviceUrl: '<?php echo $this->core->conf['conf']['path']; ?>/api/checkvalue/student',
    onSelect: function (suggestion) {
    	$(uid).val(suggestion.data);
    }
});

</script>