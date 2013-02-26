/**
 * {license_notice}
 *
 * @category    checkout gift card
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
(function($) {
    "use strict";
    $.widget('mage.giftCard', {
        _create: function() {
            $(this.options.checkStatus).on('click', $.proxy(function() {
                if (this.element.validation().valid()) {
                    var giftCardStatusId = this.options.giftCardStatusId,
                        giftCardSpinnerId = $(this.options.giftCardSpinnerId),
                        messages = this.options.messages;
                    $.ajax({
                        url: this.options.giftCardStatusUrl,
                        type: 'post',
                        cache: false,
                        data: {'giftcard_code': $(this.options.giftCardCodeSelector).val()},
                        beforeSend: function() {
                            giftCardSpinnerId.show();
                        },
                        success: function(response) {
                            $(messages).hide();
                            $(giftCardStatusId).html(response);
                        },
                        complete: function() {
                            giftCardSpinnerId.hide();
                        }
                    });
                }
            }, this));
        }
    });
})(jQuery);
