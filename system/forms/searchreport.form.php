
<div class="heading">Senate report printing</div>
<form id="overview" name="overview" method="get"  action="<?php echo $this->core->conf['conf']['path'] . "/report/batch/"; ?>">
	<div class="label">Mode of delivery</div>
	<select name="time" id="time" class="submit" width="250" style="width: 250px">
		<option value="Fulltime">Fulltime</option>
		<option value="Distance" selected>Distance</option>
		<option value="Parttime">Part-time</option>
	</select>

<br>
	<div class="label">Print statement of result for year:</div>

	<select name="uid" id="uid" class="submit" width="250" style="width: 250px">
		<option value="2010">2010</option>
		<option value="2011">2011</option>
		<option value="2012">2012</option>
		<option value="2013">2013</option>
		<option value="2014">2014</option>
		<option value="2015">2015</option>
		<option value="2016" selected>2016</option>
		<option value="2017">2017</option>
		<option value="2018">2018</option>
		<option value="2019">2019</option>
		<option value="2020">2020</option>
	</select>
<br>
	<div class="label">MAJOR/MINOR :</div>

	<select name="major" style="width: 230px">
		<?php echo $programs;  ?> 
	</select>
	<br>
	<div class="label">MINOR/MAJOR :</div>

	<select name="minor" style="width: 230px">
		<?php echo $programs;  ?> 
	</select>

	<input type="submit" class="submit" value="Show print list"/>
</form>
