var VarienRulesForm = new Class.create();
VarienRulesForm.prototype = {
    initialize : function(newChildUrl){
        this.newChildUrl = newChildUrl;
        
    	var params = $$('.rule-param'), i, label, elem, cont;
    	for (var i=0; i<params.length; i++) {
    		container = params[i];
    		container.rulesObject = this;
    		label = Element.down(params[i], '.label');
    		elem = Element.down(params[i], '.element').down();
    		Event.observe(label, 'click', this.showParamInputField.bind(this, container));
    		Event.observe(elem, 'change', this.hideParamInputField.bind(this, container));
    		Event.observe(elem, 'blur', this.hideParamInputField.bind(this, container));
    	}
    },
    
    showParamInputField: function (container, event) {
    	Element.addClassName(container, 'rule-param-edit');
    	Element.down(container, '.element').down().focus();
    },
    
    hideParamInputField: function (container, event) {
    	Element.removeClassName(container, 'rule-param-edit');
    	var label = Element.down(container, '.label'), elem;
    
    	if (!container.hasClassName('rule-param-new-child')) {
        	elem = Element.down(container, 'select');
        	if (elem) {
        		label.innerHTML = elem.options[elem.selectedIndex].text;
        	}
    
        	elem = Element.down(container, 'input.input-text');
        	if (elem) {
        		label.innerHTML = elem.value;
        	}
    	} else {
    	    elem = Element.down(container, 'select');
        	
    	    if (elem.value) {
    	        this.addRuleNewChild(elem);
    	    }
    
        	elem.value = '';
    	}
    },
    
    addRuleNewChild: function (elem) {
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
        
        Ajax.Request(self.newChildUrl, {
            method: 'get',
            onSuccess: function (transport) {
                console.log('success');
            },
            onFailure: function (transport) {
                console.log('failure');
            }
        });
        console.log(new_id, new_type);
    }
}