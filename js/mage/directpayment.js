/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     js
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
var directPayment = Class.create();
directPayment.prototype = {
	initialize: function (iframeId, controller, orderSaveUrl, cgiUrl, orderPlaceUrl)
    {		
        this.iframeId = iframeId;
        this.controller = controller;
        this.orderSaveUrl = orderSaveUrl;
        this.orderPlaceUrl = orderPlaceUrl;
        this.cgiUrl = cgiUrl;        
        this.code = 'directpayment';
        this.inputs = {
            'directpayment_cc_type'       : 'cc_type',
            'directpayment_cc_number'     : 'cc_number',
            'directpayment_expiration'    : 'cc_exp_month',
            'directpayment_expiration_yr' : 'cc_exp_year',
            'directpayment_cc_cid'        : 'cc_cid'
        };
        this.isValid = true;
        this.paymentRequestSent = false;
        this.isResponse = false;
        this.orderIncrementId = false;
        this.successUrl = false;
        this.buttons = [];
        
        this.onSaveOnepageOrderSuccess = this.saveOnepageOrderSuccess.bindAsEventListener(this);        
        this.onLoadIframe = this.loadIframe.bindAsEventListener(this);
        
        this.preparePayment();        
    },
    
    validate: function ()
    {
    	this.isValid = true;
		for (var elemIndex in this.inputs) {
			if ($(elemIndex)) {				
				if (!Validation.validate($(elemIndex))) {
					this.isValid = false;
				}
			}
		}
    	
    	return this.isValid;
    },
    
    disableInputs: function()
    {
    	for (var elemIndex in this.inputs) {
			if ($(elemIndex)) {				
				$(elemIndex).writeAttribute('disabled','disabled');
			}
		}
    },
    
    enableInputs: function()
    {
    	for (var elemIndex in this.inputs) {
			if ($(elemIndex)) {				
				$(elemIndex).writeAttribute('disabled',false);
			}
		}
    },
    
    disableServerValidation: function()
    {
    	for (var elemIndex in this.inputs) {
			if ($(elemIndex)) {				
				$(elemIndex).stopObserving();
			}
		}
    },
    
    preparePayment: function ()
    {	
    	if ($(this.iframeId)) {
	    	switch (this.controller) {
		    	case 'onepage':
		    		var button = $('review-buttons-container').down('button');
		    		button.writeAttribute('onclick','');
		    		button.observe('click', function(obj){
		    			return function(){
			    			if ($(obj.iframeId)) {			    				
			    				if (obj.validate()) {
				    				//TODO: custom logic
				    				obj.saveOnepageOrder();			    				
			    				}			    							    				
			    			}
			    			else {
			    				review.save();
			    			}
		    			}
		    		}(this));	    		
		    		break;
		    	case 'multishipping':
		    		$('review-button').up('form').writeAttribute('onsubmit','return false;');
		    		$('review-button').up('form').observe('submit', function(obj){
		    	        return function(){
			    			if (obj.validate()) {
			    				showLoader();
			    				//TODO: custom logic
			    				$('review-button').up('form').submit();
			    			}			    			
		    			}
		    		}(this));
		    		break;
		    	case 'sales_order_create':	    		
	    		this.disableServerValidation();
	    		$('p_method_'+this.code).observe('click', function(obj){
	    			return function(){
	    				obj.disableServerValidation();
	    			}
	    		}(this));		    		
		    	this.buttons = document.getElementsByClassName('scalable save');
		    	for(var i = 0; i < this.buttons.length; i++){
		    		var button = this.buttons[i];			    		
			    	button.writeAttribute('onclick','');
			    	button.observe('click', function(obj){			    		
			    		return function(){
			    			var paymentMethod = $(this).up('form').getInputs('radio','payment[method]').find(function(radio){return radio.checked;}).value;				    			
			    			if (paymentMethod == obj.code) {					    			
			    				if (obj.validate()) {
			    					//TODO: custom logic
				    				toggleSelectsUnderBlock($('loading-mask'), false);
				    				$('loading-mask').show();
				    	            setLoaderPosition();
				    				obj.disableInputs();					    				
				    				obj.paymentRequestSent = true;
				    				obj.orderRequestSent = true;
				    				$(this).up('form').writeAttribute('target',$(obj.iframeId).readAttribute('name'));
				    				$(this).up('form').submit();
			    				}				    								    			
				    		}
			    			else {
			    				$(this).up('form').writeAttribute('target','_top');
			    				order.submit();
			    			}
		    			}				    	
			    	}(this));
		    	}
	    		break;
	    	}
	    	
	    	$(this.iframeId).observe('load', this.onLoadIframe.bind(this));
    	}
    },
    
    loadIframe: function() 
    {    	
    	if (this.paymentRequestSent) {    		
    		$(this.iframeId).show();    		
    		switch (this.controller) {
	    		case 'onepage':
	    			review.resetLoadWaiting();
	    			break;
	    		case 'sales_order_edit':
		    	case 'sales_order_create':
		    		if (this.orderRequestSent) {
		    			$(this.iframeId).hide();
			    		var data = $(this.iframeId).contentWindow.document.body.innerHTML;		    		
			    		this.saveAdminOrderSuccess(data);
			    		this.orderRequestSent = false;
		    		}
		    		else {
		    			this.paymentRequestSent = false;
		    			$(this.iframeId).show();
		    			this.enableInputs();
		    			toggleSelectsUnderBlock($('loading-mask'), true);
		    			$('loading-mask').hide();
		    			for(var i = 0; i < this.buttons.length; i++) {
		    				var button = this.buttons[i];			    		
		    		    	button.writeAttribute('onclick','');
		    		    	button.observe('click', function(obj){			    		
		    		    		return function(){
		    		    			if (!obj.successUrl) {
		    		    				window.location = obj.orderSaveUrl.replace('save','');
		    		    			}
		    		    			else {
		    		    				window.location = obj.successUrl;
		    		    			}
		    		    		}
		    		    	}(this));
		    			}
		    		}
		    		break;
    		}
    	}
    },
    
    showError: function(msg)
    {
    	switch (this.controller) {
    		case 'onepage':
    			this.paymentRequestSent = false;
    	    	$(this.iframeId).hide();
    	    	$(this.iframeId).next('ul').show();  
    			break;
    		case 'sales_order_edit':
	    	case 'sales_order_create':
	    		$(this.iframeId).style.height = '0px';
	    		$(this.iframeId).hide();
	    		break;
    	}    	  	
    	alert(msg);
    },    
    
    saveOnepageOrder: function()
    {    	
    	checkout.setLoadWaiting('review');
        var params = Form.serialize(payment.form);
        if (review.agreementsForm) {
            params += '&'+Form.serialize(review.agreementsForm);
        }        
    	new Ajax.Request(
    		this.orderSaveUrl,
            {
                method:'post',
                parameters:params,
                onComplete: this.onSaveOnepageOrderSuccess,               
                onFailure: function(transport) {    				
    				review.resetLoadWaiting();
    				if (transport.status == 403) {
    		    		checkout.ajaxFailure();
    		    	}
    			}
            }
        );
    },
    
    saveOnepageOrderSuccess: function(transport) 
    {
    	if (transport.status == 403) {
    		checkout.ajaxFailure();
    	}
    	try{
            response = eval('(' + transport.responseText + ')');
        }
        catch (e) {
            response = {};
        }
        
        if (response.success && response.directpayment) {
        	this.orderIncrementId = response.directpayment.fields.x_invoice_num;
        	var paymentData = {};
            for(var key in response.directpayment.fields) {
            	paymentData[key] = response.directpayment.fields[key];
            }            
            var preparedData = this.preparePaymentRequest(paymentData);            
        	return this.sendPaymentRequest(preparedData);
        }
        else{
            var msg = response.error_messages;
            if (typeof(msg)=='object') {
                msg = msg.join("\n");
            }
            if (msg) {
            	alert(msg);
            }
        }
	},
	
	saveAdminOrderSuccess: function(data) 
    {    	
    	try{
            response = eval('(' + data + ')');
        }
        catch (e) {
            response = {};
        }
        
        if (response.directpayment) {
        	this.orderIncrementId = response.directpayment.fields.x_invoice_num;
        	var paymentData = {};
            for(var key in response.directpayment.fields) {
            	paymentData[key] = response.directpayment.fields[key];
            }            
            var preparedData = this.preparePaymentRequest(paymentData);            
        	return this.sendPaymentRequest(preparedData);
        }        
	},
    
    preparePaymentRequest: function(data)
    {
    	if ($(this.code+'_cc_cid')) {
    		data.x_card_code = $(this.code+'_cc_cid').value;
		}
    	var year = $(this.code+'_expiration_yr').value;
    	if (year.length > 2) {
    		year = year.substring(2);
    	}
        var month = parseInt($(this.code+'_expiration').value, 10);
        if (month < 10){
            month = '0' + month;
        }

        data.x_exp_date = month + '/' + year;
        data.x_card_num = $(this.code+'_cc_number').value;
        
        return data;
    },
    
    sendPaymentRequest: function(preparedData)
    {
    	tmpForm = document.createElement('form');
    	tmpForm.style.display = 'none';
    	tmpForm.enctype = 'application/x-www-form-urlencoded';
        tmpForm.method = 'POST';
        document.body.appendChild(tmpForm);
        tmpForm.action = this.cgiUrl;
        tmpForm.target = $(this.iframeId).readAttribute('name');
        tmpForm.setAttribute('target', $(this.iframeId).readAttribute('name'));

        for (var param in preparedData){
        	var field;
            if (isIE){
                field = document.createElement('<input type="hidden" name="' + param + '" value="' + preparedData[param] + '" />');
                tmpForm.appendChild(field);
            } else {
                field = document.createElement('input');
                tmpForm.appendChild(field);
                field.type = 'hidden';
                field.name = param;
                field.value = preparedData[param];
            }
        }        
        
        this.paymentRequestSent = true;
        tmpForm.submit();
        tmpForm.remove();
        
        return this.paymentRequestSent;
    }
    	
};