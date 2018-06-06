<form id="addrole" name="addrole" method="post" action="<?php echo $this->core->conf['conf']['path'] . "/roles/save"; ?>">
	<input type="hidden" name="item" value="<?php echo $item; ?>" />
	<table width="768" border="0" cellpadding="5" cellspacing="0">
              <tr>
                <td width="205" height="28" bgcolor="#EEEEEE"><strong><?php echo $this->core->translate("Information"); ?></strong></td>
                <td width="200" bgcolor="#EEEEEE"><strong><?php echo $this->core->translate("Input field"); ?></strong></td>
                <td  bgcolor="#EEEEEE"><strong><?php echo $this->core->translate("Description"); ?></strong></td>
              </tr>
              <tr>
                <td><strong><?php echo $this->core->translate("Role name"); ?> </strong></td>
                <td>
                  <input type="text" name="name"  />
		</td>
                <td>Name of Role</td>
              </tr>
              <tr>
                <td><strong><?php echo $this->core->translate("Role level"); ?> </strong></td>
                <td>
                  <input type="text" name="level"  />
		</td>
                <td>Role level</td>
              </tr>
              <tr>
                <td><strong><?php echo $this->core->translate("Role group"); ?> </strong></td>
                <td>
                  <input type="text" name="group"  />
		</td>
                <td>Role group</td>
              </tr>
            </table>
	<br />

	  <input type="submit" class="submit" name="submit" id="submit" value="<?php echo $this->core->translate("Save role"); ?>" />

      </form>
