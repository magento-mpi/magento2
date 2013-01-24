/**
 * {license_notice}
 *
 * @category    Sales Orders and Returns
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true, jquery:true*/
(function($) {
    "use strict";
    $.widget('mage.ordersReturns', {
        options: {
            zipCode: '#oar-zip', // Search by zip code.
            emailAddress: '#oar-email', // Search by email address.
            searchType: '#quick-search-type-id' // Search element used for choosing between the two.
        },

        _create: function() {
            $(this.options.searchType).on('change', $.proxy(this._onChange, this));
            this._showIdentifyBlock($(this.options.searchType).val());
        },

        /**
         * Handle onchange event for the select element when choosing either by zip code or email address.
         * @private
         * @param e - Change event.
         */
        _onChange: function(e) {
            this._showIdentifyBlock($(e.target).val());
        },

        /**
         * Show either the search by zip code option or the search by email address option.
         * @private
         * @param value - Value chosen in the select element, either 'zip' or 'email'.
         */
        _showIdentifyBlock: function(value) {
            if (value === 'zip') {
                $(this.options.zipCode).show();
                $(this.options.emailAddress).hide();
            } else {
                $(this.options.zipCode).hide();
                $(this.options.emailAddress).show();
            }
        }
    });
})(jQuery);
