/**
 * {license_notice}
 *
 * @category    Mage
 * @package     js
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint eqnull:true browser:true jquery:true*/
(function($) {
    $.extend(true, $, {
        mage: {
            cookies: (function() {
                this.set = function(name, value) {
                    var expires = arguments[2] || $.cookie.defaults.expires;
                    var path = arguments[3] || $.cookie.defaults.path;
                    var domain = arguments[4] || $.cookie.defaults.domain;
                    var secure = arguments[5] || $.cookie.defaults.secure;
                    document.cookie = name + "=" + encodeURIComponent(value) +
                        ((expires == null) ? "" : ("; expires=" + expires.toGMTString())) +
                        ((path == null) ? "" : ("; path=" + path)) +
                        ((domain == null) ? "" : ("; domain=" + domain)) +
                        ((secure === true) ? "; secure" : "");
                };
                this.get = function(name) {
                    var arg = name + "=";
                    var alen = arg.length;
                    var clen = document.cookie.length;
                    var i = 0;
                    var j = 0;
                    while (i < clen) {
                        j = i + alen;
                        if (document.cookie.substring(i, j) === arg) {
                            return $.mage.cookies.getCookieVal(j);
                        }
                        i = document.cookie.indexOf(" ", i) + 1;
                        if (i === 0) {
                            break;
                        }
                    }
                    return null;
                };
                this.clear = function(name) {
                    if($.mage.cookies.get(name)) {
                        $.mage.cookies.set(name, "", new Date("Jan 01 1970 00:00:01 GMT"));
                    }
                };
                this.getCookieVal = function(offset) {
                    var endstr = document.cookie.indexOf(";", offset);
                    if(endstr === -1){
                        endstr = document.cookie.length;
                    }
                    return decodeURIComponent(document.cookie.substring(offset, endstr));
                };
                return this;
            }())
        }
    });
})(jQuery);
