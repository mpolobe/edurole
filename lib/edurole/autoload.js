<?php
echo'<script>
	$(document).ready(function() {
		var offset = 50;
		var pageload = '. $this->core->limit .';
		var loading  = false;
		var total = '. $total .';
		var geturl = "../api/expandList/'. $this->core->page .'/'. $this->core->action .'/";

		$(window).scroll(function() {

			if($(window).scrollTop() + $(window).height() == $(document).height()) {

				if(offset <= total && loading==false) {
					loading = true;

					$.get(geturl, {"offset": offset}, function(data){
						$("#results").append(data);
						offset = offset + pageload;
						loading = false;
					}).fail(function(xhr, ajaxOptions, thrownError) {
						loading = false;
					});

				}
			}
		});

	});
</script>';
?>
