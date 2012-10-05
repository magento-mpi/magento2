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
            listSelector: null,
            removeConfirmMessage: null,
            removeSelector: null,
            clearAllConfirmMessage: null,
            clearAllSelector: null
        };

        $.mage.event.trigger('mage.compare.initialize', _compare);
        $(_compare.listSelector).decorate('list', true);

        function _confirmMessage(selector, message) {
            $(selector).on('click', function () {
                return confirm(message);
            });
        }

        _confirmMessage(_compare.removeSelector, _compare.removeConfirmMessage);
        _confirmMessage(_compare.clearAllSelector, _compare.clearAllConfirmMessage);
    });
})(jQuery);