/**
 * Animated mini-shopping cart block
 *
 * {license_notice}
 *
 * @category    design
 * @package     enterprise_default
 * @copyright   {copyright}
 * @license     {license_link}
 */


(function ($) {
    $(document).ready(function () {

        var topCartInit = {};

        topCartInit.intervalDuration = 4000;
        mage.event.trigger('mage.checkout.initialize', topCartInit);

        topCartInit.container = $(topCartInit.container);
        topCartInit.closeButton = $(topCartInit.closeButton);
        topCartInit.element = topCartInit.container.parent();
        topCartInit.elementHeader = topCartInit.container.prev();
        topCartInit.interval = null;
        topCartInit.zIndex = null;

        var setzIndex = function (zindex) {
            if (typeof zindex !== 'undefined' && zindex !== null) {
                $(topCartInit.container).css('z-index',zindex);
            }
        }
        topCartInit.closeButton.on('click', function () {
            topCartInit.container.slideUp('slow', function () {
                setzIndex(topCartInit.zIndex);
                clearTimeout(topCartInit.interval);
            });
        });

        topCartInit.element.on('mouseleave', function () {
            topCartInit.interval = setTimeout(function () {
                topCartInit.closeButton.trigger('click');
            }, topCartInit.intervalDuration);
        });

        topCartInit.element.on('mouseenter', function () {
            clearTimeout(topCartInit.interval);
        });

        topCartInit.elementHeader.on('click', function () {
            $(topCartInit.container).slideToggle('slow', function () {
                topCartInit.zIndex = $(this).css('z-index');
                if ($(this).is(':visible')) {
                    setzIndex(972);
                } else {
                    setzIndex(topCartInit.zIndex);
                }
            });
        });

    });
}(jQuery));