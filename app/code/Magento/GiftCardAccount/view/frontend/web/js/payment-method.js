/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
/*jshint jquery:true*/
define([
    "jquery",
    "jquery/ui"
], function($){
    "use strict";
    
    $.widget('mage.paymentMethod', {
        /**
         * Billing information when multi-shipping option is selected.
         * @type {Object}
         */
        options: {
            paymentName: 'payment[method]', // Attribute name for payment method form input element.
            paymentValue: 'free' // Payment method value (e.g. 'free' means gift card used for total balance).
        },

        /**
         * Process form elements, setting property values and showing/hiding their parent node.
         * @private
         */
        _create: function() {
            var options = this.options;
            $.each(this.element[0].elements, function() {
                if (this.name === options.paymentName && this.value === options.paymentValue) {
                    // Show 'No Payment Information Required' with checked radio button.
                    $(this).prop({'checked': true, 'disabled': false}).parent().show();
                } else if ($(this).closest("div.actions").length === 0) {
                    // Hide all other objects beside the buttons.
                    $(this).parent().prop('disabled', true);
                }
            });
        }
    });
    
    return $.mage.paymentMethod;
});