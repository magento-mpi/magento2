/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerRewards
 * @copyright   {copyright}
 * @license     {license_link}
 */

/*jshint browser:true jquery:true*/
(function($) {
    "use strict";
    $.widget('mage.customerRewards', {
        /**
         * options with default values
         */
        options: {
            minBalance: 0.0001,
            useRewardPoints: true,
            rewardPointsCheckBoxSelector: '#use-reward-points',
            rewardPointsPaymentSelector: '#reward-hidden-payment',
            rewardPointsBlockSelector: '#reward-block',
            paymentMethodsSelector: '#payment-methods',
            paymentForm: '#multishipping-billing-form',
            paymentMethodTemplate: '<input type="${type}" data-payment-method-hidden value="${value}" name="${name}" />'
        },

        /**
         * Initialize store credit events
         * @private
         */
        _create: function() {
            $(this.options.rewardPointsCheckBoxSelector).on('click', $.proxy(this._switchRewardPointsCheckbox, this));
            if (this.options.useRewardPoints) {
                this.options.quoteBaseGrandTotal += this.options.baseRewardCurrencyAmount;
                this.options.useRewardPoints = false;
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
            if (!this.options.useRewardPoints && this.isRewardPointsSelectorChecked) {
                this.options.quoteBaseGrandTotal -= this.options.balance;
                this.options.useRewardPoints = true;
            }
            if (this.options.useRewardPoints && !this.isRewardPointsSelectorChecked) {
                this.options.quoteBaseGrandTotal += this.options.balance;
                this.options.useRewardPoints = false;
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
         * @param element {Object} - Payment method Input element.
         */
        _switchElement: function(element) {
            if (this.isRewardPointsSelectorChecked &&
                element.attr('name') === 'payment[method]' &&
                (element.val() === 'free' && element.is(':radio'))) {
                element.prop('checked', true).removeAttr("disabled").parent().hide();
            } else if (element.attr('name') === 'payment[method]' && element.val() !== 'free') {
                element.removeAttr('disabled');
            }
        },

        /**
         * Append a hidden element to the block.
         * @private
         */
        _appendHiddenElement: function() {
            $(this.options.rewardPointsBlockSelector).append($.proxy(function() {
                $.template('paymentMethodTemplate', this.options.paymentMethodTemplate);
                return $.tmpl('paymentMethodTemplate', {type: 'hidden', name: 'payment[method]', value: 'free'});
            }, this));
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
            $(this.options.paymentMethodsSelector).find("input:radio[name='payment[method]'][value='free']").prop("disabled", true).parent().hide();
            $(this.options.paymentMethodsSelector).find("input[name='payment[method]']:not([value='free'])").removeAttr("disabled");
            var selectRadio = $(this.options.paymentMethodsSelector).find("input:radio[name='payment[method]']:not(disabled, [value='free'])");
            selectRadio.first().prop('checked', true);
            if (selectRadio.length === 1) {
                selectRadio.hide();
            }
            $('[data-payment-method-hidden]').remove();
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