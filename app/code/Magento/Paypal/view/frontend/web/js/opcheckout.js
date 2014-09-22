/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
define([
    "jquery",
    "jquery/ui",
    "Magento_Checkout/js/opc-order-review"
], function($){
    "use strict";

    $.widget('mage.opcheckoutPaypalIframe', $.mage.opcOrderReview, {
        options: {
            review: {
                submitContainer: '#checkout-review-submit'
            }
        },

        _create: function() {
            var events = {};
            events['contentUpdated' + this.options.review.container] = function() {
                var paypalIframe = this.element.find(this.options.review.container)
                    .find('[data-container="paypal-iframe"]');
                if (paypalIframe.length) {
                    paypalIframe.show();
                    $(this.options.review.submitContainer).hide();
                }
            };
            this._on(events);
        }
    });
});