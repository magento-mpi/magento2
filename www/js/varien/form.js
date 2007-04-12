VarienForm = Class.create();
VarienForm.prototype = {
    initialize: function(formId){
        this.form       = $(formId);
        this.validator  = new Validation(this.form);
        this.elementFocus   = this.elementOnFocus.bindAsEventListener(this);
        this.elementBlur    = this.elementOnBlur.bindAsEventListener(this);
        this.childLoader    = this.elementChildLoad.bindAsEventListener(this);
        this.highlightClass = 'highlight';
        this.bindElements();
        Form.Element.focus(Form.Methods.findFirstElement(this.form))
    },

    bindElements:function (){
        var elements = Form.Methods.getElements(this.form);
        for (var row in elements) {
            if (elements[row].id) {
                Event.observe(elements[row],'focus',this.elementFocus);
                Event.observe(elements[row],'blur',this.elementBlur);
            }
        }
    },

    elementOnFocus: function(event){
        var element = Event.findElement(event, 'fieldset');
        Element.addClassName(element, this.highlightClass);
    },

    elementOnBlur: function(event){
        var element = Event.findElement(event, 'fieldset');
        Element.removeClassName(element, this.highlightClass);
    },

    setElementsRelation: function(parent, child, dataUrl){
        if (parent=$(parent)) {
            // TODO: array of relation
            this.tmpChild=child;
            this.tmpUrl=dataUrl;
            Event.observe(parent,'change',this.childLoader);
        }
    },
    
    elementChildLoad: function(event){
        element = Event.element(event);
        if (element.value) {
            new Ajax.Request(this.tmpUrl,{
                    method: 'post',
                    parameters: {"paernt":element.value}
                    //onComplete: close.bind(element)
            });
        }
    }
}