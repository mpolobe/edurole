<div class="heading"><?php echo $this->core->translate("Add payment"); ?></div>
<form id="idsearch" name="paymentadd" method="get" action="<?php echo $this->core->conf['conf']['path']; ?>/payments/save">

<div class="label"><?php echo $this->core->translate("Type of payment"); ?></div>

	<select name="paymenttype" style="width: 260px">
		<option value="0" selected>-choose-</option>
		<option value="20">CREDIT NOTE</option>
		<option value="10">Cash payment (LOCAL)</option>
		<option value="1">Cash deposit (BANK)</option>
		<option value="2">Cheque</option>
		<option value="5">Direct bank transfer</option>
		<option value="6">Work Program</option>
	</select><br />



<div class="label"><?php echo $this->core->translate("Bank"); ?></div>
	<select name="bank" style="width: 260px">
		<option value="0" selected>-choose-</option>
		<option value="8">Internal</option>
		<option value="1">ZANACO Billmuster</option>
		<option value="2">ZANACO Main</option>
		<option value="3">ZANACO FOREX</option>
		<option value="4">Finance Bank Curent</option>
		<option value="5">Barclays Bank Curent</option>
		<option value="10">Other</option>
	</select><br />

	<div class="label"><?php echo $this->core->translate("Student ID"); ?>:</div>
	<input type="text" name="uidvisible" id="studentid" class="submit" style="width: 260px" value="<?php echo $item; ?>"/>
	<input type="hidden" name="uid" id="uid" class="submit" style="width: 260px" value="<?php echo $item; ?>"/><br />

	<div class="label"><?php echo $this->core->translate("Payment Amount"); ?>:</div>
	<input type="text" name="amount" id="amount" class="submit" style="width: 260px" value="<?php echo $amount; ?>"/> ZMW<br />

	<div class="label"><?php echo $this->core->translate("Payment reference / cheque"); ?>:</div>
	<input type="text" name="reference" id="paymentid" class="submit" style="width: 260px" value="<?php echo $paymentid; ?>"/><br />

	<div class="label"><?php echo $this->core->translate("Payment for"); ?>:</div>
	<input type="text" name="description" id="description" class="submit" style="width: 260px" value="<?php echo $description; ?>"/><br />

	<div class="label"><?php echo $this->core->translate("Date of Payment"); ?>:</div>
	<input type="text" name="date" id="date" class="submit" style="width: 260px" value="<?php echo date('Y-m-d'); ?>"/> YYYY-MM-DD<br />

	<div class="label"><?php echo $this->core->translate("Submit"); ?></div>
	<input type="submit" class="submit" value="<?php echo $this->core->translate("Add Payment"); ?>" style="width: 260px"/>
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