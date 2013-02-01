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
})(jQuery, window);
