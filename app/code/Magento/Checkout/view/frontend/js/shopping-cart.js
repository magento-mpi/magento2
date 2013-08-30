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
    $.widget('mage.shoppingCart', {
        _create: function() {
            if ($(this.options.updateCartActionContainer).length > 0) { /* <!--[if lt IE 8]> Only */
                $(this.options.emptyCartButton).on('click', $.proxy(function() {
                    $(this.options.emptyCartButton).attr('name', 'update_cart_action_temp');
                    $(this.options.updateCartActionContainer)
                        .attr('name', 'update_cart_action').attr('value', 'empty_cart');
                }, this));
            }
            $(this.options.continueShoppingButton).on('click', $.proxy(function() {
                location.href = this.options.continueShoppingUrl;
            }, this));
        }
    });
})(jQuery);
