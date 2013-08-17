$(document).ready(function(){

	$("#username").change(function() { 

		var usr = $("#username").val();

		if(usr.length >= 8){
			$("#status").html('<img src="templates/default/images/loading.gif" align="absmiddle">&nbsp;Checking for duplicates...');

			$.ajax({
				    type: "POST",
				    url: "system/api.php",
				    data: "username="+ usr,
				    success: function(msg){

				$("#status").ajaxComplete(function(event, request, settings){

					if(msg == 'OK'){
       						$("#username").removeClass('object_error');
						$("#username").addClass("object_ok");
						$(this).html('&nbsp;<img src="templates/default/images/check.png"> Please continue');
					}else{
						$("#username").removeClass('object_ok');
						$("#username").addClass("object_error");
						$(this).html(msg);
					}

   				});

 			}

		});
	} else {

		$("#status").html('<font color="red">' + '&nbsp;<img src="templates/default/images/error.png"> Your NRC is invalid <strong></strong></font>');
		$("#username").removeClass('object_ok'); 
		$("#username").addClass("object_error");

	}

});



