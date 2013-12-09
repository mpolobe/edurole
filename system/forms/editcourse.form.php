<script type="text/javascript">
	Aloha.ready( function() {
		var $ = Aloha.jQuery;
		$('.editable').aloha();
	});
</script>

<form id="addcourse" name="addcourse" method="post" action="<? echo $this->core->conf['conf']['path'] . "/courses/save"; ?>">
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
			<? echo $internal; ?>
                  </select></td>
                <td>Functional head of course</td>
              </tr>
              <tr>
                <td><strong>Course coordinator - Distance</strong></td>
                <td>
                  <select name="distance" id="distance">
			<? echo $distance; ?>
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
	<br />

	  <input type="submit" class="submit" name="submit" id="submit" value="Create course" />
        <p>&nbsp;</p>

      </form>