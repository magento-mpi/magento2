/**
 * {license_notice}
 *
 * @category    mage side bar
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
(function ($) {
    $(document).ready(function () {
        var checkout = {};
        mage.event.trigger('mage.checkout.initialize', checkout);
        mage.decorator.list(checkout.cartId, checkout.nonRecursive);
        $(':button[title=' + checkout.title + ']').on('click', function () {
            $(location).attr('href', checkout.checkOutUrl);
        })

    });
}(jQuery));
