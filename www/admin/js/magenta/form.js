Mage.Form = function(form){
    var that = this;
    
    this.elements = new Ext.util.MixedCollection(false);
    
    this.form = Ext.getDom(form);
    this.action = form.action;
    this.method = form.method;
    this.timeout = 100;
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
        var formData = [];
        var elm;
        for(i=0; i < this.elements.getCount(); i++) {
            elm = this.elements.itemAt(i);
            if (elm.tagName.toLowerCase() == 'fieldset') {
                continue;
            }
            if (this.scipFile && elm.type.toLowerCase() == 'file') {
                continue;
            }
            formData.push(elm.name+'='+elm.value);
        }
        var cb = {
            success : this.successDelegate,
            failure : this.failureDelegate,
            timeout : this.timeout,
            argument: {"url": this.action, "method":this.method, "form": this.form, "reset":reset, "callBack": callBack}
        }
        params = formData.join('&');
        this.transId = Ext.lib.Ajax.request(this.method, this.action, cb, params);
        this.elements.clear();
    }
    
    this.processSuccess = function(response) {
        if (typeof response.argument.callBack == 'function') {
            response.argument.callBack(response, {success:true});
        }
    }
    
    this.processFailure = function(response) {
        if (typeof response.argument.callBack == 'function') {
            response.argument.callBack(response, {failure:true});
        }
    }
    
    this.successDelegate = this.processSuccess.createDelegate(this);
    this.failureDelegate = this.processFailure.createDelegate(this);
    
};
