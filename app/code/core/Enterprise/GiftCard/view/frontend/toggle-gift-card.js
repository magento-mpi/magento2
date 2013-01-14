/**
 * {license_notice}
 *
 * @category    frontend giftcard
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */

/*jshint browser:true jquery:true*/
(function($) {
    "use strict";
    $.widget('mage.toggleGiftCard', {

        options: {
            amountSelector: '#giftcard_amount_input',
            amountBoxSelector: '#giftcard_amount_box',
            amountLabelSelector: '#amount_label_input',
            amountLabelDropDownSelector: '#amount_label_select'
        },

        /**
         * Bind handlers to events
         */
        _create: function() {
            this.element.on('change', $.proxy(this._toggleGiftCard, this))
                .trigger('change');
        },

        /**
         * Toggle gift card
         * @private
         */
        _toggleGiftCard: function() {
            if (this.element.val() === 'custom') {
                $(this.options.amountSelector)
                    .add(this.options.amountBoxSelector)
                    .add(this.options.amountLabelSelector)
                    .show();
                $(this.options.amountLabelDropDownSelector).hide();
            } else {
                $(this.options.amountSelector)
                    .add(this.options.amountBoxSelector)
                    .add(this.options.amountLabelSelector)
                    .hide();
                $(this.options.amountLabelDropDownSelector).show();
            }
        }
    });
})(jQuery);
