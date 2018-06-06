<div class="heading"><?php echo $this->core->translate("Confirm payment"); ?></div>
<form id="idsearch" name="paymentadd" method="get" action="<?php echo $this->core->conf['conf']['path']; ?>/payments/modify">

	<div class="label"><?php echo $this->core->translate("Type of payment"); ?></div>
	<select name="paymenttype" style="width: 260px">
		<option value="4">Billmuster</option>
	</select><br />

	<div class="label"><?php echo $this->core->translate("Student ID"); ?>:</div>
	<input type="text" name="uidvisible" id="studentid" class="submit" style="width: 260px" value="<?php echo $item; ?>"/>
	<input type="hidden" name="uid" id="uid" class="submit" style="width: 260px" value="<?php echo $item; ?>"/><br />

	<div class="label"><?php echo $this->core->translate("Payment Reference"); ?>:</div>
	<input type="text" name="referencevisible" id="paymentid" class="submit" style="width: 260px"/>
	<input type="hidden" name="reference" id="reference" class="submit" style="width: 260px"/><br />


	<div class="label"><?php echo $this->core->translate("Submit"); ?></div>
	<input type="submit" class="submit" value="<?php echo $this->core->translate("Confirm Payment"); ?>" style="width: 125px"/>
</form>

 <script type="text/javascript">
 document.getElementById("paymentid").focus();
 </script>


<script>
$('#paymentid').autocomplete({
    serviceUrl: '/edurole/api/checkvalue/payment',
    onSelect: function (suggestion) {
  	$(reference).val(suggestion.data);
    }
});

$('#studentid').autocomplete({
    serviceUrl: '/edurole/api/checkvalue/student',
    onSelect: function (suggestion) {
    	$(uid).val(suggestion.data);
    }
});
</script>