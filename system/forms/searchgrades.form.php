<form id="studysearch" name="studysearch" method="get" action="">
	<div class="heading">View grades by study</div>
	<div class="label">Show all grades from:</div>
	<input type="hidden" name="action" value="view">
	<select name="studies" id="studies" class="submit" width="300" style="width: 250px">
		<?php echo $study; ?>
	</select> <br/>
	<div class="label"></div><input type="submit" class="submit" value="View records"/>
</form>


<form id="programmesearch" name="programmesearch" method="get" action="">
	<div class="heading">View grades by programme</div>
	<div class="label">Show all grades from:</div>
	<input type="hidden" name="action" value="view">
	<select name="program" id="program" class="submit" width="300" style="width: 250px">
		<?php echo $program; ?>
	</select>
	<br/>
	<div class="label"></div><input type="submit" class="submit" value="View records"/>
</form>


<form id="coursesearch" name="coursesearch" method="get" action="">
	<div class="heading">View grades by course</div>
	<div class="label">Show all grades from:</div>
	<input type="hidden" name="action" value="view">
	<select name="course" id="course" class="submit" width="300" style="width: 250px">
		<?php echo $courses; ?>
	</select>
	<br/>
	<div class="label"></div><input type="submit" class="submit" value="View records"/>
</form>