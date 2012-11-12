/**
 * {license_notice}
 *
 * @category    PageCache
 * @package     js
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
(function ($) {
    $(document).ready(function () {
        var _data = {
            cookieName: undefined,
            cookieLifetime: undefined,
            cookieExpireAt: null
        };
        $.mage.event.trigger('mage.nocachecookie.initialize', _data);
        if (_data.cookieLifetime > 0) {
            _data.cookieExpireAt = new Date();
            _data.cookieExpireAt.setTime(_data.cookieExpireAt.getTime() + _data.cookieLifetime * 1000);
        }
        $.mage.cookies.set(_data.cookieName, 1, _data.cookieExpireAt);
    });
})(jQuery);
