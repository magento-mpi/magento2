/**
 * {license_notice}
 *
 * @category    gift registry multi-ship address option
 * @package     enterprise
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint jquery:true*/
/*global alert:true*/
(function($) {
    "use strict";
    $.widget('mage.addressOption', {
        options: {
            addressOptionTmpl: '#address-option-tmpl' // 'Use gift registry shipping address' option.
        },

        /**
         * Add the gift registry shipping address option to every gift registry item on the multishipping page.
         * @private
         */
        _create: function() {
            $.each(this.options.registryItems, $.proxy(this._addAddressOption, this));
        },

        /**
         * Add a 'Use gift registry shipping address' option to items on the multishipping page. Bind a change
         * handler on the quantity field of gift registry items to prevent changing the value.
         * @private
         * @param x {Number} - Index value - Unused.
         * @param object {Object} - JSON Object - {"item": #, "address": #}
         */
        _addAddressOption: function(x, object) {
            var _this = this;
            this.element.find('select[id^="ship_"]').each(function(y, element) {
                var arr = $(element).attr('id').split('_');
                if (arr[2] && parseInt(arr[2], 10) === object.item) {
                    var selectedIndices = _this.options.selectedAddressIndices,
                        selectOption = $(_this.options.addressOptionTmpl).tmpl([{
                            text: $.mage.__('Use gift registry shipping address'),
                            value: _this.options.addressItemPrefix + object.address
                        }]).appendTo(element);
                    if (selectedIndices.length > 0) {
                        _this._setSelected(selectOption, parseInt(arr[1], 10), selectedIndices);
                    }
                    $(element).closest('tr').find('input[type="text"]').on('change', function(event) {
                        var qty = $(event.target);
                        qty.val(qty.prop('defaultValue'));
                        alert($.mage.__('Changing quantity for gift registry items is not allowed during checkout. You can change item quantity on the Gift Registry Info page or directly in your shopping cart.'));
                    });
                }
            });
        },

        /**
         * Search for the target index and set the selected attribute when there is a match. This will mark
         * the 'Use gift registry shipping address' option as 'selected'.
         * @private
         * @param option {Object} - Select option object.
         * @param index - {Number} - The target index value being searched for.
         * @param indices - {Array} - An array of indices to iterate through looking for the target.
         */
        _setSelected: function(option, index, indices) {
            for (var i = 0; i < indices.length; i++) {
                if (indices[i] === index) {
                    option.attr('selected', 'selected');
                    break;
                }
            }
        }
    });
})(jQuery);
