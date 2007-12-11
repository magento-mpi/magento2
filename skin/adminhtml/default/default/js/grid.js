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
var varienGrid = new Class.create();

varienGrid.prototype = {
    initialize : function(containerId, url, pageVar, sortVar, dirVar, filterVar){
        this.containerId = containerId;
        this.url = url;
        this.pageVar = pageVar || false;
        this.sortVar = sortVar || false;
        this.dirVar  = dirVar || false;
        this.filterVar  = filterVar || false;
        this.tableSufix = '_table';
        this.useAjax = false;
        this.rowClickCallback = false;
        this.checkboxCheckCallback = false;
        this.preInitCallback = false;
        this.initCallback = false;
        this.initRowCallback = false;

        this.reloadParams = false;

        this.trOnMouseOver  = this.rowMouseOver.bindAsEventListener(this);
        this.trOnMouseOut   = this.rowMouseOut.bindAsEventListener(this);
        this.trOnClick      = this.rowMouseClick.bindAsEventListener(this);
        this.trOnDblClick   = this.rowMouseDblClick.bindAsEventListener(this);
        this.trOnKeyPress   = this.keyPress.bindAsEventListener(this);

        this.thLinkOnClick      = this.doSort.bindAsEventListener(this);
        this.initGrid();
    },
    initGrid : function(){
        if(this.preInitCallback){
            this.preInitCallback(this);
        }
        if($(this.containerId+this.tableSufix)){
            this.rows = $$('#'+this.containerId+this.tableSufix+' tbody tr');
            for (var row=0; row<this.rows.length; row++) {
                if(row%2==0){
                    Element.addClassName(this.rows[row], 'even');
                }
                if(this.rows[row].tagName) {
                    Element.addClassName(this.rows[row], 'pointer');
                }

                Event.observe(this.rows[row],'mouseover',this.trOnMouseOver);
                Event.observe(this.rows[row],'mouseout',this.trOnMouseOut);
                Event.observe(this.rows[row],'click',this.trOnClick);
                Event.observe(this.rows[row],'dblclick',this.trOnDblClick);

                if(this.initRowCallback){
                    this.initRowCallback(this, this.rows[row]);
                }
            }
        }
        if(this.sortVar && this.dirVar){
            var columns = $$('#'+this.containerId+this.tableSufix+' thead a');

            for(var col=0; col<columns.length; col++){
                Event.observe(columns[col],'click',this.thLinkOnClick);
            }
        }
        this.bindFilterFields();
        if(this.initCallback){
            this.initCallback(this)
        }
    },
    getContainerId : function(){
        return this.containerId;
    },
    rowMouseOver : function(event){
        var element = Event.findElement(event, 'tr');
        Element.addClassName(element, 'on-mouse');
    },
    rowMouseOut : function(event){
        var element = Event.findElement(event, 'tr');
        Element.removeClassName(element, 'on-mouse');
    },
    rowMouseClick : function(event){
        if(this.rowClickCallback){
            try{
                this.rowClickCallback(this, event);
            }
            catch(e){}
        }
        varienGlobalEvents.fireEvent('gridRowClick', event);
    },
    rowMouseDblClick : function(event){
        varienGlobalEvents.fireEvent('gridRowDblClick', event);
    },
    keyPress : function(event){

    },
    doSort : function(event){
        var element = Event.findElement(event, 'a');

        if(element.name && element.target){
            this.addVarToUrl(this.sortVar, element.name);
            this.addVarToUrl(this.dirVar, element.target);
            this.reload(this.url);
        }
        Event.stop(event);
        return false;
    },
    loadByElement : function(element){
        if(element && element.name){
            this.reload(this.addVarToUrl(element.name, element.value));
        }
    },
    reload : function(url){
        url = url || this.url;
        if(this.useAjax){
            new Ajax.Updater(
                this.containerId,
                url+'?ajax=true',
                {
                    onComplete:this.initGrid.bind(this),
                    onFailure:this._processFailure.bind(this),
                    evalScripts:true,
                    parameters:this.reloadParams || {},
                    loaderArea: this.containerId
                }
            );
            return;
        }
        else{
            if(this.reloadParams){
                $H(this.reloadParams).each(function(pair){
                    url = this.addVarToUrl(pair.key, pair.value);
                }.bind(this));
            }
            location.href = url;
        }
    },
    _processFailure : function(transport){
        location.href = BASE_URL;
    },
    addVarToUrl : function(varName, varValue){
        var re = new RegExp('\/('+varName+'\/.*?\/)');
        this.url = this.url.replace(re, '/');
        this.url+= varName+'/'+varValue+'/';
        //this.url = this.url.replace(/([^:])\/{2,}/g, '$1/');
        return this.url;
    },
    doExport : function(){
        if($(this.containerId+'_export')){
            location.href = $(this.containerId+'_export').value;
        }
    },
    bindFilterFields : function(){
        var filters = $$('#'+this.containerId+' .filter input', '#'+this.containerId+' .filter select');
        for (var i in filters){
            Event.observe(filters[i],'keypress',this.filterKeyPress.bind(this));
        }
    },
    filterKeyPress : function(event){
        if(event.keyCode==Event.KEY_RETURN){
            this.doFilter();
        }
    },
    doFilter : function(){
        var filters = $$('#'+this.containerId+' .filter input', '#'+this.containerId+' .filter select');
        var elements = [];
        for(var i in filters){
            if(filters[i].value && filters[i].value.length) elements.push(filters[i]);
        }
        this.reload(this.addVarToUrl(this.filterVar, encode_base64(Form.serializeElements(elements))));
    },
    resetFilter : function(){
        this.reload(this.addVarToUrl(this.filterVar, ''));
    },
    checkCheckboxes : function(element){
        elements = Element.getElementsBySelector($(this.containerId), 'input[name="'+element.name+'"]');
        for(var i=0; i<elements.length;i++){
            this.setCheckboxChecked(elements[i], element.checked);
        }
    },
    setCheckboxChecked : function(element, checked){
        element.checked = checked;
        if(this.checkboxCheckCallback){
            this.checkboxCheckCallback(this,element,checked);
        }
    },
    inputPage : function(event, maxNum){
        var element = Event.element(event);
        var keyCode = event.keyCode || event.which;
        if(keyCode==Event.KEY_RETURN){
            this.setPage(element.value);
        }
        /*if(keyCode>47 && keyCode<58){

        }
        else{
             Event.stop(event);
        }*/
    },
    setPage : function(pageNumber){
        this.reload(this.addVarToUrl(this.pageVar, pageNumber));
    }
};

