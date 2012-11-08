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
        options: {
        },
        _create: function () {
            this.giftCardCode = $(this.options.giftCardCodeSelector);
            $(this.options.applyButton).on('click', $.proxy(function () {
                this.giftCardCode.attr('data-validate', '{required:true}');
                this.element.mage().validate().submit();
            }, this));

            $(this.options.checkStatus).on('click', $.proxy(function () {
                var giftCardStatusId = this.options.giftCardStatusId;
                var giftCardSpinnerId = $(this.options.giftCardSpinnerId);
                $.ajax({
                    url: this.options.giftCardStatusUrl,
                    type: 'post',
                    cache: false,
                    data: {'giftcard_code': $(this.options.giftCardCodeSelector).val()},
                    beforeSend: function () {
                        giftCardSpinnerId.show();
                    },
                    success: function (response) {
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
