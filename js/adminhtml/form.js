var varienForm = new Class.create();

varienForm.prototype = {
    initialize : function(formId, validationUrl){
        this.formId = formId;
        this.validationUrl = validationUrl;
        this.submitUrl = false;
        
        if($(this.formId)){
            this.validator  = new Validation(this.formId, {onElementValidate : this.checkErrors.bind(this)});
        }
        this.errorSections = new Hash();
    },
    
    checkErrors : function(result, elm){
        if(!result)
            elm.setHasError(true, this);
        else
            elm.setHasError(false, this);
    },
    
    submit : function(url){
        this.errorSections = new Hash();
        this.canShowError = true;
        this.submitUrl = url;
        if(this.validator.validate()){
            if(this.validationUrl){
                this._validate();
            }
            else{
                this._submit();
            }
            return true;
        }
        return false;
    },
    
    _validate : function(){
        new Ajax.Request(this.validationUrl,{
            method: 'post',
            parameters: $(this.formId).serialize(),
            onComplete: this._processValidationResult.bind(this)
        });
    },
    
    _processValidationResult : function(transport){
        var response = transport.responseText.evalJSON();
        if(response.error){
            if($('messages')){
                $('messages').innerHTML = response.message;
            }
        }
        else{
            this._submit();
        }
    },
    
    _submit : function(){
        if(this.submitUrl){
            $(this.formId).action = this.submitUrl;
        }
        $(this.formId).submit();
    }
}

/**
 * redeclare Validation.isVisible function
 * 
 * use for not visible elements validation
 */
Validation.isVisible = function(elm){
    while(elm && elm.tagName != 'BODY') {
        if(elm.disabled) return false;
        if(Element.hasClassName(elm, 'template') && Element.hasClassName(elm, 'no-display')){
            return false;
        }
        elm = elm.parentNode;
    }
    return true;
}

/**
 *  Additional elements methods
 */
var varienElementMethods = {
    setHasChanges : function(element, event){
        if($(element).hasClassName('no-changes')) return;
        var elm = element;
        while(elm && elm.tagName != 'BODY') {
            if(elm.statusBar)
                Element.addClassName($(elm.statusBar), 'changed')
            elm = elm.parentNode;
        }            
    },
    setHasError : function(element, flag, form){
        var elm = element;
        while(elm && elm.tagName != 'BODY') {
            if(elm.statusBar){
                if(form.errorSections.keys().indexOf(elm.statusBar.id)<0) 
                    form.errorSections[elm.statusBar.id] = flag;
                if(flag){
                    Element.addClassName($(elm.statusBar), 'error');
                    if(form.canShowError && $(elm.statusBar).show){
                        form.canShowError = false;
                        $(elm.statusBar).show();
                    }
                    form.errorSections[elm.statusBar.id] = flag;
                }
                else if(!form.errorSections[elm.statusBar.id]){
                    Element.removeClassName($(elm.statusBar), 'error')
                }
            }
            elm = elm.parentNode;
        }
        this.canShowElement = false;
    }
}

Element.addMethods(varienElementMethods);

// Global bind changes
function varienWindowOnload(){
    var dataElements = $$('input', 'select', 'textarea');
    for(var i=0; i<dataElements.length;i++){
        if(dataElements[i] && dataElements[i].id){
            Event.observe(dataElements[i], 'change', dataElements[i].setHasChanges.bind(dataElements[i]));
        }
    }
}
Event.observe(window, 'load', varienWindowOnload);

/**
 * Fix errorrs in IE
 */
Event.pointerX = function(event){
    try{
        return event.pageX || (event.clientX +(document.documentElement.scrollLeft || document.body.scrollLeft));
    }
    catch(e){
        
    }
}
Event.pointerY = function(event){
    try{
        return event.pageY || (event.clientY +(document.documentElement.scrollTop || document.body.scrollTop));
    }
    catch(e){
        
    }
}