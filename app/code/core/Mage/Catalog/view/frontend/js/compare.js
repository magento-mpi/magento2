/**
 * {license_notice}
 *
 * @category    mage compare list
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
/*global confirm:true*/
(function ($) {
    $(document).ready(function () {
        var _compare = {
            // Filled in initialization event
            listSelector: null,
            removeConfirmMessage: null,
            removeSelector: null,
            clearAllConfirmMessage: null,
            clearAllSelector: null,
            popUpWindowSelector: null,
            windowName: null, /* Name of window set from the name attribute of the element that invokes the click */
            windowURL: null, /* Url used for the popup */
            // Default values
            centerBrowser: 0, /* Center window over browser window? {1 (YES) or 0 (NO)}. overrides top and left. */
            centerScreen: 0, /* Center window over entire screen? {1 (YES) or 0 (NO)}. overrides top and left. */
            height: 600, /* Sets the height in pixels of the window. */
            left: 0, /* Left position when the window appears. */
            location: 0, /* Determines whether the address bar is displayed {1 (YES) or 0 (NO)}. */
            menubar: 0, /* Determines whether the menu bar is displayed {1 (YES) or 0 (NO)}. */
            resizable: 1, /* Whether the window can be resized {1 (YES) or 0 (NO)}. Can also be overloaded using resizable.*/
            scrollbars: 1, /* Determines whether scrollbars appear on the window {1 (YES) or 0 (NO)}. */
            status: 0, /* Whether a status line appears at the bottom of the window {1 (YES) or 0 (NO)}. */
            width: 820, /* Sets the width in pixels of the window. */
            top: 0, /* Top position when the window appears. */
            toolbar: 0 /* Determines whether a toolbar (includes the forward and back buttons) is displayed {1 (YES) or 0 (NO)}. */
        };
        $.mage.event.trigger('mage.compare.initialize', _compare);
        $.mage.decorator.list(_compare.listSelector, true);
        function _confirmMessage(selector, message) {
            $(selector).on('click', function () {
                return confirm(message);
            });
        }

        _confirmMessage(_compare.removeSelector, _compare.removeConfirmMessage);
        _confirmMessage(_compare.clearAllSelector, _compare.clearAllConfirmMessage);
        $(_compare.popUpWindowSelector).popupWindow(_compare);
    });
})(jQuery);