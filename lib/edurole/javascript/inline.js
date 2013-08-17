
jQuery(document).ready(function(){


jQuery(function() {
	jQuery('.datepicker').datepicker({
		dateFormat : 'yy-mm-dd'
	});
});	

jQuery("#username").change(function() { 
	var usr = jQuery("#username").val();

	if(usr.length >= 8){

		jQuery("#status").html('<img src="templates/default/images/loading.gif" align="absmiddle">&nbsp;Checking for duplicates...');

		jQuery.ajax({
			type: "POST",
			url: "system/api.php",
			data: "username="+ usr,
			success: function(msg){

				jQuery("#status").ajaxComplete(function(event, request, settings){

					if(msg == 'OK'){
						jQuery("#username").removeClass('object_error');
						jQuery("#username").addClass("object_ok");
						jQuery(this).html('&nbsp;<img src="templates/default/images/check.png"> Please continue');
					}else{
						jQuery("#username").removeClass('object_ok');
						jQuery("#username").addClass("object_error");
						jQuery(this).html(msg);
					}

				});

			}

		});
	} else {

		jQuery("#status").html('<font color="red">' + '&nbsp;<img src="templates/default/images/error.png"> Your NRC is invalid <strong></strong></font>');
		jQuery("#username").removeClass('object_ok'); 
		jQuery("#username").addClass("object_error");

	
	}
});
});	


var Aloha = window.Aloha || ( window.Aloha = {} );
		
	Aloha.settings = {
		logLevels: { 'error': true, 'warn': true, 'info': true, 'debug': false, 'deprecated': true },
		errorhandling: false,
		ribbon: {enable: true},
		locale: 'en',
		plugins: {
		format: {
			config: [  'b', 'i', 'p', 'sub', 'sup', 'del', 'title', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'pre', 'removeFormat' ]
		}
		},
		sidebar: {
			disabled: true
		}
	};
