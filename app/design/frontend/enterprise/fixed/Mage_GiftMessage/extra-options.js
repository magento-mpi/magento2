/**
* {license_notice}
*
* @category    design
* @package     enterprise_default
* @copyright   {copyright}
* @license     {license_link}
*/
/*jshint jquery:true*/
(function($) {
    "use strict";
    $.widget('mage.extraOptions', {
        options: {
            events: 'billingSave shippingSave',
            additionalContainer: '#onepage-checkout-shipping-method-additional-load'
        },

        /**
         * Set up event handler for requesting any additional extra options from the backend. Extra
         * options include Gift Receipt and Printed Card.
         * @private
         */
        _create: function() {
            var _this = this;
            this.element.on(this.options.events, function() {
                $.ajax({
                    url: _this.options.additionalUrl,
                    type: 'post',
                    success: function(response) {
                        $(_this.options.additionalContainer).html(response).trigger('contentUpdated');
                    }
                });
            });
        }
    });
})(jQuery);
