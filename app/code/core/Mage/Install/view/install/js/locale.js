/**
 * {license_notice}
 *
 * @category    install locale
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true */
/*global location:true */
(function ($) {
    $(document).ready(function () {
        // Trigger initialize event
        var installData = { changeUrl: null };
        $.mage.event.trigger("mage.install.initialize", installData);
        // Setting php session for locale, timezone and currency
        $('#locale').on('change', function () {
            var url = installData.changeUrl + 'locale/' + $('#locale').val() + '/?timezone=' + $('#timezone').val() + '&amp;currency=' + $('#currency').val();
            $(location).attr('href', url);
        });
    });
})(jQuery);
