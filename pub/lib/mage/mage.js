/**
 * {license_notice}
 *
 * @category    mage
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
(function($, window, undefined) {
    "use strict";

    /* Redirect Utility */
    $.extend(true, $, {
        mage: {

            /**
             * Method handling redirects and page refresh
             * @param url       - redirect URL
             * @param type      - 'assign','reload','replace'
             * @param timeout   - timeout in milliseconds before processing the redirect or reload
             * @param forced    - true|false used for 'reload' only
             */
            redirect: function(url, type, timeout, forced) {
                forced = forced ? true : false;
                timeout = timeout ? timeout : 0;
                type = type ? type : "assign";
                var _redirect = function() {
                    window.location[type](type === 'reload' ? forced : url);
                };
                if (timeout) {
                    setTimeout(_redirect, timeout);
                } else {
                    _redirect();
                }
            }
        }
    });
})(jQuery, window);
