/**
 * {license_notice}
 *
 * @category    install locale
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true */
/*global location:true mage:true */
(function ($) {
    $(document).ready(function () {
        // Trigger initalize event
        mage.install = {};
        mage.event.trigger("mage.install.initialize", mage.install);
        // Setting php session for locale, timezone and currency
        $('#locale').on('change', function () {
            var url = mage.install.changeUrl + 'locale/' + $('#locale').val() + '/?timezone=' + $('#timezone').val() + '&amp;currency=' + $('#currency').val();
            $(location).attr('href', url);
        });
    });
}(jQuery));
