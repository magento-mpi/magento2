/**
 * GiftCard client side validation rules
 *
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCard
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*global productConfigure:true*/
(function ($) {
    $.validator.addMethod('giftcard-min-amount', function(v) {
        return (productConfigure.giftcardConfig.parsePrice(v) >= productConfigure.giftcardConfig.minAllowedAmount);
    }, 'Entered amount is too low');

    $.validator.addMethod('giftcard-max-amount', function(v) {
        if (productConfigure.giftcardConfig.maxAllowedAmount === 0) {
            return true;
        }
        return (productConfigure.giftcardConfig.parsePrice(v) <= productConfigure.giftcardConfig.maxAllowedAmount);
    }, 'Entered amount is too high');
})(jQuery);
