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
            amountSelector: '#giftcard-amount-input',
            amountBoxSelector: '#giftcard-amount-box',
            amountLabelSelector: '#amount-label-input',
            amountLabelDropDownSelector: '#amount-label-select'
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
            var jQueryObjects = $(this.options.amountSelector)
                .add(this.options.amountBoxSelector)
                .add(this.options.amountLabelSelector);

            if (this.element.val() === 'custom') {
                jQueryObjects.show();
                $(this.options.amountLabelDropDownSelector).hide();
            } else {
                jQueryObjects.hide();
                $(this.options.amountLabelDropDownSelector).show();
            }
        }
    });
})(jQuery);
