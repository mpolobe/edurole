<?php

echo '<form id="editschool" name="editschool" method="post" action="/schools&action=save">
	<p>Please enter the following information</p>
        <table 	cellspacing="0" >
          <tr>
            <td><table width="768" border="0" cellpadding="5" cellspacing="0">
              <tr>
                <td width="205" height="28" bgcolor="#EEEEEE"><strong>Information</strong></td>
                <td width="200" bgcolor="#EEEEEE"><strong>Input field</strong></td>
                <td  bgcolor="#EEEEEE"><strong>Description</strong></td>
              </tr>
              <tr>
                <td><strong>Accommodation</strong></td>
                <td>
                  <select name="accommodation" id="accommodation">
				' . $accommodation . '
                  </select></td>
                <td>Selected accommodation</td>
              </tr>
              <tr>
                <td><strong>Room Type</strong></td>
                <td>
                  <input type="text" name="name" value="' . $results['RoomType'] . '" />
				  </td>
                <td>Room type</td>
              </tr>
              <tr>
                <td><strong>Room number</strong></td>
                <td>
				<input type="text" name="name" value="' . $results['RoomNumber'] . '" />
				</td>
                <td></td>
              </tr>
				<tr>
                <td><strong>Housing status</strong></td>
                <td>
                  <select name="status" id="status">
				  	<option value="2">Checked out</option>
					<option value="1">Payment completed</option>
					<option value="0">Payment due</option>
                  </select></td>
                <td></td>
              </tr>
            </table></td>
          </tr>
        </table>
	<br />
	  <input type="hidden" name="item" value="' . $this->core->cleanGet['item'] . '" />
	  <input type="submit" class="submit" name="submit" id="submit" value="Save changes to school" />
        <p>&nbsp;</p>

      </form>';
?>
