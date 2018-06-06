<script type="text/javascript">
	Aloha.ready( function() {
		var $ = Aloha.jQuery;
		$('.editable').aloha();
	});
</script>

<form id="addcourse" name="addcourse" method="post" action="<?php echo $this->core->conf['conf']['path'] . "/courses/save"; ?>">
	<p>Please enter the following information</p>
	<input type="hidden" name="item" value="<?php echo $item; ?>" />
	<table width="768" border="0" cellpadding="5" cellspacing="0">
              <tr>
                <td width="205" height="28" bgcolor="#EEEEEE"><strong>Information</strong></td>
                <td width="200" bgcolor="#EEEEEE"><strong>Input field</strong></td>
                <td  bgcolor="#EEEEEE"><strong>Description</strong></td>
              </tr>
              <tr>
                <td><strong>Course name </strong></td>
                <td>
                  <input type="text" name="name" value="<?php echo $name; ?>" />
		</td>
                <td>Name of course</td>
              </tr>
              <tr>
                <td><strong>Course coordinator - Internal</strong></td>
                <td>
                  <select name="internal" id="internal">
			<?php echo $internal; ?>
                  </select></td>
                <td>Functional head of course</td>
              </tr>
              <tr>
                <td><strong>Course coordinator - Distance</strong></td>
                <td>
                  <select name="distance" id="distance">
			<?php echo $distance; ?>
                  </select></td>
                <td>Functional head of course</td>
              </tr>
              <tr>
                <td><strong>Optional description</strong></td>
                <td>
			<textarea rows="4" cols="37" class="editable" name="description"><?php echo $description; ?></textarea>
		  </td>
                <td></td>
              </tr>
	</table>
<br>
<h2>Select which courses are a prerequisite for this course</h2><br>
		<table>
		<tr>
<form id="nselected" name="nselectedfr" method="post" action="<?php echo $this->core->conf['conf']['path'] . "/courses/save/" . $this->core->item; ?>">

		<td  width="175">
		<b>This course is a:</b><br>
		<input type="radio" name="ds" value="1" style="width: 70px"> Minor  <br>
		<input type="radio" name="ds" value="2" style="width: 70px"> Major  <br>
		<input type="radio" name="ds" value="0" style="width: 70px"> Both
		</td>
	
	<td  >
	<input type="hidden" name="item" value="<?php echo $item; ?>" />
	<b>Not selected</b><br>
	<select name="nselected[]" multiple="multiple" size="10" style="width: 130px">';
		<?php echo $notselectedcourses;  ?> 
	</select>  <br>
	<input type="submit" class="submit" name="submit" id="submit" value="Add Selected" style="width: 130px" /></form>
	</td>


	<td> 
	<form id="selected" name="selectedfr" method="post" action="<?php echo $this->core->conf['conf']['path'] . "/courses/save/" . $this->core->item; ?>">
	<input type="hidden" name="item" value="<?php echo $item; ?>" />
		<b>Selected Prerequisites</b><br>
		<select name="selected[]" multiple="multiple" size="10" style="width: 130px">
			<?php echo $selectedcourses;  ?> 
		</select><br>
		<input type="submit" class="submit" name="submit" id="submit" value="Remove Selected" style="width: 130px" />
	</form>
	</td>


</tr>

            </table>
	<br />

	  <input type="submit" class="submit" name="submit" id="submit" value="Save course" />
        <p>&nbsp;</p>

      </form>