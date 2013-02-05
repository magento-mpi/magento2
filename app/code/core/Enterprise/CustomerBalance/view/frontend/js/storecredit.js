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
    $.widget('mage.storeCredit', {

        /**
         * options with default values
         */
        options: {
            minBalance: 0.0001,
            customerBalanceSubstracted: true,
            customerBalanceCheckBoxSelector: '#use-customer-balance',
            customerBalancePaymentSelector: '#customerbalance-hidden-payment',
            customerBalanceBlockSelector: '#customerbalance-block',
            paymentMethodsSelector: '#payment-methods',
            paymentForm: '#multishipping-billing-form'
        },

        /**
         * Initialize store credit events
         * @private
         */
        _create: function() {
            $(this.options.customerBalanceCheckBoxSelector).on('click', $.proxy(this._switchCustomerBalanceCheckbox, this));
            if (this.options.customerBalanceSubstracted) {
                this.options.quoteBaseGrandTotal += this.options.baseCustomerBalAmountUsed;
                this.options.customerBalanceSubstracted = false;
            }
            this._switchCustomerBalanceCheckbox();
            this._setUseCustomerBalanceEnabled();
        },

        /**
         * Calculate resultant payment amount and hide/show payment options.
         * @private
         */
        _switchCustomerBalanceCheckbox: function() {
            this.isCustomerBalanceChecked = $(this.options.customerBalanceCheckBoxSelector).is(':checked');
            if (!this.options.customerBalanceSubstracted && this.isCustomerBalanceChecked) {
                this.options.quoteBaseGrandTotal -= this.options.balance;
                this.options.customerBalanceSubstracted = true;
            }
            if (this.options.customerBalanceSubstracted && !this.isCustomerBalanceChecked) {
                this.options.quoteBaseGrandTotal += this.options.balance;
                this.options.customerBalanceSubstracted = false;
            }
            if (this.options.quoteBaseGrandTotal < this.options.minBalance) {
                $(this.options.paymentForm).find(':input').each($.proxy(function(index, element) {
                    this._switchElement($(element));
                }, this));
                this._setHiddenElement();
            } else {
                this._showPaymentMethod();
            }
        },

        /**
         * Toggle element visibility based on the checkbox state
         * @private
         */
        _switchElement: function(element) {
            if (this.isCustomerBalanceChecked) {
                if (element.attr('name') === 'payment[method]') {
                    if (element.val() === 'free' && element.is(':radio')) {
                        element.attr('checked', 'checked').removeAttr("disabled").parent().hide();
                    }
                }
            } else {
                if (element.attr('name') === 'payment[method]' && element.val() !== 'free') {
                    element.removeAttr('disabled');
                }
            }
        },

        /**
         * Append a hidden element to the block.
         * @private
         */
        _appendHiddenElement: function() {
            $('<input>').attr({
                type: 'hidden',
                id: this.options.customerBalancePaymentSelector.replace(/^(#|.)/, ""),
                name: 'payment[method]',
                value: 'free'
            }).appendTo(this.options.customerBalanceBlockSelector);
        },

        /**
         * Create and set the hidden element if it does not exist and show or hide the payment methods element.
         * @private
         */
        _setHiddenElement: function() {
            if (this.isCustomerBalanceChecked) {
                this._appendHiddenElement();
                $(this.options.paymentMethodsSelector).hide().attr("disabled", "disabled");
                $(this.options.paymentMethodsSelector).find(':input').each($.proxy(function(index, element) {
                    $(element).attr("disabled", "disabled");
                }, this));
            } else {
                $(this.options.paymentMethodsSelector).show().removeAttr("disabled");
            }
        },

        /**
         * Show payment method element.
         * @private
         */
        _showPaymentMethod: function() {
            $("input[name='payment[method]']").each($.proxy(function(index, element) {
                element = $(element);
                if (element.val() !== 'free') {
                    element.removeAttr('disabled');
                }
            }, this));
            var selectRadio = $("input:radio[name='payment[method]']:not(disabled):not([value='free'])");
            selectRadio.first().attr('checked', true);
            if (selectRadio.length === 1) {
                selectRadio.hide();
            }
            $(this.options.customerBalancePaymentSelector).remove();
            $(this.options.paymentMethodsSelector).show();
        },

        /**
         * Set the customer balance checkbox to visible and formats the balance amount.
         * @private
         */
        _setUseCustomerBalanceEnabled: function() {
            $(this.options.customerBalanceCheckBoxSelector).removeAttr('disabled');
            if (this.options.isAllowed) {
                $(this.options.customerBalanceAmountSelector).html(this.options.formattedBalance);
            }
        }
    });

})(jQuery);