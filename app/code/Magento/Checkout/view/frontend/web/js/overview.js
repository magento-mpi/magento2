/**
 * @category    checkout multi-shipping review order overview
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
/*jshint jquery:true*/
/*global alert*/
define([
    "jquery",
    "jquery/ui",
    "mage/translate"
], function($){
    "use strict";
    
    $.widget('mage.orderOverview', {
        options: {
            opacity: 0.5, // CSS opacity for the 'Place Order' button when it's clicked and then disabled.
            pleaseWaitLoader: 'span.please-wait', // 'Submitting order information...' Ajax loader.
            placeOrderSubmit: 'button[type="submit"]', // The 'Place Order' button.
            agreements: '#checkout-agreements' // Container for all of the checkout agreements and terms/conditions
        },

        /**
         * Bind a submit handler to the form.
         * @private
         */
        _create: function() {
            this.element.on('submit', $.proxy(this._showLoader, this));
        },

        /**
         * Verify that all agreements and terms/conditions are checked. Show the Ajax loader. Disable
         * the submit button (i.e. Place Order).
         * @return {Boolean}
         * @private
         */
        _showLoader: function() {
            if ($(this.options.agreements).find('input[type="checkbox"]:not(:checked)').length > 0) {
                alert($.mage.__('Please agree to all Terms and Conditions before placing the orders.'));
                return false;
            }
            this.element.find(this.options.pleaseWaitLoader).show().end()
                .find(this.options.placeOrderSubmit).prop('disabled', true).css('opacity', this.options.opacity);
            return true;
        }
    });
    
    return $.mage.orderOverview;
});