function get_mail(url) {
	$.ajax({
		url: url,
		type: "POST",
		dataType: "json",
		success: function(data){
			jQuery(data).each(function(key,val){
					if(data.success == true){
						jQuery(".mailcount").empty();
						jQuery(".mailcount").append(data.mailcount);
					} else{
						jQuery(".mailcount").empty();
						jQuery(".mailcount").append("-");
						return;
					}
			});
		},
		error: function(data){
			console.log(data);
		}
	});
}