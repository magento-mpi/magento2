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
    $.widget('mage.onePageStoreCredit', $.mage.storeCredit, {
        _switchElement: function(element) {
            this.checkFree = false;
            if (element.attr('name') === "payment[method]" && element.val() === "free") {
                if (this.isCustomerBalanceChecked) {
                    this.checkFree = true;
                    element.attr('checked', 'checked').removeAttr('disabled').parent().hide();
                } else {
                    element.prop("disabled", true);
                }
            }
        },

        /**
         * Create and set the hidden element if it does not exist and show or hide the payment methods element.
         * @private
         */
        _setHiddenElement: function() {
            if (this.isCustomerBalanceChecked) {
                if (!this.checkFree) {
                    this._appendHiddenElement();
                }
                $(this.options.paymentMethodsSelector).hide().prop("disabled", "disabled").find(":input").attr('disabled', 'disabled');
                this.options.payment.switchMethod();
            }
        },

        /**
         * Show payment method element.
         * @private
         */
        _showPaymentMethod: function() {
            $("input:radio[name='payment[method]'][value='free']").prop("disabled",true).parent().hide();
            $("input[name='payment[method]']:not([value='free'])").removeAttr("disabled");
            var selectRadio = $("input:radio[name='payment[method]']:not(disabled, [value='free'])");
            selectRadio.first().attr('checked', true);
            if (selectRadio.length === 1) {
                selectRadio.hide();
            }
            $(this.options.customerBalancePaymentSelector).remove();
            $(this.options.paymentMethodsSelector).show();
            this.options.payment.switchMethod(this.options.payment.lastUsedMethod);
        }
    });

})(jQuery);