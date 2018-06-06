

<form id="idsearch" name="idsearch" method="get" action="<?php echo $this->core->conf['conf']['path'] . "/information/search"; ?>">

	<div class="label"><?php echo $this->core->translate("Enter student number"); ?>:</div>
	<input type="text" name="uid" id="student-id" class="submit" style="width: 125px"/>
	<input type="hidden" name="payid" id="payid" value="<?php echo $item; ?>"/>
	<input type="hidden" name="date" id="date" value="<?php echo $this->core->cleanGet['date']; ?>"/>
	<input type="submit" class="submit" value="<?php echo $this->core->translate("Open Record"); ?>"/>
</form>


<form id="namesearch" name="namesearch" method="get" action="<?php echo $this->core->conf['conf']['path'] . "/information/search"; ?>">
	<div class="heading"><?php echo $this->core->translate("Search by Name"); ?></div>
	<input type="hidden" name="payid" id="payid" value="<?php echo $item; ?>"/>
	<input type="hidden" name="date" id="date" value="<?php echo $this->core->cleanGet['date']; ?>"/>
	<div class="padding">
		<div class="label"><?php echo $this->core->translate("Enter students first name"); ?>:</div>
		<input type="text" name="studentfirstname" id="studentfirstname" style="width: 230px" class="submit"/><br>
	</div>
	<div class="padding">
		<div class="label"> <?php echo $this->core->translate("and/or surname"); ?>:</div>
		<input type="text" name="studentlastname" id="studentlastname" style="width: 230px" class="submit"/>
	</div>
	<div class="label"><?php echo $this->core->translate("Show as"); ?>:</div>
	<select name="listtype" class="submit">
		<option value="list"><?php echo $this->core->translate("List of Students"); ?></option>
		<option value="profiles"><?php echo $this->core->translate("Profile View"); ?></option>
	</select> <input type="submit" class="submit" value="<?php echo $this->core->translate("Search Records"); ?>"/>
	</select>
</form>
