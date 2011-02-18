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

    listTypes:                  $H({}),
    current:                    $H({}),
    submitResponce:             $H({}),
    blockWindow:                null,
    blockForm:                  null,
    blockFormFields:            null,
    blockFormAdd:               null,
    blockFormConfirmed:         null,
    blockConfirmed:             null,
    blockIFrame:                null,
    blockMask:                  null,
    blockErrorMsg:              null,
    windowHeight:               null,
    сonfirmedCurrentId:         null,
    confirmCallback:            null,
    cancelCallback:             null,
    onLoadIFrameCallback:       null,
    iFrameJSVarname:            null,

    /**
     * Initialize object
     */
    initialize: function() {
        this._initWindowElements();
    },

    /**
     * Initialize window elements
     */
    _initWindowElements: function() {
        this.blockWindow                = $('product_composite_configure');
        this.blockForm                  = $('product_composite_configure_form');
        this.blockFormFields            = $('product_composite_configure_form_fields');
        this.blockFormAdd               = $('product_composite_configure_form_additional');
        this.blockFormConfirmed         = $('product_composite_configure_form_confirmed');
        this.blockConfirmed             = $('product_composite_configure_confirmed');
        this.blockIFrame                = $('product_composite_configure_iframe');
        this.blockMask                  = $('popup-window-mask');
        this.blockErrorMsg              = $$('#product_composite_configure .error-msg')[0];
        this.windowHeight               = $('html-body').getHeight();
        this.iFrameJSVarname            = this.blockForm.select('input[name="as_js_varname"]')[0].value;
    },

    /**
     * Add product list types as scope and their urls 
     * expamle: addListType('product_to_add', {urlFetch: 'http://magento...'})
     * expamle: addListType('wishlist', {urlSubmit: 'http://magento...'})
     *
     * @param type types as scope
     * @param urls obj can be
     *             - {urlFetch: 'http://magento...'} for fetching configuration fields through ajax
     *             - {urlConfirm: 'http://magento...'} for submit configured data through iFrame when clicked confirm button
     *             - {urlSubmit: 'http://magento...'} for submit configured data through iFrame
     */
    addListType: function(type, urls) {
        if ('undefined' == typeof this.listTypes[type]) {
            this.listTypes[type] = {};
        }
        Object.extend(this.listTypes[type], urls);
        return this;
    },

    /**
     * Show configuration fields of item, if it not found then get it through ajax
     *
     * @param listType type of list as scope
     * @param itemId 
     */
    showItemConfiguration: function(listType, itemId) {
        if (!listType || !itemId) {
            return false;
        }
        this._initWindowElements();
        this.current.listType = listType;
        this.current.itemId = itemId;
        this.сonfirmedCurrentId = this.blockConfirmed.id+'['+listType+']['+itemId+']';

        if (!$(this.сonfirmedCurrentId) || !$(this.сonfirmedCurrentId).innerHTML) {
            this._requestItemConfiguration(listType, itemId);
        } else {
            this._processFieldsData('item_restore');
            this._showWindow();
        }
    },

    /**
     * Get configuration fields of product through ajax and show them
     *
     * @param listType type of list as scope
     * @param itemId
     */
    _requestItemConfiguration: function(listType, itemId) {
        if (!this.listTypes[this.current.listType].urlFetch) {
            return false;
        }
        var url = this.listTypes[listType].urlFetch;
        if (url) {
            new Ajax.Request(url, {
                parameters: {id: itemId},
                onSuccess: function(transport) {
                    var response = transport.responseText;
                    if (response) {
                        this.blockFormFields.update(response);
                        this._showWindow();
                    }
                }.bind(this)
            });
        }
    },

    /**
     * Triggered on confirm button click
     * Do submit configured data through iFrame if needed
     */
    onConfirmBtn: function() {
        if (productCompositeConfigureForm.validate()) {
            this._processFieldsData('item_confirm');
            var url = this.listTypes[this.current.listType].urlConfirm;
            if (url) {
                this.submit(url);
            } else {
                this._closeWindow();
                if (Object.isFunction(this.confirmCallback)) {
                    this.confirmCallback();
                }
            }
        }
        return this;
    },

    /**
     * Triggered on cancel button click
     */
    onCancelBtn: function() {
        this._closeWindow();
        if (Object.isFunction(this.cancelCallback)) {
            this.cancelCallback();
        }
        return this;
    },

    /**
     * Submit configured data through iFrame
     *
     * @param url
     */
    submit: function(url) {
        // prepare data
        if (!url && !this.listTypes[this.current.listType].urlSubmit) {
            return false;
        }
        url = url ? url : this.listTypes[this.current.listType].urlSubmit;
        this._processFieldsData('all_confirmed_to_form');
        // do submit
        this.blockIFrame.setAttribute('onload', 'productConfigure.onLoadIFrame()');
        this.blockForm.action = url;
        this.blockForm.submit();
        varienLoaderHandler.handler.onCreate({options: {loaderArea: true}});
        return this;
    },

    /**
     * Add dynamically additional fields for form
     *
     * @param fields
     */
    addFields: function(fields) {
        fields.each(function(elm) {
            this.blockFormAdd.insert(elm);
        }.bind(this));
        return this;
    },

    /**
     * Triggered when iFrame was loaded. Get response from iFrame and handle it
     */
    onLoadIFrame: function() {
        varienLoaderHandler.handler.onComplete();
        this._processFieldsData('all_form_to_confirmed');
        var response = this.blockIFrame.contentWindow[this.iFrameJSVarname];
        if (response && "object" == typeof response) {
            if (this.listTypes[this.current.listType].urlConfirm) {
                if (response.ok) {
                    this.blockErrorMsg.hide();
                    this._closeWindow();
                    this.clean('current');
                } else if (response.error) {
                    this.showItemConfiguration(this.current.listType, this.current.itemId);
                    this.blockErrorMsg.show();
                    this.blockErrorMsg.innerHTML = response.message;
                }
            }
            if (Object.isFunction(this.onLoadIFrameCallback)) {
                this.onLoadIFrameCallback(response);
            }
        } else {
            this.clean('current');
        }
    },

    /**
     * Helper for fetching content from iFrame
     */
    _getIFrameContent: function() {
        var content = (this.blockIFrame.contentWindow || this.blockIFrame.contentDocument);
        if (content.document) {
            content=content.document;
        }
        return content;
    },

    /**
     * Show configuration window
     */
    _showWindow: function() {
        toggleSelectsUnderBlock(this.blockMask, false);
        this.blockMask.setStyle({'height':this.windowHeight+'px'}).show();
        this.blockWindow.setStyle({'marginTop':-this.blockWindow.getHeight()/2 + "px", 'display':'block'});
    },

    /**
     * Close configuration window
     */
    _closeWindow: function() {
        toggleSelectsUnderBlock(this.blockMask, true);
        this.blockErrorMsg.hide();
        this.blockMask.style.display = 'none';
        this.blockWindow.style.display = 'none';
    },

    /**
     * Attach callback function triggered when confirm button was clicked 
     *
     * @param confirmCallback
     */
    setConfirmCallback: function(confirmCallback) {
        this.confirmCallback = confirmCallback;
        return this;
    },

    /**
     * Attach callback function triggered when cancel button was clicked
     *
     * @param cancelCallback
     */
    setCencelCallback: function(cancelCallback) {
        this.cancelCallback = cancelCallback;
        return this;
    },

    /**
     * Attach callback function triggered when iFrame was loaded
     *
     * @param onLoadIFrameCallback
     */
    setOnLoadIFrameCallback: function(onLoadIFrameCallback) {
        this.onLoadIFrameCallback = onLoadIFrameCallback;
        return this;
    },

    /**
     * Clean object data
     *
     * @param method can be 'all' or 'current'
     */
    clean: function(method) {
        switch (method) {
            case 'all':
                    this.current = $H({});
                    this.blockConfirmed.update();
            break;
            case 'current':
                    var pattern = new RegExp(this.blockConfirmed.id+'\\['+this.current.listType+'\\]');
                    this.blockConfirmed.childElements().each(function(elm) {
                        if (elm.id.match(pattern)) {
                            elm.remove();
                        }
                    }.bind(this));
            break;
            default:
                    return false;
            break;
        }
        this._getIFrameContent().body.innerHTML = '';
        this.blockFormAdd.update();
        this.blockIFrame.removeAttribute('onload');
        this.blockFormConfirmed.update();
        this.blockForm.action = '';

        return this;
    },

    /**
     * Process fields data: save, restore, move saved to form and back
     *
     * @param method can be 'item_confirm', 'item_restore', 'all_confirmed_to_form', 'all_form_to_confirmed'
     */
    _processFieldsData: function(method) {

        /**
         * Internal function for rename fields names of current list type
         *
         * @param method can be 'all_confirmed_to_form', 'all_form_to_confirmed'
         * @param blockItem
         */
        var _renameFields = function(method, blockItem) {
            var pattern     = null;
            var replacement = null;
            var scopeArr    = blockItem.id.match(/.*\[(\w+)\]\[(\w+)\]$/);
            var listType    = scopeArr[1];
            var itemId      = scopeArr[2];
            if (method == 'all_confirmed_to_form') {
                pattern = RegExp('(\\w+)(\\[?)');
                replacement = 'items['+itemId+'][$1]$2';
            } else if (method == 'all_form_to_confirmed') {
                pattern = new RegExp('items\\['+itemId+'\\]\\[(\\w+)\\](.*)');
                replacement = '$1$2';
            } else {
                return false;
            }
            if (listType == this.current.listType) {
                var rename = function (elms) {
                    for (var i = 0; i < elms.length; i++) {
                        if (elms[i].name) {
                            elms[i].name = elms[i].name.replace(pattern, replacement);
                        }
                    }
                };
                rename(blockItem.getElementsByTagName('input'));
                rename(blockItem.getElementsByTagName('select'));
                rename(blockItem.getElementsByTagName('textarea'));
            }
        }.bind(this);    

        switch (method) {
            case 'item_confirm':
                    if (!$(this.сonfirmedCurrentId)) {
                        this.blockConfirmed.insert(new Element('div', {id: this.сonfirmedCurrentId}));
                    } else {
                        $(this.сonfirmedCurrentId).update();
                    }
                    this.blockFormFields.childElements().each(function(elm) {
                        $(this.сonfirmedCurrentId).insert(elm);
                    }.bind(this));
            break;
            case 'item_restore':
                    this.blockFormFields.update();

                    // clone confirmed to form
                    $(this.сonfirmedCurrentId).childElements().each(function(elm) {
                        var cloned = elm.cloneNode(true);
                        this.blockFormFields.insert(cloned);
                    }.bind(this));

                    // get confirmed values
                    var fieldsValue = {};
                    var getConfirmedValues = function (elms) {
                        for (var i = 0; i < elms.length; i++) {
                            if (elms[i].name) {
                                if ('undefined' == typeof fieldsValue[elms[i].name] ) {
                                    fieldsValue[elms[i].name] = {};
                                }
                                if (elms[i].type == 'checkbox') {
                                    fieldsValue[elms[i].name][elms[i].value] = elms[i].checked;
                                } else if (elms[i].type == 'radio') {
                                    if (elms[i].checked) {
                                        fieldsValue[elms[i].name] = elms[i].value;
                                    }
                                } else {
                                    fieldsValue[elms[i].name] = Form.Element.getValue(elms[i]);
                                }
                            }
                        }
                    }.bind(this);
                    getConfirmedValues($(this.сonfirmedCurrentId).getElementsByTagName('input'));
                    getConfirmedValues($(this.сonfirmedCurrentId).getElementsByTagName('select'));
                    getConfirmedValues($(this.сonfirmedCurrentId).getElementsByTagName('textarea'));

                    // restore confirmed values
                    var restoreConfirmedValues = function (elms) {
                        for (var i = 0; i < elms.length; i++) {
                            if ('undefined' != typeof fieldsValue[elms[i].name]) {
                                if (elms[i].type == 'checkbox') {
                                    elms[i].checked = fieldsValue[elms[i].name][elms[i].value];
                                } else if (elms[i].type == 'radio') {
                                    if (elms[i].value == fieldsValue[elms[i].name]) {
                                        elms[i].checked = true;
                                    }
                                } else {
                                    elms[i].setValue(fieldsValue[elms[i].name]);
                                }
                            }
                        }
                    }.bind(this);
                    restoreConfirmedValues(this.blockFormFields.getElementsByTagName('input'));
                    restoreConfirmedValues(this.blockFormFields.getElementsByTagName('select'));
                    restoreConfirmedValues(this.blockFormFields.getElementsByTagName('textarea'));
            break;
            case 'all_confirmed_to_form':
                    this.blockFormConfirmed.update();
                    this.blockConfirmed.childElements().each(function(blockItem) {
                        _renameFields(method, blockItem);
                        this.blockFormConfirmed.insert(blockItem);
                    }.bind(this));
            break;
            case 'all_form_to_confirmed':
                    this.blockFormConfirmed.childElements().each(function(blockItem) {
                        _renameFields(method, blockItem);
                        this.blockConfirmed.insert(blockItem);
                    }.bind(this));
            break;
        }
    },

    /**
     * Check if qty selected correctly
     *
     * @param object element
     * @param object event
     */
    changeOptionQty: function(element, event)
    {
        var checkQty = true;
        if ('undefined' != typeof event) {
            if (event.keyCode == 8 || event.keyCode == 46) {
                checkQty = false;
            }
        }
        if (checkQty && (Number(element.value) <= 0 || isNaN(Number(element.value)))) {
            element.value = 1;
        }
    }
};

Event.observe(window, 'load',  function() {
    productConfigure = new ProductConfigure();
});
