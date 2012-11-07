/**
 * {license_notice}
 *
 * @category    checkout gift card
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
(function ($) {
    $.widget('mage.giftCard', {
        _create: function () {
            $(this.options.checkStatus).on('click', $.proxy(function () {
                if (!$(this.element).validation().valid()) {
                    return;
                }
                var giftCardStatusId = this.options.giftCardStatusId;
                var giftCardSpinnerId = $(this.options.giftCardSpinnerId);
                var messages = this.options.messages;
                $.ajax({
                    url: this.options.giftCardStatusUrl,
                    type: 'post',
                    cache: false,
                    data: {'giftcard_code': $(this.options.giftCardCodeSelector).val()},
                    beforeSend: function () {
                        giftCardSpinnerId.show();
                    },
                    success: function (response) {
                        if ($(messages)) {
                            $(messages).hide();
                        }
                        $(giftCardStatusId).html(response);
                    },
                    complete: function (response) {
                        giftCardSpinnerId.hide();
                    }
                });
            }, this));
        }
    });
})(jQuery);
