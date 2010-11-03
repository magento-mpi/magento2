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
    cachedHtml: {},
    listTypes : {},

    initialize: function() {

    },

    addListType: function(type, url) {
        this.listTypes[type] = url;
    },

    showItemConfiguration: function(listType, itemId) {
        if (typeof this.cachedHtml[listType] == 'undefined') {
            this.cachedHtml[listType] = {};
        }
        if (typeof this.cachedHtml[listType][itemId] == 'undefined') {
            this._requestItemConfiguration(listType, itemId);
        } else {
            $('catalog_product_composite_configure_fields').update(this.cachedHtml[listType][itemId]);
            $('catalog_product_composite_configure').style.display = 'block';
        }
    },

    _requestItemConfiguration: function(listType, productId) {
        var url = this.listTypes[listType];
        new Ajax.Request(url, {
            parameters: {productId: productId},
            onSuccess: function(transport) {
                var responce = transport.responseText;
                if (responce) {
                    this.cachedHtml[listType][productId] = responce;
                    this.showItemConfiguration(listType, productId);
                }

            }.bind(this)
        });
    },

    onCancelBtn: function() {
        $('catalog_product_composite_configure').style.display = 'none';
    },

    onOkBtn: function() {
        
    }
};

productConfigure = new ProductConfigure();
