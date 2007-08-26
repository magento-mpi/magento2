var Checkout = Class.create();
Checkout.prototype = {
    initialize: function(accordion, progressUrl, reviewUrl, saveMethodUrl){
        this.accordion = accordion;
        this.progressUrl = progressUrl;
        this.reviewUrl = reviewUrl;
        this.saveMethodUrl = saveMethodUrl;
        this.billingForm = false;
        this.shippingForm= false;
        this.syncBillingShipping = false;
        this.method = '';
        this.payment = '';
        this.loadWaiting = false;

        this.onSetMethod = this.nextStep.bindAsEventListener(this);
        
        this.accordion.disallowAccessToNextSections = true;
    }, 
    
    reloadProgressBlock: function(){
        var updater = new Ajax.Updater($$('.col-right')[0], this.progressUrl, {method: 'get'});
    },
        
    reloadReviewBlock: function(){
        var updater = new Ajax.Updater('checkout-review-load', this.reviewUrl, {method: 'get'});
    },
    
    setLoadWaiting: function(step) {
        if (step) {
            if (this.loadWaiting) {
                this.setLoadWaiting(false);
            }
            $(step+'-buttons-container').setStyle({opacity:.5});
            $(step+'-please-wait').setStyle({display:''});
        } else {
            if (this.loadWaiting) {
                $(this.loadWaiting+'-buttons-container').setStyle({opacity:1});
                $(this.loadWaiting+'-please-wait').setStyle({display:'none'});
            }
        }
        this.loadWaiting = step;
    },
    
    setMethod: function(){
        if ($('login:guest') && $('login:guest').checked) {
            this.method = 'guest';
            var request = new Ajax.Request(
                this.saveMethodUrl,
                {method: 'post', /*onSuccess: this.onSetMethod, */parameters: {method:'guest'}}
            );
            $('register-customer-password').style.display = 'none';
            this.nextStep();
        }
        else if($('login:register') && $('login:register').checked) {
            this.method = 'register';
            var request = new Ajax.Request(
                this.saveMethodUrl,
                {method: 'post', /*onSuccess: this.onSetMethod, */parameters: {method:'register'}}
            );
            $('register-customer-password').style.display = 'block';
            this.nextStep();
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
            //this.setShipping();
            shipping.nextStep();
        } else {
            $('shipping:same_as_billing').checked = false
            this.reloadProgressBlock();
        }
        this.accordion.openNextSection(true);
    },
    
    setShipping: function() {
        this.reloadProgressBlock();
        this.accordion.openNextSection(true);
    },
        
    setShippingMethod: function() {
        this.reloadProgressBlock();
        this.accordion.openNextSection(true);
    },
    
    setPayment: function() {
        this.reloadProgressBlock();
        this.accordion.openNextSection(true);
    },

    setReview: function() {
        this.reloadProgressBlock();
        this.reloadReviewBlock();
        this.accordion.openNextSection(true);
    },
    
    back: function(){
        if (this.loadWaiting) return;
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
        this.onComplete = this.resetLoadWaiting.bindAsEventListener(this);
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
    
    newAddress: function(isNew){
        if (isNew) {
            this.resetSelectedAddress();
            $('billing-new-address-form').style.display = 'block';
        } else {
            $('billing-new-address-form').style.display = 'none';
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
        if (checkout.loadWaiting!=false) return;
        
        var validator = new Validation(this.form);
        if (validator.validate()) {
            checkout.setLoadWaiting('billing');
            if (checkout.method=='register' && $('billing:customer_password').value != $('billing:confirm_password').value) {
                alert('Error: Passwords do not match');
                return;
            }
            if ($('billing:use_for_shipping') && $('billing:use_for_shipping').checked) {
                $('billing:use_for_shipping').value=1;
            }
            var request = new Ajax.Request(
                this.saveUrl,
                {
                    method: 'post', 
                    onComplete: this.onComplete,
                    onSuccess: this.onSave, 
                    parameters: Form.serialize(this.form)
                }
            );
        }
    },
    
    resetLoadWaiting: function(transport){
        checkout.setLoadWaiting(false);
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
        this.onComplete = this.resetLoadWaiting.bindAsEventListener(this);
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

    newAddress: function(isNew){
        if (isNew) {
            this.resetSelectedAddress();
            $('shipping-new-address-form').style.display = 'block';
        } else {
            $('shipping-new-address-form').style.display = 'none';
        }
        shipping.setSameAsBilling(false);
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
        $('billing-address-select') && this.newAddress(!$('billing-address-select').value);
        $('shipping:same_as_billing').checked = true;
        if (!$('billing-address-select') || !$('billing-address-select').value) {
            arrElements = Form.getElements(this.form);
            for (var elemIndex in arrElements) {
                if (arrElements[elemIndex].id) {
                    var sourceField = $(arrElements[elemIndex].id.replace(/^shipping:/, 'billing:'));
                    if (sourceField){
                        arrElements[elemIndex].value = sourceField.value;
                    }
                }
            }
            $('shipping:country_id').value = $('billing:country_id').value;
            shippingRegionUpdater.update();
            $('shipping:region_id').value = $('billing:region_id').value;
            $('shipping:region').value = $('billing:region').value;
            //shippingForm.elementChildLoad($('shipping:country_id'), this.setRegionValue.bind(this));
        } else {
            $('shipping-address-select').value = $('billing-address-select').value;
        }
    },

    setRegionValue: function(){
        $('shipping:region').value = $('billing:region').value; 
    },
    
    save: function(){
    	if (checkout.loadWaiting!=false) return;
        var validator = new Validation(this.form);
        if (validator.validate()) {
            checkout.setLoadWaiting('shipping');
            var request = new Ajax.Request(
                this.saveUrl,
                {
                    method:'post', 
                    onComplete: this.onComplete,
                    onSuccess: this.onSave, 
                    parameters: Form.serialize(this.form)
                }
            );
        }
    },
    
    resetLoadWaiting: function(transport){
        checkout.setLoadWaiting(false);
    },

    nextStep: function(){
        var updater = new Ajax.Updater(
            'checkout-shipping-method-load', 
            this.methodsUrl, 
            {method:'get', onSuccess: checkout.setShippingMethod.bind(checkout)}
        );
        //checkout.setShipping();
    }
}

// shipping method
var ShippingMethod = Class.create();
ShippingMethod.prototype = {
    initialize: function(form, saveUrl){
        this.form = form;
        this.saveUrl = saveUrl;
        this.onSave = this.nextStep.bindAsEventListener(this);
        this.onComplete = this.resetLoadWaiting.bindAsEventListener(this);
    },
    
    validate: function() {
    	var methods = document.getElementsByName('shipping_method');
    	if (methods.length==0) {
    		alert('Your order can not be completed at this time as there is no shipping methods available for it. Please make neccessary changes in your shipping address.');
    		return false;
    	}
    	for (var i=0; i<methods.length; i++) {
    		if (methods[i].checked) {
    			return true;
    		}
    	}
    	alert('Please specify shipping method.');
    	return false;
    },

    save: function(){
    	if (checkout.loadWaiting!=false) return;
        if (this.validate()) {
            checkout.setLoadWaiting('shipping-method');
            var request = new Ajax.Request(
                this.saveUrl,
                {
                    method:'post',
                    onComplete: this.onComplete,
                    onSuccess: this.onSave,
                    parameters: Form.serialize(this.form)
                }
            );
        }
    },
    
    resetLoadWaiting: function(transport){
        checkout.setLoadWaiting(false);      
    },

    nextStep: function(){
        checkout.setPayment();
    }
}


// payment
var Payment = Class.create();
Payment.prototype = {
    initialize: function(form, saveUrl){
        this.form = form;
        this.saveUrl = saveUrl;
        this.onSave = this.nextStep.bindAsEventListener(this);
        this.onComplete = this.resetLoadWaiting.bindAsEventListener(this);
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

    validate: function() {
    	var methods = document.getElementsByName('payment[method]');
    	if (methods.length==0) {
    		alert('Your order can not be completed at this time as there is no payment methods available for it.');
    		return false;
    	}
    	for (var i=0; i<methods.length; i++) {
    		if (methods[i].checked) {
    			return true;
    		}
    	}
    	alert('Please specify payment method.');
    	return false;
    },

    save: function(){
    	if (checkout.loadWaiting!=false) return;
        var validator = new Validation(this.form);
        if (this.validate() && validator.validate()) {
            checkout.setLoadWaiting('payment');
            var request = new Ajax.Request(
                this.saveUrl,
                {
                    method:'post',
                    onComplete: this.onComplete,
                    onSuccess: this.onSave,
                    parameters: Form.serialize(this.form)
                }
            );
        }
    },
    
    resetLoadWaiting: function(){
        checkout.setLoadWaiting(false);   
    },

    nextStep: function(){
        checkout.setReview();
    }
}

var Review = Class.create();
Review.prototype = {
    initialize: function(saveUrl, successUrl){
        this.saveUrl = saveUrl;
        this.successUrl = successUrl;
        this.onSave = this.nextStep.bindAsEventListener(this);
        this.onComplete = this.resetLoadWaiting.bindAsEventListener(this);
    },
    
    save: function(){
    	if (checkout.loadWaiting!=false) return;
        checkout.setLoadWaiting('review');
        var request = new Ajax.Request(
            this.saveUrl,
            {
                method:'post',
                parameters:{save:true},
                onComplete: this.onComplete,
                onSuccess: this.onSave
            }
        );
    },
    
    resetLoadWaiting: function(transport){ 
        checkout.setLoadWaiting(false);     
    },

    nextStep: function(transport){
        if (transport && transport.responseText) {
            try{
                response = eval('(' + transport.responseText + ')');
            }
            catch (e) { 
                response = {};
            }
            if (response.success) {
                window.location=this.successUrl;
            }
            else{
                alert('error');
            }
        }
    }
}