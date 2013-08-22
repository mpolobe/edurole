<?php
$dean = $fetch[5];

include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";

$select = new optionBuilder($core);
$select->showUsers("4", $dean);
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
                <td><strong>School name </strong></td>
                <td>
                  <input type="text" name="name" value="' . $fetch[3] . '" /></td>
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
			<textarea rows="4" cols="37" name="description">' . $fetch[4] . '</textarea>
		  </td>
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
