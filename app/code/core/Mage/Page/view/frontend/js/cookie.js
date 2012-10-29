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
        $.mage.event.trigger('mage.cookie.init', cookieInit);
        $.extend($.cookie.defaults, cookieInit);
    });
})(jQuery);