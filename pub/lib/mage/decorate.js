/**
 * {license_notice}
 *
 * @category    cart
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */

/*jshint eqnull:true */
(function ($) {
    mage.decorator = {};

    mage.decorator.list = function (list, isRecursive) {
        var items;
        if ($(list).length > 0) {
            if (isRecursive) {
                items = $(list).children();
            } else {
                items = $(list).find('li');
            }
            this.general(items, ['odd', 'even', 'last']);
        }

    };

    mage.decorator.general = function (elements, decoratorParams) {
        // Flip default jQuery odd even selection to work it intuitively, assuming index 0 to be the 1st element (odd)
        var allSupportedParams = {
            even: 'odd',
            odd: 'even',
            last: 'last',
            first: 'first'
        };

        decoratorParams = decoratorParams || allSupportedParams;

        if (elements) {
            $.each(decoratorParams, function (index, param) {
                if (param === 'even' || param === 'odd') {
                    elements.filter(':' + param).removeClass('odd even').addClass(allSupportedParams[param]);
                } else {
                    elements.filter(':' + param).addClass(allSupportedParams[param]);
                }
            });
        }
    };
}(jQuery));