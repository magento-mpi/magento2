/**
 * {license_notice}
 *
 * @category    cart
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
(function ($) {
    $.extend(true, $, {
        mage: {
            decorator: (function () {
                /**
                 * Decorate a list (e.g. a <ul> containing <li>) recursively if specified.
                 * @param {string} list
                 * @param {boolean} isRecursive
                 */
                this.list = function (list, isRecursive) {
                    var items;
                    if ($(list).length > 0) {
                        if (typeof(isRecursive) === undefined) {
                            items = $(list).find('li');
                        } else if (isRecursive) {
                            items = $(list).find('li');
                        } else {
                            items = $(list).children();
                        }
                        this.general(items, ['odd', 'even', 'last']);
                    }
                };

                /**
                 * Annotate a set of DOM elements with decorator classes.
                 * @param {Object} elements
                 * @param {array} decoratorParams
                 */
                this.general = function (elements, decoratorParams) {
                    var allSupportedParams = {
                        even: 'odd', // Flip jQuery odd/even so that index 0 is odd.
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

                this.table = function (table, instanceOptions) {
                    if ($(table).length > 0) {
                        // set default options
                        var _options = {
                            'tbody': false,
                            'tbody tr': ['odd', 'even', 'first', 'last'],
                            'thead tr': ['first', 'last'],
                            'tfoot tr': ['first', 'last'],
                            'tr td': ['last']
                        };
                        var _table = $(table);
                        var _this = this;
                        $.extend(_options, instanceOptions || {});
                        // Decorator
                        $.each(_options, function (key, value) {
                            if (_options[key]) {
                                if (key === 'tr td') {
                                    $.each(_table.find('tr'), function () {
                                        _this.general($(this).find('TD'), _options['tr td']);
                                    });
                                } else {
                                    _this.general(_table.find(key), value);
                                }
                            }
                        });
                    }
                };
                return this;
            }())
        }
    });
})(jQuery);
