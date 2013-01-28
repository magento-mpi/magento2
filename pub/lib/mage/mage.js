/**
 * {license_notice}
 *
 * @category    mage
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
(function($, window) {
    "use strict";
    $.extend(true, $, {
        mage: {
            /**
             * Url redirection after a specified timeout.
             * @param {string} url
             * @param {Integer} timeout
             */
            urlRedirectTimeout: function(url, timeout) {
                setTimeout(function() {
                    window.location.href = url;
                }, timeout);
            }
        }
    });
})(jQuery, window);
