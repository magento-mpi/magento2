/**
 * {license_notice}
 *
 * @category    design
 * @package     base_default
 * @copyright   {copyright}
 * @license     {license_link}
 */
(function ($, undefined) {
    "use strict";
    $(document).on("login:setMethod", function() {
        var inputPrefix = 'captcha-input-box-',
            imagePrefix = 'captcha-image-box-';

        $("[id^='" + inputPrefix + "'], [id^='" + imagePrefix + "']").addClass("hidden");

        if ($("#login\\:guest").is(':checked')) {
            $("#" + inputPrefix + "guest_checkout").removeClass("hidden");
            $("#" + imagePrefix + "guest_checkout").removeClass("hidden");
        }
        if ($("#login\\:register").is(':checked')) {
            $("#" + inputPrefix + "register_during_checkout").removeClass("hidden");
            $("#" + imagePrefix + "register_during_checkout").removeClass("hidden");
        }
    });
    $(document).on('billing-request:completed', function() {
        if (typeof window.checkout != 'undefined') {
            $("#guest_checkout, #register_during_checkout").captcha.refresh();
        }
    });
    $("#captcha-reload").on("click", function() {
        $(this).captcha.refresh();
    });
})(jQuery);

/**
 * Need to remove when we refactor onepage checkout
 * @deprecated
 */
document.observe('login:setMethod', function(event) {
    jQuery(document).trigger('login:setMethod');
});

/**
 * Need to remove when we refactor onepage checkout
 * @deprecated
 */
document.observe('billing-request:completed', function(event) {
    jQuery(document).trigger('billing-request:completed');
});