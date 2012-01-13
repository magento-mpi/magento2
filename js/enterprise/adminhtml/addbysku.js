/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @category    design
 * @package     default_default
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Add By SKU class
 *
 * @method submitConfigured()
 * @method removeAllFailed()
 */
var AddBySku = Class.create();
AddBySku.prototype = {
    /**
     * Constructor
     *
     * @param order            Instance of AdminOrder
     * @param productConfigure Instance of ProductConfigure
     * @param data             Array (see initialize())
     */
    initialize : function (order, productConfigure, data)
    {
        if (!data) data = {};
        this.lastId = 0;
        this.configuredIds = [];
        this.dataContainerId = data.dataContainerId;
        this.deleteButtonHtml = data.deleteButtonHtml;
        this.order = order;
        this.productConfigure = productConfigure;
        this.listType = data.listType;
        this.errorGridId = data.errorGridId;
        this.fileFieldName = data.fileFieldName;
        this.fileUploadUrl = data.fileUploadUrl;
        this.skuFieldName = data.skuFieldName;

        var that = this;
        var adminCheckout = {
            controllerParamFieldNames : {'customerId': 'customer', 'storeId': 'store'},

            initAreas: function(){
            },

            submitConfigured : function ()
            {
                // Save original source grids configuration to be restored later
                var oldSourceGrids = that.order.sourceGrids;
                // Leave only error grid (don't submit information from other grids right now)
                that.order.sourceGrids = {'sku_errors': this.errorSourceGrid};
                // Save old response handler function to override it
                var parentResponseHandler = that.order.loadAreaResponseHandler;
                that.order.loadAreaResponseHandler = function (response)
                {
                    if (!response['errors']) {
                        // If response is empty loadAreaResponseHandler() won't update the area
                        response['errors'] = '<span></span>';
                    }
                    parentResponseHandler.call(that.order, response);
                };
                that.order.productGridAddSelected('sku');
                that.order.sourceGrids = oldSourceGrids;
            },

            removeAllFailed : function ()
            {
                var oldLoadingAreas = that.order.loadingAreas;
                // We need to override this field, otherwise layout is going to be broken
                that.order.loadingAreas = 'errors';
                var url = that.order.loadBaseUrl + 'block/' + that.listType;
                new Ajax.Request(url, {
                    parameters: that.order.prepareParams({'json': true, 'sku_remove_failed': '1'}),
                    loaderArea: 'html-body',
                    onSuccess: function(transport)
                    {
                        var response = transport.responseText.evalJSON();
                        if (!response.errors) {
                            // If response is empty loadAreaResponseHandler() won't update the area
                            response.errors = '<span></span>';
                        }
                        this.loadAreaResponseHandler(response);
                    }.bind(that.order),
                    onComplete: function ()
                    {
                        this.loadingAreas = oldLoadingAreas;
                    }.bind(that.order)
                })
            }
        };

        var adminOrder = {
            controllerParamFieldNames : {'customerId': 'customerId', 'storeId': 'storeId'},

            initAreas: function(){
                setTimeout(function(){
                var skuAreaId = order.getAreaId('additional_area'),
                    skuButton = new ControlButton(Translator.translate('Add Products By SKU'));
                skuButton.onClick = function(){
                    $(skuAreaId).show();
                    this.remove();
                };
                order.dataArea.onLoad = order.dataArea.onLoad.wrap( function (proceed) {
                    proceed();
                    this._parent.itemsArea.setNode($(this._parent.getAreaId('items')));
                    this._parent.itemsArea.onLoad();
                });
                order.itemsArea.onLoad = order.itemsArea.onLoad.wrap( function (proceed) {
                    proceed();
                    if (!$(skuAreaId).visible()) {
                        this.addControlButton(skuButton);
                    }
                });
                order.dataArea.onLoad();
                }, 10);
            },

            submitConfigured : function ()
            {
                var area = ['errors', 'search', 'items', 'shipping_method', 'totals', 'giftmessage','billing_method'];
                var table = $('sku_errors_table');
                var elements = table.select('input[type=checkbox][name=sku_errors]:checked');
                var fieldsPrepare = {};
                fieldsPrepare['from_error_grid'] = '1';
                elements.each(function (elem) {
                    function _addFields(fieldNames, parent, id) {
                        if (typeof fieldNames == 'string') {
                            fieldNames = [fieldNames];
                        }
                        for (var i = 0; i < fieldNames.length; i++) {
                            var elem = parent.select('input[name=' + fieldNames[i] + ']');
                            var paramKey = 'add_by_sku[' + id + '][' + fieldNames[i] + ']';
                            if (elem.length) {
                                fieldsPrepare[paramKey] = elem[0].value;
                            }
                        }
                    }

                    if (!elem.value || (elem.value == 'on')) {
                        return;
                    }
                    var tr = elem.up('tr');
                    if (tr) {
                        _addFields(['qty', that.skuFieldName], tr, elem.value);
                    }
                });
                that.order.productConfigureSubmit('errors', area, fieldsPrepare, that.configuredIds);
                that.configuredIds = [];
            },

            removeAllFailed : function ()
            {
                that.order.loadArea('errors', true, {'sku_remove_failed': '1'});
            }
        };

        // Strategy
        var provider = this.order instanceof (window.AdminOrder || function(){}) ? adminOrder : adminCheckout;
        provider.initAreas();
        this.submitConfigured = provider.submitConfigured;
        this.removeAllFailed = provider.removeAllFailed;
        this.controllerParamFieldNames = provider.controllerParamFieldNames;

        /**
         * Observe quantity input in error grid and compare with max-allowed value
         */
        document.observe('keyup', function (event)
        {
            var $errorTable = event.findElement('#sku_errors_table');
            if ($errorTable) {
                $errorTable.select('input[name="qty"]').each(function ($qty)
                {
                    var tr = $qty.up('tr');
                    var sku = tr.select('input[name="' + that.skuFieldName + '"]')[0].value;
                    var $maxAllowed = $(sku + '_max_allowed');
                    if ($maxAllowed && (parseInt($qty.value) <= parseInt($maxAllowed.innerHTML))) {
                        tr.removeClassName('qty-not-available');
                    } else if ($maxAllowed) {
                        tr.addClassName('qty-not-available');
                    }
                });
            }
        });
    },

    /**
     * Delete element from queue
     *
     * @param obj Element to remove
     */
    del : function(obj)
    {
        var tr = obj.up('tr');
        if( $('id_' + tr.id) ) {
            var itemId = $('id_' + tr.id).value;
            var newElement = document.createElement('input');
            newElement.type = 'hidden';
            newElement.value = itemId;
            newElement.name = 'deleteSku[]';
            $(this.dataContainerId).appendChild(newElement);
        }
        tr.remove();
    },

    /**
     * Remove row from error grid and update counter of products requiring attention
     *
     * @param obj Table row to be removed
     */
    errorDel : function (obj)
    {
        this.del(obj);
        $('sku-attention-num').innerHTML--;
    },

    /**
     * Add new input for SKU and Qty
     */
    add : function()
    {
        var newElement = document.createElement('tr');
        newElement.innerHTML = this.getTemplate();
        $(this.dataContainerId).appendChild(newElement);
    },

    /**
     * HTML to be inserted upon add()
     */
    getTemplate : function()
    {
        var id = ++this.lastId;
        return '<td class="value"><input id="sku_' + id + '" type="text" name="' + this.skuFieldName + '" value="" class="input-text"></td>'
               + '<td class="value"><input id="sku_qty_' + id  +'" type="text" name="qty" value="1" class="input-text"></td>'
               + '<td>' + this.deleteButtonHtml + '</td>';
    },

    /**
     * Submit selected CSV file (if any)
     */
    submitCsvFile : function ()
    {
        var $file = Element.select('body', 'input[name="' + this.fileFieldName + '"]')[0];
        var $inputFileContainer = $file.up();
        if (!$file.value) {
            return false;
        }
        // Hide 'add by SKU' area on order creation page (not available on manage shopping cart page)
        this.order.hideArea && this.order.hideArea('additional_area');
        var $form = new Element('form', {
            'action': this.fileUploadUrl,
            'method': 'post',
            'enctype': 'multipart/form-data'
        });
        // We need to insert same file input element into the form. Simple copy of name/value doesn't work.
        $form.insert($file);
        // Inserting element to other place removes it from the old one. Creating new file input element on same place
        // to avoid confusing effect that it has disappeared.
        $inputFileContainer.insert(new Element('input', {'type': 'file', 'name': this.fileFieldName}));
        $form.insert(new Element('input', {'type': 'hidden', 'name': this.controllerParamFieldNames['customerId'], 'value': this.order.customerId}));
        $form.insert(new Element('input', {'type': 'hidden', 'name': this.controllerParamFieldNames['storeId'], 'value': this.order.storeId}));
        $form.insert(new Element('input', {'type': 'hidden', 'name': 'form_key', 'value': FORM_KEY}));
        // For IE we must make the form part of the DOM, otherwise browser refuses to submit it
        Element.select(document, 'body')[0].insert($form);
        $form.submit();
        // Show loader
        varienLoaderHandler.handler.onCreate({options: {loaderArea: true}});
        return true;
    },

    /**
     * Submit input to be added to order
     */
    submitAddForm : function ()
    {
        this.order.additionalAreaButton && Element.show(this.order.additionalAreaButton);
        if (this.submitCsvFile()) {
            return;
        }
        // No file selected for upload: submit other inputs
        var areas = ['errors', 'additional_area', 'search', 'items', 'shipping_method', 'totals', 'giftmessage', 'billing_method'];
        var table = $(this.skuFieldName + '_table');
        var elements = table.select('input[type=text]');
        var fieldsPrepare = {};
        var sku = '';
        var that = this;
        elements.each(function (elem) {
            if (!elem.value) {
                return;
            }
            var qty = 0;
            if (elem.name == that.skuFieldName) {
                sku = elem.value;
            } else {
                qty = elem.value;
            }
            var paramKey = 'add_by_sku[' + sku + '][qty]';
            fieldsPrepare[paramKey] = qty;
        });
        if (fieldsPrepare != {}) {
            this.order.hideArea('additional_area');
            this.order.loadArea(areas, true, fieldsPrepare);
        }
    },

    /**
     * Configure a product
     *
     * @param id Product ID
     */
    configure : function (id)
    {
        var that = this;
        var descrElem = $('id_' + id);
        // Don't process configured element by addBySku() observer method (it won't be serialized by serialize())
        this.productConfigure.setConfirmCallback(this.listType, function ()
        {
            // It is vital to push string element, check this line in configure.js:
            // this.itemsFilter[listType].indexOf(itemId) != -1
            that.configuredIds.push(String(id));
            var $notice = Element.select(descrElem, '.notice');
            if ($notice.length) {
                // Remove message saying product requires configuration
                $notice[0].remove();
            }
            var $qty = that.productConfigure.getCurrentConfirmedQtyElement();
            if ($qty) { // Grouped products do not have this
                // Synchronize qtys between configure window and grid
                Element.select(descrElem.up('tr'), 'input[name="qty"]')[0].value = $qty.value;
            }
        });
        this.productConfigure.showItemConfiguration(this.listType, id);
    },

    /**
     * Intercept click on "Add to cart" button and submit CSV file (if it was selected) instead of executing original
     * action
     */
    observeAddToCart : function ()
    {
        this.addToCartButtonEvents = [];
        var that = this;
        $('products_search').select('button.button-to-cart').each(function (button)
        {
            // Save original event
            that.addToCartButtonEvents[button.id] = button.onclick;
            // Submit CSV file or perform an original event
            button.onclick = function ()
            {
                that.submitCsvFile() || that.addToCartButtonEvents[this.id]();
                that.clearAddForm();
            }
        });
    },

    /**
     * Return add form to untouched state
     */
    clearAddForm : function ()
    {
        var $rows = $(this.dataContainerId).select('tr');
        var rowNum = $rows.length;
        for (var i = 1; i < rowNum; i++) {
            // Remove all rows except the first
            $rows[i].remove();
        }
        // First row input fields: set empty SKU and qty=1
        $rows[0].select('input[name="' + this.skuFieldName + '"]')[0].value = '';
        $rows[0].select('input[name="qty"]')[0].value = '1';
    },

    /**
     * Add parameters for error source grid (see adminCheckout.submitConfigured() described in constructor)
     *
     * @param params
     */
    addErrorSourceGrid : function (params)
    {
        this.errorSourceGrid = params;
    }
};
