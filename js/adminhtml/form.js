var varienForm = new Class.create();

varienForm.prototype = {
    initialize : function(formId){
        this.formId = formId;
        this.validator  = new Validation(this.formId);
    },
    
    submit : function(){
        if(this.validator.validate()){
            $(this.formId).submit();
            return true;
        }
        return false;
    }
}
