<form id="addrolegroup" name="addrolegroup" method="post" action="<?php echo $this->core->conf['conf']['path'] . "/roles/groupsave"; ?>">
	<input type="hidden" name="item" value="<?php echo $item; ?>" />
	<table width="768" border="0" cellpadding="5" cellspacing="0">
              <tr>
                <td width="205" height="28" bgcolor="#EEEEEE"><strong><?php echo $this->core->translate("Information"); ?></strong></td>
                <td width="200" bgcolor="#EEEEEE"><strong><?php echo $this->core->translate("Input field"); ?></strong></td>
                <td  bgcolor="#EEEEEE"><strong><?php echo $this->core->translate("Description"); ?></strong></td>
              </tr>
              <tr>
                <td><strong><?php echo $this->core->translate("Group level"); ?> </strong></td>
                <td>
                  <input type="text" name="level"  />
		</td>
                <td>Group level</td>
              </tr>
              <tr>
                <td><strong><?php echo $this->core->translate("Group Description"); ?> </strong></td>
                <td>
                  <input type="text" name="name"  />
		</td>
                <td>Group desciption</td>
              </tr>
              <tr>
                <td><strong><?php echo $this->core->translate("Lowest role"); ?> </strong></td>
                <td>
                  	<select name="high" id="high" class="submit" width="250" style="width: 250px">
				<?php echo $roles; ?>
			</select>
		</td>
                <td>Role level</td>
              </tr>
              <tr>
                <td><strong><?php echo $this->core->translate("Highest role"); ?> </strong></td>
                <td>
                  	<select name="low" id="low" class="submit" width="250" style="width: 250px">
				<?php echo $roles; ?>
			</select>
		</td>
                <td>Role group</td>
              </tr>
            </table>
	<br />

	  <input type="submit" class="submit" name="submit" id="submit" value="<?php echo $this->core->translate("Save Group"); ?>" />

      </form>
