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
            $('.cart.summary > .block > .title').dropdown({autoclose:false, menu:'.title + .content'});
            if ($('#co-shipping-method-form .fieldset.rates').length > 0 && $('#co-shipping-method-form .fieldset.rates :checked').length === 0 ) {
                $('.block.shipping > .title').addClass('active');
                $('.block.shipping').addClass('active');
            }
        }

        if ($('[role="navigation"]').length) {
            $('[role="navigation"]').navigationMenu({
                responsive: true,
                submenuContiniumEffect: true
            });
        } else {
            $('<nav class="navigation" role="navigation"></nav>').navigationMenu({
                responsive: true,
                submenuContiniumEffect: true
            });
        }
    });

})(window.jQuery);
