<script type="text/javascript">
	Aloha.ready( function() {
		var $ = Aloha.jQuery;
		$('.editable').aloha();
	});
</script>

<form id="sendsms" name="sendsms" method="post" action="<?php echo $this->core->conf['conf']['path'] . "/sms/send"; ?>">
	<table width="" border="0" cellpadding="5" cellspacing="0">
             <?php 
		if($this->core->action == "newbulk"){
			echo'<input type="hidden" name="recipients" value="'.$recipients.'">
			<input type="hidden" value="'.$guids.'" class="editable" name="uids">';
		}else {
		echo'<tr>
                <td width="205" height="28" bgcolor="#EEEEEE"><strong>Information</strong></td>
                <td bgcolor="#EEEEEE"><strong>Input field</strong></td>

              </tr>
              <tr>
                <td><strong>Recipients</strong></td>
                <td>
                  	<textarea rows="5" cols="50" class="editable" name="recipients">'.$recipients .'</textarea>
			<input type="hidden" value="'.$uids.'" class="editable" name="uids">
		</td>
              </tr>';
		}
		?>
              <tr>
                <td><strong>SMS message</strong></td>
                <td>
			<textarea rows="5" cols="100"  maxlength="160" class="editable" name="message"></textarea>
		  </td>
              </tr>
            </table>
	<br />
		Supported smart variables are: <b> %ID%, %NAME%, %PHONE%, %NRC%, %MODE%, %STATUS% </b><br><br>
	  <input type="submit" class="submit" name="submit" id="submit" value="Send SMS message" />
        <p>&nbsp;</p>

      </form>