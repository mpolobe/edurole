<?php
include $this->core->conf['conf']['classPath'] . "showoptions.inc.php";

$select = showUsers("100", null);

echo '<form id="editprogramme" name="editprogramme" method="post" action="?id=programmes&action=save">
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
                  <input type="text" name="name" value="' . $fetch[1] . '" /></td>
                <td></td>
              </tr>
              <tr>
		<td width="150"><b>Programme Coordinator</b></td>
                <td>
                  <select name="coordinator" id="coordinator">
			' . $select . '
                  </select></td>
                <td>Functional course coordinator</td>
              </tr>
		<tr><td>Programme Type</td>
		<td><select name="programtype">

		<option value="0" ';
if ($fetch[3] == "0") {
	echo 'selected=""';
}
echo '>-choose-</option>
		<option value="1" ';
if ($fetch[3] == "1") {
	echo 'selected=""';
}
echo '>Minor</option>
		<option value="2" ';
if ($fetch[3] == "2") {
	echo 'selected=""';
}
echo '>Major</option>
		<option value="3" ';
if ($fetch[3] == "3") {
	echo 'selected=""';
}
echo '>Available as both</option>

		</select></td>
		<td></td>
		</tr>

            </table></td>
          </tr>
        </table>
	<br />
	  <input type="hidden" name="item" value="' . $this->core->cleanGet['item'] . '" />
	  <input type="submit" class="submit" name="submit" id="submit" value="Save changes to programme" />
        <p>&nbsp;</p>

      </form>';
?>
