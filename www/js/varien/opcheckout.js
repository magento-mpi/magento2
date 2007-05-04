var Checkout = Class.create();
Checkout.prototype = {
    initialize: function(accordion, statusUrl, reviewUrl, saveMethodUrl){
        this.accordion = accordion;
        this.statusUrl = statusUrl;
        this.reviewUrl = reviewUrl;
        this.saveMethodUrl = saveMethodUrl;
        this.billingForm = false;
        this.shippingForm= false;
        this.syncBillingShipping = false;
        this.method = '';
        this.payment = '';

        this.onSetMethod = this.nextStep.bindAsEventListener(this);
    }, 
    
    reloadStatusBlock: function(){
        var updater = new Ajax.Updater('column-left', this.statusUrl, {method: 'get'});
    },
        
    reloadReviewBlock: function(){
        var updater = new Ajax.Updater('checkout-review-load', this.reviewUrl, {method: 'get'});
    },
    
    setMethod: function(){
        if ($('checkout_method:guest') && $('checkout_method:guest').checked) {
            this.method = 'guest';
            var request = new Ajax.Request(
                this.saveMethodUrl,
                {method: 'post', onSuccess: this.onSetMethod, parameters: {method:'guest'}}
            );
        }
        else if($('checkout_method:register') && $('checkout_method:register').checked) {
            this.method = 'register';
            var request = new Ajax.Request(
                this.saveMethodUrl,
                {method: 'post', onSuccess: this.onSetMethod, parameters: {method:'register'}}
            );
        }
        else{
            alert('Choose checkout type');
            return false;
        }
    },
    
    nextStep: function(){
        if ($('billing-login-info')){
            if (this.method == 'register'){
                Element.show('billing-login-info');
            }
            else{
                Element.hide('billing-login-info');
            }
        }
        this.accordion.openNextSection(true);
    },

    setBilling: function() {
        if ($('billing:use_for_shipping') && $('billing:use_for_shipping').checked){
            shipping.syncWithBilling();
        } else {
            $('shipping:same_as_billing').checked = false;
        }
        
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
                var fieldName = arrElements[elemIndex].id.replace(/^billing:/, '');
                arrElements[elemIndex].value = elementValues[fieldName] ? elementValues[fieldName] : '';
                if (fieldName == 'country_id' && billingForm){
                    billingForm.elementChildLoad(arrElements[elemIndex]);
                }
            }
        }
    },
    
    setUseForShipping: function(flag) {
        $('shipping:same_as_billing').checked = flag;
    },
    
    save: function(){
        var validator = new Validation(this.form);
        if (validator.validate()) {
            if (checkout.method=='register' && $('billing:customer_password').value != $('billing:confirm_password').value) {
                alert('Error: Passwords do not match');
                return;
            }
            if ($('billing:use_for_shipping') && $('billing:use_for_shipping').checked) {
                $('billing:use_for_shipping').value=1;
            }
            var request = new Ajax.Request(
                this.saveUrl,
                {method: 'post', onSuccess: this.onSave, parameters: Form.serialize(this.form)}
            );
        }
    },

    nextStep: function(transport){
        if (transport && transport.responseText){
            try{
                response = eval('(' + transport.responseText + ')');
            }
            catch (e) { 
                response = {};
            }
        }
        if (response.error){
            alert(response.message);
            return false;
        }
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
        if ($('billing:use_for_shipping').checked) {
            checkout.setShippingMethod();
        }
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
                {method:'get', onSuccess: this.onAddressLoad}
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
                var fieldName = arrElements[elemIndex].id.replace(/^shipping:/, '');
                arrElements[elemIndex].value = elementValues[fieldName] ? elementValues[fieldName] : '';
                if (fieldName == 'country_id' && shippingForm){
                    shippingForm.elementChildLoad(arrElements[elemIndex]);
                }
            }
        }
    },
    
    setSameAsBilling: function(flag) {
        $('shipping:same_as_billing').checked = flag;
        $('billing:use_for_shipping').checked = flag;
        if (flag) {
            this.syncWithBilling();
        }
    },
    
    syncWithBilling: function () {
        $('shipping:same_as_billing').checked = true;
        arrElements = Form.getElements(this.form);
        for (var elemIndex in arrElements) {
            if (arrElements[elemIndex].id) {
                var sourceField = $(arrElements[elemIndex].id.replace(/^shipping:/, 'billing:'));
                arrElements[elemIndex].value = sourceField.value;
            }
        }
        shippingForm.elementChildLoad($('shipping:country_id'), this.setRegionValue.bind(this));
        
    },

    setRegionValue: function(){
        $('shipping:region').value = $('billing:region').value; 
    },
    
    save: function(){
        var validator = new Validation(this.form);
        if (validator.validate()) {
            var request = new Ajax.Request(
                this.saveUrl,
                {method:'post', onSuccess: this.onSave, parameters: Form.serialize(this.form)}
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
