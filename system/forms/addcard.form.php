<div class="heading"><?php echo $this->core->translate("Assign EduCard to user"); ?></div>
<form id="idsearch" name="cardadd" method="get" action="">
	<div class="label"><?php echo $this->core->translate("User ID for the user"); ?>:</div>
	<input type="text" name="userid" id="userid" class="submit" style="width: 125px"/>
	<input type="hidden" name="card" id="cardh" value="<?php echo $card ?>"/>
	<input type="submit" class="submit" value="<?php echo $this->core->translate("Assign Card"); ?>" style="width: 125px"/>
</form>

 <script type="text/javascript">
 document.getElementById("userid").focus();
 </script>
