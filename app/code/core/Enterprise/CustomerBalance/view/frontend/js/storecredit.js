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
            customerBalanceSubstracted: true
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

            if (!this.options.customerBalanceSubstracted && $(this.options.customerBalanceCheckBoxSelector).is(':checked')) {
                this.options.quoteBaseGrandTotal -= this.options.balance;
                this.options.customerBalanceSubstracted = true;
            }
            if (this.options.customerBalanceSubstracted && !$(this.options.customerBalanceCheckBoxSelector).is(':checked')) {
                this.options.quoteBaseGrandTotal += this.options.balance;
                this.options.customerBalanceSubstracted = false;
            }
            if (this.options.quoteBaseGrandTotal < this.options.minBalance) {
                $(this.options.paymentForm).find(':input:not(:hidden)').each($.proxy(function(index, element) {
                    this._switchElement($(element));
                }, this));
                this._setHiddenElement();
            } else {
                this._showPaymentMethod();
            }
        },

        _switchElement: function(element) {
            if ($(this.options.customerBalanceCheckBoxSelector).is(':checked')) {
                if (element.attr('name') === 'payment[method]') {
                    if (element.val() === 'free' && element.is(':radio')) {
                        element.attr('checked', 'checked');
                        element.removeAttr('disabled');
                        element.parent().hide();
                    }
                }
            } else {
                if (element.attr('name') === 'payment[method]' && element.val() !== 'free') {
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
                $(this.options.paymentMethodsSelector).hide();
                $('<input>').attr({
                    type: 'hidden',
                    id: this.options.customerBalancePaymentSelector,
                    name: 'payment[method]',
                    value: 'free'
                }).appendTo(this.options.customerBalanceBlockSelector);
            } else {
                if ($(this.options.customerBalancePaymentSelector).length > 0) {
                    $(this.options.customerBalanceBlockSelector).find(this.options.customerBalancePaymentSelector).hide();
                }
                $(this.options.paymentMethodsSelector).show();
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