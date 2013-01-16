/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Persistent
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
            $(this.options.splitButton + ' ' + this.options.arrowButton).on('click', $.proxy(this._toggleDropDown, this));
            $(document).on('click', $.proxy(this._hideDropDown, this));
        },

        /**
         * Toggle css class for the split button to hide or show drop down menu
         * @private
         * @param {Object} e
         */
        _toggleDropDown: function(e) {
            $(e.target).closest(this.options.splitButton).toggleClass(this.options.activeClass);
            return false;
        },

        /**
         * Hide drop down menu when clicked any where on the page
         * @private
         * @param {Object} e
         */
        _hideDropDown: function(e) {
            $('.' + this.options.activeClass).removeClass(this.options.activeClass);
        }
    });
})(jQuery);
