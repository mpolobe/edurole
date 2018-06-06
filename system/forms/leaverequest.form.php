<script type="text/javascript">

jQuery(document).ready(function(){


  jQuery( function() {
    $( ".datepicker" ).datepicker();
  } );


});

</script>

<form id="leave" name="leave" method="post"  enctype="multipart/form-data"  action="<?php echo $this->core->conf['conf']['path']; ?>/staff/submitleave/">
	<p>Please enter the following information</p>
	<table width="768" border="0" cellpadding="5" cellspacing="0">
        <tr>
            <td width="205" height="28" bgcolor="#EEEEEE"><strong>Information</strong></td>
            <td width="560" bgcolor="#EEEEEE"><strong>Input field</strong></td>
        </tr>
        <tr>
            <td><strong>Description for leave:</strong></td>
            <td>
                  <input type="text" name="name" value="" />
                 Eg. Holiday, or Maternity leave
		    </td>
        </tr>
        <tr>
            <td><strong>Leave start and end dates</strong></td>
            <td>
                      <input class="datepicker" type="text" name="start" value="" /> to 
                      <input class="datepicker" type="text" name="end" value="" />
        </tr>
        <tr>
            <td><strong>Optional attachment</strong></td>
            <td>
		      <input type="file" name="file" accept=".pdf,*.doc,*.docx"> <br> 
            </td>
        </tr>
    </table>

    <br />

	  <input type="submit" class="submit" name="submit" id="submit" value="Submit leave request" />
        <p>&nbsp;</p>

      </form>
