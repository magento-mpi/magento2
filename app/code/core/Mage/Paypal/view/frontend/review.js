/**
 * {license_notice}
 *
 * @category    design
 * @package     base_default
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Controller of order review form that may select shipping method
 */
OrderReviewController = Class.create();
OrderReviewController.prototype = {
    _canSubmitOrder : false,
    _pleaseWait : false,
    _copyElement : false,
    onSubmitShippingSuccess : false,

    /**
     * Add listeners to provided objects, if any
     * @param orderForm - form of the order submission
     * @param orderFormSubmit - element for the order form submission (optional)
     * @param shippingSelect - dropdown with available shipping methods (optional)
     * @param shippingSubmitForm - form of shipping method submission (optional, requires shippingSelect)
     * @param shippingResultId - element where to update results of shipping ajax submission (optional, requires shippingSubmitForm)
     */
    initialize : function(orderForm, orderFormSubmit, shippingSelect, shippingSubmitForm, shippingResultId, shippingSubmit)
    {
        if (!orderForm) {
            return;
        }
        this.form = orderForm;
        if (orderFormSubmit) {
            this.formSubmit = orderFormSubmit;
            Event.observe(orderFormSubmit, 'click', this._submitOrder.bind(this));
        }

        this._updateOrderSubmit(false);
    },

    /**
     * Register element that should show up when ajax request is in progress
     * @param element
     */
    addPleaseWait : function(element)
    {
        if (element) {
            this._pleaseWait = element;
        }
    },

    /**
     * Dispatch an ajax request of shipping method submission
     * @deprecated since 1.7.0.0
     * @param event
     * @param url - url where to submit shipping method
     * @param resultId - id of element to be updated
     */
    _submitShipping : function(event, url, resultId)
    {
    },

    /**
     * Set event observer to Update Order button
     * @param element
     * @param url - url to submit on Update button
     * @param resultId - id of element to be updated
     */
    setUpdateButton : function(element, url, resultId)
    {
        if (element) {
            Event.observe(element, 'click', this._submitUpdateOrder.bind(this, url, resultId));
            if (!this._validateForm()) {
                this._updateOrderSubmit(true);
            }
            this.formValidator.reset();
            this._clearValidation('');
        }
    },

    /**
     * Set event observer to copy data from shipping address to billing
     * @param element
     */
    setCopyElement : function(element)
    {
        if (element) {
            this._copyElement = element;
            Event.observe(element, 'click', this._copyShippingToBilling.bind(this));
        }
    },

    /**
     * Copy data from shipping address to billing
     */
    _copyShippingToBilling : function ()
    {
        if (!this._copyElement) {
            return;
        }
        if (this._copyElement.checked) {
            $$('[id^="shipping:"]').each(function(el){
                var newId = el.id.replace('shipping:','billing:');
                if (newId && $(newId) && $(newId).type != 'hidden') {
                    $(newId).value = el.value;
                    $(newId).setAttribute('readonly', 'readonly');
                    $(newId).addClassName('local-validation');
                    $(newId).setStyle({opacity:.5})
                }
            });
            this._clearValidation('billing');
        } else {
            $$('[id^="billing:"]').invoke('removeAttribute', 'readonly');
            $$('[id^="billing:"]').invoke('removeClassName', 'local-validation');
            $$('[id^="billing:"]').invoke('setStyle', {opacity:1});
        }
    },

    /**
     * Dispatch an ajax request of Update Order submission
     * @param url - url where to submit shipping method
     * @param resultId - id of element to be updated
     */
    _submitUpdateOrder : function(url, resultId)
    {
        this._copyShippingToBilling();
        if (url && resultId && this._validateForm()) {
            if (this._copyElement && this._copyElement.checked) {
                this._clearValidation('billing');
            }
            this._updateOrderSubmit(true);
            if (this._pleaseWait) {
                this._pleaseWait.show();
            }

            var formData = this.form.serialize(true);
            formData.isAjax = true;
            new Ajax.Updater(resultId, url, {
                parameters: formData,
                onComplete: function() {
                    if (this._pleaseWait) {
                        this._pleaseWait.hide();
                    }
                }.bind(this),
                onSuccess: this._onSubmitShippingSuccess.bind(this),
                evalScripts: true
            });
        } else {
            if (this._copyElement && this._copyElement.checked) {
                this._clearValidation('billing');
            }
        }
    },

    /**
     * Validate Order form
     */
    _validateForm : function()
    {
        if (!this.form) {
            return false;
        }
        if (!this.formValidator) {
            this.formValidator = new Validation(this.form);
        }

        return this.formValidator.validate();
    },

    /**
     * Clear validation result for all form elements or for elements with id prefix
     * @param idprefix
     */
    _clearValidation : function(idprefix)
    {
        var prefix = '';
        if (idprefix) {
            prefix = '[id*="' + idprefix + ':"]';
            $$(prefix).each(function(el){
                el.up().removeClassName('validation-failed')
                    .removeClassName('validation-passed')
                    .removeClassName('validation-error');
            });
        }
        $$('.validation-advice' + prefix).invoke('remove');
        $$('.validation-failed' + prefix).invoke('removeClassName', 'validation-failed');
        $$('.validation-passed' + prefix).invoke('removeClassName', 'validation-passed');
        $$('.validation-error' + prefix).invoke('removeClassName', 'validation-error');
    },

    /**
     * Attempt to submit order
     */
    _submitOrder : function()
    {
        if (this._canSubmitOrder && this._validateForm()) {
            this.form.submit();
            if (this._pleaseWait) {
                this._pleaseWait.show();
            }
        }
        this._updateOrderSubmit(true);
    },

    /**
     * Explicitly enable order submission
     */
    _onSubmitShippingSuccess : function()
    {
        this._updateOrderSubmit(false);
        if (this.onSubmitShippingSuccess) {
            this.onSubmitShippingSuccess();
        }
    },

    /**
     * Check/Set whether order can be submitted
     * Also disables form submission element, if any
     * @param shouldDisable - whether should prevent order submission explicitly
     */
    _updateOrderSubmit : function(shouldDisable)
    {
        this._canSubmitOrder = !shouldDisable;
        if (this.formSubmit) {
            this.formSubmit.disabled = shouldDisable;
            this.formSubmit.removeClassName('no-checkout');
            this.formSubmit.setStyle({opacity:1});
            if (shouldDisable) {
                this.formSubmit.addClassName('no-checkout');
                this.formSubmit.setStyle({opacity:.5});
            }
        }
    }
};
