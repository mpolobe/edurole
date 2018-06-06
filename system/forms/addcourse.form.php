<script type="text/javascript">
	Aloha.ready( function() {
		var $ = Aloha.jQuery;
		$('.editable').aloha();
	});
</script>


<form id="addcourse" name="addcourse" method="post" action="<?php echo $this->core->conf['conf']['path'] . "/courses/save"; ?>">
	<p>Please enter the following information</p>
	<table width="768" border="0" cellpadding="5" cellspacing="0">

              <tr>
                <td width="205" height="28" bgcolor="#EEEEEE"><strong>Information</strong></td>
                <td width="200" bgcolor="#EEEEEE"><strong>Input field</strong></td>
                <td  bgcolor="#EEEEEE"><strong>Description</strong></td>
              </tr>

              <tr>
                <td><strong>Course Code </strong></td>
                <td>
                  <input type="text" name="name" /></td>
                <td>Name of course</td>
              </tr>

              <tr>
                <td><strong>Course Coordinator - Internal</strong></td>
                <td>
                  <select name="internal" id="internal">
			<?php echo $internal; ?>
                  </select></td>
                <td>Functional head of course</td>
              </tr>

              <tr>
                <td><strong>Course Coordinator - Distance</strong></td>
                <td>
                  <select name="distance" id="distance">
			<?php echo $distance; ?>
                  </select></td>
                <td>Functional head of course</td>
              </tr>

              <tr>
                <td><strong>Course Credits</strong></td>
                <td>
                  <input type="text" name="credits" style="width: 60px;"/> <b> Credits</b>
		</td>
                <td></td>
              </tr>

              <tr>
                <td><strong>Exam Weight</strong></td>
                <td>
                  <input type="text" name="examweight" style="width: 60px;"/> <b>%</b>
		</td>
                <td></td>
              </tr>

              <tr>
                <td><strong>Assessment Weight</strong></td>
                <td>
                  <input type="text" name="assessmentweight" style="width: 60px;"/> <b>%</b>
		</td>
                <td></td>
              </tr>

            <tr>
                <td><strong>Year</strong></td>
                <td>
                  <select name="year" id="year">
			<option value="1">Year 1</option>
			<option value="2">Year 2</option>
			<option value="3">Year 3</option>
			<option value="4">Year 4</option>
			<option value="5">Year 5</option>
			<option value="0" selected>All years</option>
                  </select></td>
                <td>Functional head of course</td>
              </tr>

            <tr>
                <td><strong>Terms</strong></td>
                <td>
                  <select name="term" id="term">
			<option value="1">Term 1</option>
			<option value="2">Term 2</option>
			<option value="3">Term 3</option>
			<option value="0" selected>All terms</option>
                  </select></td>
                <td>Functional head of course</td>
              </tr>

              <tr>
                <td><strong>Course Name</strong></td>
                <td>
			<textarea rows="4" cols="37" class="editable" name="description"></textarea>
		  </td>
                <td></td>
              </tr>

            </table>
	<br />

	  <input type="submit" class="submit" name="submit" id="submit" value="Create course" />
        <p>&nbsp;</p>

      </form>