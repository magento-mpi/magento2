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
var directPost = Class.create();
directPost.prototype = {
	initialize: function (methodCode, iframeId, controller, orderSaveUrl, cgiUrl, nativeAction)
    {		
        this.iframeId = iframeId;
        this.controller = controller;
        this.orderSaveUrl = orderSaveUrl;
        this.nativeAction = nativeAction;
        this.cgiUrl = cgiUrl;        
        this.code = methodCode;
        this.inputs = [
            'cc_type',
            'cc_number',
            'expiration',
            'expiration_yr',
            'cc_cid'
        ];
        this.isValid = true;
        this.paymentRequestSent = false;
        this.isResponse = false;
        this.orderIncrementId = false;
        this.successUrl = false;
        this.hasError = false;        
        
        this.onSaveOnepageOrderSuccess = this.saveOnepageOrderSuccess.bindAsEventListener(this);        
        this.onLoadIframe = this.loadIframe.bindAsEventListener(this);
        this.onSubmitAdminOrder = this.submitAdminOrder.bindAsEventListener(this);
                	
        this.preparePayment();        
    },
    
    validate: function ()
    {
    	this.isValid = true;
    	this.inputs.each(function(elemIndex) {
			if ($(this.code+'_'+elemIndex)) {				
				if (!Validation.validate($(this.code+'_'+elemIndex))) {
					this.isValid = false;
				}
			}
		}, this);
    	
    	return this.isValid;
    },
    
    changeInputOptions: function(param, value)
    {
    	this.inputs.each(function(elemIndex) {
			if ($(this.code+'_'+elemIndex)) {		
				$(this.code+'_'+elemIndex).writeAttribute(param, value);
			}
		}, this);
    },    
    
    preparePayment: function ()
    {
    	this.changeInputOptions('autocomplete', 'off');
    	if ($(this.iframeId)) {
	    	switch (this.controller) {
		    	case 'onepage':
		    		var button = $('review-buttons-container').down('button');
		    		button.writeAttribute('onclick','');
		    		button.observe('click', function(){
		    			if ($(this.iframeId)) {			    				
		    				if (this.validate()) {				    				
			    				this.saveOnepageOrder();			    				
		    				}			    							    				
		    			}
		    			else {
		    				review.save();
		    			}
		    		}.bind(this));	    		
		    		break;		    	
		    	case 'sales_order_create':
		    	case 'sales_order_edit':		    		
			    	var buttons = document.getElementsByClassName('scalable save');			    	
			    	for(var i = 0; i < buttons.length; i++){			    			    		
			    		buttons[i].writeAttribute('onclick','');
			    		buttons[i].observe('click', this.onSubmitAdminOrder);				    		
			    	}
	    		break;
	    	}
	    	
	    	$(this.iframeId).observe('load', this.onLoadIframe);
    	}
    },
    
    loadIframe: function() 
    {    	
    	if (this.paymentRequestSent) {    		    		
    		switch (this.controller) {
	    		case 'onepage':
	    			this.paymentRequestSent = false;
	    			if (!this.hasError) {
	    				$(this.iframeId).show();
	    				review.resetLoadWaiting();
	    			}
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
		    			if (!this.hasError) {
		    				$(this.iframeId).show();
		    			}
		    			this.changeInputOptions('disabled', false);
		    			toggleSelectsUnderBlock($('loading-mask'), true);
		    			$('loading-mask').hide();
		    			enableElements('save');
		    		}
		    		break;
    		}
    	}
    },
    
    showError: function(msg)
    {
    	switch (this.controller) {
    		case 'onepage':
    			this.hasError = true;
    	    	$(this.iframeId).hide();    	    	  
    			break;
    		case 'sales_order_edit':
	    	case 'sales_order_create':
	    		this.hasError = true;
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
        params += '&controller=' + this.controller;
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
        
        if (response.success && response.directpost) {
        	this.orderIncrementId = response.directpost.fields.x_invoice_num;
        	var paymentData = {};
            for(var key in response.directpost.fields) {
            	paymentData[key] = response.directpost.fields[key];
            }            
            var preparedData = this.preparePaymentRequest(paymentData);            
        	this.sendPaymentRequest(preparedData);
        }
        else{
            var msg = response.error_messages;
            if (typeof(msg)=='object') {
                msg = msg.join("\n");
            }
            if (msg) {
            	alert(msg);
            }
            
            if (response.update_section) {
                $('checkout-'+response.update_section.name+'-load').update(response.update_section.html);
                response.update_section.html.evalScripts();
            }

            if (response.goto_section) {
                checkout.gotoSection(response.goto_section);
                checkout.reloadProgressBlock();
            }
        }
	},
	
	submitAdminOrder: function()
	{
		if (editForm.validate()) {				    				
			var paymentMethodEl = $(editForm.formId).getInputs('radio','payment[method]').find(function(radio){return radio.checked;});					    			
			if (paymentMethodEl.value == this.code) {
				toggleSelectsUnderBlock($('loading-mask'), false);
				$('loading-mask').show();
	            setLoaderPosition();
	            this.changeInputOptions('disabled', 'disabled');					    				
	            this.paymentRequestSent = true;
	            this.orderRequestSent = true;
	            $(editForm.formId).writeAttribute('action', this.orderSaveUrl);
	            $(editForm.formId).writeAttribute('target', $(this.iframeId).readAttribute('name'));
	            $(editForm.formId).appendChild(this.createHiddenElement('controller', this.controller));
				disableElements('save');
				if (order) {
					Ajax.activeRequestCount++;
					this.recollectQuote();
				}
				else {
					$(editForm.formId).submit();
				}
    		}
			else {
				$(editForm.formId).writeAttribute('action', this.nativeAction);
				$(editForm.formId).writeAttribute('target','_top');
				disableElements('save');
				$(editForm.formId).submit();
			}
		}
	},
	
	recollectQuote: function()
	{
		var area = ['sidebar', 'items', 'shipping_method', 'billing_method','totals', 'giftmessage'];
		area = order.prepareArea(area);
        var url = order.loadBaseUrl + 'block/' + area;
		var info = $('order-items_grid').select('input', 'select', 'textarea');
        var data = {};
        for(var i=0; i<info.length; i++){
            if(!info[i].disabled && (info[i].type != 'checkbox' || info[i].checked)) {
                data[info[i].name] = info[i].getValue();
            }
        }
        data.reset_shipping = true;
        data.update_items = true;
        if ($('coupons:code') && $F('coupons:code')) {
        	data['order[coupon][code]'] = $F('coupons:code');
        }
        data.json = true;
        new Ajax.Request(url, {
            parameters:data,
            loaderArea: 'html-body',
            onSuccess: function(transport) {
        		$(editForm.formId).submit();
            }.bind(this)
        });
        
	},
	
	saveAdminOrderSuccess: function(data) 
    {    	
    	try{
            response = eval('(' + data + ')');
        }
        catch (e) {
            response = {};
        }
                
        if (response.directpost) {
        	this.orderIncrementId = response.directpost.fields.x_invoice_num;
        	var paymentData = {};
            for(var key in response.directpost.fields) {
            	paymentData[key] = response.directpost.fields[key];
            }            
            var preparedData = this.preparePaymentRequest(paymentData);            
        	this.sendPaymentRequest(preparedData);
        }
        else {
        	if (response.redirect) {
            	window.location = response.redirect;
            }
        	if (response.error_messages) {
	        	var msg = response.error_messages;
	            if (typeof(msg)=='object') {
	                msg = msg.join("\n");
	            }
	            if (msg) {
	            	alert(msg);
	            }
        	}
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
        	tmpForm.appendChild(this.createHiddenElement(param, preparedData[param]));
        }        
        
        this.paymentRequestSent = true;
        tmpForm.submit();        
        
        return this.paymentRequestSent;
    },
    
    createHiddenElement: function(name, value)
    {
    	var field;
    	if (isIE) {
    		field = document.createElement('<input type="hidden" name="' + name + '" value="' + value + '" />');
    	}
    	else {
    		field = document.createElement('input');
    		field.type = 'hidden';
            field.name = name;
            field.value = value;
    	}
    	
    	return field;
    }    	
};
