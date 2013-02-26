/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerRewards
 * @copyright   {copyright}
 * @license     {license_link}
 */

/*jshint browser:true jquery:true*/
(function($, undefined) {
    "use strict";
    $.widget('mage.customerReqards', {

        /**
         * options with default values
         */
        options: {
            minBalance: 0.0001,
            rewardPointsSubstracted: true,
            rewardPointsCheckBoxSelector: '#use-reward-points',
            rewardPointsPaymentSelector: '#reward-hidden-payment',
            rewardPointsBlockSelector: '#reward-block',
            paymentMethodsSelector: '#payment-methods',
            paymentForm: '#multishipping-billing-form'
        },

        /**
         * Initialize store credit events
         * @private
         */
        _create: function() {
            $(this.options.rewardPointsCheckBoxSelector).on('click', $.proxy(this._switchRewardPointsCheckbox, this));
            if (this.options.rewardPointsSubstracted) {
                this.options.quoteBaseGrandTotal += this.options.baseRewardCurrencyAmount;
                this.options.rewardPointsSubstracted = false;
            }
            this._switchRewardPointsCheckbox();
            this._setUseRewardPointsEnabled();
        },

        /**
         * Calculate resultant payment amount and hide/show payment options.
         * @private
         */
        _switchRewardPointsCheckbox: function() {
            this.isRewardPointsSelectorChecked = $(this.options.rewardPointsCheckBoxSelector).is(':checked');
            if (!this.options.rewardPointsSubstracted && this.isRewardPointsSelectorChecked) {
                this.options.quoteBaseGrandTotal -= this.options.balance;
                this.options.rewardPointsSubstracted = true;
            }
            if (this.options.rewardPointsSubstracted && !this.isRewardPointsSelectorChecked) {
                this.options.quoteBaseGrandTotal += this.options.balance;
                this.options.rewardPointsSubstracted = false;
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
            if (this.isRewardPointsSelectorChecked) {
                if (element.attr('name') === 'payment[method]') {
                    if (element.val() === 'free' && element.is(':radio')) {
                        element.prop('checked', true).removeAttr("disabled").parent().hide();
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
                id: this.options.rewardPointsPaymentSelector.replace(/^(#|.)/, ""),
                name: 'payment[method]',
                value: 'free'
            }).appendTo(this.options.rewardPointsBlockSelector);
        },

        /**
         * Create and set the hidden element if it does not exist and show or hide the payment methods element.
         * @private
         */
        _setHiddenElement: function() {
            if (this.isRewardPointsSelectorChecked) {
                this._appendHiddenElement();
                $(this.options.paymentMethodsSelector).hide().prop("disabled", true).find(":input").prop("disabled", true);
            } else {
                $(this.options.paymentMethodsSelector).show().removeAttr("disabled");
            }
        },

        /**
         * Show payment method element.
         * @private
         */
        _showPaymentMethod: function() {
            $("input:radio[name='payment[method]'][value='free']").prop("disabled", true).parent().hide();
            $("input[name='payment[method]']:not([value='free'])").removeAttr("disabled");
            var selectRadio = $("input:radio[name='payment[method]']:not(disabled, [value='free'])");
            selectRadio.first().attr('checked', true);
            if (selectRadio.length === 1) {
                selectRadio.hide();
            }
            $(this.options.rewardPointsPaymentSelector).remove();
            $(this.options.paymentMethodsSelector).show();
        },

        /**
         * Set the customer rewards checkbox to visible.
         * @private
         */
        _setUseRewardPointsEnabled: function() {
            $(this.options.rewardPointsCheckBoxSelector).removeAttr('disabled');
        }
    });

})(jQuery);