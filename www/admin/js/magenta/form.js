Mage.Form = function(form){
    var that = this;
    
    this.elements = new Ext.util.MixedCollection(false);
    
    this.form = Ext.getDom(form);
    this.action = form.action;
    this.method = form.method;
    this.timeout = 100;
    this.transId = null;
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
    
    /* public functions */
    this.appendForm = function(form, force) {
        if (form.action != this.action && !force) {
            return false;
        }
        this._parseElements(form);
    }
    
    /* private function */
    this.sendForm = function(reset, callBack) {
        var i = 0;
        var formData = [];
        for(i=0; i < this.elements.getCount(); i++) {
            if (this.elements.itemAt(i).tagName.toLowerCase() == 'fieldset') {
                continue;
            }
            formData.push(this.elements.itemAt(i).name+'='+this.elements.itemAt(i).value);
        }
        var cb = {
            success : this.successDelegate,
            failure : this.failureDelegate,
            timeout : this.timeout,
            argument: {"url": this.action, "method":this.method, "form": this.form, "reset":reset, "callBack": callBack}
        }
        Ext.dump(formData);
        params = formData.join('&');
        this.transId = Ext.lib.Ajax.request(this.method, this.action, cb, params);
        this.elements.clear();
    }
    
    this.processSuccess = function(response) {
        
    }
    
    this.processFailure = function(response) {
        
    }
    
    this.successDelegate = this.processSuccess.createDelegate(this);
    this.failureDelegate = this.processFailure.createDelegate(this);
    
};
