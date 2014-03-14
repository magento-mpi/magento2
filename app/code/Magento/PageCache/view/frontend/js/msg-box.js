/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/*jshint browser:true jquery:true expr:true*/
(function ($) {
    "use strict";
    /**
     * MsgBox Widget checks if cookie for displaying message box is set, if so it displays box, in other case - not
     */
    $.widget('mage.msgBox', {
        options: {
            msgBoxCookieName: 'message_box_display'
        },
        _create: function() {
            var msgboxCookie = $.mage.cookies.get(this.options.msgBoxCookieName);
            var msgboxBlock = $('.main div.messages');

            if (msgboxCookie === "1") {
                msgboxBlock.show();
            } else {
                msgboxBlock.hide();
            }
        }
    });
})(jQuery);