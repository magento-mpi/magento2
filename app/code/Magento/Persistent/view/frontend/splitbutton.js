/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Persistent
 * @copyright   {copyright}
 * @license     {license_link}
 */

/*jshint browser:true jquery:true*/
(function($, undefined) {
    "use strict";
    $.widget('mage.splitButton', {
        /**
         * options with default values
         */
        options: {
            splitButton: '.split-button',
            arrowButton: '.change',
            activeClass: 'active'
        },

        /**
         * Initialize split button events
         * @private
         */
        _create: function() {
            $(document).on('click', this.options.splitButton + ' > ' + this.options.arrowButton, $.proxy(this._toggleDropDown, this));
            $(document).on('click', $.proxy(this._hideDropDown, this));
        },

        /**
         * Toggle css class for the split button to hide or show drop down menu
         * Saves current state of the target. Closes all open drop downs and then
         * depending on the stored state the target drop down is toggled.
         * @private
         * @param {Object} e
         */
        _toggleDropDown: function(e) {
            var state = $(e.target).closest(this.options.splitButton).hasClass(this.options.activeClass);
            this._hideDropDown();
            if (state) {
                this._hideDropDown();
            } else {
                $(e.target).closest(this.options.splitButton).addClass(this.options.activeClass);
            }
            return false;
        },

        /**
         * Hide all the drop down menus when clicked any where on the page
         * @private
         */
        _hideDropDown: function() {
            $(document).find(this.options.splitButton).removeClass(this.options.activeClass);
        }
    });
})(jQuery);
