/**
 * {license_notice}
 *
 * @category    mage
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint jquery:true*/
(function($, window) {
    "use strict";
    $.extend(true, $, {
        mage: {
            constant: {
                KEY_BACKSPACE: 8,
                KEY_TAB: 9,
                KEY_RETURN: 13,
                KEY_ESC: 27,
                KEY_LEFT: 37,
                KEY_UP: 38,
                KEY_RIGHT: 39,
                KEY_DOWN: 40,
                KEY_DELETE: 46,
                KEY_HOME: 36,
                KEY_END: 35,
                KEY_PAGEUP: 33,
                KEY_PAGEDOWN: 34
            },

            /**
             * Url redirection
             * @param {string} url
             * @param {Integer} time
             */
            urlRedirectTimeout: function(url, time) {
                setTimeout(function() {
                    window.location.href = url;
                }, time);
            }
        }
    });
})(jQuery, window);