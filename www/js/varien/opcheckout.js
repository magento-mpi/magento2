var Checkout = Class.create();
Checkout.prototype = {
    initialize: function(accordion, statusUrl, reviewUrl){
        this.accordion = accordion;
        this.statusUrl = statusUrl;
        this.reviewUrl = reviewUrl;
        this.type = '';
        this.billing = '';
        this.payment = '';
        this.shipping= '';
    }, 
    
    reloadStatusBlock: function(){
        var updater = new Ajax.Updater('column-left', this.statusUrl, {method: 'get'});
    },
        
    reloadReviewBlock: function(){
        var updater = new Ajax.Updater('checkout-review-load', this.reviewUrl, {method: 'get'});
    },
    
    setType: function(){
        if ($('checkout_type:guest') && $('checkout_type:guest').checked) {
            //alert('guest');
        }
        else if($('checkout_type:register') && $('checkout_type:register').checked) {
            //alert('register');
        }

        this.accordion.openNextSection(true);
    },

    setBilling: function() {
        this.reloadStatusBlock();
        this.accordion.openNextSection(true);
    },

    setPayment: function() {
        this.reloadStatusBlock();
        this.accordion.openNextSection(true);
    },

    setShipping: function() {
        this.reloadStatusBlock();
        this.accordion.openNextSection(true);
    },
        
    setShippingMethod: function() {
        this.reloadStatusBlock();
        this.accordion.openNextSection(true);
    },
    
    setReview: function() {
        this.reloadStatusBlock();
        this.reloadReviewBlock();
        this.accordion.openNextSection(true);
    },
    
    back: function(){
        this.accordion.openPrevSection(true);
    }
}

// billing
var Billing = Class.create();
Billing.prototype = {
    initialize: function(form, addressUrl, saveUrl){
        this.form = form;
        this.addressUrl = addressUrl;
        this.saveUrl = saveUrl;
        this.onAddressLoad = this.fillForm.bindAsEventListener(this);
        this.onSave = this.nextStep.bindAsEventListener(this);
    },

    setAddress: function(addressId){
        if (addressId) {
            request = new Ajax.Request(
                this.addressUrl+addressId,
                {method:'get', onSuccess: this.onAddressLoad}
            );
        }
        else {
            this.fillForm(false);
        }
    },
    
    resetSelectedAddress: function(){
        var selectElement = $('billing-address-select')
        if (selectElement) {
            selectElement.value='';
        }
    },

    fillForm: function(transport){
        var elementValues = {};
        if (transport && transport.responseText){
            try{
                elementValues = eval('(' + transport.responseText + ')');
            }
            catch (e) { 
                elementValues = {};
            }
        }
        else{
            this.resetSelectedAddress();
        }
        arrElements = Form.getElements(this.form);
        for (var elemIndex in arrElements) {
            if (arrElements[elemIndex].id) {
                var fieldName = arrElements[elemIndex].id.replace(/billing:/, '');
                arrElements[elemIndex].value = elementValues[fieldName] ? elementValues[fieldName] : '';
                if (fieldName == 'country_id' && billingForm){
                    billingForm.elementChildLoad(arrElements[elemIndex]);
                }
            }
        }
    },
    
    save: function(){
        var validator = new Validation(this.form);
        if (validator.validate()) {
            var request = new Ajax.Request(
                this.saveUrl,
                {method: 'post', onSuccess: this.onSave, parameters: Form.serialize(this.form)}
            );
        }
    },

    nextStep: function(){
        checkout.setBilling();
    }
}

