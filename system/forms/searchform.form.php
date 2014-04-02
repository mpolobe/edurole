<div class="heading"><?php echo $this->core->translate("Search by student number"); ?></div>
<form id="idsearch" name="idsearch" method="get" action="">
	<div class="label"><?php echo $this->core->translate("Enter student number"); ?>:</div>
	<input type="text" name="uid" id="student-id" class="submit" style="width: 125px"/>
	<input type="submit" class="submit" value="<?php echo $this->core->translate("Open Record"); ?>"/>
</form>

<form id="namesearch" name="namesearch" method="get" action="">
	<div class="heading"><?php echo $this->core->translate("Search by Name"); ?></div>

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

<form id="studysearch" name="studysearch" method="get" action="">
	<div class="heading"><?php echo $this->core->translate("View students by study"); ?></div>
	<div class="label"><?php echo $this->core->translate("Show all students from"); ?>:</div>
	<input type="hidden" name="search" value="study">
	<select name="q" id="studies" class="submit" width="250" style="width: 250px">
		<?php echo $study; ?>
	</select> <br/>

	<div class="label"><?php echo $this->core->translate("Show as"); ?>:</div>
	<select name="listtype" class="submit">
		<option value="list"><?php echo $this->core->translate("List of Students"); ?></option>
		<option value="profiles"><?php echo $this->core->translate("Profile View"); ?></option>
	</select>
	<input type="submit" class="submit" value="<?php echo $this->core->translate("View Records"); ?>"/>
</form>


<form id="programmesearch" name="programmesearch" method="get" action="">
	<div class="heading"><?php echo $this->core->translate("View students by programme"); ?></div>
	<div class="label"><?php echo $this->core->translate("Show all students from"); ?>:</div>
	<input type="hidden" name="search" value="programme">
	<select name="q" id="program" class="submit" width="250" style="width: 250px">
		<?php echo $program; ?>
	</select>
	<br/>

	<div class="label"><?php echo $this->core->translate("Show as"); ?>:</div>
	<select name="listtype" class="submit">
		<option value="list"><?php echo $this->core->translate("List of Students"); ?></option>
		<option value="profiles"><?php echo $this->core->translate("Profile View"); ?></option>
	</select>
	<input type="submit" class="submit" value="<?php echo $this->core->translate("View records"); ?>"/>
</form>


<form id="coursesearch" name="coursesearch" method="get" action="">
	<div class="heading"><?php echo $this->core->translate("View students by course"); ?></div>
	<div class="label"><?php echo $this->core->translate("Show all students from"); ?>:</div>
	<input type="hidden" name="search" value="course">
	<select name="q" id="course" class="submit" width="250" style="width: 250px">
		<?php echo $courses; ?>
	</select>
	<br/>

	<div class="label"><?php echo $this->core->translate("Show as"); ?>:</div>
	<select name="listtype" class="submit">
		<option value="list"><?php echo $this->core->translate("List of Students"); ?></option>
		<option value="profiles"><?php echo $this->core->translate("Profile View"); ?></option>
	</select>
	<input type="submit" class="submit" value="<?php echo $this->core->translate("View records"); ?>"/>
</form>
