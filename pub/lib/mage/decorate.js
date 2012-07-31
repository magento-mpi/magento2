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
            if (!isRecursive) {
                items = $(list).find('li');
            } else {
                items = $(list).children();
            }
            this.general(items, ['odd', 'even', 'last']);
        }

    };

    mage.decorator.general = function (elements, decoratorParams) {
        // Flip  default jQuery odd even selection
        var allSupportedParams = {
            even: 'odd',
            odd: 'even',
            last: 'last',
            first: 'first'
        };
        var decoratorParams = decoratorParams || allSupportedParams;
        if (elements) {
            $.each(decoratorParams, function (index, param) {
                elements.filter(':' + param).removeClass('odd even').addClass(allSupportedParams[param]);
            });
        }
    }

}(jQuery));