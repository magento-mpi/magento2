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
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
var centinelValidator = Class.create();

centinelValidator.prototype = {
    
    initialize: function()
    {
        this.methods = Array();
    },

    registerMethod: function(methodCode, validationUrl, frameId, formId)
    {
        var method = new centinelValidationMethod(methodCode, validationUrl, frameId, formId);
        this._setMethod(methodCode, method);
    },

    validate: function(methodCode)
    {
        var method = this._getMethod(methodCode);
        if (!method) {
            return true;
        }
        if (method.state == true && method.checksum == this._generateChecksum(method.getForm())) {
            return true
        }
        this._startValidation(method);
        return false;
    },
    
    validationStart: function(methodCode, message)
    {
        this._getMethod(methodCode).getFrame().show();
        this._showMessage(message);
    },

    validationFailed: function(methodCode, message)
    {
        var method = this._getMethod(methodCode);
        method.getFrame().hide();
        method.state = false;
        this._showMessage(message);
    },

    validationComplete: function(methodCode, message)
    {
        var method = this._getMethod(methodCode);
        method.getFrame().hide();
        method.state = true;
        this._showMessage(message);
    },

    _showMessage: function(message)
    {
        if (message != '') {
            alert(message);
        }
        return this;
    },

    _startValidation: function(method)
    {
        method.getFrame().hide();
        method.state = false;
        method.checksum = this._generateChecksum(method.getForm());
        var formParams = Form.serialize(method.getForm());
        method.getFrame().src = method.validationUrl + '?' + formParams;
        return this;
    },

    _setMethod: function(methodCode, method)
    {
        this.methods[methodCode] = method;
        return this;
    },

    _getMethod: function(methodCode)
    {
        return this.methods[methodCode];
    },
    
    _generateChecksum: function(form)
    {
        return Form.serialize(form);
    }
}

var centinelValidationMethod = Class.create();
centinelValidationMethod.prototype = {
    initialize: function(methodCode, validationUrl, frameId, formId)
    {
        this.methodCode = methodCode;
        this.validationUrl = validationUrl;
        this.state = false;
        this.checksum = false;
        this._frameId = frameId;
        this._formId = formId;
    },
    
    getForm: function()
    {
        return $(this._formId);
    },

    getFrame: function(){
        return $(this._frameId);
    }
}

centinelValidator = new centinelValidator();
