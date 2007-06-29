var varienForm = new Class.create();

varienForm.prototype = {
    initialize : function(formId){
        this.formId = formId;
        this.onElementChange = this.registerElementChanges.bindAsEventListener(this);
        this.validator  = new Validation(this.formId, {onElementValidate : this.showNotVisibleElement});
        this.initFormElements();
        this.canShowElement = false;
    },
    
    initFormElements : function(){
        var elements = Form.getElements(this.formId);
        for(var i in elements){
            Event.observe(elements[i], 'change', this.onElementChange);
        }
    },
    
    showNotVisibleElement : function(result, elm){
        if(!result){
            while(elm.tagName != 'BODY') {
                if(!$(elm).visible()){ 
                    if(this.canShowElement){
                        if($(elm).tabsObject && $(elm).tabObject){
                            $(elm).tabsObject.showTabContent($(elm).tabObject);
                            this.canShowElement = false;
                        }
                        else {
                            Element.show(elm);
                            this.canShowElement = false;
                        }
                    }
                }
                elm = elm.parentNode;
            }
            this.canShowElement = false;
        }
    },
    
    submit : function(){
        this.canShowElement = true;
        if(this.validator.validate()){
            $(this.formId).submit();
            return true;
        }
        return false;
    },
    
    registerElementChanges : function(event){
        elm = Event.element(event);
        while(elm.tagName != 'BODY') {
            if($(elm).changeRelation){
                Element.addClassName($($(elm).changeRelation), 'changed')
            }
            elm = elm.parentNode;
        }            
    }
}

/**
 * redeclare Validation.isVisible function
 * 
 * use for not visible elements validation
 */
Validation.isVisible = function(elm){
    while(elm.tagName != 'BODY') {
        if(Element.hasClassName(elm, 'template') && Element.hasClassName(elm, 'no-display')){
            return false;
        }
        elm = elm.parentNode;
    }
    return true;
}

