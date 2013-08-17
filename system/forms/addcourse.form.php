<?php
include "includes/functions/showoptions.inc.php";

$select = showUsers("100", null);

echo '<form id="addschool" name="addschool" method="post" action="?id=schools&action=save">
	<p>Please enter the following information</p>
	<table width="768" border="0" cellpadding="5" cellspacing="0">
              <tr>
                <td width="205" height="28" bgcolor="#EEEEEE"><strong>Information</strong></td>
                <td width="200" bgcolor="#EEEEEE"><strong>Input field</strong></td>
                <td  bgcolor="#EEEEEE"><strong>Description</strong></td>
              </tr>
              <tr>
                <td><strong>School name </strong></td>
                <td>
                  <input type="text" name="name" /></td>
                <td>Name of school</td>
              </tr>
              <tr>
                <td><strong>Dean/Rector of school</strong></td>
                <td>
                  <select name="dean" id="dean">
			' . $select . '
                  </select></td>
                <td>Functional head of school</td>
              </tr>
              <tr>
                <td><strong>Optional description</strong></td>
                <td>
			<textarea rows="4" cols="37" name="description"></textarea>
		  </td>
                <td></td>
              </tr>
            </table>
	<br />

	  <input type="submit" class="submit" name="submit" id="submit" value="Create school" />
        <p>&nbsp;</p>

      </form>';

?>