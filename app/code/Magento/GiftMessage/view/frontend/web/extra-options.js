/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
/*jshint jquery:true*/
define([
    "jquery",
    "jquery/ui"
], function($){
    "use strict";

    $.widget('mage.extraOptions', {
        options: {
            events: 'billingSave shippingSave',
            additionalContainer: '#onepage-checkout-shipping-method-additional-load'
        },

        /**
         * Set up event handler for requesting any additional extra options from the backend.
         * @private
         */
        _create: function() {
            this.element.on(this.options.events, $.proxy(this._addExtraOptions, this));
        },

        /**
         * Fetch the extra options using an Ajax call. Extra options include Gift Receipt and
         * Printed Card.
         * @private
         */
        _addExtraOptions: function() {
            $.ajax({
                url: this.options.additionalUrl,
                context: this,
                type: 'post',
                async: false,
                success: function(response) {
                    $(this.options.additionalContainer).html(response).trigger('contentUpdated');
                }
            });
        }
    });
    
    return $.mage.extraOptions;
});