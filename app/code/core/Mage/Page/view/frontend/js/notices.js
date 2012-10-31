/**
 * {license_notice}
 *
 * @category    notices
 * @package     js
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
(function ($) {
    $(document).ready(function () {
        var _data = {
            cookieBlockSelector: undefined,
            cookieAllowButtonSelector: undefined,
            cookieName: undefined,
            cookieValue: undefined,
            cookieExpires: undefined,
            noCookiesUrl: undefined
        };

        $.mage.event.trigger('mage.nocookies.initialize', _data);

        $(_data.cookieBlockSelector).show();
        $(_data.cookieAllowButtonSelector).on('click', function () {
            $.mage.cookies.set(_data.cookieName, _data.cookieValue, _data.cookieExpires);
            if ($.mage.cookies.get(_data.cookieName)) {
                window.location.reload();
            } else {
                window.location.href = _data.noCookiesUrl;
            }
        });
    });
})(jQuery);
