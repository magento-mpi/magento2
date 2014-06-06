/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

;
(function($) {
    'use strict';

    $(document).ready(function() {

        if ($('body').hasClass('checkout-cart-index')) {
            if ($('#co-shipping-method-form .fieldset.rates').length > 0 && $('#co-shipping-method-form .fieldset.rates :checked').length === 0 ) {
                $("#block-shipping").on("collapsiblecreate" ,function() {
                    $("#block-shipping").collapsible("forceActivate");
                });
            }
        }
    });

})(window.jQuery);
