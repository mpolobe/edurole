<script type="text/javascript">

jQuery(document).ready(function(){


  jQuery( function() {
   	jQuery('.datepicker').datepicker({
		dateFormat : 'yy-mm-dd'
	});

  } );


});

</script>

<div class="heading"><?php echo $this->core->translate("Period information form"); ?></div>
<form id="periodadd" name="periodadd" method="POST" action="<?php echo $this->core->conf['conf']['path']; ?>/periods/save/<?php echo $item; ?>">


	<div class="label"><?php echo $this->core->translate("Period Name"); ?>:</div>
	<input type="text" name="name" id="name" class="submit" style="width: 260px" value="<?php echo $name; ?>"/> <br />

	<div class="label"><?php echo $this->core->translate("Period Start Date"); ?>:</div>
	<input type="text" name="start" id="paymentid" class="datepicker" style="width: 260px" value="<?php echo $psdate; ?>"/> YYYY-MM-DD<br />

	<div class="label"><?php echo $this->core->translate("Period End date"); ?>:</div>
	<input type="text" name="end" id="description" class="datepicker" style="width: 260px" value="<?php echo $pedate; ?>"/> YYYY-MM-DD<br />

	<div class="label"><?php echo $this->core->translate("Year"); ?>:</div>
	<select name="year" class="submit" style="width: 250px">
		<option value="" selected> -- CHOOSE -- </option>
		<option value="2018">2018</option>
		<option value="2017">2017</option>
		<option value="2016">2016</option>
		<option value="2015">2015</option>
		<option value="2014">2014</option>
		<option value="2013">2013</option>
		<option value="2012">2012</option>
		<option value="2011">2011</option>
		<option value="2010">2010</option>
		<option value="2009">2009</option>
		<option value="2008">2008</option>
	</select><br>

	<div class="label"><?php echo $this->core->translate("Quarter"); ?>:</div>
                  <select name="term" id="term" class="submit" style="width: 250px">>
			<option value="" selected> -- CHOOSE -- </option>
			<option value="1">Term 1</option>
			<option value="2">Term 2</option>
			<option value="3">Term 3</option>
			<option value="4">Term 4</option>
         </select><br />

	<div class="label"><?php echo $this->core->translate("Delivery Method"); ?>:</div>
	<select name="delivery" id="time" class="submit" width="250" style="width: 250px">
		<option value="Fulltime">Fulltime</option>
		<option value="Block">Block release</option>
		<option value="Distance">Distance Education</option>
	</select>

	<div class="heading"><?php echo $this->core->translate("Course Registration Dates"); ?></div>
	<div class="label"><?php echo $this->core->translate("Registration Period Start"); ?>:</div>
	<input type="text" name="cstart" id="cstart" class="datepicker" style="width: 260px" value="<?php echo $csdate; ?>"/> YYYY-MM-DD<br />

	<div class="label"><?php echo $this->core->translate("Registration Period End"); ?>:</div>
	<input type="text" name="cend" id="cend" class="datepicker" style="width: 260px" value="<?php echo $cedate; ?>"/> YYYY-MM-DD<br />

	<br>
	<div class="label"><?php echo $this->core->translate("Submit"); ?></div>
	<input type="submit" class="submit" value="<?php echo $this->core->translate("Save Period"); ?>" style="width: 260px"/>
	<br>


</form>
