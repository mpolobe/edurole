<script type="text/javascript">
	Aloha.ready( function() {
		var $ = Aloha.jQuery;
		$('.editable').aloha();
	});
</script>

<form id="addassignment" name="addassignment" method="post" enctype="multipart/form-data" action="<?php echo $this->core->conf['conf']['path'] . "/assignments/save/".$item; ?>">
	<p>Please enter the following information</p>
	<table width="768" border="0" cellpadding="5" cellspacing="0">
              <tr class="heading">
                <td width="205"><strong>Information</strong></td>
                <td width="200"><strong>Input field</strong></td>
                <td  ><strong>Description</strong></td>
              </tr>
              <tr>
                <td><strong>Assignment name </strong></td>
                <td>
                  <input type="text" name="name" value="<?php echo $name; ?>"/></td>
                <td>Name of assignment</td>
              </tr>
              <tr>
                <td><strong>Assignment details</strong></td>
                <td>
			<textarea rows="10" cols="55" class="editable" name="description"><?php echo $description; ?></textarea>
		  </td>
                <td>Describe your assignment here.</td>
              </tr>
		<tr>
                <td><strong>Assignment Deadline</strong></td>
                <td>
			 <input type="text" name="deadline" value="<?php echo $deadline; ?>"> <br>	
		  </td>
                <td>YYYY-MM-DD HH:MM:SS</td>
              </tr>
              </tr>
		<tr>
                <td><strong>Assignment Deadline</strong></td>
                <td>
			 <input type="checkbox" name="upload" style="width: 20px;" value="1"> <b>Students will need to upload assignment results</b> <br>	
		  </td>
                <td>Select to enable uploads</td>
              </tr>
		<tr>
                <td><strong>Files</strong></td>
                <td>
			 <input type="file" name="file" accept=".pdf,*.doc,*.docx"> <br>	
		  </td>
                <td>Add the file you want</td>
              </tr>
              </tr>
		<tr>
                <td><strong>Web Links</strong></td>
                <td>
			 <input type="text" name="links[]" value="<?php echo $links[0]; ?>"> <br>	
			 <input type="text" name="links[]" value="<?php echo $links[1]; ?>">
		  </td>
                <td>For example http://www.google.com</td>
              </tr>
            </table>
	<br />

	  <input type="submit" class="submit" name="submit" id="submit" value="Create assignment" />
        <p>&nbsp;</p>

      </form>
