/**
 * {license_notice}
 *
 * @category    cart
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
(function($) {
    $.extend(true, $, {
        mage: {
            decorator: (function() {
                this.list = function (list, isRecursive) {
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
                return this;
            }())
        }
    });
})(jQuery);
