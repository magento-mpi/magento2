Accordion = Class.create();
Accordion.prototype = {
    initialize: function(elem, clickableEntity, checkAllow) {
        this.container = $(elem);
        this.checkAllow = checkAllow || false;
        var headers = $$('#' + elem + ' .section ' + clickableEntity);
        headers.each(function(header) {
            Event.observe(header,'click',this.sectionClicked.bindAsEventListener(this));
        }.bind(this));
    },
    
    sectionClicked: function(event) {
        this.openSection(Event.element(event).parentNode);
    },
    
    openSection: function(section) {
        var section = $(section);
        
        // Check allow
        if (this.checkAllow && !Element.hasClassName(section, 'allow')){
            return;
        }

        if(section.id != this.currentSection) {
            this.closeExistingSection();
            this.currentSection = section.id;
            var contents = document.getElementsByClassName('a-item',section);
            contents[0].show();
        }
    },
    
    closeExistingSection: function() {
        if(this.currentSection) {
            var contents = document.getElementsByClassName('a-item',this.currentSection);
            contents[0].hide();
        }
    }
}