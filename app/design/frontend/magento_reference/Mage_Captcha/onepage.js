/**
 * {license_notice}
 *
 * @category    mage
 * @package     captcha
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
(function($, window, document, undefined) {
    "use strict";

    $(document).on("login:setMethod", function() {
        $("[role='guest_checkout'], [role='register_during_checkout']").hide();
        var type = ($("#login\\:guest").is(':checked')) ? 'guest_checkout' : 'register_during_checkout';
        $("[role='" + type + "']").show();
    });

    $(document).on('billing-request:completed', function() {
        if (typeof window.checkout !== 'undefined') {
            $(".captcha-reload:visible").trigger("click");
        }
    });
})(jQuery, window, document);