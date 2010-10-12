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
	initialize: function (iframeId, controller, orderSaveUrl)
    {		
        this.iframeId = iframeId;
        this.controller = controller;
        this.orderSaveUrl = orderSaveUrl;
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
        this.preparePayment();
        this.paymentData = {};
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
    
    preparePayment: function ()
    {    	
    	if ($(this.iframeId)) {
	    	switch (this.controller) {
		    	case 'onepage':
		    		var button = $('review-buttons-container').down('button');
		    		button.writeAttribute('onclick','');
		    		button.observe('click', function(obj){
		    			return function(){
			    			if (obj.validate()) {			    				
			    				//TODO: custom logic
			    				obj.saveOnepageOrder();
			    				//review.save();			    				
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
			    	var buttons = document.getElementsByClassName('scalable save');
			    	for(var i = 0; i < buttons.length; i++){
			    		var button = buttons[i];			    		
				    	button.writeAttribute('onclick','');
				    	button.observe('click', function(obj){			    		
				    		return function(){
				    			var paymentMethod = $('edit_form').getInputs('radio','payment[method]').find(function(radio){return radio.checked;}).value;
				    			if (paymentMethod == obj.code) {
					    			//TODO: custom logic				    				
					    		}
				    			order.submit();
			    			}				    	
				    	}(this));
			    	}
		    		break;
	    	}
	    	
	    	$(this.iframId).observe('load', function(obj){
	    		return function() {
	    			if (obj.paymentRequestSent) {
	    				//TODO: parse request, etc
	    				obj.paymentRequestSent = true;
	    			}
	    			else {
	    				//TODO: send payment request
	    			}
	    		}
	    	}(this));
    	}
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
                onComplete: function(transport) {
    				if (transport && transport.responseText) {
    					try{
    		                response = eval('(' + transport.responseText + ')');
    		            }
    		            catch (e) {
    		                response = {};
    		            }
    		            
    		            if (response.success && response.data) {
    		                for(var key in response.data) {
    		                	this.paymentData.key = response.data.key;
    		                }
    		                if (this.paymentData.length) {
    		                }
    		            }
    		            else{
    		                var msg = response.error_messages;
    		                if (typeof(msg)=='object') {
    		                    msg = msg.join("\n");
    		                }
    		                alert(msg);
    		            }
    				}
    			},                
                onFailure: function(transport) {    				
    				review.resetLoadWaiting();
    				alert('Can not load order url');
    			}
            }
        );
    },
    
    preparePaymentRequest: function(data)
    {
    	for (var elemId in this.inputs) {
    		if (elemId == 'directpayment_cc_cid') {
    			this.paymentData.x_fp_sequence = $(elemId).value;
    		}
    	}
    }
    	
};