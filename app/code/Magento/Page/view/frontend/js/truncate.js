/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage Page
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint jquery:true*/
(function($) {
    "use strict";
    $.widget('mage.truncateOptions', {
        options: {
            detailsLink: 'a.details',
            mouseEvents: 'mouseover mouseout',
            truncatedFullValue: 'div.truncated_full_value'
        },

        /**
         * Establish the event handler for mouse events on the appropriate elements.
         * @private
         */
        _create: function() {
            this.element.on(this.options.mouseEvents, $.proxy(this._toggleShow, this))
                .find(this.options.detailsLink).on(this.options.mouseEvents, $.proxy(this._toggleShow, this));
        },

        /**
         * Toggle the "show" class on the associated element.
         * @private
         * @param event {Object} - Mouse over/out event.
         */
        _toggleShow: function(event) {
            $(event.currentTarget).find(this.options.truncatedFullValue).toggleClass('show');
        }
    });
})(jQuery);
