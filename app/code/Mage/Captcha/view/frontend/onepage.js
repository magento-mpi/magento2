/**
 * {license_notice}
 *
 * @category    mage
 * @package     captcha
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
(function($, window, document) {
    "use strict";
    $(document).on("login",function() {
        $("[role='guest_checkout'], [role='register_during_checkout']").hide();
        var type = ($("#login\\:guest").is(':checked')) ? 'guest_checkout' : 'register_during_checkout';
        $("[role='" + type + "']").show();
    }).on('billingSave', function() {
            $(".captcha-reload:visible").trigger("click");
        });
})(jQuery, window, document);