/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/

(function($) {
    'use strict';
    $.widget('mage.opcheckout', $.mage.opcheckout, {
        options: {
            giftRegistry: {
                radioTemplateSelector: '#gift-registry-billing',
                checkboxTemplateSelector: '#gift-registry-shipping',
                listContainerSelector: '.form-list'
            }
        },

        _create: function() {
            this._super();
            this._injectElement();
        },
        /**
         * injecting template for shipping and billing form
         * @private
         */
        _injectElement: function() {
            $(this.options.giftRegistry.radioTemplateSelector).tmpl().appendTo($(this.options.giftRegistry.listContainerSelector, this.options.billing.form).last('li'));
            $(this.options.giftRegistry.checkboxTemplateSelector).tmpl()
                .appendTo($(this.options.giftRegistry.listContainerSelector, this.options.shipping.form).last('li'))
                .on('click', $.proxy(this._checkboxHandler, this));
        },

        /**
         * event handler for the checkbox
         * @private
         */
        _checkboxHandler: function(e) {
            var checked = $(e.target).is(':checked'),
                shippingAddressDropdown = $(this.options.shipping.addressDropdownSelector);
            shippingAddressDropdown.prop('disabled', checked);
            if (checked) {
                $(this.options.shipping.newAddressFormSelector).hide();
            } else {
                if (!shippingAddressDropdown.length) {
                    $(this.options.shipping.newAddressFormSelector).show();
                }
            }
        },

        /**
         * ajax call to save billing and shipping saving form
         * @private
         */
        _billingSave: function() {
            if ($('input:radio:checked', this.options.billing.form).val() === "2") {
                this._ajaxContinue(this.options.billing.saveUrl, $(this.options.billing.form).serialize(), false, function(response) {
                    if (response.goto_section) {
                        response.goto_section = null;
                    }
                    //Trigger indicating billing save. eg. GiftMessage listens to this to inject gift options
                    this.element.trigger('billingSave');
                    //Trigger shipping save
                    $(this.options.shipping.continueSelector).trigger('click');
                }, this);

            } else {
                this._super();
            }
        }
    });
})(jQuery, window);
