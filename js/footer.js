<script type="text/javascript">
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

</script>