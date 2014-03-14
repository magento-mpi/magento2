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
     * MsgBox Widget checks if message box is displayed and sets cookie
     */
    $.widget('mage.msgBox', {
        options: {
            msgBoxCookieName: 'message_box_display',
            msgBoxSelector: '.main div.messages'
        },
        _create: function() {
            if ($(this.options.msgBoxSelector).text().trim().length > 0) {
                var cookieExpires = new Date(new Date().getTime() + 315360000);
                $.mage.cookies.set(this.options.msgBoxCookieName, 1, {expires: cookieExpires});
                $(this.options.msgBoxSelector).show();
            } else {
                $(this.options.msgBoxSelector).hide();
            }
        }
    });
})(jQuery);

jQuery(document).ready(function($){
    $('body').msgBox();
});
