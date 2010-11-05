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

    cachedHtml:       {},
    listTypes:        {},
    confirmCallback:  {},
    cancelCallback:   {},
    current:          {},
    confWindow:       {},
    confFields:       {},

    /**
     * Initialize object
     */
    initialize: function() {
        this.cachedHtml.base = {};
        this.cachedHtml.confirmed = {};
        this.confWindow = $('catalog_product_composite_configure');
        this.confFields = $('catalog_product_composite_configure_fields');
    },

    /**
     * Add product list types as scope and their urls for fetching configuration fields through ajax 
     * expamle: addListType('product_to_add', 'http://magento...')
     * expamle: addListType('wishlist', 'http://magento...')
     *
     * @param type types as scope
     * @param url for fetching configuration fields through ajax
     */
    addListType: function(type, url) {
        this.listTypes[type] = url;
    },

    /**
     * Show configuration fields of product, if it not found then get it through ajax
     *
     * @param listType type of list as scope
     * @param itemId product id
     */
    showItemConfiguration: function(listType, itemId) {
        this.current.listType = listType;
        this.current.itemId = itemId;
        if (typeof this.cachedHtml.base[listType] == 'undefined') {
            this.cachedHtml.base[listType] = {};
            this.cachedHtml.confirmed[listType] = {};
        }
        if (typeof this.cachedHtml.base[listType][itemId] == 'undefined') {
            this._requestItemConfiguration(listType, itemId);
        } else {
            if (typeof this.cachedHtml.confirmed[listType][itemId] != 'undefined') {
                this.confFields.update(this.cachedHtml.confirmed[listType][itemId]);
            } else {
                this.confFields.update(this.cachedHtml.base[listType][itemId]);
            }
            this.confWindow.style.display = 'block';
        }
    },

    /**
     * Get configuration fields of product through ajax put to cache and show them
     *
     * @param listType type of list as scope
     * @param productId product id
     */
    _requestItemConfiguration: function(listType, productId) {
        var url = this.listTypes[listType];
        if (url) {
            new Ajax.Request(url, {
                parameters: {productId: productId},
                onSuccess: function(transport) {
                    var response = transport.responseText;
                    if (response) {
                        this.cachedHtml.base[listType][productId] = response;
                        this.showItemConfiguration(listType, productId);
                    }
                }.bind(this)
            });
        }
    },

    /**
     * Triggered on confirm button click
     */
    onConfirmBtn: function() {
        this.cachedHtml.confirmed[this.current.listType][this.current.itemId] = this.cachedHtml.base[this.current.listType][this.current.itemId];
        this.confWindow.style.display = 'none';
        if (this.confirmCallback) {
            this.confirmCallback();
        }
    },

    /**
     * Triggered on cancel button click
     */
    onCancelBtn: function() {
        this.confWindow.style.display = 'none';
        if (this.cancelCallback) {
            this.cancelCallback();
        }
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
    }
};

document.observe("dom:loaded", function() {
    productConfigure = new ProductConfigure();
});
