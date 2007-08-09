var VarienRulesForm = new Class.create();
VarienRulesForm.prototype = {
    initialize : function(parent, newChildUrl){
        this.newChildUrl = newChildUrl;
        var elems = $$('.rule-param');
        for (var i=0; i<elems.length; i++) {
            this.initParam(elems[i]);
        }
    },
    
    initParam: function (container) {
        container.rulesObject = this;
		var label = Element.down(container, '.label');
		var elem = Element.down(container, '.element').down();
		Event.observe(label, 'click', this.showParamInputField.bind(this, container));
		Event.observe(elem, 'change', this.hideParamInputField.bind(this, container));
		Event.observe(elem, 'blur', this.hideParamInputField.bind(this, container));
    },
    
    showParamInputField: function (container, event) {
    	Element.addClassName(container, 'rule-param-edit');
    	var elemContainer = Element.down(container, '.element');
    	
    	var elem = Element.down(elemContainer, 'input.input-text');
    	if (elem) {
    	   elem.focus();
    	}
    	
    	var elem = Element.down(elemContainer, 'select');
    	if (elem) {
    	   elem.focus();
    	   // trying to emulate enter
    	   /*
    	   if (document.createEventObject) {
        	   var event = document.createEventObject();
        	   event.altKey = true;
    	       event.keyCode = 40;
    	       elem.fireEvent("onkeydown", evt);
    	   } else {
    	       var event = document.createEvent("Events");
    	       event.altKey = true;
    	       event.keyCode = 40;
    	       elem.dispatchEvent(event);
    	   }
    	   */
    	}
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
        
        var max_id = 0, i;
        var children_inputs = Selector.findChildElements(children_ul, $A('input[type=hidden]'));
        if (children_inputs.length) {
            children_inputs.each(function(el){
                if (el.id.match(/:type$/)) {
                    i = 1*el.id.replace(/^.*:.*([0-9]+):.*$/, '$1');
                    max_id = i > max_id ? i : max_id;
                }
            });
        }
        var new_id = parent_id+'.'+(max_id+1);
        var new_type = elem.value;
        
        var new_elem = document.createElement('LI');
        children_ul.appendChild(new_elem);

        new Ajax.Updater(new_elem, this.newChildUrl, {
            parameters: { type:new_type.replace('/','-'), id:new_id },
            onComplete: this.onAddNewChildComplete.bind(this, new_elem),
        });
    },
    
    onAddNewChildComplete: function (new_elem) {
        var elems = new_elem.getElementsByClassName('rule-param'); //TODO: only in the received element
        for (var i=0; i<elems.length; i++) {
            this.initParam(elems[i]);
        }
    }
}