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
    var location = window.location;

    /**
     * @depricated
     */
    $.extend(true, $, {
        mage: {
            urlRedirect: function(url, history, timeout) {
                return  setTimeout(history ? location.replace : location.assign, timeout, url);
            },
            reloadPage: function(timeout) {
                return setTimeout(this.reloadPage, timeout);
            }
        }
    });

    /* Redirect Utility */
    $.extend(true, $, {
        mage: {
            redirect: function(url, type, timeout, forced) {
                forced = forced ? true : false;
                timeout = timeout ? timeout : 0;
                type = type ? type : "assign";
                var _redirect = function() {
                    location[type](type === 'reload' ? forced : url);
                }
                if(timeout) {
                    setTimeout(_redirect, timeout);
                } else {
                    _redirect();
                }
            }
        }
    });
})(jQuery, window);
