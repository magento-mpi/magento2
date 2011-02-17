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
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

ProductConfigure = Class.create();
ProductConfigure.prototype = {

    cachedHtml:       $H({}),
    configuredData:   $H({}),
    listTypes:        $H({}),
    current:          $H({}),
    confirmCallback:  null,
    cancelCallback:   null,
    confWindow:       null,
    confFields:       null,
    confMask:         null,
    windowHeight:     null,
    errorMsgBlock:    null,

    /**
     * Initialize object
     */
    initialize: function() {
        this.confWindow     = $('catalog_product_composite_configure');
        this.confFields     = $('catalog_product_composite_configure_fields');
        this.errorMsgBlock  = $$('#catalog_product_composite_configure .error-msg')[0];
        this.confMask       = $('popup-window-mask');
        this.windowHeight   = $('html-body').getHeight();
    },

    /**
     * Add product list types as scope and their urls for fetching configuration fields through ajax 
     * expamle: addListType('product_to_add', 'http://magento...')
     * expamle: addListType('wishlist', 'http://magento...')
     *
     * @param type types as scope
     * @param urlFetch for fetching configuration fields through ajax
     * @param urlConfirm for preprocessing configured data through ajax
     */
    addListType: function(type, urlFetch, urlConfirm) {
        this.listTypes[type] = {urlFetch: urlFetch, urlConfirm: urlConfirm};
    },

    /**
     * Show configuration fields of product, if it not found then get it through ajax
     *
     * @param listType type of list as scope
     * @param itemId product id
     */
    showItemConfiguration: function(listType, itemId) {
        if (!listType || !itemId) {
            return false;
        }
        this.current.listType = listType;
        this.current.itemId = itemId;
        this._prepareCachedAndConfigured(listType, itemId);

        if (this._empty(this.cachedHtml[listType][itemId])) {
            this._requestItemConfiguration(listType, itemId);
        } else {
            this.confFields.update(this.cachedHtml[listType][itemId]);
            if (!this._empty(this.configuredData[listType][itemId])) {
                this._processFieldsData('restore');
            }
            this._showWindow();
        }
    },

    /**
     * Get configuration fields of product through ajax put to cache and show them
     *
     * @param listType type of list as scope
     * @param itemId product id
     */
    _requestItemConfiguration: function(listType, itemId) {
        var url = this.listTypes[listType].urlFetch;
        if (url) {
            new Ajax.Request(url, {
                parameters: {productId: itemId},
                onSuccess: function(transport) {
                    var response = transport.responseText;
                    if (response) {
                        this.cachedHtml[listType][itemId] = response;
                        this.showItemConfiguration(listType, itemId);
                    }
                }.bind(this)
            });
        }
    },

    /**
     * Triggered on confirm button click
     * Do preprocessing configured data through ajax if needed
     */
    onConfirmBtn: function() {
        this._processFieldsData('save');
        var url = this.listTypes[this.current.listType].urlConfirm;
        if (url) {
            new Ajax.Request(url, {
                parameters: this.configuredData[this.current.listType][this.current.itemId],
                onSuccess: function(transport) {
                    if (transport.responseText.isJSON()) {
                        var response = transport.responseText.evalJSON();
                        if (response.ok) {
                            this.errorMsgBlock.hide();
                            this._closeWindow();
                            if (Object.isFunction(this.confirmCallback)) {
                                this.confirmCallback();
                            }
                        } else if (response.error) {
                            delete this.cachedHtml[this.current.listType][this.current.itemId];
                            this.showItemConfiguration(this.current.listType, this.current.itemId);
                            this._processFieldsData('restore');
                            this.errorMsgBlock.show();
                            this.errorMsgBlock.innerHTML = response.messages;
                        }
                    }
                }.bind(this)
            });
        } else {
            this._closeWindow();
            if (Object.isFunction(this.confirmCallback)) {
                this.confirmCallback();
            }
        }
    },

    /**
     * Triggered on cancel button click
     */
    onCancelBtn: function() {
        this._closeWindow();
        if (Object.isFunction(this.cancelCallback)) {
            this.cancelCallback();
        }
    },

    /**
     * Show configuration window
     */
    _showWindow: function() {
        toggleSelectsUnderBlock(this.confMask, false);
        this.confMask.setStyle({'height':this.windowHeight+'px'}).show();
        this.confWindow.setStyle({'marginTop':-this.confWindow.getHeight()/2 + "px", 'display':'block'});
    },

    /**
     * Close configuration window
     */
    _closeWindow: function() {
        toggleSelectsUnderBlock(this.confMask, true);
        this.errorMsgBlock.hide();
        this.confMask.style.display = 'none';
        this.confWindow.style.display = 'none';
    },


    /**
     * Attach callback function triggered on confirm button click
     *
     * @param confirmCallback
     */
    setConfirmCallback: function(confirmCallback) {
        this.confirmCallback = confirmCallback;
    },

    /**
     * Attach callback function triggered on cancel button click
     *
     * @param cancelCallback
     */
    setCencelCallback: function(cancelCallback) {
        this.cancelCallback = cancelCallback;
    },

    /**
     * Get configured data. All or filtered
     *
     * @param listType type of list as scope. e.g.: '$current$', 'product_to_add', empty
     * @param itemId product id. e.g.: '$current$', 195, empty
     */
    getConfiguredData: function(listType, itemId) {
        // get current listType
        if (listType == '$current$'
            && typeof this.current.listType != 'undefined'
            && typeof itemId == 'undefined'
            && typeof this.configuredData[this.current.listType] != 'undefined'        
        ) {
            return this.configuredData[this.current.listType];
        // get current listType and current itemId
        } else if (listType == '$current$' && itemId  == '$current$'
            && typeof this.current.listType != 'undefined'
            && typeof this.current.itemId != 'undefined'
            && typeof this.configuredData[this.current.listType] != 'undefined'
            && typeof this.configuredData[this.current.listType][this.current.itemId] != 'undefined'
        ) {
            return this.configuredData[this.current.listType][this.current.itemId];
        // get current listType and specified itemId
        } else if (listType == '$current$' && itemId
            && typeof this.current.listType != 'undefined'
            && typeof this.configuredData[this.current.listType] != 'undefined'
            && typeof this.configuredData[this.current.listType][itemId] != 'undefined'
        ) {
            return this.configuredData[this.current.listType][itemId];
        // get by listType
        } else if (listType
            && typeof itemId != 'undefined'
            && typeof this.configuredData[listType] != 'undefined'
        ) {
            return this.configuredData[listType];
        // get by listType and itemId
        } else if (listType && itemId
            && typeof this.configuredData[listType] != 'undefined'
            && typeof this.configuredData[listType][itemId] != 'undefined'
        ) {
            return this.configuredData[listType][itemId];
        }
        return this.configuredData;
    },

    /**
     * Clean object data
     */
    clean: function() {
        this.current        = $H({});
        this.cachedHtml     = $H({});
        this.configuredData = $H({});
    },

    /**
     * Save or restore current fields data
     *
     * @param method can be 'save' or 'restore'
     */
    _processFieldsData: function(method) {
        var fields          = this.confFields.select('input,textarea,select');
        var fieldsConfirmed = this.configuredData[this.current.listType][this.current.itemId];

        switch (method) {
            case 'save':
                fields.each(function(e) {
                    if (e.name) {
                        if (e.type == 'checkbox') {
                            fieldsConfirmed[e.name] = e.checked;
                        } else {
                            fieldsConfirmed[e.name] = e.value;
                        }
                    }
                }.bind(this));
            break;
            case 'restore': 
                fields.each(function(e) {
                    if (typeof fieldsConfirmed[e.name] != 'undefined') {
                        if (e.type == 'checkbox') {
                            e.checked = fieldsConfirmed[e.name];
                        } else {
                            e.value = fieldsConfirmed[e.name];
                        }

                    }
                }.bind(this));
            break;
        }
    },

    /**
     * Prepare cached and configured variables for filling
     *
     * @param listType type of list as scope
     * @param itemId product id
     */
    _prepareCachedAndConfigured: function(listType, itemId) {
        if (typeof this.cachedHtml[listType] == 'undefined') {
            this.cachedHtml[listType] = {};
        }
        if (typeof this.cachedHtml[listType][itemId] == 'undefined') {
            this.cachedHtml[listType][itemId] = null;
        }
        if (typeof this.configuredData[listType] == 'undefined') {
            this.configuredData[listType] = {};
        }
        if (typeof this.configuredData[listType][itemId] == 'undefined') {
            this.configuredData[listType][itemId] = {};
        }
    },

    /**
     * Is object has any property ?
     *
     * @param obj
     */
    _empty: function(obj) {
        for(var i in obj) {
            if(obj.hasOwnProperty(i)) {
                return false;
            }
        }
        return true;
    }
};

document.observe("dom:loaded", function() {
    productConfigure = new ProductConfigure();
});
