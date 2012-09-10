/**
 * {license_notice}
 *
 * @category    cookie
 * @package     js
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
/*global mage:true */
(function ($) {
    $(document).ready(function () {
        var cookieInit = {
        // Default values
            path: '/',
            domain: document.domain
        };
        // Trigger initialize event
        mage.event.trigger('mage.cookie.init', cookieInit);

        $.cookie.defaults = {
            path: cookieInit.path,
            domain: cookieInit.domain
        };
    });
}(jQuery));