/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
var AdminOrder = new Class.create();
AdminOrder.prototype = {
    initialize : function(data){
        if(!data) data = {};
        this.loadBaseUrl    = false;
        this.customerId     = data.customer_id ? data.customer_id : false;
        this.storeId        = data.store_id ? data.store_id : false;
        this.currencyId     = false;
        this.addresses      = data.addresses ? data.addresses : new Hash();
        this.shippingAsBilling = data.shippingAsBilling ? data.shippingAsBilling : false;
        this.products       = $H({});//new Hash();
        this.items          = $H({});
        this.billingAddress = {};
        this.shippingAddress= {};
        this.paymentMethod  = {};
        this.shippingMethod = {};
        this.billingAddressContainer = '';
        this.shippingAddressContainer= '';
    },
    
    setLoadBaseUrl : function(url){
        this.loadBaseUrl = url;
    },
    
    setAddresses : function(addresses){
        this.addresses = addresses;
    },
    
    setCustomerId : function(id){
        this.customerId = id;
        this.loadArea('header', false);
        this.customerSelectorHide();
        this.storeSelectorShow();
    },
    
    setStoreId : function(id){
        this.storeId = id;
        this.storeSelectorHide();
        this.sidebarShow();
        this.loadArea(['header', 'sidebar','data'], true);
        this.dataShow();
    },
    
    setCurrencyId : function(id){
        this.currencyId = id;
        this.loadArea(['sidebar', 'data'], true);
        //this.loadArea('data');
    },
    
    selectAddress : function(id, container){
        if(this.addresses[id]){
            this.fillAddressFields(container, this.addresses[id]);
        }
        else{
            this.fillAddressFields(container, {});
        }
        
        var data = this.serializeData(container);
        data['order[customer_address_id]'] = id;
        if(this.isShippingField(container)){
            this.reloadShippingMethod(data);
        }
        else{
            this.saveData(data);
        }
    },
    
    isShippingField : function(fieldId){
        if(this.shippingAsBilling){
            return fieldId.include('billing');
        }
        return fieldId.include('shipping');
    },
    
    bindAddressFields : function(container) {
        var fields = $(container).getElementsBySelector('input', 'select');
        for(var i=0;i<fields.length;i++){
            Event.observe(fields[i], 'change', this.changeAddressField.bind(this));
        }
    },
    
    changeAddressField : function(event){
        var field = Event.element(event);
        var re = /[^\[]*\[([^\]]*)_address\]\[([^\]]*)\](\[(\d)\])?/;
        var matchRes = field.name.match(re);
        var type = matchRes[1];
        var name = matchRes[2];
        
        if(name == 'postcode' || name == 'country_id'){
            if(type == 'billing' && this.shippingAsBilling) {
                this.reloadShippingMethod(this.serializeData(this.billingAddressContainer));
            }
            if(type == 'shipping' && !this.shippingAsBilling){
                this.reloadShippingMethod(this.serializeData(this.shippingAddressContainer));
            }
        }
        
    },
    
    fillAddressFields : function(container, data){
        var regionIdElem = false;
        var regionIdElemValue = false;
        var fields = $(container).getElementsBySelector('input', 'select');
        var re = /[^\[]*\[[^\]]*\]\[([^\]]*)\](\[(\d)\])?/;
        for(var i=0;i<fields.length;i++){
            var matchRes = fields[i].name.match(re);
            var name = matchRes[1];
            var index = matchRes[3];
            
            if(index){
                if(data[name]){
                    var values = data[name].split("\n");
                    fields[i].value = values[index] ? values[index] : '';
                }
                else{
                    fields[i].value = '';
                }
            }
            else{
                fields[i].value = data[name] ? data[name] : '';
            }
            
            if(fields[i].changeUpdater) fields[i].changeUpdater();
            if(name == 'region' && data['region_id'] && !data['region']){
                fields[i].value = data['region_id'];
            }
        }
    },
    
    syncAddressForms : function(flag){
        this.shippingAsBilling = flag;
        if($('order:shipping_address_customer_address_id')) {
            $('order:shipping_address_customer_address_id').disabled=flag;
        }
        if($(this.shippingAddressContainer)){
            var dataFields = $(this.shippingAddressContainer).getElementsBySelector('input', 'select');
            for(var i=0;i<dataFields.length;i++) dataFields[i].disabled = flag;
        }
    },
    
    setShippingAsBilling : function(flag){
        this.syncAddressForms(flag);
        if(flag){
            var data = this.serializeData(this.billingAddressContainer);
        }
        else{
            var data = this.serializeData(this.shippingAddressContainer);
        }
        data['shippingAsBilling'] = flag ? 1 : 0;
        this.reloadShippingMethod(data);
    },
    
    syncAddressFields : function(field){
        
    },
    
    reloadShippingMethod : function(data){
        data['setShipping'] = true;
        this.loadArea(['shipping_method', 'totals'], this.getAreaId('shipping_method'), data);
    },

    setShippingMethod : function(method){
        var data = $H({});
        data['order[shipping_method]'] = method;
        this.reloadShippingMethod(data);
    },
    
    switchPaymentMethod : function(method){
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
        
        var data = $H({});
        data['order[payment_method]'] = method;
        this.saveData(data);
    },
    
    applyCoupon : function(code){
        
    },
    
    removeCoupon : function(){
        
    },
    
    addProduct : function(id){
        this.loadArea(['items', 'shipping_method', 'totals'], this.getAreaId('items'), {addProduct:id});
    },
    
    removeQuoteItem : function(id){
        this.loadArea(['items', 'shipping_method', 'totals'], this.getAreaId('items'), {removeItem:id});
    },
    
    moveQuoteItem : function(id, to){
        this.loadArea(['sidebar_'+to, 'items', 'shipping_method', 'totals'], this.getAreaId('items'), {moveItem:id, moveTo:to});
    },
    
    productGridShow : function(){
        this.showArea('search');
    },
    
    productGridRowInit : function(grid, row){
        var checkbox = $(row).getElementsByClassName('checkbox')[0];
        var inputs = $(row).getElementsByClassName('input-text');
        if (checkbox && inputs.length > 0) {
            checkbox.inputElements = inputs;
            for (var i = 0; i < inputs.length; i++) {
                inputs[i].checkboxElement = checkbox;
                if (this.products[checkbox.value] && this.products[checkbox.value][inputs[i].name]) {
                    inputs[i].value = this.products[checkbox.value][inputs[i].name];
                }
                inputs[i].disabled = !checkbox.checked;
                Event.observe(inputs[i],'keyup', this.productGridRowInputChange.bind(this));
                Event.observe(inputs[i],'change',this.productGridRowInputChange.bind(this));
            }
        }        
    },
    
    productGridRowInputChange : function(event){
        var element = Event.element(event);
        if (element && element.checkboxElement && element.checkboxElement.checked){
            this.products[element.checkboxElement.value][element.name] = element.value;
        }
    },
    
    productGridRowClick : function(grid, event){
        var trElement = Event.findElement(event, 'tr');
        var isInput = Event.element(event).tagName == 'INPUT';
        if (trElement) {
            var checkbox = Element.getElementsBySelector(trElement, 'input');
            if (checkbox[0]) {
                var checked = isInput ? checkbox[0].checked : !checkbox[0].checked;
                grid.setCheckboxChecked(checkbox[0], checked);
            }
        }
    },
    
    productGridCheckboxCheck : function(grid, element, checked){
        if (checked) {
            if(element.inputElements) {
                this.products[element.value]={};
                for(var i = 0; i < element.inputElements.length; i++) {
                    element.inputElements[i].disabled = false;
                    if (element.inputElements[i].name == 'qty') {
                        if (!element.inputElements[i].value) {
                            element.inputElements[i].value = 1;
                        }
                    }
                    this.products[element.value][element.inputElements[i].name] = element.inputElements[i].value;
                }
            }
        } else {
            if(element.inputElements){
                for(var i = 0; i < element.inputElements.length; i++) {
                    element.inputElements[i].disabled = true;
                }
            }
            this.products.remove(element.value);
        }
        grid.reloadParams = {'products[]':this.products.keys()};
    },
    
    productGridAddSelected : function(){
        this.hideArea('search');
    },
    
    selectCustomer : function(grid, event){
        var element = Event.findElement(event, 'tr');
        if(element.id){
            this.setCustomerId(element.id);
        }
    },
    
    customerSelectorHide : function(){
        this.hideArea('customer-selector');
    },
    
    customerSelectorShow : function(){
        this.showArea('customer-selector');
    },

    storeSelectorHide : function(){
        this.hideArea('store-selector');
    },
    
    storeSelectorShow : function(){
        this.showArea('store-selector');
    },
    
    dataHide : function(){
        this.hideArea('data');
    },

    dataShow : function(){
        this.showArea('data');
    },
    
    sidebarHide : function(){
        if(this.storeId === false && $('page:left') && $('page:container')){
            $('page:left').hide();
            $('page:container').removeClassName('container');
            $('page:container').addClassName('container-collapsed');
        }
    },
    
    sidebarShow : function(){
        if($('page:left') && $('page:container')){
            $('page:left').show();
            $('page:container').removeClassName('container-collapsed');
            $('page:container').addClassName('container');
        }
    },
    
    itemsUpdate : function(){
        var qtys = $('order:items_grid').getElementsByClassName('item-qty').collect(function(el){
            var h = {};
            h[el.id.split(':')[1]] = el.value;
            return h;
        });
        this.loadArea(['items','shipping_method','totals'], 'order:items', {updateItems: qtys.toJSON()});
    },
    
    loadArea : function(area, indicator, params){
        var url = this.loadBaseUrl;
        if(area) url+= 'block/' + area
        if(indicator) indicator = 'html-body';
        params = this.prepareParams(params);
        params.json = true;
        this.loadingAreas = area;
        new Ajax.Request(url, {
            parameters:params,
            loaderArea: indicator,
            onSuccess: function(transport) {
                var response = transport.responseText.evalJSON();
                if(!this.loadingAreas){
                    this.loadingAreas = [];
                }
                if(typeof this.loadingAreas == 'string'){
                    this.loadingAreas = [this.loadingAreas];
                }
                if(this.loadingAreas.indexOf('messages'==-1)) this.loadingAreas.push('messages');
                for(var i=0; i<this.loadingAreas.length; i++){
                    var id = this.loadingAreas[i];
                    if(response[id] && $(this.getAreaId(id))){
                        $(this.getAreaId(id)).update(response[id]);
                    }
                }
            }.bind(this)
        });
    },
    
    saveData : function(data){
        this.loadArea(false, false, data);
    },
    
    showArea : function(area){
        var id = this.getAreaId(area);
        if($(id)) $(id).show();
    },
    
    hideArea : function(area){
        var id = this.getAreaId(area);
        if($(id)) $(id).hide();
    },
    
    getAreaId : function(area){
        return 'order:'+area;
    },
    
    prepareParams : function(params){
        if(!params) params = new Hash();
        if(!params.customer_id) params.customer_id = this.customerId;
        if(!params.store_id) params.store_id = this.storeId;
        if(!params.currency_id) params.currency_id = this.currencyId;
        if(!params.products) params.products = this.products.toQueryString();
        return params;
    },
    
    serializeData : function(container){
        var fields = $(container).getElementsBySelector('input', 'select', 'textarea');
        var data = $H({});
        for(var i=0; i<fields.length; i++) {
            data[fields[i].name] = fields[i].value;
        }
        return data;
    }
}