// payment
var Payment = Class.create();
Payment.prototype = {
    initialize: function(form, saveUrl){
        this.form = form;
        this.saveUrl = saveUrl;
        this.onSave = this.nextStep.bindAsEventListener(this);
        var elements = Form.getElements(form);
        var method = null;
        for (var i=0; i<elements.length; i++) {
            if (elements[i].name=='payment[method]') {
                if (elements[i].checked) {
                    method = elements[i].value;
                }
            } else {
                elements[i].disabled = true;
            }
        }
        if (method) this.switchMethod(method);
    },
    
    switchMethod: function(method){
        if (this.currentMethod && $('payment_form_'+this.currentMethod)) {
            var form = $('payment_form_'+this.currentMethod);
            form.style.display = 'none';
            var elements = form.getElementsByTagName('input');
            for (var i=0; i<elements.length; i++) elements[i].disabled = true;
            var elements = form.getElementsByTagName('select');
            for (var i=0; i<elements.length; i++) elements[i].disabled = true;

        }
        if ($('payment_form_'+method)){
            var form = $('payment_form_'+method);
            form.style.display = '';
            var elements = form.getElementsByTagName('input');
            for (var i=0; i<elements.length; i++) elements[i].disabled = false;
            var elements = form.getElementsByTagName('select');
            for (var i=0; i<elements.length; i++) elements[i].disabled = false;
            this.currentMethod = method;
        }
    },

    save: function(){
        var validator = new Validation(this.form);
        if (validator.validate()) {
            var request = new Ajax.Request(
                this.saveUrl,
                {
                    method:'post',
                    onSuccess: this.onSave,
                    parameters: Form.serialize(this.form)
                }
            );
        }
    },

    nextStep: function(){
        checkout.setShipping();
    }
}

// shipping
var Shipping = Class.create();
Shipping.prototype = {
    initialize: function(form, addressUrl, saveUrl, methodsUrl){
        this.form = form;
        this.addressUrl = addressUrl;
        this.saveUrl = saveUrl;
        this.methodsUrl = methodsUrl;
        this.onAddressLoad = this.fillForm.bindAsEventListener(this);
        this.onSave = this.nextStep.bindAsEventListener(this);
    },

    setAddress: function(addressId){
        if (addressId) {
            request = new Ajax.Request(
                this.addressUrl+addressId,
                {
                    method:'get',
                    onSuccess: this.onAddressLoad
                }
            );
        }
        else {
            this.fillForm(false);
        }
    },
    
    resetSelectedAddress: function(){
        var selectElement = $('shipping-address-select')
        if (selectElement) {
            selectElement.value='';
        }
    },

    fillForm: function(transport){
        var elementValues = {};
        if (transport && transport.responseText){
            try{
                elementValues = eval('(' + transport.responseText + ')');
            }
            catch (e) { 
                elementValues = {};
            }
        }
        else{
            this.resetSelectedAddress();
        }
        arrElements = Form.getElements(this.form);
        for (var elemIndex in arrElements) {
            if (arrElements[elemIndex].id) {
                var fieldName = arrElements[elemIndex].id.replace(/shipping:/, '');
                arrElements[elemIndex].value = elementValues[fieldName] ? elementValues[fieldName] : '';
                if (fieldName == 'country_id' && shippingForm){
                    shippingForm.elementChildLoad(arrElements[elemIndex]);
                }
            }
        }
    },
    
    save: function(){
        var validator = new Validation(this.form);
        if (validator.validate()) {
            var request = new Ajax.Request(
                this.saveUrl,
                {
                    method:'post',
                    onSuccess: this.onSave,
                    parameters: Form.serialize(this.form)
                }
            );
        }
    },

    nextStep: function(){
        var updater = new Ajax.Updater('checkout-shipping-method-load', this.methodsUrl, {method:'get'});
        checkout.setShipping();
    }
}

// shipping method
var ShippingMethod = Class.create();
ShippingMethod.prototype = {
    initialize: function(form, saveUrl){
        this.form = form;
        this.saveUrl = saveUrl;
        this.onSave = this.nextStep.bindAsEventListener(this);
    },

    save: function(){
        var validator = new Validation(this.form);
        if (validator.validate()) {
            var request = new Ajax.Request(
                this.saveUrl,
                {
                    method:'post',
                    onSuccess: this.onSave,
                    parameters: Form.serialize(this.form)
                }
            );
        }
    },

    nextStep: function(){
        checkout.setReview();
    }
}
