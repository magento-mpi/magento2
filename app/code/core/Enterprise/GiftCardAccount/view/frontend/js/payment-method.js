/**
 * {license_notice}
 *
 * @category    gift card account
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint jquery:true*/
(function($) {
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
         * Process form elements, setting attribute values and showing/hiding their parent node.
         * @private
         */
        _create: function() {
            var options = this.options;
            $.each(this.element[0].elements, function() {
                if (this.name === options.paymentName && this.value === options.paymentValue) {
                    // Show 'No Payment Information Required' with checked radio button.
                    $(this).attr({'checked': true, 'disabled': false}).parent().show();
                } else {
                    // Show 'Check / Money order' with no radio button.
                    $(this).parent().attr('disabled', true).hide();
                }
            });
        }
    });
})(jQuery);
