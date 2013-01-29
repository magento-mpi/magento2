/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerBalance
 * @copyright   {copyright}
 * @license     {license_link}
 */

/*jshint browser:true jquery:true*/

(function($, undefined) {
    "use strict";
    $.widget('mage.onepageStoreCredit', $.mage.storeCredit, {
        _switchElement: function(element) {
            this.checkFree = false;
            if (element.attr('name') === "payment[method]" && element.val() === "free") {
                if ($(this.options.customerBalanceCheckBoxSelector).is(':checked')) {
                    this.checkFree = true;
                    element.attr('checked', 'checked');
                    element.removeAttr('disabled');
                    element.parent().hide();
                } else {
                    element.removeAttr('disabled');
                }
            }
        },

        /**
         * Create and set the hidden element if it does not exist and show or hide the payment methods element.
         * @private
         */
        _setHiddenElement: function() {
            if ($(this.options.customerBalanceCheckBoxSelector).is(':checked')) {
                if (!this.checkFree) {
                    $('<input>').attr({
                        type: 'hidden',
                        id: this.options.customerBalancePaymentSelector,
                        name: 'payment[method]',
                        value: 'free'
                    }).appendTo(this.options.customerBalanceBlockSelector);
                }
                $(this.options.paymentMethodsSelector).hide();
                this.options.payment.switchMethod();
            }
        },

        /**
         * Show payment method element.
         * @private
         */
        _showPaymentMethod: function() {
            $("input[name='payment[method]']").each($.proxy(function(index, element) {
                element = $(element);
                element.removeAttr('disabled');
            }, this));
            $(this.options.paymentMethodsSelector).show();
            this.options.payment.switchMethod(this.options.payment.lastUsedMethod);
        }
    });

})(jQuery);