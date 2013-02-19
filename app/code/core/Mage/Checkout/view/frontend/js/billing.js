/**
 * {license_notice}
 *
 * @category    one page checkout billing step
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true evil:true*/
(function($) {
    'use strict';
    $.widget('mage.billing', {
        options: {
            addressDropdownSelector: '#billing-address-select',
            newAddressForm: '#billing-new-address-form',
            continueSelector: '#billing-buttons-container .button',
            sectionSelectorPrefix: '#opc-'
        },

        _create: function() {
            $(this.options.addressDropdownSelector).on('change', $.proxy(this._addressDropdownHandler, this));
            $(this.options.continueSelector).on('click', $.proxy(this._ajaxBillingSave, this));
        },

        /**
         * Enable or disable new address for customer with multiple address dropdown
         * @private
         * @param e - address select element
         */
        _addressDropdownHandler: function(e) {
            if (!$(e.target).val()) {
                $(this.options.newAddressForm).show();
            } else {
                $(this.options.newAddressForm).hide();
            }
        },

        /**
         * Save Billing info using ajax
         */
        _ajaxBillingSave: function() {
            if (this.element.validation().valid()) {
                $.ajax({
                    url: this.options.saveBillingUrl,
                    type: 'post',
                    cache: false,
                    context: this,
                    data: this.element.serialize(),
                    success: function(response) {
                        var responseObj = eval('(' + response + ')');
                        this.element.trigger('gotoSection', responseObj.goto_section);
                    }
                });
            }
        }
    });
})(jQuery);
