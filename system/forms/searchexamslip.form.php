
<div class="heading">Search by student number</div>
<form id="results" name="results" method="get"  action="<?php echo $this->core->conf['conf']['path'] . "/examination/results/"; ?>">
	<div class="label">Enter student number:</div>
	<input type="text" name="uid" id="student-id" class="submit" style="width: 125px"/>
	<input type="submit" class="submit" value="Open record"/>
</form>
<br>
<div class="heading">Print list of exam slips per intake</div>
<form id="overview" name="overview" method="get"  action="<?php echo $this->core->conf['conf']['path'] . "/examination/batch/"; ?>">
	<div class="label">Mode of delivery</div>
	<select name="time" id="time" class="submit" width="250" style="width: 250px">
		<option value="Fulltime">Fulltime</option>
		<option value="Distance">Distance</option>
		<option value="Parttime">Part-time</option>
	</select>


<br>

	<div class="label">Print statement of result for intake year:</div>

	<select name="period" id="period" class="submit" width="250" style="width: 250px">
		<?php echo $periods; ?>
	</select>
<br>
	<div class="label">School</div>
	<select name="schools" id="schools" class="submit" width="250" style="width: 250px">
		<?php echo $schools; ?>
	</select>
<br>

	<input type="submit" class="submit" value="Show print list"/>
</form>