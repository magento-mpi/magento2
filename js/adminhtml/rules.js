var VarienRulesForm = new Class.create();
VarienRulesForm.prototype = {
    initialize : function(newChildUrl){
        this.newChildUrl = newChildUrl;
        
    	var params = $$('.rule-param'), i, label, elem, cont;
    	for (var i=0; i<params.length; i++) {
    		cont = params[i];
    		label = Element.down(params[i], '.label');
    		elem = Element.down(params[i], '.element').down();
    		Event.observe(label, 'click', this.showParamInputField.bind(cont));
    		Event.observe(elem, 'change', this.hideParamInputField.bind(cont));
    		Event.observe(elem, 'blur', this.hideParamInputField.bind(cont));
    	}
    },
    
    showParamInputField: function (event) {
    	Element.addClassName(this, 'rule-param-edit');
    	Element.down(this, '.element').down().focus();
    },
    
    hideParamInputField: function (event) {
    	Element.removeClassName(this, 'rule-param-edit');
    	var label = Element.down(this, '.label'), elem;
    
    	if (!this.hasClassName('rule-param-new-child')) {
        	elem = Element.down(this, 'select');
        	if (elem) {
        		label.innerHTML = elem.options[elem.selectedIndex].text;
        	}
    
        	elem = Element.down(this, 'input.input-text');
        	if (elem) {
        		label.innerHTML = elem.value;
        	}
    	} else {
    	    elem = Element.down(this, 'select');
        	
    	    if (elem.value) {
            	this.addNewChild(elem);
    	    }
    
        	elem.value = '';
    	}
    },
    
    addNewChild: function (event) {
        var parent_id = elem.id.replace(/^.*:(.*):.*$/, '$1');
        var children_ul = $(elem.id.replace(/[^:]*$/, 'children'));
        var children_inputs = Selector.findChildElements(children_ul, $A('input[type=hidden]'));
        if (children_inputs.length) {
            var new_id = '';
            children_inputs.each(function(el){
                if (el.id.match(/:type$/)) {
                    new_id = el.id.replace(/^.*:(.*):.*$/, '$1');
                }
            });
        } else {
            new_id = parent_id+'.1';
        }
        var new_type = elem.value;
        
        /*Ajax.request(this.newChildUrl, {
        
        });*/
        console.log(new_id, new_type);
    }
}