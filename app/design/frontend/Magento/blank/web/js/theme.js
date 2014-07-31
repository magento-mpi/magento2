/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
define([
    "jquery",
    "jquery/ui"
],function($) {
    'use strict';

    $(function() {

        if ($('body').hasClass('checkout-cart-index')) {
            if ($('#co-shipping-method-form .fieldset.rates').length > 0 && $('#co-shipping-method-form .fieldset.rates :checked').length === 0 ) {
                $("#block-shipping").on("collapsiblecreate" ,function() {
                    $("#block-shipping").collapsible("forceActivate");
                });
            }
        }
        if($('.cart-summary').length){
            $('.cart-summary').mage('sticky', {
                container: '.cart-container'
            });
        }

        $( ".panel.header > .header.links" ).clone().appendTo( "#store\\.links" );
    });

});
