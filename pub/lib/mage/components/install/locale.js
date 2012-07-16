/**
 * {license_notice}
 *
 * @category    install locale
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */

/*jshint eqnull:true */
(function ($) {
    $(document).ready(function () {
        // Trigger initalize event
        mage.install = {};
        mage.event.trigger("mage.install.initialize", mage.install);
        // Setting php session for locale, timezone and currency
        $('#locale').on('change', function () {
            var url = mage.install.changeUrl + 'locale/' + $('#locale').val() + '/?timezone=' + $('#timezone').val() + '&amp;currency=' + $('#currency').val();
            //demo
            if ($('#locale').val() === 'de_DE') {
                $.cookie(mage.language.cookieKey, 'de', { path: '/' });
            }
            $(location).attr('href', url);

        });
    });
}(jQuery));
