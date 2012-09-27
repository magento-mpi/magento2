/**
 * {license_notice}
 *
 * @category    Mage
 * @package     mage.translate
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true*/
(function($) {
    $.extend(true, $, {
        mage: {
            translate: (function() {
                /**
                 * Key-value translations storage
                 * @type {Object}
                 * @private
                 */
                var _data = {};

                /**
                 * Add new translation (two string parameters) or several translations (object)
                 * @param {(Object.<string>|string)}
                 * @param {string}
                 */
                this.add = function() {
                    if (arguments.length > 1) {
                        _data[arguments[0]] = arguments[1];
                    } else if (typeof arguments[0] === 'object') {
                        $.extend(_data, arguments[0]);
                    }
                };

                /**
                 * Make a translation
                 * @param {string} text
                 * @return {string}
                 */
                this.translate = function(text) {
                    return _data[text] ? _data[text] : text;
                };

                return this;
            }())
        }
    });
    /**
     * Sort alias for jQuery.mage.translate.translate method
     * @type {function(string): string}
     */
    $.mage.__ = $.proxy($.mage.translate.translate, $.mage.translate);
})(jQuery);
