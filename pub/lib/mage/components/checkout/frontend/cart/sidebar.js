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
        var checkout = {
            //Filled in initialization event
            cartId:null,
            isRecursive:null  ,
            checkOutUrl:null,
            checkOutBtn:null,
            confirmMessage:null,
            checkOutListCloseBtn:null
        };
        mage.event.trigger('mage.checkout.initialize', checkout);
        mage.decorator.list(checkout.cartId, checkout.isRecursive);
        $(checkout.checkOutBtn).on('click', function () {
            $(location).attr('href', checkout.checkOutUrl);
        });

        $(checkout.checkOutListCloseBtn).on('click', function () {
            return confirm(checkout.confirmMessage);
        });

    });
}(jQuery));
