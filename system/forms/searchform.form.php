<form id="idsearch" name="idsearch" method="get" action="">
	<input type="hidden" name="id" value="information">

	<div class="label">Enter student number:</div>
	<input type="text" name="uid" id="student-id" class="submit" style="width: 125px"/>
	<input type="submit" class="submit" value="Open record"/>
</form>

<form id="namesearch" name="namesearch" method="get" action="">
	<div class="heading">Search by name</div>
	<input type="hidden" name="id" value="information">

	<div class="padding">
		<div class="label">Enter students first name:</div>
		<input type="text" name="studentfirstname" id="studentfirstname" style="width: 230px" class="submit"/><br>
	</div>
	<div class="padding">
		<div class="label"> and/or surname:</div>
		<input type="text" name="studentlastname" id="studentlastname" style="width: 230px" class="submit"/>
	</div>
	<div class="label">Show as:</div>
	<select name="listtype" class="submit">
		<option value="profiles">Profile view</option>
		<option value="list">List of students</option>
	</select> <input type="submit" class="submit" value="Search records"/>
	</select>
</form>

<form id="studysearch" name="studysearch" method="get" action="">
	<div class="heading">View students by study</div>
	<div class="label">Show all students from:</div>
	<input type="hidden" name="search" value="study">
	<select name="q" id="studies" class="submit" width="250" style="width: 250px">
		<?php echo $study; ?>
	</select> <br/>

	<div class="label">Show as:</div>
	<select name="listtype" class="submit">
		<option value="list">List of students</option>
		<option value="profiles">Profile view</option>
	</select>
	<input type="submit" class="submit" value="View records"/>
</form>


<form id="programmesearch" name="programmesearch" method="get" action="">
	<div class="heading">View students by programme</div>
	<div class="label">Show all students from:</div>
	<input type="hidden" name="search" value="programme">
	<select name="q" id="program" class="submit" width="250" style="width: 250px">
		<?php echo $program; ?>
	</select>
	<br/>

	<div class="label">Show as:</div>
	<select name="listtype" class="submit">
		<option value="list">List of students</option>
		<option value="profiles">Profile view</option>
	</select>
	<input type="submit" class="submit" value="View records"/>
</form>


<form id="coursesearch" name="coursesearch" method="get" action="">
	<div class="heading">View students by course</div>
	<div class="label">Show all students from:</div>
	<input type="hidden" name="search" value="course">
	<select name="q" id="course" class="submit" width="250" style="width: 250px">
		<?php echo $courses; ?>
	</select>
	<br/>

	<div class="label">Show as:</div>
	<select name="listtype" class="submit">
		<option value="list">List of students</option>
		<option value="profiles">Profile view</option>
	</select>
	<input type="submit" class="submit" value="View records"/>
</form>