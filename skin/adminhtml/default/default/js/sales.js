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
        this.products       = new Hash();
        this.billingAddress = {};
        this.shippingAddress= {};
        this.paymentMethod  = {};
        this.shippingMethod = {};
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
        this.loadArea('header', false);
        this.storeSelectorHide();
        this.sidebarShow();
        this.loadArea('sidebar', false);
        this.loadArea('data');
        this.dataShow();
    },
    
    setCurrencyId : function(id){
        this.currencyId = id;
        this.loadArea('sidebar');
        this.loadArea('data');
    },
    
    selectAddress : function(id, container){
        if(this.addresses[id]){
            this.fillAddressFields(container, this.addresses[id]);
        }
        else{
            this.fillAddressFields(container, {});
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
            
            if(name == 'country_id'){
                if(fields[i].changeUpdater) fields[i].changeUpdater();
            }
            if(name == 'region' && data['region_id'] && !data['region']){
                fields[i].value = data['region_id'];
            }
        }
    },
    
    toggleAddressSync : function(flag){
        if($('order:shipping_address_fields')){
            var dataFields = $('order:shipping_address_fields').getElementsBySelector('input', 'select');
            for(var i=0;i<dataFields.length;i++) dataFields[i].disabled = flag;
        }
        if($('order:shipping_address_address_id')) $('order:shipping_address_address_id').disabled=flag;
    },
    
    addProduct : function(id){
        alert(id);
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
    
    loadArea : function(area, indicator, params){
        var url = this.loadBaseUrl + 'block/' + area;
        params = this.prepareParams(params);
        new Ajax.Updater(
            this.getAreaId(area),
            url,
            {
                evalScripts:true,
                parameters:params,
                loaderArea: indicator
            }
        );
    },
    
    showArea : function(area){
        var id = 'order:'+area;
        if($(id)) $(id).show();
    },
    
    hideArea : function(area){
        var id = 'order:'+area;
        if($(id)) $(id).hide();
    },
    
    getAreaId : function(area){
        return 'order:'+area;
    },
    
    prepareParams : function(params){
        if(!params) params = {};
        if(!params.customer_id) params.customer_id = this.customerId;
        if(!params.store_id) params.store_id = this.storeId;
        if(!params.currency_id) params.currency_id = this.currencyId;
        return params;
    }
}