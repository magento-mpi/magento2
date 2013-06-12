/**
 * {license_notice}
 *
 * @category    mage customer view
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true, jquery:true*/
/*global confirm:true*/
(function($, window) {
    "use strict";
    $.widget('mage.address', {
        /**
         * Options common to all instances of this widget.
         * @type {Object}
         */
        options: {
            deleteConfirmMessage: $.mage.__('Are you sure you want to delete this address?')
        },

        /**
         * Bind event handlers for adding and deleting addresses.
         * @private
         */
        _create: function() {
            $(this.options.addAddress).on('click', $.proxy(this._addAddress, this));
            $(this.options.deleteAddress).on('click', $.proxy(this._deleteAddress, this));
        },

        /**
         * Add a new address.
         * @private
         */
        _addAddress: function() {
            window.location = this.options.addAddressLocation;
        },

        /**
         * Delete the address whose id is specified in a data attribute after confirmation from the user.
         * @private
         * @param {Event}
         * @return {Boolean}
         */
        _deleteAddress: function(e) {
            if (confirm(this.options.deleteConfirmMessage)) {
                if (typeof $(e.target).parent().data('address') !== 'undefined') {
                    window.location = this.options.deleteUrlPrefix + $(e.target).parent().data('address');
                }
                else {
                    window.location = this.options.deleteUrlPrefix + $(e.target).data('address');
                }
            }
            return false;
        }
    });
})(jQuery, window);
