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
        if ($(list).length > 0) {
            if (typeof nonRecursive != 'undefined') {
                var items = $(list).find('li');
            } else {
                var items = $(list).children();
            }
            this.general(items, ['odd', 'even', 'last']);
        }

    };

    mage.decorator.general = function (elements, decorateParams) {
        var allSupportedParams = ['odd', 'even', 'first', 'last'];
        var _decorateParams = {};
        var total = elements.length;

        if (total) {
            // determine params called
            if (typeof(decorateParams) == 'undefined') {
                decorateParams = allSupportedParams;
            }
            if (!decorateParams.length) {
                return;
            }
            for (var k in allSupportedParams) {
                _decorateParams[allSupportedParams[k]] = false;
            }
            for (var k in decorateParams) {
                _decorateParams[decorateParams[k]] = true;
            }

            // decorate elements
            if (_decorateParams.first) {
                $(elements[0]).addClass('first');
            }
            if (_decorateParams.last) {
                $(elements[total - 1]).addClass('last');
            }
            for (var i = 0; i < total; i++) {
                if ((i + 1) % 2 == 0) {
                    if (_decorateParams.even) {
                        $(elements[i]).addClass('even');
                    }
                }
                else {
                    if (_decorateParams.odd) {
                        $(elements[i]).addClass('odd');
                    }
                }
            }
        }

    };

    mage.decorator.dataList = function (list) {
        var list = $(list);
        if (list.length > 1) {
            this.general(list.find('dt'), ['odd', 'even', 'last']);
            this.general(list.find('dd'), ['odd', 'even', 'last']);
        }
    }

}(jQuery));