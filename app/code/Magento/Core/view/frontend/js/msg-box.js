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
                $.mage.cookies.set(this.options.msgBoxCookieName, null, {expires: null});
            }
        }
    });
})(jQuery);

jQuery(document).ready(function($){
    $('body').msgBox();
});
