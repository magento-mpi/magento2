/**
 * {license_notice}
 *
 * @category    cookie
 * @package     js
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
(function ($) {
    $(document).ready(function () {
        var cookieInit = {
            expires: null,
            path: '/',
            domain: document.domain,
            secure: false
        };
        // Trigger initialize event
        $.mage.event.trigger('mage.cookie.init', cookieInit);

        $.cookie.defaults = {
            expires: cookieInit.expires,
            path: cookieInit.path,
            domain: cookieInit.domain,
            secure: cookieInit.secure
        };
    });
})(jQuery);