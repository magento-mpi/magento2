/**
 * {license_notice}
 *
 * @category    checkout coupon discount codes
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
(function ($) {
    $.widget('mage.discountCode', {
        options: {
        },
        _create: function() {
            this.couponCode = $(this.options.couponCodeSelector);
            this.removeCoupon = $(this.options.removeCouponSelector);

            $(this.options.applyButton).on('click', $.proxy(function() {
                this.couponCode.attr('data-validate', '{required:true}');
                this.removeCoupon.attr('value', '0');
                this.element.mage().validate().submit();
            }, this));

            $(this.options.cancelButton).on('click', $.proxy(function() {
                this.couponCode.removeAttr('data-validate');
                this.removeCoupon.attr('value', '1');
                this.element.submit();
            }, this));
        }
    });
})(jQuery);
