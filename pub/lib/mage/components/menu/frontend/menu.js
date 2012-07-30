/**
 * {license_notice}
 *
 * @category    frontend home menu
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */

(function ($) {
    // Default fields to initialize for menu
    var menuInit = {
        showDelay: 100,
        hideDelay: 100,
        menuSelector: '#nav .parent'
    };

    function show(subElement) {
        if (subElement.data('hideTimeId')) {
            clearTimeout(subElement.data('hideTimeId'));
        }
        subElement.data('showTimeId', setTimeout(function () {
            if (!subElement.hasClass('shown-sub')) {
                subElement.addClass('shown-sub');
            }
        }, menuInit.showDelay));
    }

    function hide(subElement) {
        if (subElement.data('showTimeId')) {
            clearTimeout(subElement.data('showTimeId'));
        }
        subElement.data('hideTimeId', setTimeout(function () {
            if (subElement.hasClass('shown-sub')) {
                subElement.removeClass('shown-sub');
            }
        }, menuInit.hideDelay));
    }

    $(document).ready(function () {
        // Trigger initalize event
        mage.event.trigger("mage.menu.initialize", menuInit);
        $(menuInit.menuSelector).on('mouseover', function () {
            $(this).addClass('over');
            show($(this).children('ul'));
        });
        $(menuInit.menuSelector).on('mouseout', function () {
            $(this).removeClass('over');
            hide($(this).children('ul'));
        });
    });
}(jQuery));