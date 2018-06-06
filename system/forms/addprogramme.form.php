<script type="text/javascript">
	Aloha.ready( function() {
		var $ = Aloha.jQuery;
		$('.editable').aloha();
	});
</script>

<form id="addhousing" name="addhousing" method="post" action="<?php echo $this->core->conf['conf']['path'] . "/programmes/save/" . $this->core->item; ?>">
	<p>Please enter the following information</p>
        <table cellspacing="0" >
          <tr>
            <td><table width="768" border="0" cellpadding="5" cellspacing="0">
              <tr>
                <td width="205" height="28" bgcolor="#EEEEEE"><strong>Information</strong></td>
                <td width="200" bgcolor="#EEEEEE"><strong>Input field</strong></td>
                <td  bgcolor="#EEEEEE"><strong>Description</strong></td>
              </tr>
              <tr>
		<td width="150"><b>Name of Programme</b></td>
                <td>
                  <input type="text" name="name" value="" /></td>
                <td></td>
              </tr>
              <tr>
		<td width="150"><b>Programme Coordinator</b></td>
                <td>
                  <select name="coordinator" id="coordinator">
					<?php echo $users; ?>
                  </select></td>
                <td>Functional course coordinator</td>
              </tr>
		<tr><td>Programme Type</td>
		<td><select name="programtype">

		<option value="0">-choose-</option>
		<option value="1">Minor</option>
		<option value="2">Major</option>
		<option value="5" >Diploma</option>
		<option value="4" >Compulsory</option>
		<option value="3" >Available as both</option>

		</select></td>
		<td></td>
		</tr>

            </table></td>
          </tr>
        </table>
	<br />
	  <input type="hidden" name="item" value="" />
	  <input type="submit" class="submit" name="submit" id="submit" value="Save changes to programme" />
        <p>&nbsp;</p>

      </form>