function openGridRow(grid, event){
    var element = Event.findElement(event, 'tr');
    var link = Event.findElement(event, 'a');
    if(link.href){
        return;
    }
    if(element.id){
        setLocation(element.id);
    }
}

var varienGridMassaction = Class.create();
varienGridMassaction.prototype = {
    container: null,
    grid: null,
    checkedValues: $H({}),
    checkedVisibleValues:  $H({}),
    oldCallbacks: {},
    items: {},
    currentItem: false,
    fieldTemplate: new Template('<input type="hidden" name="#{name}" value="#{value}" />'),
    initialize: function (containerId, grid, checkedValues, formFieldNameInternal, formFieldName) {
       this.setOldCallback('row_click', grid.rowClickCallback);
       this.setOldCallback('init',      grid.initCallback);
       this.setOldCallback('init_row',  grid.initRowCallback);
       this.setOldCallback('pre_init',  grid.preInitCallback);

       this.grid      = grid;
       this.container = $(containerId);
       this.containerId = containerId;
       this.form      = $(containerId + '-form');
       this.validator = new Validation(this.form);
       this.formHiddens    = $(containerId + '-form-hiddens');
       this.formAdditional = $(containerId + '-form-additional');
       this.select    = $(containerId + '-select');
       this.checkboxAll  = $(grid.containerId + '-checkbox-all');

       if(this.grid.rows.size() > 0) {
           this.checkboxAll.checked = true;
       }

       checkedValues.each(function(item){
           this.checkedValues[item] = item;
       }.bind(this));

       this.formFieldName = formFieldName;
       this.formFieldNameInternal = formFieldNameInternal;

       this.grid.initCallback = this.onGridInit.bind(this);
       this.grid.preInitCallback = this.onGridPreInit.bind(this);
       this.grid.initRowCallback = this.onGridRowInit.bind(this);
       this.grid.rowClickCallback = this.onGridRowClick.bind(this);
       this.grid.rows.each(function(row){
           this.initGridRow(row);
       }.bind(this));

       this.select.observe('change', this.onSelectChange.bindAsEventListener(this))
    },
    setItems: function(items) {
        this.items = items;
    },
    getItems: function() {
        return this.items;
    },
    getItem: function(itemId) {
        if(this.items[itemId]) {
            return this.items[itemId];
        }
        return false;
    },
    getOldCallback: function (callbackName) {
        return this.oldCallbacks[callbackName] ? this.oldCallbacks[callbackName] : Prototype.emptyFunction;
    },
    setOldCallback: function (callbackName, callback) {
        this.oldCallbacks[callbackName] = callback;
    },
    onGridPreInit: function(grid) {
        this.checkedVisibleValues = $H({});
        if(this.grid.rows.size() > 0) {
               this.checkboxAll.checked = true;
        }
        this.getOldCallback('pre_init')(grid);
    },
    onGridInit: function(grid) {

        this.getOldCallback('init')(grid);
    },
    onGridRowInit: function(grid, row) {
        this.initGridRow(row);
        this.getOldCallback('init_row')(grid, row);
    },
    onGridRowClick: function(grid, evt) {
        if(Event.element(evt).isMassactionCheckbox) {
           this.setCheckbox(Event.element(evt));
        } else if (checkbox = this.findCheckbox(evt)) {
           checkbox.checked = !checkbox.checked;
           this.setCheckbox(checkbox);
        } else {
            this.getOldCallback('row_click')(grid, evt);
        }
    },
    onSelectChange: function(evt) {
        var item = this.getSelectedItem();
        if(item) {
            this.formAdditional.update($(this.containerId + '-item-' + item.id + '-block').innerHTML);
        } else {
            this.formAdditional.update('');
        }
    },
    findCheckbox: function(evt) {
        checkbox = false;
        Event.element(evt).childElements().each(function(element){
            if(element.isMassactionCheckbox) {
                checkbox = element;
            }
        }.bind(this));
        return checkbox;
    },
    initGridRow: function(row) {
        var checkboxes = row.getElementsByClassName('massaction-checkbox');
        checkboxes.each(function(checkbox) {
           checkbox.isMassactionCheckbox = true;
           if(this.checkedValues.keys().indexOf(checkbox.value)!==-1) {
               checkbox.checked = true;
               this.setCheckbox(checkbox);
           } else {
               this.checkboxAll.checked = false;
           }
        }.bind(this));

        if(checkboxes.size() == 0) {
            this.checkboxAll.checked = false;
        }
    },
    checkCheckboxes: function(source) {
        this.grid.rows.each(function(row){
            var checkboxes = row.getElementsByClassName('massaction-checkbox');
            checkboxes.each(function(checkbox) {
               checkbox.checked = source.checked;
               this.setCheckbox(checkbox);
            }.bind(this));
        }.bind(this));
    },
    setCheckbox: function(checkbox) {
        if(checkbox.checked) {
            this.checkedValues[checkbox.value] = checkbox.value;
            this.checkedVisibleValues[checkbox.value] = this.checkedValues[checkbox.value];
        } else {
            this.checkedValues.remove(checkbox.value);
            this.checkedVisibleValues.remove(checkbox.value);
        }

        if(!this.grid.reloadParams) {
            this.grid.reloadParams = {};
        }

        this.grid.reloadParams[this.formFieldNameInternal] = this.checkedValues.keys().join(',');
    },
    getSelectedItem: function() {
        if(this.getItem(this.select.value)) {
            return this.getItem(this.select.value);
        } else {
            return false;
        }
    },
    apply: function() {
        var item = this.getSelectedItem();
        if(!item) {
            return;
        }
        this.currentItem = item;
        var fieldName = (item.field ? item.field : this.formFieldName) + '[]';
        var fieldsHtml = '';
        this.checkedVisibleValues.keys().each(function(item){
            fieldsHtml += this.fieldTemplate.evaluate({name: fieldName, value: item});
        }.bind(this));

        this.formHiddens.update(fieldsHtml);

        if(!this.validator.validate()) {
            return;
        }

        if(this.grid.useAjax && item.url) {
            return alert(this.form.serialize(false));
            new Ajax.Request(item.url, {
                'method': 'post',
                'parameters': this.form.serialize(true),
                'onComplete': this.onMassactionComplete.bind(this)
            });
        } else if(item.url) {
            this.form.action = item.url;
            this.form.submit();
        }
    },
    onMassactionComplete: function(transport) {
           if(this.currentItem.complete) {
               try {
                  var listener = this.getListener(this.currentItem.complete) || Prototype.emptyFunction();
                  listener(grid, this, transport);
               } catch (e) {}
           }
    },
    getListener: function(strValue) {
        return eval(strValue);
    }
}