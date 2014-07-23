/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
(function (factory) {
    if (typeof define !== 'undefined' && define.amd) {
        define([
            'jquery',
            'mage/backend/validation'
        ], factory);
    } else {
        factory(window.jQuery);
    }
})(function ($) {
    "use strict";
    var rma = window.rma;
    if (rma === undefined) {
        return;
    }
    rma.addLoadProductsCallback(function () {
        $('[class^="rma-action-links-"]').each(function (el, val) {
            var className = false;
            $(val).attr('class').split(' ').each(function (el, val) {
                if (el.search(/rma-action-links-/i) !== -1) {
                    className = el;
                }
            });
            $.validator.addMethod(
                className,
                function (v, elem) {
                    var isValid = true;
                    var columnId = $(elem).parents().children('[id^=itemDiv_]').attr('id');
                    $('#' + columnId).find('.mage-error').each(function (el, val) {
                        if ($(val).css('display') != 'none') {
                            isValid = false;
                        }
                    });
                    return isValid;
                },
                $.mage.__("Click Details for more required fields.")
            );
        });
    });
});
