jQuery(document).ready(function(){

	jQuery(function() {
		jQuery('.datepicker').datepicker({
			dateFormat : 'yy-mm-dd'
		});
	});	


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
	
	var ExpandingFormElement = Class.create({

		initialize: function(options) {
		
        this.options = options

        this.entryModel = $(options.entryModel)
        this.container = $(this.entryModel.parentNode)

        this.container.cleanWhitespace()

        if(this.container.childNodes.length > 1) {
            throw new Error("The container (parentNode) of the entryModel must contain only the entryModel, and no other nodes (put it in a <div> of its own). The container has " + this.container.childNodes.length + " elements after white space removal.")
        }

        this.entryModel.remove()

		$(options.addEntryLinkElement).observe('click',function() {
				this.addEntry()
			}.bind(this));
		} ,

		addEntry: function(values) {
			var copiedElement = this.entryModel.cloneNode(true)

			this.observeCopiedElement(copiedElement)

			var index = this.getNumberOfEntries()

			this.replaceInputNamesInElement(copiedElement, index)

			this.container.appendChild(copiedElement);

			if(values != null) {
				this.setEntryValues(copiedElement, values)
		}

		jQuery('#spouse').ddslick({width:280,
			onSelected: function(selectedData){
				console.log(selectedData.selectedData.text);
			}   
		});

		jQuery('#education').ddslick({width:280,
			onSelected: function(selectedData){
				console.log(selectedData.selectedData.text);
			}   
		});
    } ,

    setEntryValues: function(element, values) {
       $H(values).each(function(entry) {
          var input = this.getInputFromElementByName(element, entry.key)

          if(input) {
              input.value = entry.value;
          }
       }.bind(this));
    } ,

    getInputFromElementByName: function(element, name) {
        var matchedInput = null;

        var inputs = element.select('input','textarea','select')

        inputs.each(function(input) {
           if(input.name.indexOf("[" + name + "]") != -1) {
               matchedInput = input;

               return $break;
           }

           return null;
        });

        return matchedInput;
    } ,

    getNumberOfEntries: function() {
        return this.container.childNodes.length
    } ,

    observeCopiedElement: function(element) {
        var deleteEntryElement;

        if((deleteEntryElement = element.down('.' + this.options.deleteEntryElementClass))) {
            deleteEntryElement.observe('click',function() {
                if(this.options.deletionConfirmText) {
                    if(confirm(this.options.deletionConfirmText)) {
                        element.remove()
                    }
                }
                else {
                    element.remove()
                }
            }.bind(this))
        }
    } ,

    replaceInputNamesInElement: function(element, index) {
        $(element).select("input","textarea","select").each(function(input) {
            input.name = input.name.replace("#index",index)
        }.bind(this))
    }
	});

   var contactsExpander = new ExpandingFormElement({
      entryModel: 'contact-element',
      addEntryLinkElement: 'add-contact',
      deleteEntryElementClass: 'delete-contact',
      deletionConfirmText: "Are you sure you want to delete contact?"
   })


   var educationExpander = new ExpandingFormElement({
      entryModel: 'education-element',
      addEntryLinkElement: 'add-education',
      deleteEntryElementClass: 'delete-education',
      deletionConfirmText: "Are you sure you want to delete education?"
   })

});

jQuery('#mda').ddslick({width:280,
	onSelected: function(selectedData) { },
	enableKeyboard: true,
	keyboard: [{ "up":38, "down":40, "select":13 }]
});
jQuery('#mdb').ddslick({width:280,
	onSelected: function(selectedData){
		console.log(selectedData.selectedData.text);
	}
});
jQuery('#gender').ddslick({width:280,
	onSelected: function(selectedData){
		console.log(selectedData.selectedData.text);
	}
});
jQuery('#nationality').ddslick({width:280, height:300,
    onSelected: function(selectedData){
        console.log(selectedData.selectedData.text);
    }
});
jQuery('#country').ddslick({width:280, height:300,
    onSelected: function(selectedData){
        console.log(selectedData.selectedData.text);
    }
});
jQuery('#dissability').ddslick({width:80,
    onSelected: function(selectedData){
        console.log(selectedData.selectedData.text);
    }
});
jQuery('#dissabilitytype').ddslick({width:200,
    onSelected: function(selectedData){
        console.log(selectedData.selectedData.text);
    }
});
jQuery('#mstatus').ddslick({width:280,
    onSelected: function(selectedData){
        console.log(selectedData.selectedData.text);
    }
});
jQuery('#studytype').ddslick({width:280,
    onSelected: function(selectedData){
        console.log(selectedData.selectedData.text);
	}
});
jQuery('#payment').ddslick({width:280,
    onSelected: function(selectedData){
        console.log(selectedData.selectedData.text);
    }
});
jQuery('#day').ddslick({width:70, height:300,
    onSelected: function(selectedData){
        console.log(selectedData.selectedData.text);
    }
});
jQuery('#month').ddslick({width:120, height:300,
    onSelected: function(selectedData){
        console.log(selectedData.selectedData.text);
    }
});
jQuery('#year').ddslick({width:90, height:300,
    onSelected: function(selectedData){
        console.log(selectedData.selectedData.text);
    }
});
});
