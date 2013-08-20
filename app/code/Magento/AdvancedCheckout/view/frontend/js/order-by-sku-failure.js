/**
 * {license_notice}
 *
 * @category    order by sku failure
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint jquery:true*/
(function ($) {
    /**
     * This widget handles Order By Sku Failure rendering.
     */
    "use strict";
    $.widget('mage.orderBySkuFailure', {
        options: {
            itemSelector: '[data-role="row"]',
            qtyIncrementsSelector: '[data-role="qty-increments"]',
            qtyInputSelector: '[data-role="input-qty"]',
            qtyMaxAllowedSelector: '[data-role="qty-max-allowed"]',
            qtyMinAllowedSelector: '[data-role="qty-min-allowed"]',
            skuFailedQtySelector: '[data-role="sku-failed-qty"]',
            skuOutOfStockSelector: '[data-role="sku-out-of-stock"]'
        },

        /**
         * This method binds elements found in this widget.
         * @private
         */
        _bind: function () {
            // bind quantity input
            this._on(this.options.qtyInputSelector, {
                keyup: this._validateInputQuantity,
                change: this._skuFailedQty
            });
        },

        /**
         * This method constructs a new widget.
         * @private
         */
        _create: function() {
            this._bind();
            this.element.decorate('table');
            this.element.find(this.options.skuOutOfStockSelector).each($.proxy(this._skuOutOfStock, this));
            this.element.find(this.options.qtyInputSelector).each($.proxy(this._validateItemQuantity, this));
        },

        /**
         * This method validates the input quantity.
         * @private
         */
        _validateInputQuantity: function (event) {
            this._validateItemQuantity(null, event.target);
        },

        /**
         * This method validates the item quantity.
         * @private
         */
        _validateItemQuantity: function (index, element) {
            if (element.disabled) {
                // remove indication of failed quantity when input disabled
                $(element).removeClass('validation-failed');
            }
            else {
                // obtain values for validation
                var itemRow = $(element).closest(this.options.itemSelector);
                var maxAllowed = itemRow.find(this.options.qtyMaxAllowedSelector);
                var minAllowed = itemRow.find(this.options.qtyMinAllowedSelector);
                var qtyIncrements = itemRow.find(this.options.qtyIncrementsSelector);
                var qty = parseFloat($(itemRow).find(this.options.qtyInputSelector).val());

                // validate quantity
                var isMaxAllowedValid = !maxAllowed.length || (qty <= parseFloat($(maxAllowed).val()));
                var isMinAllowedValid = !minAllowed.length || (qty >= parseFloat($(minAllowed).val()));
                var isQtyIncrementsValid = !qtyIncrements.length || (qty % parseFloat($(qtyIncrements).val()) === 0);
                if (isMaxAllowedValid && isMinAllowedValid && isQtyIncrementsValid && qty > 0) {
                    // remove indication of failed quantity
                    $(element).removeClass('validation-failed');
                } else {
                    // show indication of failed quantity
                    $(element).addClass('validation-failed');
                }
            }
        },

        /**
         * This method disables the quantity input for the item containing the given element.
         * @private
         */
        _skuOutOfStock: function(index, element) {
            var qtyInput = this._getQtyInput(element);
            qtyInput.prop('disabled', true);
            qtyInput.addClass('disabled');
        },

        /**
         * This method handles update of quantity on item previously failed for quantity.
         * @private
         */
        _skuFailedQty: function(event) {
            var inputQty = $(event.target);
            var itemRow = inputQty.closest(this.options.itemSelector);
            var failedQtyElement = itemRow.find(this.options.skuFailedQtySelector);

            // update hidden sku failed quantity element with changed quantity
            if (failedQtyElement) {
                $(failedQtyElement).val(inputQty.val());
            }
        },

        /**
         * This method returns the quantity input element for the item containing the given element.
         * @private
         */
        _getQtyInput: function(element) {
            var itemRow = $(element).closest(this.options.itemSelector);
            return $(itemRow).find(this.options.qtyInputSelector);
        }
    });
})(jQuery);