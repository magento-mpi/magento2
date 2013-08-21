/**
 * {license_notice}
 *
 * @category    CE
 * @package     CE_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
(function($, window) {
    "use strict";
    $.widget('mage.paypalCheckout', {
        /**
         * Initialize store credit events
         * @private
         */
        _create: function() {
            this.element.on('click', '[data-action="checkout-form-submit"]', $.proxy(function(e) {
                var returnUrl = $(e.target).data('checkout-url');
                if (this.options.confirmUrl && this.options.confirmMessage) {
                    if (window.confirm(this.options.confirmMessage)) {
                        returnUrl = this.options.confirmUrl;
                    }
                }
                if (this.options.isCatalogProduct) {
                    $(this.options.paypalCheckoutSelector).val(returnUrl);
                    $(this.options.productAddToCartForm).submit();
                } else {
                    $.mage.redirect(returnUrl);
                }
            }, this));
        }
    });
})(jQuery, window);