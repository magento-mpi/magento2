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
    mage.decorator.list = function (list, nonRecursive) {
        var items;
        if ($(list).length > 0) {
            if (typeof nonRecursive != 'undefined') {
                items = $(list).find('li');
            } else {
                items = $(list).children();
            }
            this.general(items, ['odd', 'even', 'last']);
        }

    };

    mage.decorator.general = function (elements, decorateParams) {
        var allSupportedParams = ['odd', 'even', 'first', 'last'];
        var _decorateParams = {};
        var total = elements.length;
        var k;

        if (total) {
            // determine params called
            if (typeof(decorateParams) == 'undefined') {
                decorateParams = allSupportedParams;
            }
            if (!decorateParams.length) {
                return;
            }
            for (k in allSupportedParams) {
                _decorateParams[allSupportedParams[k]] = false;
            }
            for (k in decorateParams) {
                _decorateParams[decorateParams[k]] = true;
            }

            // decorate elements
            if (_decorateParams.first && !$(elements[0]).hasClass('first')) {
                $(elements[0]).addClass('first');
            }
            if (_decorateParams.last && !$(elements[0]).hasClass('last')) {
                $(elements[total - 1]).addClass('last');
            }
            for (var i = 0; i < total; i++) {
                if ((i + 1) % 2 === 0 && _decorateParams.even) {
                    $(elements[i]).removeClass('odd');
                    if (!$(elements[i]).hasClass('even')) {
                        $(elements[i]).addClass('even');
                    }
                }
                if ((i + 1) % 2 === 1 && _decorateParams.odd) {
                    $(elements[i]).removeClass('even');
                    if (!$(elements[i]).hasClass('odd')) {
                        $(elements[i]).addClass('odd');
                    }
                }
            }
        }
    };
}(jQuery));