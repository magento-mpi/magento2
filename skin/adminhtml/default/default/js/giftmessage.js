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

var GiftMessage = Class.create();

GiftMessage.prototype = {
    uniqueId: 0,
    initialize: function (buttonId) {
        GiftMessageStack.addObject(this);
        this.buttonId = buttonId;
        this.initListeners();
    },
    editGiftMessage: function (evt) {
        var popUpUrl = this.url + '?uniqueId=' + this.uniqueId;
        this.popUp = window.open(popUpUrl, 'giftMessage', 'width=425,height=480,resizable=yes,scrollbars=yes');
        this.popUp.focus();
        Event.stop(evt);
    },
    initListeners: function () {
        var items = $(this.buttonId).getElementsByClassName('listen-for-click');
        items.each(function(item) {
           Event.observe(item, 'click', this.editGiftMessage.bindAsEventListener(this));
           item.controller = this;
        }.bind(this));
    },
    reloadContainer: function (url) {
        new Ajax.Updater(this.buttonId, url, {onComplete:this.initListeners.bind(this)});
    },
    initWindow: function (windowObject) {
        this.windowObj = windowObject;
    }
};

var GiftMessageStack = {
    _stack: [],
    _nextUniqueId: 0,
    addObject: function(giftMessageObject) {
       giftMessageObject.uniqueId = this.uniqueId();
       this._stack.push(giftMessageObject);
       return this;
    },
    uniqueId: function() {
        return 'objectStack' + (this._nextUniqueId++);
    },
    getObjectById: function(id) {
        var giftMessageObject = false;
        this._stack.each(function(item){
           if(item.uniqueId == id) {
               giftMessageObject = item;
           }
        });
        return giftMessageObject;
    }
};

var GiftMessageWindow = Class.create();
GiftMessageWindow.prototype = {
    initialize: function(uniqueId, formId, removeUrl) {
        this.uniqueId = uniqueId;
        this.removeUrl = removeUrl;
        if(window.opener) {
            this.parentObject = window.opener.GiftMessageStack.getObjectById(this.uniqueId);
            this.parentObject.initWindow(this);
        }
        if(formId) {
            this.form = new varienForm(formId, true);
            this.formElement = $(formId);
            this.initListeners();
        }
    },
    initListeners: function() {
        removeButtons = this.formElement.getElementsByClassName('listen-remove');
        removeButtons.each(function(item){
            Event.observe(item, 'click', this.remove.bindAsEventListener(this));
        }.bind(this));

        cancelButtons = this.formElement.getElementsByClassName('listen-cancel');
        cancelButtons.each(function(item){
            Event.observe(item, 'click', this.cancel.bindAsEventListener(this));
        }.bind(this));
    },
    cancel: function(evt)  {
        Event.stop(evt);
        window.opener.focus();
        window.close();
    },
    close: function()  {
        window.opener.focus();
        window.close();
    },
    remove: function(evt)  {
        Event.stop(evt);
        if(this.confirmMessage && !window.confirm(this.confirmMessage)) {
            return;
        }
        window.location.href = this.removeUrl;
    },
    updateParent: function (url, buttonUrl) {
        if(this.parentObject) {
            this.parentObject.url = url
            this.parentObject.reloadContainer(buttonUrl);
        }
        setTimeout(function(){
            window.opener.focus();
            window.close();
        }, 3000);
    }
};
