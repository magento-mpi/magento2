Accordion = Class.create();
Accordion.prototype = {
    initialize: function(elem, clickableEntity, checkAllow) {
        this.container = $(elem);
        this.checkAllow = checkAllow || false;
        this.sections = $$('#' + elem + ' .section');
        var headers = $$('#' + elem + ' .section ' + clickableEntity);
        headers.each(function(header) {
            Event.observe(header,'click',this.sectionClicked.bindAsEventListener(this));
        }.bind(this));
    },
    
    sectionClicked: function(event) {
        this.openSection($(Event.element(event)).up('.section'));
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
            $(this.currentSection).addClassName('active');
            var contents = document.getElementsByClassName('a-item',section);
            contents[0].show();
            //Effect.SlideDown(contents[0]);
        }
    },
    
    openNextSection: function(setAllow){
        for (section in this.sections) {
            var nextIndex = parseInt(section)+1;
            if (this.sections[section].id == this.currentSection && this.sections[nextIndex]){
                if (setAllow) {
                    Element.addClassName(this.sections[nextIndex], 'allow')
                }
                this.openSection(this.sections[nextIndex]);
                return;
            }
        }
    },
    
    openPrevSection: function(setAllow){
        for (section in this.sections) {
            var prevIndex = parseInt(section)-1;
            if (this.sections[section].id == this.currentSection && this.sections[prevIndex]){
                if (setAllow) {
                    Element.addClassName(this.sections[prevIndex], 'allow')
                }
                this.openSection(this.sections[prevIndex]);
                return;
            }
        }
    },
    
    closeExistingSection: function() {
        if(this.currentSection) {
            $(this.currentSection).removeClassName('active');
            var contents = document.getElementsByClassName('a-item',this.currentSection);
            contents[0].hide();
            //Effect.SlideUp(contents[0]);
        }
    }
}