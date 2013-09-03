/**
 * {license_notice}
 *
 * @category    mage compare list
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
/*global confirm:true*/
(function ($) {
    $.widget('mage.compareItems', {
        _create: function() {
            this.element.decorate('list', true);
            this._confirm(this.options.removeSelector, this.options.removeConfirmMessage);
            this._confirm(this.options.clearAllSelector, this.options.clearAllConfirmMessage);
        },

        /**
         * Set up a click event on the given selector to display a confirmation request message
         * and ask for that confirmation.
         * @param selector Selector for the confirmation on click event
         * @param message Message to display asking for confirmation to perform action
         * @private
         */
        _confirm: function(selector, message) {
            $(selector).on('click', function() {
                return confirm(message);
            });
        }
    });
})(jQuery);