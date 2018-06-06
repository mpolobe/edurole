
<div style="position: absolute; top: -100px; "> 
<div class="heading"><?php echo $this->core->translate("Search by EduCard"); ?></div>
<form id="idsearch" name="idsearch" method="get" action="">
	<div class="label"><?php echo $this->core->translate("Scan card"); ?>:</div>
	<input type="text" name="card" id="card" class="submit" style="width: 125px"/>
	<input type="submit" class="submit" value="<?php echo $this->core->translate("Open"); ?>" style="width: 125px"/>
</form>
</div>

 <script type="text/javascript">
 document.getElementById("card").focus();
 </script>