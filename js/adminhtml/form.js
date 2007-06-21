var varienForm = new Class.create();

varienForm.prototype = {
    initialize : function(formId){
        this.formId = formId;
        this.validator  = new Validation(this.formId, {onElementValidate : this.showNotVisibleElement});
    },
    
    showNotVisibleElement : function(result, elm){
        if(!result){
            while(elm.tagName != 'BODY') {
                if(!$(elm).visible()){ 
                    if($(elm).tabsObject && $(elm).tabObject){
                        $(elm).tabsObject.showTabContent($(elm).tabObject);
                    }
                    else {
                        Element.show(elm);
                    }                    
                    return true;
                }
                elm = elm.parentNode;
            }            
        }
    },
    
    submit : function(){
        if(this.validator.validate()){
            $(this.formId).submit();
            return true;
        }
        return false;
    }
}

/**
 * redeclare Validation.isVisible function
 * 
 * use for not visible elements validation
 */
Validation.isVisible = function(elm){
    return true;
}