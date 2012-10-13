/**
 * {license_notice}
 *
 * @category    mage checkout shopping cart
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
(function($) {
    $(document).ready(function() {
        var initData = {
            updateCartActionContainer: undefined /* <!--[if lt IE 8]> Only */
        };

        $.mage.event.trigger('mage.shoppingCart.initialize', initData);

        if ($(initData.updateCartActionContainer).length > 0) { /* <!--[if lt IE 8]> Only */
            $(initData.emptyCartButton).on('click', function() {
                $(initData.emptyCartButton).attr('name', 'update_cart_action_temp');
                $(initData.updateCartActionContainer).attr('name', 'update_cart_action');
                $(initData.updateCartActionContainer).attr('value', 'empty_cart');
            });
        }

        $(initData.continueShoppingButton).on('click', function() {
            location.href = initData.continueShoppingUrl;
        });
    });
})(jQuery);
