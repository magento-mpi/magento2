/**
 * {license_notice}
 *
 * @category    CE
 * @package     CE_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
(function($, undefined) {
    "use strict";
    $.widget('mage.paypalCheckout', {
        /**
         * Initialize store credit events
         * @private
         */
        _create: function() {
            this.element.on('click', $.proxy(function(e) {
                var returnUrl = $(e.target).parent().prop("href");
                if (this.options.confirmUrl.length > 0) {
                    if (confirm(this.options.confirmMessage)) {
                        returnUrl = this.options.confirmUrl;
                    }
                }
                if (this.options.isCatalogProduct === '1') {
                    $(this.options.paypalCheckoutSelector).val(returnUrl);
                    $(this.options.productAddToCartForm).submit();
                    e.stopPropagation();
                }
            }, this));
        }
    });
})(jQuery);