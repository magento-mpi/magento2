Mage.Form = function(form){
    var that = this;
    
    this.elements = new Ext.util.MixedCollection(false);
    
    this.form = Ext.getDom(form);
    this.id = form.id;
    this.action = form.action;
    this.method = form.method;
    this.timeout = 10000;
    this.transId = null;
    this.scipFile = true;
    this.enctype = form.getAttribute("enctype");
    if(this.enctype && this.enctype.toLowerCase() == "multipart/form-data"){
        this.isUpload  = true;
    }                


    this._parseElements = function(form) {
        var i = 0;
        for(i=0; i < form.elements.length; i++) {
            this.elements.add(form.elements[i].name, form.elements[i]);
        }
    }
    
    this._parseElements(this.form);
    

    this.appendForm = function(form, force) {
        if (form.action != this.action && !force) {
            return false;
        }
        this._parseElements(form);
    }
    

    this.sendForm = function(callBack, reset) {
        if (!this.action) {
            Ext.MessageBox.alert('Form','From action do not set');
            return false;
        }
        var i = 0;
        var formData = '';
        var elm;
        for(i=0; i < this.elements.getCount(); i++) {
            elm = this.elements.itemAt(i);
            oElement = elm;
			oDisabled = elm.disabled;
			oName = elm.name;
			oValue = elm.value;

			// Do not submit fields that are disabled or
			// do not have a name attribute value.
			if(!oDisabled && oName)
			{
				switch (oElement.type)
				{
					case 'select-one':
					case 'select-multiple':
						for(var j=0; j<oElement.options.length; j++){
							if(oElement.options[j].selected){
								if(window.ActiveXObject){
									formData += encodeURIComponent(oName) + '=' + encodeURIComponent(oElement.options[j].attributes['value'].specified?oElement.options[j].value:oElement.options[j].text) + '&';
								}
								else{
									formData += encodeURIComponent(oName) + '=' + encodeURIComponent(oElement.options[j].hasAttribute('value')?oElement.options[j].value:oElement.options[j].text) + '&';
								}

							}
						}
						break;
					case 'radio':
					case 'checkbox':
						if(oElement.checked){
							formData += encodeURIComponent(oName) + '=' + encodeURIComponent(oValue) + '&';
						}
						break;
					case 'file':
						// stub case as XMLHttpRequest will only send the file path as a string.
					case undefined:
						// stub case for fieldset element which returns undefined.
					case 'reset':
						// stub case for input type reset button.
					case 'button':
						// stub case for input type button elements.
						break;
					case 'submit':
						if(hasSubmit == false){
							formData += encodeURIComponent(oName) + '=' + encodeURIComponent(oValue) + '&';
							hasSubmit = true;
						}
						break;
					default:
						formData += encodeURIComponent(oName) + '=' + encodeURIComponent(oValue) + '&';
						break;
				}
			}            

//            if (elm.tagName.toLowerCase() == 'fieldset') {
//                continue;
//            }
//            if (this.scipFile && elm.type.toLowerCase() == 'file') {
//                continue;
//            }
//            
//            formData.push(encodeURIComponent(elm.name)+'='+encodeURIComponent(elm.value));
        }
           // this._isFormSubmit = true;
    		formData = formData.substr(0, formData.length - 1);
        
        var cb = {
            success : this.successDelegate,
            failure : this.failureDelegate,
            timeout : this.timeout,
            argument: {"url": this.action, "method":this.method, "form": this.form, "reset":reset, "callBack": callBack}
        }
       // params = formData.join('&');
        this.transId = Ext.lib.Ajax.request(this.method, this.action, cb, formData);
        this.elements.clear();
    }
    
    this.processSuccess = function(response) {
        if (typeof response.argument.callBack == 'function') {
            response.argument.callBack(response, {success:true});
        }
    }
    
    this.processFailure = function(response) {
        if (typeof response.argument.callBack == 'function') {
            response.argument.callBack(response, {success:false});
        }
    }
    
    this.successDelegate = this.processSuccess.createDelegate(this);
    this.failureDelegate = this.processFailure.createDelegate(this);
    
};
