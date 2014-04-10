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
            $(this.options.searchType).on('change', $.proxy(this._showIdentifyBlock, this)).trigger('change');
        },

        /**
         * Show either the search by zip code option or the search by email address option.
         * @private
         * @param e - Change event. Event target value is either 'zip' or 'email'.
         */
        _showIdentifyBlock: function(e) {
            var value = $(e.target).val();
            $(this.options.zipCode).toggle(value === 'zip');
            $(this.options.emailAddress).toggle(value === 'email');
        }
    });
})(jQuery);
