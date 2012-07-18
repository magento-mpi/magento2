/**
 * {license_notice}
 *
 * @category    frontend home menu
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */

(function ($) {
    // Default value for menu
    var menuInit = {
        showDelay: 100,
        hideDelay: 100,
        menuId: '#nav',
        parentClass: '.parent'
    };

    function show(subElement) {
        if (subElement.attr('hideTimeId')) {
            clearTimeout(subElement.attr('hideTimeId'));
        }
        subElement.attr('showTimeId', setTimeout(function () {
            if (!subElement.hasClass('shown-sub')) {
                subElement.addClass('shown-sub');
            }
        }, menuInit.showDelay));
    }

    function hide(subElement) {
        if (subElement.attr('showTimeId')) {
            clearTimeout(subElement.attr('showTimeId'));
        }
        subElement.attr('hideTimeId', setTimeout(function () {
            if (subElement.hasClass('shown-sub')) {
                subElement.removeClass('shown-sub');
            }
        }, menuInit.hideDelay));
    }

    $(document).ready(function () {
        // Trigger initalize event
        mage.event.trigger("mage.menu.initialize", menuInit);
        var menuSelector = menuInit.menuId + ' ' + menuInit.parentClass;
        $(menuSelector).on('mouseover', function () {
            $(this).addClass('over');
            show($(this).children('ul'));
        });
        $(menuSelector).on('mouseout', function () {
            $(this).removeClass('over');
            hide($(this).children('ul'));
        });
    });
}(jQuery));